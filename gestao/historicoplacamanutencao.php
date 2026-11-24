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
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";

  } elseif ($perfil <> '4' && $perfil <>'5'){
    
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
    
  }*/

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula;
  $_SESSION['nome'] = $nome;
  $_SESSION['usuario'] = $usuariof;

  include"../conecta03.php";
  include_once"./func/log3.php";
  header("Content-type: text/html; charset=utf-8");

  $placa = $_POST['placa'];

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

    <!-- estilo próprio -->
    <link href="../src/css/styles.css" rel="stylesheet" />
    
    <!--data tables - css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css">
    

  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include "./menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;" class="mb-2">

        <div class="container-fluid px-4" style="width: 80%;">
          <h1 class="h1 pt-2 pb-2"> Histórico de Manutenções </h1>
          <?php 
          print "<h5 class='h5'>Placa: $placa </h5>";
          ?>

          <!--div listagem historico-->
          <div style="width: 100%;" class="mt-4 m-auto">
            <table id="tabela1" class="table table-striped">
              <thead>
                <tr>
                  <th>Placa </th>
                  <th>Data de Cadastro </th>
                  <th>Atualizado em </th>
                  <th>Data de Agendamento </th>
                  <th>Data de Conclusão </th>
                  <th>Tipo </th>
                  <th>Status </th>
                  <th>Ordem de Serviço</th>
                </tr>
              </thead>

              <tbody>
                <?php
                  //idtbcondutor, , matricula, ativo, , cartaoassoc, , , statuscond AND (tipo='2' OR tipo='3')
                $sql = "SELECT * FROM bdfrota.tbmanprev WHERE placa='$placa'  ORDER BY data;";

                $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                
                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $idtbmanprev=$row['idtbmanprev'];
                  $placa = $row['placa'];
                  $atualizadoem = $row['atualizadoem'];                
                  $data = $row['data'];
                  $dataagendamento = $row['dataagendamento'];
                  $dataretirada = $row['dataretirada'];
                  $tipom = $row['tipo'];
                  $status = $row['status'];               

                  $data1 = explode(" ", $data);
                  $time = $data1[1];
                  $data2 = explode("-", $data1[0]);
                  $data3 = $data2[2].'/'.$data2[1].'/'.$data2[0];

                  //$data3 = DateTime::createFromFormat('Y-m-d H:i:s', $data)->format('d/m/Y H:i:s');

                  $atualizadoem1 = explode(" ", $atualizadoem);
                  $timeatualizadoem = $atualizadoem1[1];
                  $atualizadoem2 = explode("-", $atualizadoem1[0]);
                  $atualizadoem3 = $atualizadoem2[2].'/'.$atualizadoem2[1].'/'.$atualizadoem2[0];

                  $dataagendamento1 = explode(" ", $dataagendamento);
                  $timedataagendamento = $dataagendamento1[1];
                  $dataagendamento2 = explode("-", $dataagendamento1[0]);
                  $dataagendamento3 = $dataagendamento2[2].'/'.$dataagendamento2[1].'/'.$dataagendamento2[0];

                  $dataretirada1 = explode(" ", $dataretirada);
                  $timedataretirada = $dataagendamento1[1];
                  $dataretirada2 = explode("-", $dataretirada1[0]);
                  $dataretirada3 = $dataretirada2[2].'/'.$dataretirada2[1].'/'.$dataretirada2[0];

                  //tratando tipo
                  if($tipom=='MP'){
                    $tipom='Manutenção Preventiva';
                  } elseif($tipom=='OS'){
                    $tipom='Ordem de Serviço';
                  } elseif($tipom=='MC'){
                    $tipom='Manutenção Corretiva';
                  } elseif($tipom=='SS'){
                    $tipom='Sinistro';
                  }

                  $os="
                    <form method='post' action='https://ffasip.ddns.net:4242/frotas/pdf/fpdf/ordemdeservico.php' target='_blank'>
                      <input type='hidden' name='idtbmanprev' id='nome' value='$idtbmanprev'>
                      <button style='border: none; background-color: transparent;' type='submit' title='Editar Manutenção'> 
                        <span class='material-symbols-outlined'>visibility</span> 
                      </button>
                    </form>";

                  print "
                  <tr>
                    <td>$placa </td>
                    <td>$data3 $time </td>
                    <td>$atualizadoem3 $timeatualizadoem</td>
                    <td>$dataagendamento3 </td>
                    <td>$dataretirada3 </td>
                    <td>$tipom </td>
                    <td>$status </td>
                    <td>$os</td>                  
                  </tr>";             
                }

                mysqli_close($con);
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

    <!-- jquery -  js -->
    <script type="text/javascript" src='https://code.jquery.com/jquery-3.7.1.js'></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap - 5.1.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- datatables -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>

    <!-- script próprio -->
    <script src="../src/js/scripts.js"></script>

    <!-- script local -->
    <script>
      //função data table para criar barra de rolagem
      $(document).ready(function () {
        DataTable.datetime('DD/MM/YYYY HH:mm:ss');
        DataTable.datetime('DD/MM/YYYY');
 
        new DataTable('#tabela1', {  
          order:[1, "desc"],

          language: {
            decimal:        "",
            emptyTable:     "Nada para exibir",
            info:           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            infoEmpty:      "Exibindo página 0 de 0 de 0 registros",
            infoFiltered:   "(filtrado do total de _MAX_ registros)",
            infoPostFix:    "",
            thousands:      ".",
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