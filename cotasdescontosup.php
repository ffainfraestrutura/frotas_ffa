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
} elseif ($perfil <> '1'){
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

        <!-- vendor jquery js -->
        <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

        <script language="javascript">   
function moeda(a, e, r, t) {
    let n = ""
      , h = j = 0
      , u = tamanho2 = 0
      , l = ajd2 = ""
      , o = window.Event ? t.which : t.keyCode;
    if (13 == o || 8 == o)
        return !0;
    if (n = String.fromCharCode(o),
    -1 == "0123456789".indexOf(n))
        return !1;
    for (u = a.value.length,
    h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
        ;
    for (l = ""; h < u; h++)
        -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
    if (l += n,
    0 == (u = l.length) && (a.value = ""),
    1 == u && (a.value = "0" + r + "0" + l),
    2 == u && (a.value = "0" + r + l),
    u > 2) {
        for (ajd2 = "",
        j = 0,
        h = u - 3; h >= 0; h--)
            3 == j && (ajd2 += e,
            j = 0),
            ajd2 += l.charAt(h),
            j++;
        for (a.value = "",
        tamanho2 = ajd2.length,
        h = tamanho2 - 1; h >= 0; h--)
            a.value += ajd2.charAt(h);
        a.value += r + l.substr(u - 2, u)
    }
    return !1
}
 </script>  
        
    </head>


    <body class="sb-nav-fixed vsc-initialized">

        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">

        
        <h1 class="h1 pt-2 pb-2"> Desconto Cotas Indevidas </h1>



            <!--div tabela - entrar dados do sistema-->
            <div id="containertab">
                <table id="datatablesSimple" class="display" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Matricula</th>
                            <th>Valor</th>
                            <th>Aceito</th>
                            <th>Data</th>
                            <th>Doc</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php

                    $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = $matricula ";

                    $resultado = mysql_query($sql) or die (mysql_error());
                    $row = mysql_fetch_array($resultado);
                    $idtbsupervisor = $row[0];

                    $sql = "SELECT * FROM bdfrota.tbcotaextraindevida where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idtbsupervisor') order by dataac desc";
                        
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculaf = $row['matricula'];
                        $id = $row['idtbcotaextraindevida'];
                        $idpedidotec = $row['idpedidotec'];
                        $valdesc = $row['valdesc'];
                        $actec = $row['actec'];
                        $docactec = $row['docactec'];
                        $dataac = $row['dataac'];

                        if ($actec == 0) {
                           $status = 'pendente';
                        } elseif ($actec == 1) {
                            $status = 'aceito';
                        } elseif ($actec == 2) {
                            $status = 'não aceito';
                        } else {
                            $status = 'aguardando';
                        }

                    
                            print"                        
                            <tr>
                            <td>$idpedidotec</td>
                            <td>$matriculaf</td>
                            <td>R$ $valdesc</td>
                            <td>$status</td>
                            <td>$dataac</td>";
                            if ($docactec <> '') {
                                  print"<td> <form action='./pdf/fpdf/reciboassinado.php' method='post' target='_blank'>
                                        
                            <input type='hidden' name='id' value='$id'>
                            <button class='btn btn-link' type='submit'><i class='fa-solid fa-eye'></i></button></a></td>
                              </tr>";
                              } else {
                                print"<td> <button class='btn btn-link'><i class='fa-solid fa-close'></i> </button></td>
                              </tr>";
                              }



                        }


               //CRIAR O NÃO ACEITADO E CREDITAR O COMBUSTÍVELD DEPOIS DE ACEITAR 

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

        <script>
            //função data table para criar barra de rolagem
            $(document).ready(function () {
                $('#datatablesSimple').DataTable({
                scrollX: true,
                });
            });

            //função para formatar valor do input - moeda
            function formatarMoeda(i) {
                var v = i.value.replace(/\D/g,'');
                v = (v/100).toFixed(2) + '';
                v = v.replace(".", ",");
                v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                i.value = v;
            }
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    </body>

</html>