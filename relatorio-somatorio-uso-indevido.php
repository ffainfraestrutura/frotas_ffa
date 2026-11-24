<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$diai = $_POST["diai"];
$diaf = $_POST["diaf"];
// Status fixo para padronização
$situacao = "Aguardando Avaliação do Gestor";

// Filtro de urgência
if (isset($_POST["urgencia"]) && $_POST["urgencia"] != "") {
    $urgencia_filtro = $_POST["urgencia"];
} else {
    $urgencia_filtro = "";
}
$matsup = $_POST["matsupf"];

include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "https://combustivelapi.com.br/api/precos/");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 0);
$resultado = json_decode(curl_exec($curl), true);
$precos = [
    "gasolina" => $resultado['precos']['gasolina']['br'],
    "diesel" => $resultado["precos"]["diesel"]["br"],
]
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Portal FFA - Relatório Somatório Uso Indevido</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- CSS customizado para o relatório de somatório -->
    <style>
        /* Destaque para rankings */
        .ranking-1 {
            background: linear-gradient(135deg, #dc3545, #ff6b7a) !important;
            color: white !important;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .ranking-2 {
            background: linear-gradient(135deg, #fd7e14, #ffb366) !important;
            color: white !important;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(253, 126, 20, 0.3);
        }
        
        .ranking-3 {
            background: linear-gradient(135deg, #ffc107, #ffda6a) !important;
            color: #212529 !important;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }
        
        .ranking-alto {
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-left: 4px solid #dc3545;
        }
        
        .ranking-medio {
            background-color: rgba(253, 126, 20, 0.1) !important;
            border-left: 4px solid #fd7e14;
        }
        
        .ranking-baixo {
            background-color: rgba(40, 167, 69, 0.1) !important;
            border-left: 4px solid #28a745;
        }
        
        /* Badges para quantidade */
        .badge-count {
            font-size: 1em;
            padding: 0.5em 0.8em;
            border-radius: 20px;
        }
        
        /* Animação para top 3 */
        .top-3 {
            animation: pulse-glow 3s infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        
        /* Cabeçalho com gradiente */
        .header-gradient {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        /* Destaque para estatísticas */
        .stats-card {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                
                <!-- Cabeçalho do Relatório -->
                <div class="header-gradient text-center">
                    <h1 class="h1 mb-2">📊 Relatório Somatório - Uso Indevido por Matrícula</h1>
                    <p class="mb-0">Ranking dos funcionários com mais ocorrências de uso indevido (maior para menor)</p>
                </div>
                
                <!-- Aviso sobre Status -->
                <div class="alert alert-info mb-4">
                    <h6><i class="fas fa-info-circle"></i> <strong>Informações do Relatório:</strong></h6>
                    <ul class="mb-0">
                        <li>📈 <strong>Ranking por Quantidade:</strong> Funcionários ordenados do maior para o menor número de ocorrências</li>
                        <li>⏱️ <strong>Período considerado:</strong> Baseado na data da ocorrência selecionada</li>
                        <li>🎯 <strong>Status padrão:</strong> "Aguardando Avaliação do Gestor" para todos os registros</li>
                    </ul>
                </div>

                <form method="post">
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o periodo (Data da Ocorrência):</b></h5>
                        
                        <div class="col-md-3">
                            <label>
                                <h5>Início:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md mb-3" name="diai"
                                value="<?php echo $diai ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label>
                                <h5>Fim:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md mb-3" name="diaf"
                                value="<?php echo $diaf ?>" required>
                        </div>
                        
                        <div class="col-md-3">
                            <h5><b>Status (Fixo para Padronização)</b></h5>
                            <input type="text" class="form-control form-control-md mb-3" 
                                   value="Aguardando Avaliação do Gestor" 
                                   readonly 
                                   style="background-color: #e9ecef; color: #495057; font-weight: 500;">
                            <input type="hidden" name="situacao" value="Aguardando Avaliação do Gestor">
                        </div>
                        
                        <div class="col-md-3">
                            <h5><b>Filtrar por Urgência</b></h5>
                            <select class="form-control form-control-md mb-3" name="urgencia" id="Urgencia">
                                <option value="">Todos os Níveis</option>
                                <option value="normal" <?php echo $urgencia_filtro == "normal" ? "selected" : ""; ?>>
                                    Normal (até 2 dias)
                                </option>
                                <option value="atencao" <?php echo $urgencia_filtro == "atencao" ? "selected" : ""; ?>>
                                    Atenção (3 a 5 dias)
                                </option>
                                <option value="urgente" <?php echo $urgencia_filtro == "urgente" ? "selected" : ""; ?>>
                                    Urgente (mais de 5 dias)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class='mt-4 d-flex justify-content-between' style="width: 50%;">
                        <div class="mt-12">
                            <input type="submit" class="btn btn-success btn-lg" value="🔍 Gerar Relatório">
                        </div>
                    </div>
                </form>

                <div class="mt-4">
                    <h5 class="text-danger"><b><?php if ($diaf <> '') {
                        $dataf = explode("-", $diaf);
                        $datat = $dataf[2] . "/" . $dataf[1] . "/" . $dataf[0];
                        $datai = explode("-", $diai);
                        $datati = $datai[2] . "/" . $datai[1] . "/" . $datai[0];
                        print "📅 Período analisado: $datati até $datat";
                        
                        // Mostrar filtro de urgência aplicado
                        if ($urgencia_filtro != "") {
                            $filtro_texto = "";
                            switch($urgencia_filtro) {
                                case "normal": $filtro_texto = "✅ Normal (até 2 dias)"; break;
                                case "atencao": $filtro_texto = "⚠️ Atenção (3 a 5 dias)"; break; 
                                case "urgente": $filtro_texto = "🚨 Urgente (mais de 5 dias)"; break;
                            }
                            print " | 🎯 Filtrado por: $filtro_texto";
                        }
                    } ?></b></h5>
                </div>

                <div class="card mt-4 mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard
                        </a>
                        <div class="">
                            <button type="submit" class="btn btn-success" onclick="gerarExcel()">
                                <i class="fas fa-file-excel"></i> Gerar Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        
                        <?php if ($diai != ""): ?>
                        <!-- Estatísticas Resumo -->
                        <div class="row mb-4">
                            <?php
                            // Query para estatísticas
                            $sqlStats = "SELECT 
                                            COUNT(DISTINCT td.matricula) as total_funcionarios,
                                            COUNT(*) as total_ocorrencias,
                                            ROUND(AVG(subquery.qtd_por_funcionario), 1) as media_por_funcionario
                                        FROM bdfrota.tbdescontousoindevido td
                                        JOIN bdfrota.tbveiculo tv ON td.placa = tv.placa
                                        JOIN bdfrota.tbconsumocombustivel tc ON tv.tipovel = tc.idtbconsumocombustivel
                                        LEFT JOIN bdaniel.tbfuncionario f ON td.matricula = f.matricula
                                        JOIN (
                                            SELECT matricula, COUNT(*) as qtd_por_funcionario
                                            FROM bdfrota.tbdescontousoindevido
                                            WHERE data_ocorrencia >= '{$diai}' 
                                            AND data_ocorrencia <= '{$diaf}'
                                            AND situacao LIKE '%$situacao%'
                                            GROUP BY matricula
                                        ) subquery ON td.matricula = subquery.matricula
                                        WHERE td.data_ocorrencia >= '{$diai}' 
                                        AND td.data_ocorrencia <= '{$diaf}'
                                        AND td.situacao LIKE '%$situacao%'
                                        AND (f.status IS NULL OR f.status != 'Demitido')";
                            
                            $resultStats = mysql_query($sqlStats) or die(mysql_error());
                            $stats = mysql_fetch_array($resultStats);
                            ?>
                            
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <h3><?php echo $stats['total_funcionarios']; ?></h3>
                                    <p>👥 Funcionários com Ocorrências</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <h3><?php echo $stats['total_ocorrencias']; ?></h3>
                                    <p>📊 Total de Ocorrências</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-card">
                                    <h3><?php echo $stats['media_por_funcionario']; ?></h3>
                                    <p>📈 Média por Funcionário</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead class="table-dark">
                                <tr>
                                    <th>🏆 Ranking</th>
                                    <th>Matrícula</th>
                                    <th>Nome do Funcionário</th>
                                    <th>Centro de Custo</th>
                                    <th>📊 Total de Ocorrências</th>
                                    <th>💰 Custo Total Estimado</th>
                                    <th>📅 Período das Ocorrências</th>
                                    <th>⚠️ Nível de Criticidade</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($diai != "") {
                                    // Query para somatório por matrícula
                                    $sql = "SELECT 
                                                td.matricula,
                                                f.nome AS nome_funcionario,
                                                f.ccusto,
                                                COUNT(*) as total_ocorrencias,
                                                MIN(td.data_ocorrencia) as primeira_ocorrencia,
                                                MAX(td.data_ocorrencia) as ultima_ocorrencia,
                                                SUM(CASE 
                                                    WHEN td.valor_desconto IS NOT NULL THEN td.valor_desconto
                                                    ELSE ROUND((td.kmtotal * (
                                                        CASE 
                                                            WHEN LOWER(tc.tipocombustivel) LIKE '%gasolina%' THEN {$precos['gasolina']} / tc.consumo_litro
                                                            ELSE {$precos['diesel']} / tc.consumo_litro
                                                        END
                                                    )), 2)
                                                END) as custo_total
                                            FROM bdfrota.tbdescontousoindevido td
                                            JOIN bdfrota.tbveiculo tv ON td.placa = tv.placa
                                            JOIN bdfrota.tbconsumocombustivel tc ON tv.tipovel = tc.idtbconsumocombustivel
                                            LEFT JOIN bdaniel.tbfuncionario f ON td.matricula = f.matricula
                                            WHERE td.data_ocorrencia >= '{$diai}' 
                                            AND td.data_ocorrencia <= '{$diaf}'
                                            AND td.situacao LIKE '%$situacao%'
                                            AND (f.status IS NULL OR f.status != 'Demitido')
                                            GROUP BY td.matricula, f.nome, f.ccusto
                                            ORDER BY total_ocorrencias DESC, custo_total DESC";

                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    $dados = array();
                                    
                                    // Coleta todos os dados
                                    while ($row = mysql_fetch_array($resultado)) {
                                        // Se filtro de urgência estiver aplicado, precisa verificar cada ocorrência
                                        if ($urgencia_filtro != "") {
                                            $sqlUrgencia = "SELECT COUNT(*) as count_urgencia 
                                                           FROM bdfrota.tbdescontousoindevido 
                                                           WHERE matricula = '{$row['matricula']}'
                                                           AND data_ocorrencia >= '{$diai}' 
                                                           AND data_ocorrencia <= '{$diaf}'
                                                           AND situacao LIKE '%$situacao%'";
                                            
                                            $resultUrgencia = mysql_query($sqlUrgencia);
                                            $countTotal = mysql_fetch_array($resultUrgencia)['count_urgencia'];
                                            
                                            // Conta quantas ocorrências atendem ao filtro de urgência
                                            $sqlFiltroUrgencia = "SELECT data_ocorrencia 
                                                                 FROM bdfrota.tbdescontousoindevido 
                                                                 WHERE matricula = '{$row['matricula']}'
                                                                 AND data_ocorrencia >= '{$diai}' 
                                                                 AND data_ocorrencia <= '{$diaf}'
                                                                 AND situacao LIKE '%$situacao%'";
                                            
                                            $resultFiltro = mysql_query($sqlFiltroUrgencia);
                                            $countFiltrado = 0;
                                            
                                            while ($ocorrencia = mysql_fetch_array($resultFiltro)) {
                                                $dataOcorrencia = new DateTime($ocorrencia['data_ocorrencia']);
                                                $dataAtual = new DateTime();
                                                $diasDecorridos = $dataAtual->diff($dataOcorrencia)->days;
                                                
                                                $nivel_urgencia = "";
                                                if ($diasDecorridos <= 2) {
                                                    $nivel_urgencia = "normal";
                                                } elseif ($diasDecorridos > 2 && $diasDecorridos <= 5) {
                                                    $nivel_urgencia = "atencao";
                                                } else {
                                                    $nivel_urgencia = "urgente";
                                                }
                                                
                                                if ($nivel_urgencia == $urgencia_filtro) {
                                                    $countFiltrado++;
                                                }
                                            }
                                            
                                            // Se não tem ocorrências que atendem o filtro, pula
                                            if ($countFiltrado == 0) {
                                                continue;
                                            }
                                            
                                            // Atualiza o total de ocorrências para mostrar apenas as filtradas
                                            $row['total_ocorrencias'] = $countFiltrado;
                                        }
                                        
                                        $dados[] = $row;
                                    }
                                    
                                    $ranking = 1;
                                    foreach ($dados as $row) {
                                        // Define classes para ranking
                                        $classeRow = "";
                                        $classeRanking = "";
                                        
                                        if ($ranking == 1) {
                                            $classeRow = "ranking-1 top-3";
                                            $classeRanking = "🥇";
                                        } elseif ($ranking == 2) {
                                            $classeRow = "ranking-2 top-3";
                                            $classeRanking = "🥈";
                                        } elseif ($ranking == 3) {
                                            $classeRow = "ranking-3 top-3";
                                            $classeRanking = "🥉";
                                        } elseif ($row['total_ocorrencias'] >= 5) {
                                            $classeRow = "ranking-alto";
                                            $classeRanking = "🔴";
                                        } elseif ($row['total_ocorrencias'] >= 3) {
                                            $classeRow = "ranking-medio";
                                            $classeRanking = "🟡";
                                        } else {
                                            $classeRow = "ranking-baixo";
                                            $classeRanking = "🟢";
                                        }
                                        
                                        // Badge para quantidade
                                        $badgeCount = "";
                                        if ($row['total_ocorrencias'] >= 10) {
                                            $badgeCount = "<span class='badge bg-danger badge-count'>{$row['total_ocorrencias']}</span>";
                                        } elseif ($row['total_ocorrencias'] >= 5) {
                                            $badgeCount = "<span class='badge bg-warning badge-count'>{$row['total_ocorrencias']}</span>";
                                        } else {
                                            $badgeCount = "<span class='badge bg-success badge-count'>{$row['total_ocorrencias']}</span>";
                                        }
                                        
                                        // Nível de criticidade
                                        $criticidade = "";
                                        if ($row['total_ocorrencias'] >= 10) {
                                            $criticidade = "<span class='badge bg-danger'>🚨 CRÍTICO</span>";
                                        } elseif ($row['total_ocorrencias'] >= 5) {
                                            $criticidade = "<span class='badge bg-warning text-dark'>⚠️ ALTO</span>";
                                        } elseif ($row['total_ocorrencias'] >= 3) {
                                            $criticidade = "<span class='badge bg-info'>ℹ️ MÉDIO</span>";
                                        } else {
                                            $criticidade = "<span class='badge bg-success'>✅ BAIXO</span>";
                                        }
                                        
                                        // Período formatado
                                        $primeira = date('d/m/Y', strtotime($row['primeira_ocorrencia']));
                                        $ultima = date('d/m/Y', strtotime($row['ultima_ocorrencia']));
                                        $periodo = $primeira == $ultima ? $primeira : "$primeira a $ultima";
                                        
                                        echo "<tr class='$classeRow'>
                                                <td class='text-center'>$classeRanking $ranking°</td>
                                                <td><strong>{$row['matricula']}</strong></td>
                                                <td>{$row['nome_funcionario']}</td>
                                                <td>{$row['ccusto']}</td>
                                                <td class='text-center'>$badgeCount</td>
                                                <td><strong>R$ " . number_format($row['custo_total'], 2, ',', '.') . "</strong></td>
                                                <td class='text-center'>$periodo</td>
                                                <td class='text-center'>$criticidade</td>
                                            </tr>";
                                        
                                        $ranking++;
                                    }
                                } else {
                                    print "<tr>
                                        <td colspan='8' class='text-center'>
                                            <div class='alert alert-warning'>
                                                <i class='fas fa-exclamation-triangle'></i>
                                                <strong>Selecione um período</strong> para gerar o relatório de somatório
                                            </div>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tabela = document.querySelector('#datatablesSimple');
        
        if (tabela) {
            new simpleDatatables.DataTable(tabela, {
                perPage: 15,
                perPageSelect: [15, 25, 50, 100, -1],
                searchable: true,
                sortable: true
            });

            var select = tabela.closest('.dataTable-container').querySelector('select');
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value == -1) {
                    select.options[i].text = "Todos";
                }
            }
        }
    });

    function gerarExcel() {
        const tabela = document.querySelector('#datatablesSimple');

        // Cabeçalhos
        const headers = Array.from(tabela.querySelectorAll('thead th'))
            .map(th => th.textContent.trim().replace(/[🏆📊💰📅⚠️🥇🥈🥉🔴🟡🟢]/g, ''));

        // Linhas de dados visíveis
        const linhas = Array.from(tabela.querySelectorAll('tbody tr'));
        const dadosTratados = linhas.map(tr => {
            const tds = Array.from(tr.querySelectorAll('td'));
            return tds.map((td, index) => {
                // Remove emojis e badges, mantém apenas texto
                let texto = td.textContent.trim();
                texto = texto.replace(/[🥇🥈🥉🔴🟡🟢🚨⚠️ℹ️✅]/g, '');
                return texto;
            });
        });

        // Informações sobre filtros aplicados
        const filtroUrgencia = document.querySelector('#Urgencia').value;
        const filtroSituacao = "Aguardando Avaliação do Gestor";
        
        let infoFiltros = [`Situação: ${filtroSituacao}`];
        if (filtroUrgencia) {
            const textoFiltro = document.querySelector('#Urgencia option:checked').textContent;
            infoFiltros.push(`Urgência: ${textoFiltro}`);
        }

        // Monta a planilha com informações
        let planilha = [];
        planilha.push(['RELATÓRIO SOMATÓRIO - USO INDEVIDO POR MATRÍCULA']);
        planilha.push([]);
        if (infoFiltros.length > 0) {
            planilha.push([`Filtros aplicados: ${infoFiltros.join(' | ')}`]);
        }
        planilha.push([`Relatório gerado em: ${new Date().toLocaleString('pt-BR')}`]);
        planilha.push([]);
        planilha.push(headers);
        planilha.push(...dadosTratados);

        // Gera o arquivo Excel
        const worksheet = XLSX.utils.aoa_to_sheet(planilha);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Somatório Uso Indevido");
        
        // Nome do arquivo
        const agora = new Date();
        const dataFormatada = agora.toISOString().slice(0, 10);
        let nomeArquivo = `somatorio_uso_indevido_${dataFormatada}`;
        if (filtroUrgencia) {
            nomeArquivo += `_${filtroUrgencia}`;
        }
        nomeArquivo += '.xlsx';
        
        XLSX.writeFile(workbook, nomeArquivo);
    }
</script>

</html>
