<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];


if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '3' and $perfil <> '4' and $perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

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
     

        <!--fim div lateral-->

        <div class="callout callout-secondary ms-4 me-4">
            <div class="row mt-4 bg-danger">
            <h1 class="h1 ms-2 col-6 text-right text-white"><a class="text-white" href="./reltoaquisup.php">VISUALIZAR TO AQUI SUPERVISOR</a></h1>
            <h1 class="h1 col-1 text-right text-white">|</h1>
            <h1 class="h1 col-4 text-white"><a class="text-white" href="./reltoaqui.php">VISUALIZAR TO AQUI TÉCNICO</a></h1>
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
        <div class="container-fluid px-4 mt-4">
          <div class="row col-12">

            <div class="col-lg-6">
              <div class="card mb-4 text-center">
                <div class="card-header">
                  <i class="fas fa-chart-bar me-1"></i>
                  Ofensores Cotas Extras Indevidas (R$)
                </div>
                <div class="card-body">
                    <canvas id="cotasextrasind" width="100%" height="50"></canvas>
                </div>
                <div> 
                    <a href="./detalhes-cota-extra-indevidas2.php" class="col-auto btn btn-danger ms-3 mb-3" style="width: 70%"> Detalhes </a> 
                </div>
              </div>
            </div>
            
            <div class="col-lg-6">
              <div class="card mb-4 text-center">
                <div class="card-header">
                  <i class="fas fa-chart-bar me-1"></i>
                  Ofensores KM GPS X KM OS
                </div>
                <div class="card-body">
                    <canvas id="kmgpsos" width="100%" height="50"></canvas>
                </div>
                <div> 
                    <a href="detalhes-ofensores-gpsxos.php" class="col-auto btn btn-primary ms-3 mb-3" style="width: 70%"> Detalhes </a> 
                </div>
              </div>
            </div>

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


<!--script grafico km projetado e os-->
        <script type="text/javascript">
            //estilos do chart
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        //inserindo chart
         // Bar Chart Example
            var ctx = document.getElementById("kmgpsos");
            var myLineChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: [
<?php    

$diad = date("Y-m-d", strtotime("-1 days"));

$sql = "SELECT matricula, diferenca FROM bdfrota.tbofensoresgpstoa where data = '$diad'
ORDER BY diferenca desc LIMIT 5";

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
<?php     $sql = "SELECT matricula, diferenca FROM bdfrota.tbofensoresgpstoa where data = '$diad'
ORDER BY diferenca desc LIMIT 5";
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



        <!--script grafico cotas extras indevidas -->
        <script type="text/javascript">
            //estilos do chart
        // Set new default font family and font color to mimic Bootstrap's default styling
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';

        //inserindo chart
         // Bar Chart Example
            var ctx = document.getElementById("cotasextrasind");
            var myLineChart = new Chart(ctx, {
            type: 'horizontalBar',
            data: {
                labels: [
<?php    

//$diad = date("Y-m-d", strtotime($datafim . "+1 days"));

$sql = "SELECT  matsup, sum(valorinserido) as valor
FROM bdfrota.tbcotaextraac
where dataaceite > '$dataini 00:00:00' and dataaceite < '$diad 23:59:59' and cota_validade = '1'  group by matsup order by valor desc";

      $resultado = mysql_query($sql) or die (mysql_error());
      $i = 0;
      
      while ($i < 5) {
        $row = mysql_fetch_array($resultado);
        $matsup = $row[0];
        $valortotal = $row[1];
        
        $sql2 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matsup'";
        $resultado2 = mysql_query($sql2) or die (mysql_error());
        $row2 = mysql_fetch_array($resultado2);
        $nomesup = $row2[0];
        $nomesup1 = explode(" ", $nomesup);

        print "'$nomesup1[0]',";

        $i = $i + 1;
      }?>
                    ],
                datasets: [{
                    label: "Reais",
                    backgroundColor: "rgba(220,53,69,1)",
                    borderColor: "rgba(220,53,69,1)",
                    data: [

<?php   
$sql = "SELECT  matsup, sum(valorinserido) as valor
FROM bdfrota.tbcotaextraac
where dataaceite > '$DATAINI 00:00:00' and dataaceite < '$diad 23:59:59' and cota_validade = '1'  group by matsup order by valor desc";

      $resultado = mysql_query($sql) or die (mysql_error());
      $i = 0;
      while ($i < 5) {
        $row = mysql_fetch_array($resultado);
        $matsup = $row[0];
        $valortotal = $row[1];  

        print "'$valortotal',";
        $i = $i + 1;
      }?>],
        }],//fecha datasets
            },//fecha data
            options: {
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'reais'
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
                            /*min: 0,
                            max: 4000,*/
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