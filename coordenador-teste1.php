<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index-teste.php\"</script>";
} elseif ($perfil <> '2'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index-teste.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

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

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

        <?php include"menu-teste.php"; ?>

        <div id="layoutSidenav_content">
            <main>

                <div class="container-fluid px-4">
                    <h1 class="mt-4">Coordenador</h1>
                      <div class="callout callout-secondary">
                    <?php
                    $sql = "SELECT ROUND(valor,2) FROM bdfrota.tbcoord where matricula = '$matricula'";
                    $resultado = mysql_query($sql) or die (mysql_error());
                    $row = mysql_fetch_array($resultado);
                    $saldocoord = $row[0];
                    $saldocoord = str_replace(".", ",", $saldocoord);
                    ?>

                    <h5><p><b>Saldo = <?php print"R$ $saldocoord"; ?></b></p></h5>

                     <!--<div class="col-md-4">
                      <label for="exampleInputEmail1">Cota Diária:</label>
                      <input type="input" class="form-control form-control-md" name="cotadiaria" onKeyPress="return(moeda(this,'.',',',event))">
                    </div>

                    <div class="form-group col-md-2">
                      <label for="exampleInputEmail1"><b style="color: white">.</b></label>
                    <input type="submit" class="form-control form-control-md btn btn-success" value="Inserir">
                    </div>-->

                </div><!--</div>-->

                <div class="container-fluid px-4">
                    <h3 class="mt-4"></h3>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Coordenador
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" style="font-size: 12px;">
                              <thead>
                                <tr>
                                  <th>Matricula</th>
                                  <th>Supervisor</th>
                                  <th>Saldo</th>
                                  <th>Orc. Complementar</th>
                                  <th>Filial</th>
                                  <th>Data e Hora</th>
                                  <th>Justificativa</th>
                                  <th>Novo Orc.</th>
                                  <th>Decisão</th>
                                </tr>
                              </thead>                    
                              
                              <tbody>
                                                              
                                
                              </tbody>

                            </table>

                        </div>
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    </body>
</html>
