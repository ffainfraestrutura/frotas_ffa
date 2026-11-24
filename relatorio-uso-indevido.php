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
// Forçar situação como "Aguardando Avaliação do Gestor" para padronização do relatório
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
    <title>Relatorio Uso Indevido</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- CSS customizado para status de urgência -->
    <style>
        /* Status de urgência com animação para casos críticos */
        .table-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
        
        .table-warning {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
        
        /* Animação pulsante para casos urgentes (mais de 5 dias) */
        .badge.bg-danger {
            animation: pulse-red 2s infinite;
        }
        
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 5px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
        
        /* Badges com melhor visibilidade */
        .badge {
            font-size: 0.85em;
            font-weight: 600;
            padding: 0.35em 0.65em;
        }
                
        /* Destaque para linhas críticas */
        .table tbody tr.table-danger:hover {
            background-color: rgba(220, 53, 69, 0.2) !important;
        }
        
        .table tbody tr.table-warning:hover {
            background-color: rgba(255, 193, 7, 0.2) !important;
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <!-- Modal -->
        <div class="modal fade" id="modalJustificativa" tabindex="-1" role="dialog"
            aria-labelledby="modalJustificativaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalJustificativaLabel">Justificativa</h5>
                        <button type="button" class="close bg-transparent border-0" data-dismiss="modal"
                            aria-label="Close">
                            <span class="h2" aria-hidden="true" onclick="cancel()">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" name="justificativa" id="justificativaText" rows="4"
                            placeholder="Digite a justificativa..."></textarea>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            onclick="cancel()">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="confirm()">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2">Processos de Uso Indevido Aguardando Avaliação do Gestor</h1>
                <!-- <div class="alert alert-info mb-4">
                    <h6><i class="fas fa-filter"></i> <strong>Filtro Aplicado:</strong></h6>
                    <p class="mb-0">
                        <i class="fas fa-money-bill-wave"></i> 
                        Exibindo apenas registros com <strong>custo igual ou superior a R$ 24,00</strong>
                    </p>
                    <small class="text-muted">
                        Registros com custo inferior a R$ 24,00 foram automaticamente filtrados
                    </small>
                </div> -->
                
                <!-- Legenda de Urgência
                <div class="alert alert-info mb-4">
                    <h6><i class="fas fa-info-circle"></i> <strong>Legenda de Urgência (baseada na data da ocorrência):</strong></h6>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="badge bg-success me-2">Normal</span> 
                            Até 2 dias
                        </div>
                        <div class="col-md-4">
                            <span class="badge bg-warning text-dark me-2">Atenção</span> 
                            Entre 3 e 5 dias
                        </div>
                        <div class="col-md-4">
                            <span class="badge bg-danger me-2">Urgente</span> 
                            Mais de 5 dias (Crítico!)
                        </div>
                    </div>
                    <small class="text-muted mt-2">
                        <i class="fas fa-calendar-alt"></i> 
                        Cálculo baseado na diferença entre a data da ocorrência e hoje (<?php echo date('d/m/Y'); ?>)
                    </small>
                </div>
                
                Aviso sobre Status
                <div class="alert alert-warning mb-4">
                    <h6><i class="fas fa-exclamation-triangle"></i> <strong>Informação sobre Status:</strong></h6>
                    <p class="mb-0">
                        <i class="fas fa-clock"></i> 
                        Todos os registros neste relatório são exibidos com status fixo: 
                        <strong>"Aguardando Avaliação do Gestor"</strong> para padronização do relatório.
                    </p>
                </div> -->
                <form method="post">
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o periodo (Data da Ocorrência):</b></h5>
                        
                        <div class="col-md-3">
                            <label for="exampleInputEmail1">
                                <h5>Início:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md mb-3" name="diai"
                                value="<?php echo $diai ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label for="exampleInputEmail1">
                                <h5>Fim:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md mb-3" name="diaf"
                                value="<?php echo $diaf ?>" required>
                        </div>
                        
                        <div class="col-md-3">
                            <h5><b>Status</b></h5>
                            <!-- Campo desabilitado mostrando o status fixo -->
                            <input type="text" class="form-control form-control-md mb-3" 
                                   value="Aguardando Avaliação do Gestor" 
                                   readonly 
                                   style="background-color: #e9ecef; color: #495057; font-weight: 500;">
                            <!-- Campo hidden para enviar o valor no POST -->
                            <input type="hidden" name="situacao" value="Aguardando Avaliação do Gestor">
                        </div>
                        
                        <div class="col-md-3">
                            <h5><b>Filtrar por Tempo Decorrido</b></h5>
                            <select class="form-control form-control-md mb-3" name="urgencia" id="Urgencia">
                                <option value="">Todos os Níveis</option>
                                <option value="normal" <?php echo $urgencia_filtro == "normal" ? "selected" : ""; ?>>
                                    Normal (até 2 dias)
                                </option>
                                <option value="atencao" <?php echo $urgencia_filtro == "atencao" ? "selected" : ""; ?>>
                                    Atenção (3 a 5 dias)
                                </option>
                                <option value="urgente" <?php echo $urgencia_filtro == "urgente" ? "selected" : ""; ?>>
                                    Expirado (mais de 5 dias)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class='mt-4 d-flex justify-content-between' style="width: 50%;">

                        <div class="mt-12">
                            <input type="submit" class="btn btn-success" value="Filtrar">
                        </div>
                    </div>

                </form>
                <div class='mt-12 d-flex justify-content-between' style="width: 50%;">

                </div>

                <div class="mt-4">
                    <h5 class="text-danger"><b><?php if ($diaf <> '') {
                        $dataf = explode("-", $diaf);
                        $datat = $dataf[2] . "/" . $dataf[1] . "/" . $dataf[0];
                        $datai = explode("-", $diai);
                        $datati = $datai[2] . "/" . $datai[1] . "/" . $datai[0];
                        print "Dados dos dias $datati até $datat";
                        
                        // Mostrar filtro de urgência aplicado
                        if ($urgencia_filtro != "") {
                            $filtro_texto = "";
                            switch($urgencia_filtro) {
                                case "normal": $filtro_texto = "Normal (até 2 dias)"; break;
                                case "atencao": $filtro_texto = "Atenção (3 a 5 dias)"; break; 
                                case "urgente": $filtro_texto = "Expirado (mais de 5 dias)"; break;
                            }
                            print " | Tempo Decorrido: $filtro_texto";
                        }
                    } ?></b></h5>
                </div>
                <div class="card mt-4 mb-4">
                    <div class="card-header d-flex justify-content-between">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard Inicial
                        </a href="dashboard.php">
                        <div class="">
                            <button type="submit" class="btn btn-success" onclick="gerarExcel()">Gerar Excel</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Matricula</th>
                                    <th>Nome</th>
                                    <th>Status</th>
                                    <th>Centro de Custo</th>
                                    <th>Data da Ocorrência</th>
                                    <th>Tempo Decorrido</th>
                                    <th>Km Percorridos</th>
                                    <th>Motivo Uso Indevido</th>
                                    <th>Placa</th>
                                    <th>Custo</th>
                                    <th>Situação</th>
                                    <th>Gestor</th>
                                    <th>Decisão Gestor</th>
                                    <th>Justificativa Gestor</th>
                                    <th>Detalhes Trajeto</th>
                                    <th>Visualizar PDF</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($diai != "") {
                                    $sql = "SELECT 
                                                td.*, 
                                                tc.tipocombustivel, 
                                                tc.consumo_litro,
                                                f.nome AS nome_funcionario,
                                                f.ccusto,
                                                f.status,
                                                c.nome AS nome_coordenador,
                                                c.matricula AS matricula_coord
                                            FROM bdfrota.tbdescontousoindevido td
                                            JOIN bdfrota.tbveiculo   tv 
                                                ON td.placa = tv.placa
                                            JOIN bdfrota.tbconsumocombustivel tc 
                                                ON tv.tipovel = tc.idtbconsumocombustivel
                                            LEFT JOIN bdaniel.tbfuncionario f 
                                                ON td.matricula = f.matricula
                                            LEFT JOIN bdaniel.tbfuncionario c 
                                                ON td.matricula_coordenador = c.matricula
                                            WHERE td.data_ocorrencia >= '{$diai}' 
                                            AND td.data_ocorrencia <= '{$diaf}'
                                            AND td.situacao LIKE '%$situacao%'
                                            AND (f.status IS NULL OR f.status != 'Demitido')";

                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    $rows = array(); // Array para armazenar todos os registros
                                    $dadosParaExportar = array(); // Array para exportação Excel
                                    
                                    // Coleta todos os registros primeiro para poder filtrar por urgência e custo
                                    while ($row = mysql_fetch_array($resultado)) {
                                        // Calcula custo primeiro para filtrar
                                        if ($row['valor_desconto'] == null) {
                                            $consumoLitro = $row["consumo_litro"];
                                            $tipoCombustivel = $row["tipocombustivel"];
                                            if (strpos(strtolower($tipoCombustivel), "gasolina") !== false) {
                                                $custoKM = $precos['gasolina'] / $consumoLitro;
                                            } else {
                                                $custoKM = $precos['diesel'] / $consumoLitro;
                                            }
                                            $custoTotal = round(($row['kmtotal'] * $custoKM), 2, PHP_ROUND_HALF_UP);
                                        } else {
                                            $custoTotal = $row['valor_desconto'];
                                        }
                                        
                                        // Filtro por custo >= 24
                                        if ($custoTotal >= 24) {
                                            $row['custo_calculado'] = $custoTotal; // Adiciona custo ao array
                                            $rows[] = $row;
                                        }
                                    }
                                    
                                    // Agora processa cada registro
                                    foreach($rows as $row) {
                                        // Calcula dias decorridos desde a ocorrência
                                        $dataOcorrencia = new DateTime($row['data_ocorrencia']);
                                        $dataAtual = new DateTime();
                                        $diasDecorridos = $dataAtual->diff($dataOcorrencia)->days;

                                        // Define status de urgência baseado nos dias
                                        $nivel_urgencia = "";
                                        if ($diasDecorridos <= 2) {
                                            $nivel_urgencia = "normal";
                                        } elseif ($diasDecorridos > 2 && $diasDecorridos <= 5) {
                                            $nivel_urgencia = "atencao";
                                        } else {
                                            $nivel_urgencia = "urgente";
                                        }
                                        
                                        // Aplica filtro de urgência se selecionado
                                        if ($urgencia_filtro != "" && $nivel_urgencia != $urgencia_filtro) {
                                            continue; // Pula este registro
                                        }

                                        // Usa o custo já calculado no filtro anterior
                                        $custoTotal = $row['custo_calculado'];

                                        // Status fixo para todos os registros
                                        $status_fixo = "Aguardando Avaliação do Gestor";

                                        // Situação
                                        $situacao_display = $row['situacao'];
                                        if ($row['situacao'] !== null) {
                                            switch ($row['aval_coordenador']) {
                                                case 1:
                                                    $decisao = "Descontar";
                                                    break;
                                                case 0:
                                                    $decisao = "Não Descontar";
                                                    break;
                                                default:
                                                    $decisao = "Pendente";
                                                    break;
                                            }
                                        } else {
                                            $decisao = "";
                                        }

                                        // Define status de urgência baseado nos dias (já calculado acima)
                                        if ($diasDecorridos <= 2) {
                                            $urgencia = "<span class='badge bg-success'>Normal ($diasDecorridos dias)</span>";
                                            $urgencia_texto = "Normal ($diasDecorridos dias)";
                                            $classeRow = "";
                                        } elseif ($diasDecorridos > 2 && $diasDecorridos <= 5) {
                                            $urgencia = "<span class='badge bg-warning text-dark'>Atenção ($diasDecorridos dias)</span>";
                                            $urgencia_texto = "Atenção ($diasDecorridos dias)";
                                            $classeRow = "table-warning";
                                        } else {
                                            $urgencia = "<span class='badge bg-danger'>Expirado ($diasDecorridos dias)</span>";
                                            $urgencia_texto = "Expirado ($diasDecorridos dias)";
                                            $classeRow = "table-danger";
                                        }
                                        
                                        // Armazenar dados para exportação (sem HTML)
                                        $dadosParaExportar[] = [
                                            'matricula' => $row['matricula'],
                                            'nome' => $row['nome_funcionario'],
                                            'status' => $status_fixo,
                                            'ccusto' => $row['ccusto'],
                                            'data_ocorrencia' => $row['data_ocorrencia'],
                                            'urgencia' => $urgencia_texto,
                                            'kmtotal' => $row['kmtotal'],
                                            'tipo_uso_indevido' => $row['tipo_uso_indevido'],
                                            'placa' => $row['placa'],
                                            'custo' => "R$ {$custoTotal}",
                                            'situacao' => $situacao_display,
                                            'gestor' => "{$row['matricula_coord']}-{$row['nome_coordenador']}",
                                            'decisao_gestor' => $decisao,
                                            'justificativa' => $row['justificativa_coordenador']
                                        ];

                                        echo "<tr class='$classeRow'>
                                                <td>{$row['matricula']}</td>
                                                <td>{$row['nome_funcionario']}</td>
                                                <td>$status_fixo</td>
                                                <td>{$row['ccusto']}</td>
                                                <td>{$row['data_ocorrencia']}</td>
                                                <td>$urgencia</td>
                                                <td>{$row['kmtotal']}</td>
                                                <td>{$row['tipo_uso_indevido']}</td>
                                                <td>{$row['placa']}</td>
                                                <td>R$ {$custoTotal}</td>
                                                <td>{$situacao_display}</td>
                                                <td>{$row['matricula_coord']}-{$row['nome_coordenador']}</td>
                                                <td>{$decisao}</td>
                                                <td>{$row['justificativa_coordenador']}</td>
                                                <td>
                                                    <div class='d-flex gap-4 justify-content-center'>
                                                        <form action='./detalhes-gpsxfolga.php' method='post'>                     
                                                            <input type='hidden' name='matriculatec' value='{$row['matricula']}'>
                                                            <input type='hidden' name='nometec' value='{$row['nome_funcionario']}'>
                                                            <input type='hidden' name='placatec' value='{$row['placa']}'>
                                                            <input type='hidden' name='diaf' value='{$row['data_ocorrencia']}'>
                                                            <input type='hidden' name='diai' value='{$row['data_ocorrencia']}'>
                                                            <input type='hidden' name='matsupf' value='$matricula'>
                                                            <input type='submit' class='btn btn-sm btn-success' value='detalhes'>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td class='text-center'>
                                                    <a href='./pdfdescontousoindevido.php?id={$row['id']}' target='_blank' class='mx-auto'>
                                                        <i class='fa-solid fa-file-pdf h1'></i>
                                                    </a>
                                                </td>
                                                <td>";
                                        if ($perfil == 4) {
                                            if ($row['situacao'] == "Aguardando Avaliação do Gestor" && $row['bloqueio'] != 1) {
                                                printf('<button class="btn btn-primary" id="blockbutton-%d" onclick="toggleBloqueio(%d)">Bloquear Solicitação de Combustível</button>', $row['id'], $row['id']);
                                            } elseif ($row["bloqueio"] == 1) {
                                                printf('<button class="btn btn-primary" id="blockbutton-%d" onclick="toggleBloqueio(%d)">Desbloquear Solicitação de Combustível</button>', $row['id'], $row['id']);
                                            }
                                        }
                                        echo "</td></tr>";

                                    }
                                } else {
                                    print "<tr>
            <td colspan='17' class='text-center'>Sem Dados</td>
           </tr>";
                                }
                                ?>


                            </tbody>
                        </table>
                        
                        <!-- Armazenar dados em JavaScript para exportação -->
                        <script>
                            const todosOsDados = <?php echo json_encode($dadosParaExportar ?? []); ?>;
                        </script>
                        
                        <!--<form action="./control/gerarexcelgpsxfolga2.php" method="post">
                            <?php
                            /*
                            print "<input type='hidden' name='diai' value='$diai'>";
                            print "<input type='hidden' name='diaf' value='$diaf'>";
                            print "<input type='hidden' name='matsupf' value='$matsup'>"; */ ?>
                            <div class="form-group col-md-2">
                                <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                                <input type="submit" class="form-control form-control-md btn btn-success"
                                    value="GERAR EXCEL">
                            </div>
                        </form>
                        !-->
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>

    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!--script para criar data table-->
<script>
    var action = null;
    var id = null;
    var custoTotal = null;

    document.addEventListener('DOMContentLoaded', function () {
        var tabela = document.querySelector('#datatablesSimple');

        new simpleDatatables.DataTable(tabela, {
            perPage: 10,
            perPageSelect: [10, 25, 50, 100, -1],
            searchable: true,
            sortable: true
        });

        var select = tabela.closest('.dataTable-container').querySelector('select');
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].value == -1) {
                select.options[i].text = "Todos";
            }
        }
    });

    function toggleBloqueio(id) {
        const data = {
            matricula: <?php echo json_encode($matricula); ?>,
            id: id
        };

        fetch("./control/bloquearsolicitacaocombustivel.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erro HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.status === 200) {
                    const botao = document.getElementById(`blockbutton-${id}`);
                    if (botao) {
                        if (result.bloqueio === 1) {
                            botao.textContent = "Desbloquear Solicitação de Combustível";
                            alert("Solicitação de Combustível bloqueada com sucesso!");
                        } else {
                            botao.textContent = "Bloquear Solicitação de Combustível";
                            alert("Solicitação de Combustível desbloqueada com sucesso!");
                        }
                    }
                } else if (result.status === 204) {
                    alert("Nenhuma alteração foi realizada.");
                } else {
                    alert(`Erro: ${result.message || "Não foi possível completar a operação."}`);
                }
            })
            .catch(error => {
                console.error("Erro na requisição:", error);
                alert("Ocorreu um erro ao alternar o bloqueio da solicitação de combustível!");
            });
    }

    function gerarExcel() {
        // Usar os dados armazenados do PHP (TODOS os registros)
        if (!todosOsDados || todosOsDados.length === 0) {
            alert('Nenhum dado disponível para exportação!');
            return;
        }

        // Cabeçalhos (sem as 3 últimas colunas: Detalhes Trajeto, Visualizar PDF, Ação)
        const headers = [
            'Matricula',
            'Nome',
            'Status',
            'Centro de Custo',
            'Data da Ocorrência',
            'Tempo Decorrido',
            'Km Percorridos',
            'Motivo Uso Indevido',
            'Placa',
            'Custo',
            'Situação',
            'Gestor',
            'Decisão Gestor',
            'Justificativa Gestor'
        ];

        // Processar TODOS os dados do PHP
        const dadosTratados = todosOsDados.map(row => [
            row.matricula,
            row.nome,
            row.status,
            row.ccusto,
            row.data_ocorrencia,
            row.urgencia,
            row.kmtotal,
            row.tipo_uso_indevido,
            row.placa,
            row.custo,
            row.situacao,
            row.gestor,
            row.decisao_gestor,
            row.justificativa
        ]);

        // Informações sobre filtros aplicados
        const filtroUrgencia = document.querySelector('#Urgencia').value;
        const filtroSituacao = "Aguardando Avaliação do Gestor"; // Status fixo
        
        let infoFiltros = [`Situação: ${filtroSituacao}`];
        if (filtroUrgencia) {
            const textoFiltro = document.querySelector('#Urgencia option:checked').textContent;
            infoFiltros.push(`Tempo Decorrido: ${textoFiltro}`);
        }

        // Monta a planilha com informações de filtros
        let planilha = [];
        planilha.push(['RELATÓRIO DE USO INDEVIDO']);
        planilha.push([]);
        if (infoFiltros.length > 0) {
            planilha.push([`Filtros aplicados: ${infoFiltros.join(' | ')}`]);
        }
        planilha.push([`Relatório gerado em: ${new Date().toLocaleString('pt-BR')}`]);
        planilha.push([`Total de registros: ${dadosTratados.length}`]);
        planilha.push([]); // Linha vazia
        planilha.push(headers);
        planilha.push(...dadosTratados);

        // Gera o arquivo Excel com SheetJS
        const worksheet = XLSX.utils.aoa_to_sheet(planilha);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Relatório Uso Indevido");
        
        // Gera nome do arquivo com data atual e filtros
        const agora = new Date();
        const dataFormatada = agora.toISOString().slice(0, 10);
        let nomeArquivo = `relatorio_uso_indevido_${dataFormatada}`;
        if (filtroUrgencia) {
            nomeArquivo += `_${filtroUrgencia}`;
        }
        nomeArquivo += '.xlsx';
        
        XLSX.writeFile(workbook, nomeArquivo);
    }

</script>

</html>