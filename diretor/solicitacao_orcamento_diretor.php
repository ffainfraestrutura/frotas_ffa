<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil <> '3') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
include "../conecta.php";
include_once "../func/log.php";
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
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Solicitar Orçamento - Diretor</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 pt-1"> Solicitação de Orçamento para o Diretor </h1>
                <p class="fs-5 fw-bold">Gerente: <?php echo "$nome"; ?></p>
                <form action="control/pedirorcamento.php" method="post">
                    <!--div solicitação-->
                    <div>
                        <label class="form-label mb-0 fs-5" for="ivalor">Valor Solicitado:</label>
                        <div class="input-group mb-3">
                            <span class="input-group-text">R$: </span>
                            <input class="form-control" type="text" name="valor" id="ivalor" required="true"
                                placeholder="Informe o valor" onkeyup="formatarMoeda()">
                        </div>
                    </div>

                    <!--div justificativa-->
                    <div class="mb-3">
                        <label class="form-label mb-0 fs-5" for="ijust">Justificativa:</label>
                        <textarea step="0.01" rows="10" class="form-control" name="justificativa"
                            placeholder="Escreva a justificativa" required="true"></textarea>
                    </div>
                    <?php
                    print "<input type='hidden' name='matricula' value='$matricula'>";
                    ?>
                    <?php

                    $sql2 = "SELECT valor,orcrecebido FROM bdfrota.tbgerente where matricula = '$matricula'";
                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                    $row2 = mysql_fetch_array($resultado2);
                    $saldo = $row2[0];
                    $orccomp = $row2[1];
                    ?>
                    <div>
                        <p class="fs-6">Valor Orçamento R$: <?php echo "$orccomp"; ?></p>
                        <p class="fs-6">Saldo Atual R$: <?php echo "$saldo"; ?></p>
                    </div>

                    <!--div botão confirmar-->
                    <div>
                        <button class="btn btn-success" type="submit"> Confirmar </button>
                    </div>

                </form>
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
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="../src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</body>

</html>