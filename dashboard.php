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
} elseif ($perfil <> '3' and $perfil <> '4' and $perfil <> '5') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

$data = date('Y-m-d');
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
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">


                <!--fim div lateral-->

                <div class="callout callout-secondary ms-4 me-4">
                    <div class="row mt-4 bg-danger">
                        <h1 class="h1 ms-2 col-6 text-right text-white"><a class="text-white"
                                href="./reltoaquisup.php">VISUALIZAR TO AQUI SUPERVISOR</a></h1>
                        <h1 class="h1 col-1 text-right text-white">|</h1>
                        <h1 class="h1 col-4 text-white"><a class="text-white" href="./reltoaqui.php">VISUALIZAR TO AQUI
                                TÉCNICO</a></h1>
                    </div>
                    <!--<div id="collapseExample4">
                <form action="./dashboard.php" method="post">
                    <div class="row">

                        <div class="col-md-auto">
                            <label for="exampleInputEmail1">Início:</label>
                            <input type="date" class="form-control form-control-md" name="inicio">
                        </div>

                        <div class="col-md-auto">
                            <label for="exampleInputEmail1">Fim:</label>
                            <input type="date" class="form-control form-control-md" name="fim">
                        </div>

                        <div class="form-group col-md-auto">
                            <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                            <input type="submit" class="form-control form-control-md btn btn-secondary" value="Filtrar">
                        </div>
                    </div>
                </form>
            </div>!-->
                </div>

                <!--div graficos-->
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


    <script>
        //função para formatar
        function formatarMoeda() {
            var elemento = document.getElementById('ivalor');
            var valor = elemento.value;


            valor = valor + '';
            valor = parseInt(valor.replace(/[\D]+/g, ''));
            valor = valor + '';
            valor = valor.replace(/([0-9]{2})$/g, ",$1");

            if (valor.length > 6) {
                valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
            }

            elemento.value = valor;
            if (valor == 'NaN') elemento.value = '';

        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
            crossorigin="anonymous"></script>
                <script src="src/js/scripts.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
                        <script src="assets/demo/chart-area-demo.js"></script>
                            <script src="assets/demo/chart-bar-demo.js"></script>
                                <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
                                    <script src="src/js/datatables-simple-demo.js"></script>
                                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>


</body>

</html>