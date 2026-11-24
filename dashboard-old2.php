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
} elseif ($perfil <> '3' and $perfil <> '4' and $perfil <> '5'){
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
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">
    <main>

<div class="container-fluid px-4">

        <?php
            $sql = "SELECT data, ROUND(orcamentosistema,2),  ROUND(orcamentofinal,2),  ROUND(saldoorcamento,2), idtborcamentomensal, ROUND(orcamentocomplementar,2), ROUND(cotafrota,2) FROM bdfrota.tborcamentomensal order by data desc";
            $resultado = mysql_query($sql) or die (mysql_error());
            $row = mysql_fetch_array($resultado);
            $data = $row[0];
            $orcamentosistema = str_replace(".", ",", $row[1]);
            $orcamentofinal = str_replace(".", ",", $row[2]);
            $saldoorcamento = str_replace(".", ",", $row[3]);
            $mesesc = strftime('%B',strtotime("$data"));
            $id = $row[4];
            $orccomplementar = $row[5];
            $cotafrota = $row[6];
        ?>

        <!--div lateral-->
        <div class="float-end col-3 border-start border-ligth ps-3" style="height: 100vh;">
            <h3 class="pt-2 pb-2"> Orçamento Período </h3>

            <h5 class="mb-4 pb-2" style="border-bottom: 1px solid black;">Mês: <?php print"$mesesc"; ?> </h5>


                <div class="text-center">
                <!--orçamento sistema-->
                <div class="text-center">
                    <p class="form-label mb-0 fs-5">Orçamento Sistema:</p>
                    <p><button class="btn btn-primary" style="width: 80%; border-radius: 0.4rem;" disabled> <?php print"R$: $orcamentosistema"; ?> </button> </p>
                </div>

                <!--div orçamento final-->
                <div class="text-center">
                    <p class="form-label mb-0 fs-5">Orçamento Final:</p>
                    <p><button class="btn btn-secondary" style="width: 80%; background-color: #ffc107; border-color: #ffc107; border-radius: 0.4rem;" disabled> <?php print"R$: $orcamentofinal"; ?> </button> </p>
                </div>

                <!--div saldo orçamento-->
                <div class="text-center">
                    <p class="form-label mb-0 fs-5">Saldo Orçamento:</p>
                    <p><button class="btn btn-danger" style="width: 80%; border-radius: 0.4rem;" disabled> <?php print"R$: $saldoorcamento"; ?> </button> </p>
                </div>

                <!--div orçamento complementar-->
                <div class="text-center">
                    <p class="form-label mb-0 fs-5">Saldo Complementar:</p>
                    <p><button class="btn btn-secondary" style="width: 80%; border-radius: 0.4rem;" disabled> <?php print"R$: $orccomplementar"; ?> </button> </p>
                </div>

                <!--div orçamento frota-->
                <div class="text-center">
                    <p class="form-label mb-0 fs-5">Orçamento Frota:</p>
                    <p><button class="btn btn-success" style="width: 80%; border-radius: 0.4rem;" disabled> <?php print"R$: $cotafrota"; ?> </button> </p>
                </div>
                </div>


                <!--div botão confirmar
                <form action="./control/orcamento.php" method="post">

                <div class="mt-5 pt-3 border-top border-dark justify-content-center">
                    <div>
                        <p class="form-label mb-0 fs-5 mb-1">Orçamento Complementar:</p>
                        <p><input type="text" name="valor" class="form-control" id="ivalor" placeholder="Informe o valor do orçamento complementar" onkeyup="formatarMoeda()"> </p>
                    </div>
                    <?php print"<input type='hidden' name='id' value='$id'>"; ?>
                    <div class="text-center">
                        <button class="btn btn-success" type="submit" style="width: 80%; border-radius: 0.5rem;"> Confirmar </button>
                    </div>
                </div>
                </form>-->
        </div>

        <!--fim div lateral-->

        <div class="callout callout-secondary ms-4">
            <h1 class="h1 mt-2"></h1>

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
        <div class="container-fluid px-4 mt-4">
          <div class="row col-9">
            

            <div class="col-lg-6">
              <div class="card mb-4 text-center">
                <div class="card-header">
                  <i class="fas fa-chart-bar me-1"></i>
                  Ofensores KM fora de escala
                </div>
                <div class="card-body">
                    <canvas id="kmvalor" width="100%" height="50"></canvas>
                </div>
                <div> 
                    <a href="gpsxfolga.php" class="col-auto btn btn-primary ms-3 mb-3" style="width: 70%"> Detalhes </a> 
                </div>
              </div>
            </div>

            <div class="col-lg-6">
              <div class="card mb-4 text-center">
                <div class="card-header">
                  <i class="fas fa-chart-bar me-1"></i>
                  Ofensores Km fora do horário
                </div>
                <div class="card-body">
                    <canvas id="projetadoeos" width="100%" height="50"></canvas>
                </div>
                <div> 
                    <a href="gpsxhora.php" class="col-auto btn btn-danger ms-3 mb-3" style="width: 70%"> Detalhes </a> 
                </div>
              </div>
            </div>




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
            if(valor == 'NaN') elemento.value = '';
        
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

        <!--script grafico km e valor-->
        <script type="text/javascript">
        //estilos do chart
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        //inserindo chart
        // Bar Chart Example
            var ctx = document.getElementById("kmvalor");
            var myLineChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: [
<?php     
if (strftime('t') >= 21) {

    $mesini = date('m');
    $anoini = date('y');
    if (date('m') == 12) {
        $mesfim = 1;
        $anofim = date('y', strtotime('next year'));
    } else {
        $mesfim = date('m', strtotime('next month'));
        $anofim = date('y');
    }

} else {

    $mesfim = date('m');
    $anofim = date('y');
    if (date('m') == 1) {
        $mesini = 12;
        $anoini = date('y', strtotime('last year'));
    } else {
        $mesini = date('m', strtotime('last month'));
        $anoini = date('y');
    }

}

$diah = strftime('t');
$ontem = $diah - 1;

$dataini = "20$anoini-$mesini-21";
$datafim = date('Y-m-d', strtotime('-1 days'));

$sql = "SELECT matricula, SUM(kmtotal) as soma_kmtotal
FROM (
  SELECT matricula, DATE(data) as dia, ROUND(MAX(kmtotal) - MIN(kmtotal), 2) as kmtotal
  FROM bdfrota.tbrelatoriogpsfalta
  WHERE data > '$dataini 00:00:00' AND data < '$datafim 23:59:59'
  GROUP BY matricula, dia
) as subconsulta
GROUP BY matricula
HAVING soma_kmtotal <= 2000
ORDER BY soma_kmtotal desc LIMIT 5";

      $resultado = mysql_query($sql) or die (mysql_error());
      $i = 0;
      while ($i < 5) {
        $row = mysql_fetch_array($resultado);
        $matriculaf = $row[0];
        $totval = $row[1];
        $maior = 0;

        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomefunc = $row2[0];
        $nomeprim = explode(" ", $nomefunc);

        print "'$nomeprim[0]',";
        $i = $i + 1;
      }?>
                    ],
                datasets: [{
                    label: "KM",
                    backgroundColor: "rgba(2,117,216,1)",
                    borderColor: "rgba(2,117,216,1)",
                    data: [
<?php     $sql = "SELECT matricula, SUM(kmtotal) as soma_kmtotal
FROM (
  SELECT matricula, DATE(data) as dia, ROUND(MAX(kmtotal) - MIN(kmtotal), 2) as kmtotal
  FROM bdfrota.tbrelatoriogpsfalta
  WHERE data > '$dataini 00:00:00' AND data < '$datafim 23:59:59'
  GROUP BY matricula, dia
) as subconsulta
GROUP BY matricula
HAVING soma_kmtotal <= 2000
ORDER BY soma_kmtotal desc LIMIT 5";
      $resultado = mysql_query($sql) or die (mysql_error());
      $i = 0;
      while ($i < 5) {
        $row = mysql_fetch_array($resultado);
        $totval = $row[1];
        if ($valorkm > $maior) {
            $maior = $valorkm;
        }

        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomefunc = $row2[0];
        $nomeprim = explode(" ", $nomefunc);

        print "'$totval',";
        $i = $i + 1;
      }?>
                        ],
                }],
            },
            options: {
                    scales: {
                        xAxes: [{
                        time: {
                            unit: 'KM'
                        },
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        },
                        label: {
                            display: true,
                        }
                    }],

                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: <?php print"$maior"; ?>,
                            maxTicksLimit: 5,
                            autoSkip: false
                        },
                        gridLines: {
                            display: true
                        }
                    }],
                    events: ['click'],
                },
                legend: {
                    display: false
                }
                },//fecha options
                
            });//fecha var myLineChart 
        </script>
        
        <!--script grafico km projetado e os-->
        <script type="text/javascript">
            //estilos do chart
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        //inserindo chart
         // Bar Chart Example
            var ctx = document.getElementById("projetadoeos");
            var myLineChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: [
<?php    

$diad = date("Y-m-d", strtotime($datafim . "+1 days"));

$sql = "SELECT matricula, SUM(kmtotal) as soma_kmtotal
FROM (
  SELECT matricula, DATE(data) as dia, ROUND(MAX(kmtotal) - MIN(kmtotal), 2) as kmtotal
  FROM bdfrota.tbrelatoriogpsmovind
  WHERE data > '$dataini 00:00:00' AND data < '$diad 04:59:59'
  GROUP BY matricula, dia
) as subconsulta
GROUP BY matricula
HAVING soma_kmtotal <= 2000
ORDER BY soma_kmtotal desc LIMIT 5";

      $resultado = mysql_query($sql) or die (mysql_error());
      $i = 0;
      while ($i < 5) {
        $row = mysql_fetch_array($resultado);
        $matriculaf = $row[0];
        $totval = $row[1];
        $maior = 0;

        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomefunc = $row2[0];
        $nomeprim = explode(" ", $nomefunc);

        print "'$nomeprim[0]',";
        $i = $i + 1;
      }?>
                    ],
                datasets: [{
                    label: "KM",
                    backgroundColor: "rgba(220,53,69,1)",
                    borderColor: "rgba(220,53,69,1)",
                    data: [
<?php     $sql = "SELECT matricula, SUM(kmtotal) as soma_kmtotal
FROM (
  SELECT matricula, DATE(data) as dia, ROUND(MAX(kmtotal) - MIN(kmtotal), 2) as kmtotal
  FROM bdfrota.tbrelatoriogpsmovind
  WHERE data > '$dataini 00:00:00' AND data < '$diad 04:59:59'
  GROUP BY matricula, dia
) as subconsulta
GROUP BY matricula
HAVING soma_kmtotal <= 2000
ORDER BY soma_kmtotal desc LIMIT 5";
      $resultado = mysql_query($sql) or die (mysql_error());
      $i = 0;
      while ($i < 5) {
        $row = mysql_fetch_array($resultado);
        $totval = $row[1];
        if ($valorkm > $maior) {
            $maior = $valorkm;
        }

        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matriculaf'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomefunc = $row2[0];
        $nomeprim = explode(" ", $nomefunc);

        print "'$totval',";
        $i = $i + 1;
      }?>],
        }],//fecha datasets
            },//fecha data
            options: {
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'KM'
                        },
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        },
                        label: {
                            display: true,
                        }
                    }],

                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: <?php print"$maior"; ?>,
                            maxTicksLimit: 5,
                            autoSkip: false
                        },
                        gridLines: {
                            display: true
                        }
                    }],
                    events: ['click'],
                },
                legend: {
                    display: false
                }
                },//fecha options
                
            });//fecha var myLineChart 
        </script>
    </body>

</html>