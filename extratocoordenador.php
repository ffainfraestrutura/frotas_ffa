<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

include"conecta.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br" style="overflow-x: hidden;">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />

        <!--link bootstrap 5 - editar-->
        <link href="src/css/styles.css" rel="stylesheet" />

        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

        <!--data tables - css e js-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>        
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled" style="overflow-x: hidden;">

    	<!--nav-->
    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main style="width: 100%;" class="m-auto ms-3 mt-3">

<div class="container-fluid px-4" style="width: 100%;">
    <h1 class="h1 pb-2"> Extrato Coordenador </h1>

        <!--div tabela - entrar dados do sistema-->
    	<div id="containertab">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th style="white-space: nowrap;">ID</th>                     
                        <th style="white-space: nowrap;">Data</th>
                        <th style="white-space: nowrap;">Valor pedido</th>
                        <th style="white-space: nowrap;">Valor adicionado</th>
                        <th style="white-space: nowrap;">Aceito?</th>
                    </tr>
                </thead>

                <tbody>
                 <tr>
                        <td style="white-space: nowrap;">Matrícula</td>
                        <td style="white-space: nowrap;">Data</td>
                        <td style="white-space: nowrap;">Valor pedido</td>
                        <td style="white-space: nowrap;">Valor adicionado</td>
                        <td style="white-space: nowrap;">Aceito?</td>
                    </tr>
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
