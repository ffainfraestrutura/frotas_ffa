<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');
$semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U');

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '6' && $perfil <> '10') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

include_once "../func/log.php";
include "conecta.php";
//include_once"../func/log.php";
header("Content-type: text/html; charset=utf-8");

$hoje = date("Y-m-d");//H:i:s
$autor = $_POST['autor'];
$ccusto1 = $_POST['ccusto1'];
$status = $_POST['status'];
$dataf = $_POST['dataf'];
$datai = $_POST['datai'];

$ccusto2 = str_replace($comAcentos, $semAcentos, $ccusto1);
;

if ($datai == '' or $dataf == '') {
    $datai = date('Y-m-d');
    $dataf = date('Y-m-d');
}

// if ($status == "") {
//     $status = 1;
// }
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

    <title>Rel Chamados Frota | Portal FFA</title>

    <link href="../src/css/styles.css" rel="stylesheet" />

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css e js-->
    <link href="../src/css/styles.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">
                <h1 class="h1 pt-1"> Relatório de Chamados </h1>

                <!--div form-->
                <div class="mt-2">
                    <form action='./relatoriochamados.php' method='post'> <!-- /control/teste.php -->
                        <div class="d-flex justify-content-start">
                            <div class="col-3">
                                <label class="form-label">Data inicial:<span style="color: red;">*<span></label>
                                <input class="form-control" type="date" name="datai" <?php print "value = $datai"; ?>
                                    required>
                            </div>

                            <div class="ms-3 col-3">
                                <label class="form-label">Data final:<span style="color: red;">*<span></label>
                                <input class="form-control" type="date" name="dataf" <?php print "value = $dataf"; ?>
                                    required>
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-start">
                            <div class="col-3">
                                <label class="form-label">Status:<span style="color: red;">*<span></label>
                                <select name="status" id='status' class='form-select' required> <!-- -->
                                    <option value=' '>Selecione... </option>
                                    <option value='%%' $selected> TODOS </option>
                                    <!--<option value='0'>Todos </option>-->
                                    <?php //idtbstatuscham, status
                                    $sql2 = "SELECT * FROM bdfrota.tbastatuscham;";
                                    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                                    while ($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)) {
                                        $idtbstatuscham = $row2['idtbstatuscham'];
                                        $status1 = $row2['status'];

                                        $selected = ($status == $idtbstatuscham) ? "selected=\"selected\"" : null;
                                        print "<option value='$idtbstatuscham' $selected> $status1 </option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="ms-3 col-3">
                                <label class="form-label">Centro de custo:</label>
                                <select class="form-select" name="ccusto1" id='ccusto1'>
                                    <option value=' '> Selecione... </option>
                                    <?php
                                    $sql3 = "SELECT DISTINCT(ccusto) FROM bdfrota.tbchamados WHERE matricula<>'003535';";
                                    $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                                    while ($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)) {
                                        $ccusto = $row3['ccusto'];

                                        print "<option value='$ccusto'> $ccusto </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-start">
                            <div class="col-3">
                                <label class="form-label">Autor do chamado:</label>
                                <select class="form-select" name="autor" id="autor">
                                    <option value=' '> Selecione... </option>
                                    <?php
                                    $sql4 = "SELECT DISTINCT(nome) FROM bdfrota.tbchamados WHERE nome<>'' AND nome IS NOT NULL ORDER BY nome;";
                                    $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

                                    while ($row4 = mysqli_fetch_array($resultado4, MYSQLI_ASSOC)) {
                                        $nome = $row4['nome'];

                                        $selected = ($autor == $nome) ? "selected=\"selected\"" : null;

                                        print "<option value='$nome' $selected> $nome </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type='submit' class='btn btn-success'> Filtrar </button>
                            <button onclick="exportExcel()" class='btn btn-success'> Gerar Excel da Página </button>
                        </div>


                        <div class='mt-4'>
                            <p style="color: red;"> * Campos obrigatórios. </p>
                        </div>
                    </form>
                </div>

                <!--div tabela-->
                <div class="mt-4">
                    <table id='tabela1' class='table table-striped'>
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Data de abertura</th>
                                <th>Data de fechamento</th>
                                <th>Categoria</th>
                                <th>Problema</th>
                                <th>Centro de custo</th>
                                <th>Status</th>
                            </tr>
                        </thead>


                        <tbody>

                            <?php
                            $datafMaisUm = date('Y-m-d', strtotime($dataf . ' +1 day'));

                            $sql = "SELECT * FROM bdfrota.tbchamados WHERE data_abert BETWEEN '$datai' AND '$datafMaisUm' AND status like '$status' ";

                            if ($ccusto1 != ' ') {
                                $sql .= "AND ccusto = '$ccusto2' ";
                            }

                            if ($autor != ' ') {
                                $sql .= "AND nome = '$autor' ";
                            }

                            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                            //idtbchamados, matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente, data_abert, data_fech
                            while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                                $numero = $row['idtbchamados'];
                                $matricula = $row['matricula'];
                                $nome = $row['nome'];
                                $telefone = $row['telefone'];
                                $ccusto = $row['ccusto'];
                                $placa = $row['placa'];
                                $hodometro = $row['hodometro'];
                                $tipodechamado = $row['tipodechamado'];
                                $tiposinistro = $row['tiposinistro'];
                                $tipomanuprev = $row['tipomanuprev'];
                                $prevcord = $row['prevcord'];
                                $tipomanucor = $row['tipomanucor'];
                                $data_abert = $row['data_abert'];
                                $data_fech = $row['data_fech'];
                                $status = $row['status'];
                                $ciente = $row['ciente'] ?? '';
                                $resposta = $row['resposta'] ?? '';


                                //tratando datas
                                $data_abert = new DateTime($data_abert);
                                $data_abert1 = $data_abert->format('d/m/Y');

                                /*$data_fech = explode(" ", $data_fech);
                                $data_fech = explode("-", $data_fech[0]);
                                $data_fech1 = "$data_fech[2]".'/'."$data_fech[1]".'/'."$data_fech[0]";*/
                                if ($data_fech != '0000-00-00 00:00:00') {
                                    $data_fech = new DateTime($data_fech);
                                    $data_fech1 = $data_fech->format('d/m/Y');
                                } else {
                                    $data_fech1 = '00/00/0000';
                                }


                                if ($tipodechamado == 'Comunicado de Sinistro') {
                                    $tipo = $tiposinistro;
                                } elseif ($tipodechamado == 'Solicitação de Manutenção Preventiva') {
                                    $tipo = $tipomanuprev;
                                } elseif ($tipodechamado == 'Solicitação de Manutenção Corretiva') {
                                    $tipo = $tipomanucor;
                                } elseif ($tipodechamado == 'Solicitação de Guincho') {
                                    $tipo = "$enderecog";
                                } else {
                                    $tipo = "";
                                }

                                if ($status == 1) {
                                    $statusf = "Aberto";
                                } elseif ($status == 2) {
                                    $statusf = "Fechado";
                                } elseif ($status == 3) {
                                    $statusf = "Agendado";
                                } elseif ($status == 4) {
                                    $statusf = "Cancelado";
                                }


                                print "
                                    <tr>
                                        <td> $numero </td>
                                        <td> $data_abert1 </td>
                                        <td> $data_fech1 </td>
                                        <td> $tipodechamado </td>
                                        <td> $tipo </td>
                                        <td> $ccusto </td>
                                        <td> $statusf </td>
                                    </tr>";
                            }

                            ?>

                        </tbody>
                    </table>
                </div>

                <!-- botão gerar excel -->
                <div class='mt-4 mb-4'>
                    <form action="./control/gerarexcel.php" method="post">
                        <?php
                        print "<input type='hidden' name='datai1' value='$datai'/>
                            <input type='hidden' name='dataf1' value='$datafMaisUm'/>
                            <input type='hidden' name='autor1' value='$autor'/>
                            <input type='hidden' name='ccusto2' value='$ccusto1'/>
                            <input type='hidden' name='status2' value='$status2'/>"; ?>
                        <button type='submit' class='btn btn-success'> Gerar Excel Completo </button>
                    </form>
                </div>
            </div>
        </main>


        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <!--<div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>!-->
                </div>
            </div>
        </footer>
    </div>

    <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>

    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.12/sorting/date-eu.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- jquery e masks -->
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <script src="../src/js/scripts.js"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#tabela1').DataTable({
                columns: [
                    null,
                    { "type": "date-eu" },
                    { "type": "date-eu" },
                    null,
                    null,
                    null,
                    null,
                ],

                language: {
                    decimal: "",
                    emptyTable: "Nada para exibir",
                    info: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    infoEmpty: "Exibindo página 0 de 0 de 0 registros",
                    infoFiltered: "(filtrado do total de _MAX_ registros)",
                    infoPostFix: "",
                    thousands: ".",
                    lengthMenu: "Exibir _MENU_ registros",
                    loadingRecords: "Carregando...",
                    processing: "Processando...",
                    search: "Buscar:",
                    zeroRecords: "Nenhum resultado encontrado",
                    paginate: {
                        first: "Primeira",
                        last: "Última",
                        next: "Próxima",
                        previous: "Anterior"
                    }
                }
            });
        });
    </script>
    <script>
        function exportExcel() {
            const tableOriginal = document.getElementById('tabela1');
            const tableClone = tableOriginal.cloneNode(true);

            // Remove classes "d-none" para aparecer no Excel
            tableClone.querySelectorAll('.d-none').forEach(el => el.classList.remove('d-none'));

            // ---- resto do seu código do Excel ----
            tableClone.style.border = '1px solid #aaa';
            tableClone.style.fontFamily = 'Arial, sans-serif';
            tableClone.style.borderCollapse = 'collapse';

            const ths = tableClone.querySelectorAll('th');
            ths.forEach(th => {
                th.style.padding = '12px';
                th.style.border = '1px solid #ccc';
                th.style.textAlign = 'center';
            });

            const tds = tableClone.querySelectorAll('td');
            tds.forEach(td => {
                td.style.padding = '8px';
                td.style.border = '1px solid #ccc';
            });

            const trs = tableClone.querySelectorAll('tr');
            trs.forEach((tr, index) => {
                if (index > 0 && index % 2 === 0) {
                    tr.style.backgroundColor = '#f2f2f2';
                }
            });

            const tableHTML = tableClone.outerHTML;
            const fileName = 'Relatorio_Geral.xls';

            const dataType = 'application/vnd.ms-excel';
            const blob = new Blob(['\ufeff', tableHTML], { type: dataType });
            const url = URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = fileName;

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>



</body>

</html>