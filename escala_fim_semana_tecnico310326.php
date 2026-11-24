<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$matricula = trim($_SESSION['matricula']);
$perfil = $_SESSION['perfil'];

if ($perfil == null || $perfil != 2) {
    echo "<script>alert('Apenas coordenadores têm acesso'); window.location='index.php';</script>";
    exit;
}

include 'conecta.php';

$diaEncerramento = 5; // padrão: sexta-feira
$horaEncerramento = 15; // padrão: 15h
$sqlConfigEscala = "SELECT dia_encerramento, hora_encerramento FROM tbconfiguracao_escala_tecnicos ORDER BY id DESC LIMIT 1";
$resultConfigEscala = mysqli_query($conn, $sqlConfigEscala);

if ($resultConfigEscala && mysqli_num_rows($resultConfigEscala) > 0) {
    $configEscala = mysqli_fetch_assoc($resultConfigEscala);
    $diaTabela = (int)$configEscala['dia_encerramento'];
    $horaTabela = (int)$configEscala['hora_encerramento'];

    if ($diaTabela >= 0 && $diaTabela <= 6) {
        $diaEncerramento = $diaTabela;
    }

    if ($horaTabela >= 0 && $horaTabela <= 23) {
        $horaEncerramento = $horaTabela;
    }
}

$diasSemana = array('domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado');
$diaEncerramentoTexto = $diasSemana[$diaEncerramento];

/* ==============================
    COORDENADOR
============================== */
$sql_coord = "SELECT c.idtbcoordenador 
              FROM bdfrota.tbcoord c
              INNER JOIN bdaniel.tbfuncionario f ON c.matricula = f.matricula
              WHERE c.matricula = '$matricula'
              AND f.status = 'Ativo'";

$result_coord = mysql_query($sql_coord) or die(mysql_error());
$dados_coord = mysql_fetch_array($result_coord);

if(!$dados_coord){
    die("Coordenador não encontrado.");
}

$idtbcoordenadorlogado = $dados_coord[0];

/* ==============================
    SEMANA ATUAL (FIXA)
============================== */
$segunda = strtotime('monday this week');
$sabado  = date('Y-m-d', strtotime('+5 days', $segunda));
$domingo = date('Y-m-d', strtotime('+6 days', $segunda));

/* ==============================
    SUPERVISORES
============================== */
$sql_sup_ids = "SELECT idtbsupervisor 
                FROM bdfrota.tbsupervisor 
                WHERE idtbcoordenador = $idtbcoordenadorlogado";

$result_sup_ids = mysql_query($sql_sup_ids);
$lista_ids_sups = [];

while($row = mysql_fetch_array($result_sup_ids)) {
    $lista_ids_sups[] = $row[0];
}

$tecnicos_data = [];

if (!empty($lista_ids_sups)) {

    $lista_ids_sups_str = implode(',', $lista_ids_sups);

    /* ==============================
        TÉCNICOS
    ============================== */
    $sql_tecnicos = "SELECT DISTINCT u.matricula, f.nome, u.idtbsupervisor 
                     FROM bdfrota.tbusuario u
                     INNER JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                     WHERE u.perfil = 0
                     AND f.status = 'Ativo'
                     AND u.idtbsupervisor IN ($lista_ids_sups_str)
                     ORDER BY f.nome";

    $result_tecnicos = mysql_query($sql_tecnicos) or die(mysql_error());

    /* ==============================
        ESCALAS DA SEMANA 
    ============================== */
    $sql_escalas = "SELECT matricula, DATE(dia) as dia, status
                    FROM bdfrota.tbescala
                    WHERE DATE(dia) IN ('$sabado','$domingo')
                    AND matricula IN (
                        SELECT u.matricula
                        FROM bdfrota.tbusuario u
                        WHERE u.idtbsupervisor IN ($lista_ids_sups_str)
                    )";

    $result_escalas = mysql_query($sql_escalas) or die(mysql_error());

    $escalas = [];

    while($row_escala = mysql_fetch_array($result_escalas)) {

        $mat = trim($row_escala['matricula']);
        $dia = $row_escala['dia']; // já vem como Y-m-d por causa do DATE()
        $status = $row_escala['status'];

        $escalas[$mat][$dia] = $status;
    }

    /* ==============================
        MONTA ARRAY FINAL
    ============================== */
    while($tecnico = mysql_fetch_array($result_tecnicos)) {

        $matricula_tec = trim($tecnico[0]);
        $nome_tecnico  = $tecnico[1];
        $id_supervisor = $tecnico[2];

        // Nome supervisor
        $nome_supervisor = 'Sem supervisor';

        if($id_supervisor) {
            $sql_sup_nome = "SELECT f.nome
                             FROM bdfrota.tbsupervisor s
                             INNER JOIN bdaniel.tbfuncionario f ON s.matricula = f.matricula
                             WHERE s.idtbsupervisor = $id_supervisor";

            $result_sup_nome = mysql_query($sql_sup_nome);

            if($row_sup = mysql_fetch_array($result_sup_nome)) {
                $nome_supervisor = $row_sup[0];
            }
        }

        $sabado_status  = isset($escalas[$matricula_tec][$sabado])  ? $escalas[$matricula_tec][$sabado]  : 0;
        $domingo_status = isset($escalas[$matricula_tec][$domingo]) ? $escalas[$matricula_tec][$domingo] : 0;

        $tecnicos_data[] = [
            'matricula' => $matricula_tec,
            'nome' => $nome_tecnico,
            'supervisor' => $nome_supervisor,
            'sabado_status' => $sabado_status,
            'domingo_status' => $domingo_status
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escala Fim de Semana - Técnicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        body { 
            background: linear-gradient(135deg, #ffffff 0%,  #ffffff); 
            color:  #ffffff !important;
        }
        .card-escala { 
            border-left: 5px solid #28a745; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        }
        .sabado { background-color: #e3f2fd !important; }
        .domingo { background-color: #f5edf1 !important; }
        .status-ativo { 
            background: linear-gradient(45deg, #d4edda, #c3e6cb) !important; 
            border: 2px solid  #d4edda !important;
        }
        .status-reserva { 
            background: linear-gradient(45deg, #fff3cd, #ffeaa7) !important; 
            border: 2px solid #ffc107 !important;
        }
        .supervisor-tag { 
            background: linear-gradient(45deg, #6c757d, #5a6268); 
            border-radius: 20px; 
            padding: 6px 12px; 
            font-size: 0.85em; 
            color: white !important;
            font-weight: 500;
        }
        .btn-salvar-todos { 
            background: linear-gradient(45deg, #28a745, #20c997) !important; 
            border: none; 
            font-weight: bold;
            color: white !important;
        }
        .btn-salvar-todos:hover { 
            transform: scale(1.05); 
            box-shadow: 0 5px 15px rgba(40,167,69,0.4); 
            color: white !important;
        }
        .card-header {
            background: linear-gradient(45deg, #0d6efd, #083b65) !important;
            color: white !important;
        }
        .table-dark th {
            background: linear-gradient(45deg, #343a40, #495057) !important;
            color: white !important;
        }
        
        /* Botão flutuante */
        .btn-salvar-flutuante {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            padding: 20px 40px;
            font-size: 18px;
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
            background: linear-gradient(45deg, #28a745, #20c997) !important;
            border: none;
            color: white !important;
            font-weight: bold;
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }
        
        .btn-salvar-flutuante:hover {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 12px 35px rgba(40, 167, 69, 0.6);
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4); }
            50% { box-shadow: 0 8px 35px rgba(40, 167, 69, 0.7); }
        }
        
        .btn-salvar-flutuante.hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-5">
        <!-- HEADER -->
        <div class="card shadow-lg mb-5 card-escala">
            <div class="card-header py-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-0 fw-bold text-white">
                            <i class="fas fa-calendar-week me-3"></i>
                            Escala Fim de Semana <?php  //print_r($tecnicos_data); ?>
                            <span class="badge bg-light text-dark ms-2 fs-6">
                                <?php echo count($tecnicos_data); ?> Técnicos
                            </span>
                        </h3>
                        <small class="text-white-50">
                            📅 Sábado <?php echo date('d/m/Y', strtotime($sabado)); ?> | 
                            Domingo <?php echo date('d/m/Y', strtotime($domingo)); ?>
                            <span class="badge bg-warning text-dark ms-2">
                                🔒 Fecha: <?php echo ucfirst($diaEncerramentoTexto); ?> <?php echo $horaEncerramento; ?>h
                            </span>
                            <span class="badge bg-success ms-2">
                                <?php 
                                $total_escalados = 0;
                                foreach($tecnicos_data as $t) {
                                    if($t['sabado_status'] || $t['domingo_status']) $total_escalados++;
                                }
                                echo $total_escalados; 
                                ?> escalados
                            </span>
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end">
                         <a href="gerar_excel_escala_tecnico.php" class="btn btn-success btn-lg me-2">
                             <i class="fas fa-file-excel me-2"></i>Gerar Excel
                         </a>
                        <button class="btn btn-outline-light btn-lg" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-2"></i>Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABELA -->
        <div class="card shadow-lg card-escala">
            <div class="card-body p-0">
                <table id="tabelaEscala" class="table table-hover mb-0" style="width:100%">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 35%;"><i class="fas fa-user me-1"></i>Técnico</th>
                            <th style="width: 25%;"><i class="fas fa-users-cog me-1"></i>Supervisor</th>
                            <th style="width: 20%; text-align: center;">📅 Sábado</th>
                            <th style="width: 20%; text-align: center;">📅 Domingo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tecnicos_data as $tecnico): ?>
                        <tr>
                            <td class="fw-bold ps-3 align-middle">
                                <div><?php echo htmlspecialchars($tecnico['nome']); ?></div>
                                <small class="text-muted d-block"><?php echo $tecnico['matricula']; ?></small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="supervisor-tag"><?php echo htmlspecialchars($tecnico['supervisor']); ?></span>
                            </td>
                            <td class="text-center sabado align-middle <?php echo $tecnico['sabado_status'] ? ($tecnico['sabado_status']==1 ? 'status-ativo' : 'status-reserva') : ''; ?>">
                                <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                    <input class="form-check-input escala-checkbox mx-2" type="checkbox" 
                                           data-matricula="<?php echo $tecnico['matricula']; ?>" 
                                           data-dia="<?php echo $sabado; ?>"
                                           data-status="<?php echo $tecnico['sabado_status'] ?: 1; ?>"
                                           <?php echo $tecnico['sabado_status'] ? 'checked' : ''; ?>
                                           onchange="toggleStatus(event, this)">
                                    <label class="form-check-label fw-bold fs-6 mb-0">
                                        <?php echo $tecnico['sabado_status']==1 ? '✅ ESCALADO' : 
                                                  ($tecnico['sabado_status']==2 ? '⏳ RESERVA' : '❌ NÃO'); ?>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center domingo align-middle <?php echo $tecnico['domingo_status'] ? ($tecnico['domingo_status']==1 ? 'status-ativo' : 'status-reserva') : ''; ?>">
                                <div class="form-check form-switch d-flex justify-content-center align-items-center">
                                    <input class="form-check-input escala-checkbox mx-2" type="checkbox" 
                                           data-matricula="<?php echo $tecnico['matricula']; ?>" 
                                           data-dia="<?php echo $domingo; ?>"
                                           data-status="<?php echo $tecnico['domingo_status'] ?: 1; ?>"
                                           <?php echo $tecnico['domingo_status'] ? 'checked' : ''; ?>
                                           onchange="toggleStatus(event, this)">
                                    <label class="form-check-label fw-bold fs-6 mb-0">
                                        <?php echo $tecnico['domingo_status']==1 ? '✅ ESCALADO' : 
                                                  ($tecnico['domingo_status']==2 ? '⏳ RESERVA' : '❌ NÃO'); ?>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($tecnicos_data)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="fas fa-users fa-4x mb-4 opacity-50"></i>
                                <h4>Nenhum técnico vinculado aos seus supervisores</h4>
                                <p class="lead">Vincule supervisores e técnicos primeiro</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light border-top">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>
                            ✅ Escalados carregados automaticamente | 
                            🔄 Marque/desmarque para editar | 
                            💾 Clique "Salvar"
                        </small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small class="text-muted">
                            <i class="fas fa-search me-2"></i>Ctrl+F rápido | 
                            <i class="fas fa-sort me-2"></i>Ordenar 
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botão Salvar Flutuante -->
    <button id="btnSalvarFlutuante" class="btn-salvar-flutuante" onclick="salvarTodos()">
        <i class="fas fa-save me-2"></i>Salvar Escalas
    </button>

    <script>
 $(document).ready(function() {
    $('#tabelaEscala').DataTable({
        paging: false,        // remove paginação
        searching: false,     // remove busca
        info: false,          // remove "mostrando X de Y registros"
        lengthChange: false,  // remove seletor de quantidade
        order: [[0, 'asc']],  // mantém ordenação inicial
        columnDefs: [
            { targets: [2,3], orderable: false } // mantém essas colunas sem ordenação
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
        }
    });
    
    // Controla visibilidade do botão baseado no horário
    verificarHorario();
    // Verifica a cada minuto
    setInterval(verificarHorario, 60000);
});

const DIA_ENCERRAMENTO = <?php echo (int)$diaEncerramento; ?>;
const HORA_ENCERRAMENTO = <?php echo (int)$horaEncerramento; ?>;
const DIAS_SEMANA = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];

function passouDoDiaEncerramento(diaAtual, diaLimite) {
    if (diaLimite === 0) {
        return diaAtual >= 1 && diaAtual <= 6;
    }
    return diaAtual > diaLimite || diaAtual === 0;
}

function verificarHorario() {
    const agora = new Date();
    const diaSemana = agora.getDay(); // 0=Domingo, 1=Segunda, 2=Terça, 3=Quarta, 4=Quinta, 5=Sexta, 6=Sábado
    const horas = agora.getHours();
    
    const btnSalvar = document.getElementById('btnSalvarFlutuante');
    
    // Período de bloqueio: dia/hora configurados até a virada para segunda-feira
    const bloqueado = (diaSemana === DIA_ENCERRAMENTO && horas >= HORA_ENCERRAMENTO) ||
                      passouDoDiaEncerramento(diaSemana, DIA_ENCERRAMENTO);
    
    if (bloqueado) {
        btnSalvar.classList.add('hidden');
        console.log(`Botão ocultado - ${DIAS_SEMANA[DIA_ENCERRAMENTO]} após ${HORA_ENCERRAMENTO}h até segunda-feira 00:00`);
    } else {
        btnSalvar.classList.remove('hidden');
        
        // Calcula tempo restante até o próximo dia/hora de encerramento configurados
        if (diaSemana >= 1 && diaSemana <= 5) {
            const diasAteVencimento = diaSemana === DIA_ENCERRAMENTO ? 0 : (DIA_ENCERRAMENTO - diaSemana);
            const horasRestantes = diaSemana === DIA_ENCERRAMENTO ? (HORA_ENCERRAMENTO - horas) : ((diasAteVencimento * 24) + (HORA_ENCERRAMENTO - horas));
            console.log(`Botão visível - ${horasRestantes}h até ${DIAS_SEMANA[DIA_ENCERRAMENTO]} ${HORA_ENCERRAMENTO}:00`);
        }
    }
}

    function toggleStatus(event, checkbox) {
        let td = $(checkbox).closest('td');
        let isChecked = checkbox.checked;
        let statusClass = isChecked ? (checkbox.dataset.status == 1 ? 'status-ativo' : 'status-reserva') : '';
        
        // Atualiza cor da célula
        td.removeClass('status-ativo status-reserva').addClass(statusClass);
        
        // Atualiza label
        let label = $(checkbox).next('label');
        if(isChecked) {
            label.text(checkbox.dataset.status == 1 ? '✅ ESCALADO' : '⏳ RESERVA');
            label.removeClass('text-muted').addClass('text-success fw-bold');
        } else {
            label.text('❌ NÃO');
            label.removeClass('text-success').addClass('text-muted');
        }
    }

    function salvarTodos() {
        let escalas = [];
        let totalMarcados = 0;
        
        $('.escala-checkbox:checked').each(function() {
            escalas.push({
                matricula: $(this).data('matricula'),
                dia: $(this).data('dia'),
                status: $(this).data('status') || 1
            });
            totalMarcados++;
        });

        if(totalMarcados === 0) {
            if(confirm('Nenhum técnico escalado. Deseja limpar todas as escalas?')) {
                if(confirm('CONFIRME: Limpar TODAS as escalas do fim de semana?')) {
                    $.post('salvar_escala.php', {limpar: true}, function(resp) {
                        alert('✅ Escalas limpas!');
                        location.reload();
                    }, 'json');
                }
            }
            return;
        }

        // Mostra preview
        let preview = `⚠️ Salvar ${totalMarcados} escalas?\n\n`;
        escalas.forEach((e, i) => {
            preview += `${i+1}. ${e.matricula} - ${e.dia} (${e.status == 1 ? 'ESCALADO' : 'RESERVA'})\n`;
        });

        if(confirm(preview)) {
            $.ajax({
                url: 'salvar_escala.php',
                method: 'POST',
                data: {escalas: JSON.stringify(escalas)},
                dataType: 'json',
                success: function(resp) {
                    if(resp.success) {
                        alert(`✅ SALVO!\n${totalMarcados} escalas atualizadas com sucesso`);
                        location.reload();
                    } else {
                        alert('❌ Erro: ' + (resp.msg || 'Erro desconhecido'));
                    }
                },
                error: function() {
                    alert('❌ Erro de conexão. Verifique salvar_escala.php');
                }
            });
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
