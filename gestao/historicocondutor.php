<?php
  date_default_timezone_set('America/Sao_Paulo');

  // Defina o tempo de vida da sessão para 1 hora
  $lifetime = 3600; // 1 hora em segundos

  // Configurar os parâmetros do cookie da sessão
  session_set_cookie_params($lifetime);

  // Iniciar a sessão
  session_start();
  $nome = $_SESSION['nome'];
  $usuariof = $_SESSION['usuario'];
  $matricula = $_SESSION['matricula'];
  $perfil = $_SESSION['perfil'];

  /*if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso'); window.close();</script>";

  } elseif ($perfil <> '4' && $perfil <>'5'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão'); window.close();</script>";
      
  }*/

  /*window.location=\"index.php\"*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;

  include"../conecta03.php";
  include_once"./func/log3.php";
  header("Content-type: text/html; charset=utf-8");

  $matcond = $_POST['matcond'];

?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

    <title> Portal FFA </title>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css -->    
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css"> -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css">

    <!-- estilo próprio -->
    <link href="../src/css/styles.css" rel="stylesheet" />

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-2 pb-2"> Histórico Condutor </h1>

          <?php
          $sql = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula= '$matcond';";
            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));           
            $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
              $nomecond = utf8_encode($row['nome']);

            print "
            <h5 class='h5'>Nome: $nomecond </h5>
            <h6 class='h6'>Matrícula: $matcond </h6>"; 
          ?>

          <!--div listagem historico-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Placa </th>
                  <th>Status</th>
                  <th>Data de associação </th>
                  <th>Data de dissociação </th>
                </tr>
              </thead>

              <tbody>
                <?php
                  //idtbcondutor, , matricula, ativo, , cartaoassoc, , , statuscond
                  $sql = "SELECT * FROM bdfrota.tbcondutor WHERE matricula='$matcond';";

                  $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                  while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                    $nome = utf8_encode($row['nome']);
                    $ativo = $row['ativo'];
                    $placaassoc = utf8_encode($row['placaassoc']);
                    $dataassoc = $row['dataassoc'];
                    $datadissoc = $row['datadissoc'];

                    if($ativo =='0'){
                      $status = 'INATIVO';
                    }elseif ($ativo =='1') {
                      $status = 'ATIVO';
                    }

                    //dataassoc
                    $dataassoc1 = explode(" ", $dataassoc);
                    $dataassoc2 = explode("-", $dataassoc1[0]);
                    $dataassocf = $dataassoc2[2]."/".$dataassoc2[1]."/".$dataassoc2[0]." ".$dataassoc1[1];

                    //$datadissoc
                    $datadissoc1 = explode(" ", $datadissoc);
                    $datadissoc2 = explode("-", $datadissoc1[0]);
                    $datadissocf = $datadissoc2[2]."/".$datadissoc2[1]."/".$datadissoc2[0]." ".$datadissoc1[1];

                    print "
                    <tr>
                      <td> $placaassoc </td>
                      <td> $status </td>                  
                      <td> $dataassocf </td>
                      <td> $datadissocf </td>
                    </tr>";

                  
                  }

                  mysqli_close($con);
                ?>

              </tbody>   
            </table>
          </div>

        </div>
      </main>

      <!--<footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
          <div class="d-flex align-items-center justify-content-between small">
              <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>-->
              <!--<div>
                  <a href="#">Privacy Policy</a>
                  &middot;
                  <a href="#">Terms &amp; Conditions</a>
              </div>-->
          <!--</div>
        </div>
      </footer>-->
    </div>

    <!-- jquery - js -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>

    <!-- fontawesome - js -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap - 5.1.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- datatables - js  -->
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>

    <!-- scripts próprios -->
    <script src="../src/js/scripts.js"></script>

    <!-- script local -->
    <script>
      //função data table para criar barra de rolagem
      $(document).ready(function () {
        DataTable.datetime('DD/MM/YYYY HH:mm:ss');
      
        new DataTable('#tabela1', {
        //$('#tabela1').DataTable({
            // columns: [
            //   null,
            //   null,
            //   {"type":"date-eu"},
            //   {"type":"date-eu"},
            // ],

            order:[2, "desc"],

            language: {
              lengthMenu: "Mostrar _MENU_ registros por página",
              zeroRecords: "Nenhum registro encontrado - desculpe",
              info: "Mostrando página _PAGE_ de _PAGES_",
              infoEmpty: "Nenhum registro acessível",
              infoFiltered: "(Filtrado de _MAX_ registros totais)",
              Paginate: {
                'First': "Primeiro",
                'Last':    "Último",
                'Next':    "Seguinte",
                'Previous': "Anterior"
              },
            }
          });
      });
    </script>
  </body>
</html>