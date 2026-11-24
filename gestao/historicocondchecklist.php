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

$matcond=$_POST['matcond'];
//$placa = 'RMY4B71';

$sql="SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$matcond' ";
$resultado =  mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
  $nomecond=$row['nome'];
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
  
  <!-- estilo próprio -->
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
        <h1 class="h1 pt-2 pb-2"> Histórico de Vistorias </h1>
        <?php 
        print "<h5 class='h5'>Condutor: $nomecond </h5>";
        ?>

        <!--div listagem historico-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id="tabela1" class="table table-striped">
            <thead>
              <tr>                
                <th>Condutor </th>
                <th>Placa </th>
                <th>Tipo da vistoria</th>
                <th>Realizada Por</th>                
                <th>Data de vistoria </th>
              </tr>
            </thead>

            <tbody>
              <?php
              //idtbcondutor, , matricula, ativo, , cartaoassoc, , , statuscond AND (tipo='2' OR tipo='3') ORDER BY datavistoria;
              $sql = "SELECT * FROM bdfrota.tbvistoria WHERE matricula='$matcond' ORDER BY datavistoria DESC";

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

                $datavistoria1 = new DateTime($datavistoria);
                $datavistoriaf= $datavistoria1-> format('d/m/Y H:i:s');
                
                print "
                <tr>                  
                  <td> $nome </td>
                  <td> $placa </td>
                  <td> $status </td>
                  <td> $vistoriador </td>
                  <td>$datavistoriaf</td>                  
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

  <!-- bootstrap 5.1.3 - js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <!-- script próprio -->
  <script src="../src/js/scripts.js"></script>

  <!-- jquery - js -->
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

  <!-- datatables - js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/2.0.4/js/dataTables.js"></script>

  <script>
    //função data table para criar barra de rolagem
    $(document).ready(function () {
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