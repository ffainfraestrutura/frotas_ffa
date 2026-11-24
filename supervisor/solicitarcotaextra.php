<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

include_once "./controleacesso.php";
include_once "../conecta.php";
include_once "../func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Solicitação Cota Extra</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">


                <h1 class="h1 pt-1"> Solicitação </h1>

                <p class="fs-5 fw-bold">Supervisor: <?php echo "$nome"; ?></p>

                <?php
                // ==========================================
                // SISTEMA DE NOTIFICAÇÕES DE MANUTENÇÕES
                // ==========================================
                
                $alertas = [];

                // 1. Buscar o supervisor logado
                $sql_supervisor = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor WHERE matricula = '$matricula'";
                $resultado_supervisor = mysql_query($sql_supervisor) or die(mysql_error());
                $row_supervisor = mysql_fetch_array($resultado_supervisor);
                $idtbsupervisor = $row_supervisor[0];

                // 2. Calcular período da semana atual (segunda a domingo)
                $hoje = date('Y-m-d');
                $dia_semana = date('N'); // 1 = segunda, 7 = domingo

                if ($dia_semana == 1) {
                    $inicio_semana = $hoje; // Se hoje é segunda
                } else {
                    $inicio_semana = date('Y-m-d', strtotime('last monday'));
                }
                $fim_semana = date('Y-m-d', strtotime($inicio_semana . ' +6 days'));

                // 3. Buscar TODAS as manutenções da semana dos técnicos deste supervisor
                //    EXCETO as que já foram visualizadas HOJE (data_visualizacao = hoje)
                //    Isso permite que apareçam novamente amanhã (Notificação Diária)!
                $sql_manutencoes = "SELECT DISTINCT
                                        mp.idtbmanprev,
                                        mp.placa,
                                        f.nome,
                                        DATE_FORMAT(mp.dataagendamento, '%d/%m/%Y') AS data_agendamento,
                                        mp.tipo,
                                        mp.oficina
                                    FROM 
                                        bdfrota.tbmanprev mp
                                    JOIN 
                                        bdfrota.tbveiculo v ON mp.placa = v.placa
                                    JOIN 
                                        bdfrota.tbusuario u ON v.matcond = u.matricula
                                    JOIN 
                                        bdaniel.tbfuncionario f ON u.matricula = f.matricula
                                    LEFT JOIN
                                        bdfrota.tbnotificacoes_visualizadas nv 
                                        ON nv.referencia_id = mp.idtbmanprev 
                                        AND nv.matricula = '$matricula'
                                        AND nv.tipo_notificacao = 'manutencao'
                                        AND DATE(nv.data_visualizacao) = CURDATE()
                                    WHERE 
                                        u.idtbsupervisor = '$idtbsupervisor'
                                        AND mp.dataagendamento BETWEEN '$inicio_semana' AND '$fim_semana'
                                        AND mp.status = 'ABERTO'
                                        AND mp.tipo <> 'Desativada'
                                        AND nv.id IS NULL
                                    ORDER BY mp.dataagendamento, mp.placa";

                $resultado_manutencoes = mysql_query($sql_manutencoes) or die(mysql_error());

                // 4. Montar array de alertas com tradução de tipos
                $tipo_traducao = [
                    'MP' => 'Manutenção Preventiva',
                    'OS' => 'Ordem de Serviço',
                    'MC' => 'Manutenção Corretiva',
                    'SS' => 'Sinistro'
                ];
                
                while ($row_man = mysql_fetch_assoc($resultado_manutencoes)) {
                    $tipo_man = $row_man['tipo'];
                    
                    $alertas[] = [
                        'idtbmanprev' => $row_man['idtbmanprev'],
                        'placa' => $row_man['placa'],
                        'tecnico' => $row_man['nome'],
                        'data' => $row_man['data_agendamento'],
                        'tipo' => isset($tipo_traducao[$tipo_man]) ? $tipo_traducao[$tipo_man] : $tipo_man,
                        'oficina' => $row_man['oficina']
                    ];
                }
                ?>

                <form action="./control/solicitarcotaextra.php" method="post">
                    <!--div solicitação-->
                    <div>
                        <label class="form-label mb-0 fs-5" for="ivalor">Solicitação:</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text">R$: </span>
                            <input class="form-control" type="text" name="valor" id="ivalor" required="true"
                                placeholder="Informe o valor" onkeyup="formatarMoeda()">
                        </div>
                    </div>

                    <!--div justificativa-->
                    <div class="mb-3">
                        <label class="form-label mb-0 fs-5" for="ijust">Justificativa:</label>
                        <textarea step="0.01" rows="10" class="form-control" name="justificativa"
                            placeholder="Escreva a justificativa" required="true"></textarea>
                    </div>
                    <?php
                    print "<input type='hidden' name='matricula' value='$matricula'>";
                    ?>
                    <!--div botão confirmar-->
                    <div>
                        <button class="btn btn-success" type="submit"> Confirmar </button>
                    </div>

                </form>
            </div>
        </main>

        <!-- Modal de Alerta -->
        <div class="modal fade" id="modalAlerta" tabindex="-1" aria-labelledby="modalAlertaLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="modalAlertaLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>Manutenções Agendadas
                        </h5>
                        <button type="button" class="btn-close btn-close-white" onclick="fecharSemMarcar()"></button>
                    </div>
                    <div class="modal-body" id="modalAlertaBody">
                        <!-- Conteúdo será preenchido dinamicamente -->
                    </div>
                    <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                        <a href="../gestao/listagem-manutencoes-abertas-tecsup.php" class="btn btn-sm btn-outline-primary" target="_blank" title="Ver todas as manutenções da equipe">
                            <i class="fas fa-list me-1"></i>Ver Listagem Completa
                        </a>
                        <div>
                            <button type="button" class="btn btn-secondary" onclick="lembraDepois()">
                                <i class="fas fa-clock me-1"></i>Lembrar Depois
                            </button>
                            <button type="button" class="btn btn-dark" onclick="marcarComoLida()">
                                <i class="fas fa-check me-1"></i>Entendi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // ==========================================
        // SISTEMA DE NOTIFICAÇÕES COM FLAGUERIZAÇÃO
        // ==========================================
        
        const alertas = <?php echo json_encode($alertas); ?>;
        let currentIndex = 0;
        let modalInstance = null;

        document.addEventListener("DOMContentLoaded", function () {
            if (alertas.length > 0) {
                mostrarAlerta(0);
            }
        });

        // Função para exibir um alerta específico
        function mostrarAlerta(index) {
            if (index >= alertas.length) return;
            
            currentIndex = index;
            const alerta = alertas[index];
            const body = document.getElementById("modalAlertaBody");
            
            body.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-wrench fa-3x text-dark mb-3"></i>
                    <p class="fs-6 mb-2"><b>Placa:</b> ${alerta.placa}</p>
                    <p class="fs-6 mb-2"><b>Técnico:</b> ${alerta.tecnico}</p>
                    <p class="fs-6 mb-2"><b>Data:</b> ${alerta.data}</p>
                    <p class="fs-6 mb-2"><b>Tipo:</b> ${alerta.tipo}</p>
                    <p class="fs-6 mb-2"><b>Oficina:</b> ${alerta.oficina || 'Não informada'}</p>
                    <hr class="border-dark">
                    <p class="text-secondary small mb-0">Manutenção ${index + 1} de ${alertas.length}</p>
                    <input type="hidden" id="currentIdManprev" value="${alerta.idtbmanprev}">
                </div>
            `;

            const modalElement = document.getElementById('modalAlerta');
            modalInstance = new bootstrap.Modal(modalElement);
            modalInstance.show();
        }

        // Função: Marcar como LIDA (não mostra mais hoje)
        function marcarComoLida() {
            const idtbmanprev = document.getElementById('currentIdManprev').value;
            
            // Fechar o modal IMEDIATAMENTE para melhor UX
            if (modalInstance) {
                modalInstance.hide();
            }
            
            // Enviar requisição para marcar como visualizada (em background)
            fetch('./control/marcar_notificacao_visualizada.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'idtbmanprev=' + idtbmanprev
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✅ Notificação marcada como visualizada');
                } else {
                    console.error('❌ Erro ao marcar:', data.message);
                }
            })
            .catch(error => {
                console.error('❌ Erro na requisição:', error);
            });
            
            // Mostrar próxima notificação (se houver)
            const nextIndex = currentIndex + 1;
            if (nextIndex < alertas.length) {
                setTimeout(() => {
                    mostrarAlerta(nextIndex);
                }, 300);
            }
        }

        // Função: Lembrar Depois (NÃO marca, mostra novamente)
        function lembraDepois() {
            console.log('⏰ Adiado para próxima visualização');
            
            // Fechar modal atual
            if (modalInstance) {
                modalInstance.hide();
            }
            
            // Mostrar próxima notificação (se houver)
            const nextIndex = currentIndex + 1;
            if (nextIndex < alertas.length) {
                setTimeout(() => {
                    mostrarAlerta(nextIndex);
                }, 300);
            }
        }

        // Função: Fechar sem marcar (mesmo comportamento de Lembrar Depois)
        function fecharSemMarcar() {
            console.log('✖️ Fechado sem marcar');
            
            // Fechar modal atual
            if (modalInstance) {
                modalInstance.hide();
            }
            
            // Mostrar próxima notificação (se houver)
            const nextIndex = currentIndex + 1;
            if (nextIndex < alertas.length) {
                setTimeout(() => {
                    mostrarAlerta(nextIndex);
                }, 300);
            }
        }
    </script>

    <script>
        //função para formatar
        function formatarMoeda() {
            var elemento = document.getElementById('ivalor');
            var valor = elemento.value;


            valor = valor + '';
            valor = parseInt(valor.replace(/[\D]+/g, ''));
            valor = valor + '';
            valor = valor.replace(/([0-9]{2})$/g, ",$1");

            if (valor.length > 6) {
                valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
            }

            elemento.value = valor;
            if (valor == 'NaN') elemento.value = '';

        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="../src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</body>

</html>