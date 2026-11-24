<?php
    setlocale(LC_ALL, 'pt_BR.utf8');
    date_default_timezone_set('America/Sao_Paulo');
    session_start();
    
    $nome = $_SESSION['nome'];
    $usuariof = $_SESSION['usuario'];
    $matricula = $_SESSION['matricula'];
    $perfil = $_SESSION['perfil'];
    
    if ($perfil == null){
        echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
    } elseif ($perfil == '0'){
        echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
    }
    
    $_SESSION['perfil'] = $perfil;
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;

    $idcoord = $_POST["idtbcoord"];

    $data = date('Y-m-d');
    $ultseg = date('Y-m-d', strtotime($data . 'last monday'));

    include"conecta.php";
    include_once"./func/log.php";
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
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

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
        <?php include"./menu.php"; ?>
        <div id="layoutSidenav_content">

            <main>
                <div class="container-fluid px-4">
                    <h1 class="h1 mt-2"> Saldo supervisor por período </h1>
                    
                    <div id="collapseExample4" style="width: 33%;">
                        <form action="./detalhes-saldo-supxcoord.php" method="post">
                            <h5><b>Coordenador:</b></h5>
                            <div class="input-group">
                                <select class="form-select" id="inputGroupSelect04" name="idtbcoord">
                                    <option value=''>Selecione o coordenador...</option>
                                    <?php
                                        $sql = "SELECT distinct(a.idtbcoordenador), b.nome FROM bdfrota.tbcoord  a, bdaniel.tbfuncionario b where a.matricula = b.matricula ORDER BY b.nome;";
                                        $resultado = mysql_query($sql) or die (mysql_error());
                                        while ($row = mysql_fetch_array($resultado)) {
                                            $idtbcoord = $row[0];
                                            $nomecoord = $row[1];

                                            print"<option value='$idtbcoord'>$nomecoord</option>";
                                        }
                                    ?>      
                                </select>
                                <input type="submit" class="btn btn-success"  value="Filtrar">
                            </div>
                        </form>
                    </div>

                    <div class="card mt-4 mb-4">
                        <div class="card-header">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i>
                                Voltar para Dashboard Inicial 
                            </a href="dashboard.php">
                        </div>
                        <div class="card-body mb-5">
                            <!--tabela para entrada de dados-->
                            <table id='datatablesSimple' class='table table-bordered'>
                                <thead>
                                    <tr>
                                        <th>Matricula</th>
                                        <th>Nome</th>
                                        <th>Centro de Custo</th>
                                        <th>Saldo</th>
                                        <th>Sld. Remanejamento</th>
                                        <th>Fd. Frota</th>
                                        <th>Total Período</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php     
                                    if ($idcoord <> '') {
                                        $sql = "SELECT matricula, valor, orcrecebido FROM bdfrota.tbcoord where idtbcoordenador = '$idcoord'";
                                        $resultado = mysql_query($sql) or die (mysql_error());
                                        $row = mysql_fetch_array($resultado);

                                        $matriculaf = $row[0];
                                        $valor = $row[1];
                                        $total = $row[2];

                                        $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
                                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                                        $row2 = mysql_fetch_array($resultado2);
                                        $nome = $row2[0];
                                        $ccusto = $row2[1];

                                        /*print "
                                            <tr>
                                                <td>$matriculaf</td>
                                                <td>$nome</td>
                                                <td>$ccusto</td>
                                                <td>R$ $valor</td>
                                                <td>R$ $total</td>
                                            </tr>";*/

                                        $sql = "SELECT matricula, ROUND(valor, 2), ROUND(totorccomp, 2) FROM bdfrota.tbsupervisor where idtbcoordenador = '$idcoord' order by totorccomp desc";
                                        $resultado = mysql_query($sql) or die (mysql_error());
                                        while ($row = mysql_fetch_array($resultado)){
                                            $matriculaf = $row[0];
                                            $valor = $row[1];
                                            $valor = round($valor,2);
                                            $total = $row[2];
                                            $total = round($total,2);


                                            if(strpos($valor,".") !== false){
                                                $valor = str_replace(".",",",$valor);
                                            }

                                            if(strpos($total,".") !== false){
                                                $total = str_replace(".",",",$total);
                                            }

                                            $sql2 = "SELECT nome, ccusto FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
                                            $resultado2 = mysql_query($sql2) or die (mysql_error());
                                            $row2 = mysql_fetch_array($resultado2);
                                            $nome = $row2[0];
                                            $ccusto = $row2[1];

                                            //remanejamento positivo
                                            $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matr_autor='$matriculaf' AND data_e_hora BETWEEN  '$ultseg 00:00:00' AND '$data 23:59:59' AND tipo=1; ";                          
                                            $resultado4 = mysql_query($sql4) or die (mysql_error());
                                            $row4 = mysql_fetch_array($resultado4);
                                            $remanejpositivo = $row4[0];
                                            
                                            //remanejamento negativo
                                            $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matr_autor='$matriculaf' AND data_e_hora BETWEEN  '$ultseg 00:00:00' AND '$data 23:59:59' AND tipo=0; ";                          
                                            $resultado5 = mysql_query($sql5) or die (mysql_error());
                                            $row5 = mysql_fetch_array($resultado5);
                                            $remanejnegativo = $row5[0];
                                            
                                            $saldoremanej = $remanejpositivo-$remanejnegativo;
                                            $saldoremanej = round($saldoremanej,2);

                                            if(strpos($saldoremanej,".") !== false){
                                                $saldoremanej = str_replace(".",",",$saldoremanej);
                                            }

                                            //saldo fundo frota
                                            $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculaf' AND data_e_hora BETWEEN  '$ultseg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";                          
                                            $resultado6 = mysql_query($sql6) or die (mysql_error());
                                            $row6 = mysql_fetch_array($resultado6);
                                            $ffrota = $row6[0];
                                            $ffrota = round($ffrota, 2);
                                            if(strpos($ffrota,".") !== false){
                                                $ffrota = str_replace(".",",",$ffrota);
                                            }

                                            print "
                                                <tr>
                                                    <td>$matriculaf</td>
                                                    <td>$nome</td>
                                                    <td>$ccusto</td>
                                                    <td>R$ $valor</td>
                                                    <td>R$ $saldoremanej</td>
                                                    <td>R$ $ffrota</td>
                                                    <td>R$ $total</td>
                                                </tr>";
                                        }
                                    }
                                ?>                        
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
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
    </body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <!--script para criar data table-->
    <script>
        //função iniciar data table 
        /*$(document).ready( function () {
            $('#datatablesSimple').DataTable();
        });*/
        new DataTable('#datatablesSimple', {
            order: [[1, 'asc']],
            scrollY: true,
            layout: {
                topStart: {
                    //buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                    buttons: [{
                        extend: 'excelHtml5',
                        title: 'Saldo supervisor por período'
                    },],                
                }
            }
        });
    </script>
</html>