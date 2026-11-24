<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];
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
if (isset($_POST["situacao"]) && $_POST["situacao"] != "") {
    $situacao = $_POST["situacao"];
} else {
    $situacao = "Aguardando Desconto";
}
$matsup = $_POST["matsupf"];

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
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "../multas/menu.php"; ?>

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
                    </div>
                    <div class="row" style="width: 100%;">
                        <h5><b>Selecione o status</b></h5>
                        <div class="col-md-3">
                            <select class="form-control form-control-md mb-3" name="situacao" id="Situacao">
                                <option value="">Selecione a Situacao</option>
                                <?php
                                $sql = "select distinct(situacao) from bdfrota.tbdescontousoindevido";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $selected = $row[0] == $situacao ? "selected" : "";
                                    print "
                                    <option
                                    value='{$row[0]}' $selected>
                                    {$row[0]}</
                                    option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class='mt-2' style="width: 50%;">

                        <div class="mt-4">
                            <input type="submit" class="btn btn-success" value="Filtrar">
                        </div>

                    </div>

                </form>

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
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i>
                                Voltar para Dashboard Inicial
                            </a href="dashboard.php">
                            <button id="btnExcel" class="btn btn-success">
                                <i class="fa-solid fa-file-excel"></i> Exportar Excel
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id='datatablesSimple' class='table table-bordered'>
                            <thead>
                                <tr>
                                    <th>Matricula</th>
                                    <th>Nome</th>
                                    <th>Data da Ocorrência</th>
                                    <th>Placa</th>
                                    <th>Custo</th>
                                    <th>Situação</th>
                                    <th>Visualizar PDF</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($diai != "") {
                                    $sql = "SELECT td.*, tc.tipocombustivel, tc.consumo_litro FROM bdfrota.tbdescontousoindevido td join bdfrota.tbveiculo tv on td.placa = tv.placa join bdfrota.tbconsumocombustivel tc on tv.tipovel = tc.idtbconsumocombustivel WHERE data_ocorrencia >= '$diai' AND data_ocorrencia <= '$diaf' and td.situacao = '$situacao'";
                                    $resultado = mysql_query($sql) or die(mysql_error());
                                    while ($row = mysql_fetch_array($resultado)) {
                                        $consumoLitro = $row["consumo_litro"];
                                        $tipoCombustivel = $row["tipocombustivel"];
                                        $sql = "select nome from bdaniel.tbfuncionario where matricula = '" . $row['matricula'] . "'";
                                        $funcionario = mysql_query($sql) or die(mysql_error());
                                        $funcionario = mysql_fetch_assoc($funcionario);
                                        $situacao = $row['situacao'];
                                        $custoTotal = $row['valor_desconto'];
                                        echo "<tr >
                                                    <td>{$row['matricula']}</td>
                                                    <td>{$funcionario['nome']}</td>
                                                    <td>{$row['data_ocorrencia']}</td>
                                                    <td>{$row['placa']}</td>
                                                    <td>R$ {$custoTotal}</td>
                                                    <td>{$situacao}</td>
                                                    <td class='text-center'><a href='./pdfdescontousoindevido.php?id={$row['id']}' target='_blank' class='mx-auto'><i class='fa-solid fa-file-pdf h1'></i></a></td>
                                                    <td class='text-center'><div class='d-flex gap-4 justify-content-center'>";
                                        if ($situacao == "Aguardando Desconto") {
                                            echo "<button class='btn btn-primary' onclick='finalizar(\"" . $row['id'] . "\",\"" . $custoTotal . "\")'>Finalizar Processo</button>";
                                        }
                                        echo "</div></td>
                                                </tr>";
                                    }
                                } else {

                                    print "<tr>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td>Sem Dados</td>
                                            <td></td>
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
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<!--script para criar data table-->
<script>
    var action = null;
    var id = null;
    var custoTotal = null;
    //função iniciar data table 
    $(document).ready(function () {
        var tabela = $('#datatablesSimple').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'processos_uso_indevido',
                    className: 'd-none', // botão escondido
                    exportOptions: {
                        columns: ':visible:not(:last-child)' // evita exportar coluna Ações
                    }
                }
            ]
        });

        // Quando clicar no botão manual, dispara o do DataTables
        $('#btnExcel').on('click', function () {
            tabela.button('.buttons-excel').trigger();
        });
    });

    function finalizar(id) {
        console.log("oi");
        url = "./control/finalizardescontousoindevido.php",
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    matricula: <?php echo $matricula ?>
                })
            })
                .then(response => {
                    if (response.ok) {
                        response.json().then(data => {
                            console.log(data);
                            alert("Desconto finalizado com sucesso!");
                            window.location.reload();
                        });
                    }
                }
                )
                .catch(error => {
                    console.error('Error:', error);
                    alert("Erro ao finalizar Desconto!");
                });
    }
</script>

</html>