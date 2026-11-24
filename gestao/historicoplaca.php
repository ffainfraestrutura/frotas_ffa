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
  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
  } elseif ($perfil <> '4'){
    if($perfil <>'5'){
      echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
    }  
  }
  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;

  include"../conecta2.php";
  include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");

  $placa=$_POST['placa'];
  //$placa = 'RMY4B71';
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

    <!-- estilos próprios -->
    <link href="../src/css/styles.css" rel="stylesheet" />    

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css e js-->    
    <link href="https://cdn.datatables.net/2.0.4/css/dataTables.dataTables.css" rel="stylesheet" />

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-2 pb-2"> Histórico Placa </h1>
          <?php 
          print "<h2 class='h2'> $placa </h2>";
          ?>

          <!--div listagem historico-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Placa </th>
                  <th>Condutor </th>
                  <th>Status</th>
                  <th>Data de associação </th>
                  <th>Data de dissociação </th>
                </tr>
              </thead>

              <tbody>
                <?php
                //idtbcondutor, , matricula, ativo, , cartaoassoc, , , statuscond
                $sql = "SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa';";

                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $nome = $row['nome'];
                  $ativo = $row['ativo'];
                  $placaassoc = $row['placaassoc'];
                  $dataassoc = $row['dataassoc'];
                  $datadissoc = $row['datadissoc'];

                  if($ativo =='0'){
                    $status = 'INATIVO';
                  }elseif ($ativo =='1') {
                    $status = 'ATIVO';
                  }

                  $dataassoc1 = new DateTime($dataassoc);
                  $dataassocf = $dataassoc1-> format( 'd/m/Y H:i:s' );

                  if($datadissoc != '0000-00-00 00:00:00'){
                    $datadissoc1 = new DateTime($datadissoc);
                    $datadissocf = $datadissoc1-> format('d/m/Y H:i:s');
                  }else{                  
                    $datadissocf = "00/00/0000 00:00:00";
                  }
                  

                  print "
                  <tr>
                    <td> $placaassoc </td>
                    <td> $nome </td>
                    <td> $status </td>                  
                    <td> $dataassocf </td>
                    <td> $datadissocf </td>
                  </tr>";

                mysqli_close($conexao);
                }
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
              </div>-->
          </div>
        </div>
      </footer>
    </div>

    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- /bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- moment/datatable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- scripts próprios -->
    <script src="src/js/scripts.js"></script>

    <!-- script local -->
    <script>
      //função data table para criar barra de rolagem
      $(document).ready(function () {
          DataTable.datetime('DD/MM/YYYY HH:mm:ss');
          new DataTable('#tabela1', {
            order: [[3, 'asc']],

            language: {
            decimal:        "",
            emptyTable:     "Nada para exibir",
            info:           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            infoEmpty:      "Exibindo página 0 de 0 de 0 registros",
            infoFiltered:   "(filtrado do total de _MAX_ registros)",
            infoPostFix:    "",
            thousands:      ",",
            lengthMenu:     "Exibir _MENU_ registros",
            loadingRecords: "Carregando...",
            processing:     "Processando...",
            search:         "Buscar:",
            zeroRecords:    "Nenhum resultado encontrado",
            paginate: {
                first:      "Primeira",
                last:       "Última",
                next:       "Próxima",
                previous:   "Anterior"
            }
          }
          });
      });
    </script>

  </body>
</html>