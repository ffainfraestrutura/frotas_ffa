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

$diai = $_GET["diai"] ?? $_POST["diai"] ?? "";
$diaf = $_GET["diaf"] ?? $_POST["diaf"] ?? "";
$situacao = $_GET["situacao"] ?? $_POST["situacao"] ?? "";
$motivoUsoIndevido = $_GET["tipo_uso_indevido"] ?? $_POST["tipo_uso_indevido"] ?? "Fora de Escala";
$motivoUsoIndevido = trim($motivoUsoIndevido) === "" ? "Fora de Escala" : trim($motivoUsoIndevido);
$matsup = $_GET["matsupf"] ?? $_POST["matsupf"] ?? "";

if ($diai === "" || $diaf === "") {
    $segunda = new DateTimeImmutable('monday this week');
    $domingo = new DateTimeImmutable('sunday this week');
    $diai = $diai !== "" ? $diai : $segunda->format('Y-m-d');
    $diaf = $diaf !== "" ? $diaf : $domingo->format('Y-m-d');
}

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
    <title>Rel Desconto Uso Indevido</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
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
                <h1 class="h1 mt-2">Processos de Uso Indevido Aguardando Desconto</h1>
                <form method="get" action="">
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o periodo (Data da Ocorrência):</b></h5>
                        <div class="col-md-3">
                            <label for="diai">
                                <h5>Início:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md mb-3" name="diai" id="diai"
                                value="<?= htmlspecialchars($diai, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label for="diaf">
                                <h5>Fim:</h5>
                            </label>
                            <input min="2023-03-17" type="date" class="form-control form-control-md mb-3" name="diaf" id="diaf"
                                value="<?= htmlspecialchars($diaf, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o status</b></h5>
                        <div class="col-md-3">
                            <select class="form-control form-control-md mb-3" name="situacao" id="Situacao">
                                <option value="" <?= $situacao === "" ? "selected" : "" ?>>TODOS</option>
                                <?php
                                $sql = "select distinct(situacao) from bdfrota.tbdescontousoindevido";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $selected = $row[0] == $situacao ? "selected" : "";
                                    echo "<option value='{$row[0]}' $selected>{$row[0]}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row" style="width: 100%; margin-top: 8px;">
                        <h5><b>Motivo Uso Indevido</b></h5>
                        <div class="col-md-3">
                            <select class="form-control form-control-md mb-3" name="tipo_uso_indevido" id="tipo_uso_indevido">
                                <option value="" <?= $motivoUsoIndevido === "" ? "selected" : "" ?>>TODOS</option>
                                <?php
                                $sqlMotivo = "SELECT DISTINCT tipo_uso_indevido FROM bdfrota.tbdescontousoindevido WHERE tipo_uso_indevido IS NOT NULL AND tipo_uso_indevido <> '' ORDER BY tipo_uso_indevido";
                                $resultadoMotivo = mysql_query($sqlMotivo) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultadoMotivo)) {
                                    $valorMotivo = (string) $row[0];
                                    $selected = strtolower($valorMotivo) === strtolower((string) $motivoUsoIndevido) ? "selected" : "";
                                    echo "<option value='" . htmlspecialchars($valorMotivo, ENT_QUOTES, 'UTF-8') . "' $selected>" . htmlspecialchars($valorMotivo, ENT_QUOTES, 'UTF-8') . "</option>";
                                }
                                ?>
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
                    } ?></b></h5>
                </div>
                <div class="card mt-4 mb-4">
                    <div class="card-header d-flex justify-content-end">
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
                                            " . ($motivoUsoIndevido !== "" ? "AND td.tipo_uso_indevido LIKE '%" . mysql_real_escape_string($motivoUsoIndevido) . "%' " : "") . "
                                            AND (f.status IS NULL OR f.status != 'Demitido')";

                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    while ($row = mysql_fetch_array($resultado)) {
                                        // Calcula custo
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
                                        // Atualizado em 2309, lembrar de deixar sincronizado com frotas\relatoriodescontousoindevido.php e no excel
                                        if ($custoTotal <= 15) {
                                            continue;
                                        }

                                        // Situação
                                        $situacao = $row['situacao'];
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

                                        echo "<tr>
                                                <td>{$row['matricula']}</td>
                                                <td>{$row['nome_funcionario']}</td>
                                                <td>{$row['status']}</td>
                                                <td>{$row['ccusto']}</td>
                                                <td>{$row['data_ocorrencia']}</td>
                                                <td>{$row['kmtotal']}</td>
                                                <td>{$row['tipo_uso_indevido']}</td>
                                                <td>{$row['placa']}</td>
                                                <td>R$ {$custoTotal}</td>
                                                <td>{$situacao}</td>
                                                <td>{$row['matricula_coord']}-{$row['nome_coordenador']}</td>
                                                <td>{$decisao}</td>
                                                <td>{$row['justificativa_coordenador']}</td>
                                                <td>
                                                    <div class='d-flex gap-4 justify-content-center'>
                                                        <form action='./detalhes-gpsxfolga.php' method='post' target='_blank'>                     
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
            <td colspan='13' class='text-center'>Sem Dados</td>
           </tr>";
                                }
                                ?>


                            </tbody>
                        </table>
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
        const tabela = document.querySelector('#datatablesSimple');

        // Cabeçalhos (removendo os 3 últimos)
        const headers = Array.from(tabela.querySelectorAll('thead th'))
            .slice(0, -3)
            .map(th => th.textContent.trim());

        // Linhas de dados visíveis
        const linhas = Array.from(tabela.querySelectorAll('tbody tr'));
        const dadosTratados = linhas.map(tr => {
            return Array.from(tr.querySelectorAll('td'))
                .slice(0, -3)
                .map(td => td.textContent.trim());
        });

        // Monta a planilha
        const planilha = [headers, ...dadosTratados];

        // Gera o arquivo Excel com SheetJS
        const worksheet = XLSX.utils.aoa_to_sheet(planilha);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Relatório");
        XLSX.writeFile(workbook, "relatorio.xlsx");
    }

</script>

</html>