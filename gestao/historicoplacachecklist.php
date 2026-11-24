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
  $autoinfra = $_POST['autoinfra'];
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
    
    <link href="../src/css/styles.css" rel="stylesheet" />

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!--data tables - css -->    
    <link href="https://cdn.datatables.net/2.0.4/css/dataTables.dataTables.css" rel="stylesheet" />
    <!--<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>-->

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-2 pb-2"> Histórico de Vistorias </h1>
          <?php
          $sql2="SELECT datainfracao FROM bdfrota.tbmulta WHERE autoinfracao='$autoinfra' ";
          $resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
                
          $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
            $datainfra= $row2['datainfracao'];

            $datainfra1 = explode(" ", $datainfra);
            $horainfra = $datainfra1[1];

            $datainfra2 = explode("-", $datainfra1[0]);
            $datainfraf = $datainfra2[2]."/".$datainfra2[1]."/".$datainfra2[0];

          if($autoinfra == ''){
            print "<h5 class='h5'>Placa: $placa </h5>";
          }else{
            print "<h5 class='h5'>Placa: $placa </h5>
                    <h6 class='h6'>Autoinfração: $autoinfra </h6>
                    <p>Data infração: $datainfraf $horainfra</p>";
          }

          
          
          ?>

          <!--div listagem historico-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Placa </th>
                  <th>Condutor </th>
                  <th>Tipo da vistoria</th>
                  <th>Realizada Por</th>
                  <th>Data de vistoria </th>
                </tr>
              </thead>

              <tbody>
                <?php
                //idtbcondutor, , matricula, ativo, , cartaoassoc, , , statuscond AND (tipo='2' OR tipo='3')
                $sql = "SELECT * FROM bdfrota.tbvistoria WHERE placa='$placa'  ORDER BY datavistoria;";

                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                
                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $nome = $row['nome'];
                  $datavistoria = $row['datavistoria'];
                  $placa = $row['placa'];
                  $tipo = $row['tipo'];               
                  $vistoriador = $row['vistoriador'];

                  $sql1="SELECT tipo FROM bdfrota.tbatipovist WHERE idtbatipovist='$tipo' ";
                  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                  $row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC);
                    $status = $row1['tipo'];

                  $datavistoria1 = explode(" ", $datavistoria);
                  $timevistoria = $datavistoria1[1];
                  $datavistoria2 = explode("-", $datavistoria1[0]);
                  $datavistoria3 = $datavistoria2[2].'/'.$datavistoria2[1].'/'.$datavistoria2[0];

                  print "
                  <tr>
                    <td> $placa </td>
                    <td> $nome </td>
                    <td> $status </td>
                    <td> $vistoriador </td>                  
                    <td>$datavistoria3 $timevistoria</td>                  
                  </tr>";              
                }

                mysqli_close($conexao);
                ?>

              </tbody>   
            </table>
          </div>
          
          <!-- div voltar-->
          <!--<div class="mt-5 text-rigth d-flex justify-content-end">
            <a class="btn btn-secondary text-rigth" href="http://ffasip.ddns.net:4242/multas/testemultas2.php"> Voltar </a>
          </div>-->

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

    <!-- fontawesome - js --> 
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap - js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!--data tables - js --> 
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script>

    <!-- script próprio -->
    <script src="../src/js/scripts.js"></script>

    <script>
      //função data table para criar barra de rolagem
      $(document).ready(function () {
          /*$('#tabela1').DataTable({
            columns: [
              null,
              null,
              null,
              null,           
              {"type":"date-eu"},            
            ],

            
            }
          });*/
          /*$('#tabela1').DataTable({
              //order: [[1, 'asc']],
              //responsive: true,
          });*/
          DataTable.datetime('DD/MM/YYYY HH:mm:ss');
          new DataTable('#tabela1', {
            order: [[4, 'asc']],

            language: {
              lengthMenu: "Mostrar _MENU_ registros por página",
              zeroRecords: "Nenhum registro encontrado - desculpe",
              info: "Mostrando página _PAGE_ de _PAGES_",
              infoEmpty: "Nenhum registro acessível",
              infoFiltered: "(Filtrado de _MAX_ registros totais)",
              search: "Buscar: ",
              Paginate: {
                First: "Primeiro",
                Last:    "Último",
                Next:    "Seguinte",
                Previous: "Anterior"
              },
            }
          });
      });
    </script>

  </body>
</html>