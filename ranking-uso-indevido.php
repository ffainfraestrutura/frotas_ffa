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
$matsup = $_POST["matsupf"];
$filtro_quantidade = $_POST["filtro_quantidade"] ?? '';

include "conecta.php";
include_once "./func/log.php";
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
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Ranking Uso Indevido por Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- CSS customizado para ranking -->
    <style>
        /* Removido destaque especial para top 3 rankings */
        
        /* Níveis de risco baseados na quantidade */
        .nivel-critico {
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-left: 4px solid #dc3545;
        }
        
        .nivel-alto {
            background-color: rgba(253, 126, 20, 0.1) !important;
            border-left: 4px solid #fd7e14;
        }
        
        .nivel-medio {
            background-color: rgba(255, 193, 7, 0.1) !important;
            border-left: 4px solid #ffc107;
        }
        
        .nivel-baixo {
            background-color: rgba(40, 167, 69, 0.1) !important;
            border-left: 4px solid #28a745;
        }
        
        /* Badges para contagem de usos indevidos */
        .count-badge {
            font-size: 1.1em;
            padding: 0.5em 1em;
            border-radius: 25px;
            font-weight: bold;
            min-width: 70px;
            display: inline-block;
            text-align: center;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
        }
        
        /* Cards de estatísticas */
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card h2 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .stat-card p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }
        
        /* Melhorias na tabela */
        .table th {
            background: linear-gradient(135deg, #495057, #6c757d) !important;
            color: white !important;
            text-align: center;
            font-weight: 700;
            border: none !important;
            font-size: 1.1rem;
            padding: 1rem 0.75rem;
        }
        
        .table td {
            vertical-align: middle;
            padding: 1rem 0.75rem;
        }
        
        /* Posição destacada */
        .position-badge {
            font-size: 1.4em;
            font-weight: bold;
            min-width: 100px;
            display: inline-block;
            text-align: center;
        }
        
        /* Destaque para nomes dos funcionários */
        .funcionario-nome {
            font-weight: 600;
            color: #2c3e50;
        }
        
        /* Alert personalizado */
        .alert-ranking {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
        }
        
        /* Botões personalizados */
        .btn-ranking {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-ranking:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                
                <!-- Cabeçalho Principal -->
                <div class="mb-4">
                    <h1 class="mb-3">RANKING DE USO INDEVIDO</h1>
                    <h2 class="mb-4">Funcionários por Número de Uso Indevido</h2>
                </div>

                <form method="post">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-alt"></i> Data Início:
                            </label>
                            <input min="2023-03-17" type="date" class="form-control" name="diai"
                                value="<?php echo $diai ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-alt"></i> Data Fim:
                            </label>
                            <input min="2023-03-17" type="date" class="form-control" name="diaf"
                                value="<?php echo $diaf ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-filter"></i> Filtrar por Quantidade:
                            </label>
                            <select class="form-control" name="filtro_quantidade">
                                <option value="">Todas as quantidades</option>
                                <option value="critico" <?php echo ($filtro_quantidade == 'critico') ? 'selected' : ''; ?>>Crítico (10+ usos)</option>
                                <option value="alto" <?php echo ($filtro_quantidade == 'alto') ? 'selected' : ''; ?>>Alto (5-9 usos)</option>
                                <option value="medio" <?php echo ($filtro_quantidade == 'medio') ? 'selected' : ''; ?>>Médio (3-4 usos)</option>
                                <option value="baixo" <?php echo ($filtro_quantidade == 'baixo') ? 'selected' : ''; ?>>Baixo (1-2 usos)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 d-flex align-items-end">
                            <input type="submit" class="btn btn-primary w-100" value="FILTRAR">
                        </div>
                    </div>
                </form>

                <div class="mt-4">
                    <?php if ($diaf <> ''): ?>
                    <div class="alert alert-ranking">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line"></i> 
                            <strong>PERÍODO ANALISADO:</strong> 
                            <?php 
                                $dataf = explode("-", $diaf);
                                $datat = $dataf[2] . "/" . $dataf[1] . "/" . $dataf[0];
                                $datai = explode("-", $diai);
                                $datati = $datai[2] . "/" . $datai[1] . "/" . $datai[0];
                                echo "$datati até $datat";
                                
                                // Mostrar filtro aplicado
                                if ($filtro_quantidade) {
                                    $filtros = [
                                        'critico' => 'Crítico (10+ usos)',
                                        'alto' => 'Alto (5-9 usos)', 
                                        'medio' => 'Médio (3-4 usos)',
                                        'baixo' => 'Baixo (1-2 usos)'
                                    ];
                                    echo " | <strong>FILTRO:</strong> " . $filtros[$filtro_quantidade];
                                }
                            ?>
                        </h5>
                    </div>
                    <!-- <div class="alert alert-info mb-4">
                        <h6><i class="fas fa-filter"></i> <strong>Filtro de Custo Aplicado:</strong></h6>
                        <p class="mb-0">
                            <i class="fas fa-money-bill-wave"></i> 
                            Ranking considerando apenas registros com <strong>custo igual ou superior a R$ 24,00</strong>
                        </p>
                        <small class="text-muted">
                            Registros com custo inferior a R$ 24,00 foram automaticamente excluídos do cálculo
                        </small>
                    </div> -->
                    <?php endif; ?>
                </div>

                <div class="card shadow-lg border-0 mt-4 mb-4">
                    <div class="card-header bg-gradient d-flex justify-content-between align-items-center">
                        <a href="dashboard.php" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Dashboard
                        </a>
                        <div class="">
                            <button type="button" class="btn btn-success" onclick="gerarExcel()">
                                <i class="fas fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        
                        <?php if ($diai != ""): ?>
                        <!-- Estatísticas Resumo -->
                        <div class="row mx-0 p-4">
                            <?php
                            // Query para estatísticas básicas
                            $sqlStats = "SELECT 
                                            COUNT(DISTINCT td.matricula) as total_funcionarios,
                                            COUNT(*) as total_ocorrencias
                                        FROM bdfrota.tbdescontousoindevido td
                                        LEFT JOIN bdaniel.tbfuncionario f ON td.matricula = f.matricula
                                        WHERE td.data_ocorrencia >= '$diai' 
                                        AND td.data_ocorrencia <= '$diaf'
                                        AND td.valor_desconto >= 24.00
                                        AND (f.status IS NULL OR f.status != 'Demitido')";
                            
                            $resultStats = mysql_query($sqlStats);
                            $stats = mysql_fetch_array($resultStats);
                            
                            // Query para máximo e média
                            $sqlMaxMedia = "SELECT MAX(qtd_ocorrencias) as max_por_funcionario, 
                                                   AVG(qtd_ocorrencias) as media_por_funcionario
                                            FROM (
                                                SELECT COUNT(*) as qtd_ocorrencias
                                                FROM bdfrota.tbdescontousoindevido td
                                                LEFT JOIN bdaniel.tbfuncionario f ON td.matricula = f.matricula
                                                WHERE td.data_ocorrencia >= '$diai' 
                                                AND td.data_ocorrencia <= '$diaf'
                                                AND td.valor_desconto >= 24.00
                                                AND (f.status IS NULL OR f.status != 'Demitido')
                                                GROUP BY td.matricula
                                            ) as contagem";
                            
                            $resultMaxMedia = mysql_query($sqlMaxMedia);
                            $maxMedia = mysql_fetch_array($resultMaxMedia);
                            ?>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card">
                                    <h2><?php echo $stats['total_funcionarios'] ?: '0'; ?></h2>
                                    <p><i class="fas fa-users"></i> Funcionários com Ocorrências</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card">
                                    <h2><?php echo $stats['total_ocorrencias'] ?: '0'; ?></h2>
                                    <p><i class="fas fa-exclamation-triangle"></i> Total de Usos Indevidos</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card">
                                    <h2><?php echo $maxMedia['max_por_funcionario'] ?: '0'; ?></h2>
                                    <p><i class="fas fa-arrow-up"></i> Máximo por Funcionário</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="stat-card">
                                    <h2><?php echo number_format($maxMedia['media_por_funcionario'], 1) ?: '0'; ?></h2>
                                    <p><i class="fas fa-chart-bar"></i> Média por Funcionário</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table id='datatablesSimple' class='table table-hover mb-0'>
                                <thead>
                                    <tr>
                                        <th>POSIÇÃO</th>
                                        <th>MATRÍCULA</th>
                                        <th>NOME DO FUNCIONÁRIO</th>
                                        <th>CENTRO DE CUSTO</th>
                                        <th>TOTAL USOS INDEVIDOS</th>
                                        <th>PRIMEIRA OCORRÊNCIA</th>
                                        <th>ÚLTIMA OCORRÊNCIA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($diai != "") {
                                        // Query principal - SIMPLES e FOCADA apenas em contar
                                        $sql = "SELECT 
                                                    td.matricula,
                                                    f.nome AS nome_funcionario,
                                                    f.ccusto,
                                                    COUNT(*) as total_usos_indevidos,
                                                    MIN(td.data_ocorrencia) as primeira_ocorrencia,
                                                    MAX(td.data_ocorrencia) as ultima_ocorrencia
                                                FROM bdfrota.tbdescontousoindevido td
                                                LEFT JOIN bdaniel.tbfuncionario f ON td.matricula = f.matricula
                                                WHERE td.data_ocorrencia >= '$diai' 
                                                AND td.data_ocorrencia <= '$diaf'
                                                AND td.valor_desconto >= 24.00
                                                AND (f.status IS NULL OR f.status != 'Demitido')
                                                GROUP BY td.matricula, f.nome, f.ccusto";

                                        // Aplicar filtro por quantidade se selecionado
                                        if ($filtro_quantidade) {
                                            $sql .= " HAVING ";
                                            switch ($filtro_quantidade) {
                                                case 'critico':
                                                    $sql .= "total_usos_indevidos >= 10";
                                                    break;
                                                case 'alto':
                                                    $sql .= "total_usos_indevidos BETWEEN 5 AND 9";
                                                    break;
                                                case 'medio':
                                                    $sql .= "total_usos_indevidos BETWEEN 3 AND 4";
                                                    break;
                                                case 'baixo':
                                                    $sql .= "total_usos_indevidos BETWEEN 1 AND 2";
                                                    break;
                                            }
                                        }

                                        $sql .= " ORDER BY total_usos_indevidos DESC, td.matricula ASC";

                                        $resultado = mysql_query($sql);
                                        
                                        // Array para armazenar todos os dados para exportação
                                        $dadosParaExportar = [];
                                        
                                        if (mysql_num_rows($resultado) > 0) {
                                            $posicao = 1;
                                            
                                            while ($row = mysql_fetch_array($resultado)) {
                                                // Define classes e badges baseado na posição e quantidade
                                                $classeRow = "";
                                                $badgePosicao = "";
                                                $badgeCount = "";
                                                
                                                // Classificação por quantidade de usos indevidos (sem destaque especial para top 3)
                                                if ($row['total_usos_indevidos'] >= 10) {
                                                    $classeRow = "nivel-critico";
                                                    $badgePosicao = "{$posicao}°";
                                                } elseif ($row['total_usos_indevidos'] >= 5) {
                                                    $classeRow = "nivel-alto";
                                                    $badgePosicao = "{$posicao}°";
                                                } elseif ($row['total_usos_indevidos'] >= 3) {
                                                    $classeRow = "nivel-medio";
                                                    $badgePosicao = "{$posicao}°";
                                                } else {
                                                    $classeRow = "nivel-baixo";
                                                    $badgePosicao = "{$posicao}°";
                                                }
                                                
                                                // Badge para contagem de usos indevidos (sincronizado com cores do ranking)
                                                if ($row['total_usos_indevidos'] >= 10) {
                                                    $badgeCount = "<span class='badge bg-danger count-badge'>{$row['total_usos_indevidos']} USOS</span>";
                                                } elseif ($row['total_usos_indevidos'] >= 5) {
                                                    $badgeCount = "<span class='badge bg-warning count-badge'>{$row['total_usos_indevidos']} USOS</span>";
                                                } elseif ($row['total_usos_indevidos'] >= 3) {
                                                    $badgeCount = "<span class='badge bg-warning count-badge'>{$row['total_usos_indevidos']} USOS</span>";
                                                } else {
                                                    $badgeCount = "<span class='badge bg-success count-badge'>{$row['total_usos_indevidos']} USOS</span>";
                                                }
                                                
                                                // Formatação de datas
                                                $primeira_formatada = date('d/m/Y', strtotime($row['primeira_ocorrencia']));
                                                $ultima_formatada = date('d/m/Y', strtotime($row['ultima_ocorrencia']));
                                                
                                                // Armazenar dados para exportação (sem HTML)
                                                $dadosParaExportar[] = [
                                                    'posicao' => $badgePosicao,
                                                    'matricula' => $row['matricula'],
                                                    'nome' => $row['nome_funcionario'],
                                                    'ccusto' => $row['ccusto'],
                                                    'total_usos' => $row['total_usos_indevidos'],
                                                    'primeira_ocorrencia' => $primeira_formatada,
                                                    'ultima_ocorrencia' => $ultima_formatada
                                                ];
                                                
                                                echo "<tr class='$classeRow'>
                                                        <td class='text-center'>
                                                            <span class='position-badge'>$badgePosicao</span>
                                                        </td>
                                                        <td class='text-center'><strong>{$row['matricula']}</strong></td>
                                                        <td class='funcionario-nome'>{$row['nome_funcionario']}</td>
                                                        <td class='text-center'>{$row['ccusto']}</td>
                                                        <td class='text-center'>$badgeCount</td>
                                                        <td class='text-center'>$primeira_formatada</td>
                                                        <td class='text-center'>$ultima_formatada</td>
                                                    </tr>";
                                                
                                                $posicao++;
                                            }
                                        } else {
                                            echo "<tr>
                                                    <td colspan='7' class='text-center py-5'>
                                                        <div class='alert alert-info'>
                                                            <i class='fas fa-info-circle fa-2x mb-3'></i><br>
                                                            <strong>Nenhuma ocorrência encontrada</strong><br>
                                                            <small>No período selecionado não há registros de uso indevido com custo ≥ R$ 24,00</small>
                                                        </div>
                                                    </td>
                                                </tr>";
                                        }
                                    } else {
                                        echo "<tr>
                                                <td colspan='7' class='text-center py-5'>
                                                    <div class='alert alert-warning'>
                                                        <i class='fas fa-calendar-alt fa-2x mb-3'></i><br>
                                                        <strong>Selecione um período</strong><br>
                                                        <small>Escolha as datas para ver o ranking de uso indevido</small>
                                                    </div>
                                                </td>
                                            </tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Armazenar dados em JavaScript para exportação -->
                    <script>
                        const todosOsDados = <?php echo json_encode($dadosParaExportar ?? []); ?>;
                    </script>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Legenda das Cores -->
                    <div class="card-footer bg-light">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3"><i class="fas fa-info-circle"></i> <strong>Legenda dos Níveis:</strong></h6>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="nivel-critico" style="width: 20px; height: 20px; border-radius: 3px; margin-right: 8px;"></div>
                                            <small><strong>🔴 Crítico:</strong> 10+ usos indevidos</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="nivel-alto" style="width: 20px; height: 20px; border-radius: 3px; margin-right: 8px;"></div>
                                            <small><strong>🟠 Alto:</strong> 5-9 usos indevidos</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="nivel-medio" style="width: 20px; height: 20px; border-radius: 3px; margin-right: 8px;"></div>
                                            <small><strong>🟡 Médio:</strong> 3-4 usos indevidos</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="nivel-baixo" style="width: 20px; height: 20px; border-radius: 3px; margin-right: 8px;"></div>
                                            <small><strong>🟢 Baixo:</strong> 1-2 usos indevidos</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <div class="text-muted">Relatório de Ranking - Uso Indevido</div>
                </div>
            </div>
        </footer>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    let dataTableInstance = null; // Variável global para armazenar a instância do DataTable
    
    document.addEventListener('DOMContentLoaded', function () {
        var tabela = document.querySelector('#datatablesSimple');
        
        if (tabela) {
            dataTableInstance = new simpleDatatables.DataTable(tabela, {
                perPage: 20,
                perPageSelect: [20, 50, 100, -1],
                searchable: true,
                sortable: true,
                labels: {
                    placeholder: "Buscar funcionário...",
                    perPage: "Exibir {select} registros por página",
                    noRows: "Nenhum registro encontrado",
                    info: "Mostrando {start} a {end} de {rows} funcionários"
                }
            });

            var select = tabela.closest('.dataTable-container').querySelector('select');
            if (select) {
                for (var i = 0; i < select.options.length; i++) {
                    if (select.options[i].value == -1) {
                        select.options[i].text = "Todos os registros";
                    }
                }
            }
        }
    });

    function gerarExcel() {
        // Usar os dados armazenados do PHP (TODOS os registros)
        if (!todosOsDados || todosOsDados.length === 0) {
            alert('Nenhum dado disponível para exportação!');
            return;
        }

        // Cabeçalhos
        const headers = ['POSIÇÃO', 'MATRÍCULA', 'NOME DO FUNCIONÁRIO', 'CENTRO DE CUSTO', 'TOTAL USOS INDEVIDOS', 'PRIMEIRA OCORRÊNCIA', 'ÚLTIMA OCORRÊNCIA'];

        // Processar TODOS os dados do PHP
        const dadosTratados = todosOsDados.map(row => [
            row.posicao,
            row.matricula,
            row.nome,
            row.ccusto,
            row.total_usos,
            row.primeira_ocorrencia,
            row.ultima_ocorrencia
        ]);

        // Informações do cabeçalho
        const periodo = "<?php echo isset($datati) && isset($datat) ? "$datati até $datat" : ""; ?>";
        
        // Monta planilha
        let planilha = [];
        planilha.push(['RANKING DE USO INDEVIDO POR FUNCIONÁRIO']);
        planilha.push([]);
        if (periodo) {
            planilha.push([`Período: ${periodo}`]);
        }
        planilha.push([`Gerado em: ${new Date().toLocaleString('pt-BR')}`]);
        planilha.push([`Total de registros: ${dadosTratados.length}`]);
        planilha.push([]);
        planilha.push(headers);
        planilha.push(...dadosTratados);

        // Gera Excel
        const worksheet = XLSX.utils.aoa_to_sheet(planilha);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Ranking Uso Indevido");
        
        // Nome do arquivo
        const agora = new Date();
        const dataFormatada = agora.toISOString().slice(0, 10);
        const nomeArquivo = `ranking_uso_indevido_${dataFormatada}.xlsx`;
        
        XLSX.writeFile(workbook, nomeArquivo);
    }
</script>

</html>
