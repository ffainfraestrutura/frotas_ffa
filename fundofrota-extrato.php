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
    } elseif ($perfil <> '4' && $perfil<>'5'){
        echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
    }
    
    $_SESSION['perfil'] = $perfil;
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;

    $data = date('Y-m-d');   
    
    include"conecta.php";
    include_once"./func/log.php";
    header("Content-type: text/html; charset=utf-8");
    
    if (strftime('%A') == "domingo" ){
        $segunda = date('Y-m-d', strtotime('last monday'));
        $domingo = date('Y-m-d');
    } elseif (strftime('%A') == "segunda") {
        $segunda = date('Y-m-d');
        $domingo = date('Y-m-d', strtotime('next sunday'));
    } else {
        $segunda = date('Y-m-d', strtotime('last monday'));
        $domingo = date('Y-m-d', strtotime('next sunday'));
    }

    function pesquisaNome($mat){
        $sql3="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$mat';";
        $resultado3 = mysql_query($sql3) or die (mysql_error());
        $row3 =  mysql_fetch_array($resultado3);
            $nome = $row3[0];
        
        return $nome;
    }

    $diai = $_POST['diai'];
    $diaf = $_POST['diaf'];
    $sem = $_POST['sem'];

    if(empty($diai)){
        $diai = $ultdom;
    }

    if(empty($diaf)){
        $diaf = $data;
    }

    $ultseg = date('Y-m-d', strtotime($diai . 'last monday')); //última segunda
    $ultdom = date('Y-m-d', strtotime($diai . 'last sunday')); //última domingo
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
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.css">
        
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.7.1.js"></script> 
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script> 
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> 
        <!--<script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> -->
        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> 
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script> 
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>        
    </head>


    <body class="sb-nav-fixed vsc-initialized">
        <!--nav-->
        <?php include"menu.php"; ?>

        <div id="layoutSidenav_content">
            <main style="width: 100%;">
                <div class="container-fluid px-4" style="width: 100%;">        
                    <h1 class="h1 pt-2 pb-2"> Fundo Frota - Extrato Semanal </h1>

                    <!--div filtro-->
                    <div>
                        <form action="./fundofrota-extrato.php" method="post">
                            <!--<label for="diai">Dia inicial:</label>
                            <input type="date" name="diai" id="diai">-->
                            <div class='input-group input-group-sm' style='width: 35%'>
                                <label class='input-group-text' for="diaf">Dia final:</label>
                                <input class='form-control' type="date" name="diaf" id="diaf" <?php print "max='$data' value='$diaf' "; ?>>

                                <button class='ms-2 btn btn-success' type="submit">Filtrar</button>
                            </div>

                            
                        </form>
                    </div>

                    <!--div saldo-->
                    <div class=''>
                        <?php
                            $domantes = date('Y-m-d', strtotime($diaf . 'last sunday'));
                            //$sql1 = "SELECT ((round(sum(orcsemanal),2)*5)/100) AS saldofrota FROM bdfrota.tbsaldo where data > '$ultdom'";
                            $sql1 = "SELECT saldoatual, idtbsaldofrota, saldoinicial FROM bdfrota.tbsaldofrota WHERE data_e_hora='$domantes';";
                            
                            $resultado1 = mysql_query($sql1) or die (mysql_error());
                            $row1 = mysql_fetch_array($resultado1);
                            $saldofrota = $row1[0];
                            $idtbsaldofrota = $row1[1];
                            $saldoinicial = $row1[2];

                            $saldofrota = round($saldofrota, 2);

                            $ultdom1 = new DateTime($domantes);
                            $ultdomv = $ultdom1-> format('d/m/Y');

                            $diasemana = strftime("%A",strtotime($diaf));

                            if($data != $diaf){
                                if($diasemana!='sexta'){
                                    $diaf = date('Y-m-d', strtotime($diaf . 'next friday'));
                                } 
                            }else{
                                $diaf = $diaf;
                            }

                            $diaf1 = new DateTime($diaf);
                            $diafv = $diaf1-> format('d/m/Y');

                            print"
                            <div>
                                <h5 class='h5 pt-2 pb-2'> Semana iniciada em: $ultdomv </h5>
                                <h5 class='h5 pt-2 pb-2'> Semana finalizada em: $diafv </h5>
                            </div>
                            
                            <div class=''>
                                <h5 class='h5 pt-2 pb-2'> Saldo inicial: R$ $saldoinicial </h5>";
                            
                            if($data != $diaf){
                                print"
                                <h5 class='h5 pt-2 pb-2'> Saldo final: R$ $saldofrota </h5>";
                            }else{
                                print"
                                <h5 class='h5 pt-2 pb-2'> Saldo atual: R$ $saldofrota </h5>";
                            }
                            print"                            
                            </div>
                            ";
                        ?>                        

                    </div>

                    <!--div form filtro-->
                    

                    <!--div info -->
                    <div>
                        <table class='mt-1 display' id='tabela1'>
                            <thead>
                                <tr>
                                    <th>Data da Movimentação</th>
                                    <th>Valor (R$)</th>
                                    <th>Realizado por</th>
                                    <th>Favorecido</th>                                    
                                </tr>
                            </thead>

                            <tbody>
                            
                                <?php
                                    $sql2="SELECT SUM(valor_novo) as saldomov FROM bdfrota.tblog where acao like '%Fundo frota' AND data_e_hora BETWEEN '$domantes 00:00:00' AND '$diaf 23:59:59'; ";
                                    $resultado2 = mysql_query($sql2) or die (mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);
                                    $saldomov = $row2[0];


                                    //$sql3="SELECT data_e_hora,valor_novo, mat_autor, matricula FROM bdfrota.tblog where acao like '%Fundo frota' AND data_e_hora BETWEEN '$domantes 00:00:00' AND '$diaf 23:59:59'; ";
                                    $sql3="SELECT data_e_hora,valor, matr_autor, matricula FROM bdfrota.tbremanejamento where perfil_autor=4 AND data_e_hora BETWEEN '$domantes 00:00:00' AND '$diaf 23:59:59'; ";
                                    //print $sql2;
                                    $resultado3 = mysql_query($sql3) or die (mysql_error());
                                    
                                    while($row3 = mysql_fetch_array($resultado3)){
                                        $datamov = $row3[0];
                                        $valor = $row3[1];
                                        $autor = $row3[2];
                                        $beneficiado = $row3[3];
                                        
                                        
                                        $nomeautor = pesquisaNome($autor);
                                        $nomeben = pesquisaNome($beneficiado);

                                        $datamov1 = new DateTime($datamov);
                                        $datamovf = $datamov1-> format( 'd/m/Y H:i:s' );

                                        print"
                                        <tr>
                                            <td>$datamovf</td>
                                            <td>R$ $valor</td>
                                            <td>$nomeautor</td>
                                            <td>$nomeben</td>                                    
                                        </tr>";
                                    }
                                
                                
                                ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th>Total Movimentado</th>
                                    <?php print"<th>R$ $saldomov</th>";?>
                                    <th colspan='2'> </th>
                                </tr>
                            </tfoot>
                           
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

        <script>
            new DataTable('#tabela1', {
                layout: {
                    topStart: {
                        /*buttons: ['copy', 'csv', 'excel', 'pdf', 'print']*/
                        buttons: [{
                                    extend: 'excel',
                                    title:'Extrato Fundo Frota',
                                    messageBottom: 'Saldo inicial: <?php print "R$ $saldoinicial";?>, Saldo atual: <?php print "R$ $saldofrota";?>',
                                },]
                    }
                }
            });
            
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    </body>

</html>