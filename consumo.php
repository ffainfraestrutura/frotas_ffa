<?php
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

        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">
                <main>

<div class="container-fluid px-4">

        
            <h1 class="h1 mt-1"> Consumo de Combustível </h1>

    </div>


 <div class="container-fluid px-4">
    <h3 class="mt-4"></h3>
    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Veículo
      </div>
      <div class="card-body">
        <table id="datatablesSimple">
          <thead>
            <tr>
              <th style="white-space: nowrap;">Tipo Veículo</th>
              <th style="white-space: nowrap;">Consumo Combustível</th>
              <th style="white-space: nowrap;">Tipo Combustível</th>
              <th style="white-space: nowrap;">Alterar</th>
            </tr>
          </thead>
          
          
          <tbody>
            <?php
              $sql = "SELECT tipoveiculo, consumo_litro, tipocombustivel, idtbconsumocombustivel FROM bdfrota.tbconsumocombustivel";
              $resultado = mysql_query($sql) or die (mysql_error());
              while($row = mysql_fetch_array($resultado)){

                $tipoveiculo = $row[0];
                $consumo_litro = $row[1];
                $tipocombustivel = $row[2];
                $idveiculo = $row[3];

                print"<tr role='row'>
                    <td>$tipoveiculo</td>
                    <td>$consumo_litro km/l</td>
                    <td>$tipocombustivel</td>
                    <td><button class = 'btn btn-success'>Alterar</button>
                </tr>";

              }
              ?>
          </tbody>

          </table>

        </div>
      </div>
</div>
       <div class="container-fluid px-4">
    <h3 class="mt-4"></h3>
    <div class="card mb-4">
      <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Combustível
      </div>
      <div class="card-body">
        <table id="datatablesSimple2">
          <thead>
            <tr>
              <th style="white-space: nowrap;">Tipo Combustível</th>
              <th style="white-space: nowrap;">Valor Combustível</th>
              <th style="white-space: nowrap;">Alterar</th>
            </tr>
          </thead>
          
          
          <tbody>
            <?php
              $sql = "SELECT valorcomb, tipo, idtbconsumocombustivel FROM bdfrota.tbvalorcombustivel";
              $resultado = mysql_query($sql) or die (mysql_error());
              while($row = mysql_fetch_array($resultado)){

                $valorcomb = $row[0];
                $tipo = $row[1];
                $idcombustivel = $row[2];

                print"<tr role='row'>
                    <td>$tipo</td>
                    <td>R$ $valorcomb</td>
                    <td><button class = 'btn btn-success'>Alterar</button>
                </tr>";

              }
              ?>
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
        <script>
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
    </body>

</html>