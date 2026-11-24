<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
$matriculasuper = $_POST['coordenador'];

$data = date('Y-m-d');
$useg = date('Y-m-d', strtotime($data . 'last monday'));

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

    <link href="src/css/styles.css" rel="stylesheet" />
    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- vendor jquery js -->
    <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

    <!--datatable-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">

</head>


<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">


                <h1 class="h1 pt-2 pb-2"> Orçamento Coordenadores </h1>
                <!--div tabela - entrar dados do sistema-->
                <div id="containertab">
                    <table id="datatablesSimple" class="display" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">Matrícula</th>
                                <th style="white-space: nowrap;">Nome</th>
                                <th>Saldo</th>
                                <th>Total Orçamento</th>
                                <th>Sld. Remanejamento</th>
                                <th>Fd. Frota</th>
                                <th style="white-space: nowrap;">Enviar</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            $sql = "SELECT 
                                        c.matricula,
                                        f.nome,
                                        c.valor         AS saldo,
                                        c.orcrecebido   AS valrec,

                                        COALESCE(r.remanejpositivo,0) AS remanejpositivo,
                                        COALESCE(r.remanejnegativo,0) AS remanejnegativo,
                                        COALESCE(r.ffrota,0)          AS ffrota

                                    FROM bdfrota.tbcoord c

                                    JOIN bdaniel.tbfuncionario f 
                                        ON c.matricula = f.matricula

                                    LEFT JOIN (
                                        SELECT 
                                            matr_autor,
                                            SUM(CASE WHEN tipo = 1 THEN valor ELSE 0 END) AS remanejpositivo,
                                            SUM(CASE WHEN tipo = 0 THEN valor ELSE 0 END) AS remanejnegativo,
                                            SUM(CASE WHEN tipo = 1 AND perfil_autor = 4 THEN valor ELSE 0 END) AS ffrota
                                        FROM bdfrota.tbremanejamento
                                        WHERE data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59'
                                        GROUP BY matr_autor
                                    ) r 
                                        ON r.matr_autor = c.matricula
                                    ";

                            $resultado = mysql_query($sql) or die(mysql_error());

                            while ($row = mysql_fetch_assoc($resultado)) {

                                $matriculasup = $row['matricula'];
                                $nomesup = $row['nome'];

                                $saldo = round($row['saldo'], 2);
                                $valrec = round($row['valrec'], 2);

                                $remanejpositivo = $row['remanejpositivo'];
                                $remanejnegativo = $row['remanejnegativo'];

                                $saldoremanej = $remanejpositivo - $remanejnegativo;

                                $ffrota = str_replace(".", ",", $row['ffrota']);

                                print "                        
                                <tr>
                                    <td style='white-space: nowrap;'>$matriculasup</td>
                                    <td style='white-space: nowrap;'>$nomesup</td>  
                                    <td style='white-space: nowrap;'>R$ $saldo</td>  
                                    <td style='white-space: nowrap;'>R$ $valrec</td>  
                                    <td style='white-space: nowrap;'>R$ $saldoremanej</td>  
                                    <td style='white-space: nowrap;'>R$ $ffrota</td>  
                                    <td>
                                        <form action='./control/frotacoorddist.php' method='post'>
                                            <div class='input-group mb-3'>             
                                                <input type='text' class='ivalor form-control' placeholder='' aria-label='' aria-describedby='button-addon2' name='valor' onkeyup='formatarMoeda(this);'>
                                                <div class='input-group-append'>
                                                    <button class='btn  btn-outline-success' type='submit' id='button-addon2'><span class='material-symbols-outlined'>done</span></button>
                                                </div>
                                            </div>
                                            <input type='hidden' name='matriculasup' value='$matriculasup'>
                                            <input class='tipocota' type='hidden' name='tipocota' value ='orcdist'>
                                        </form>
                                    </td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
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

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>



    <script>
        //função data table para criar barra de rolagem
        /*$(document).ready(function () {
            $('#datatablesSimple').DataTable({
                scrollX: true,
                order:[[1,'asc']]
            });
        });*/
        new DataTable('#datatablesSimple', {
            order: [[1, 'asc']],
            layout: {
                topStart: {
                    //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    buttons: ['excel']
                }
            }
        });

        //função para formatar valor do input - moeda
        function formatarMoeda(i) {
            var v = i.value.replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace(".", ",");
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            i.value = v;
        }

    </script>


</body>

</html>