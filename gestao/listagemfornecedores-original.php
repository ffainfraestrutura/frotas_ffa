<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;*/

include"conecta.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="src/images/favicon.png"/>

  <title> Listagem de Fornecedores </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js 
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

  <!--data tables - css e js-->
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-2 pb-2"> Fornecedores Ativos </h1>

        <!-- div cadastrar novo fornecedor-->
        <div class="mt-1 text-rigth d-flex justify-content-end">
          <a class="btn btn-success text-rigth" href="./cadastrofornecedor.php"> Cadastrar Novo Fornecedor </a>
        </div>

        <!--div listagem fornecedores-->
        <div style="width: 100%;" class="mt-4 m-auto">
          <table id="tabela1" class="table table-striped">
            <thead>
              <tr>
                <th>Nome Fantasia: </th>
                <th>Telefone 1: </th>
                <th>Telefone 2: </th>
                <th>Contato: </th>
                <th> </th>
              </tr>
            </thead>

            <tbody>
              <?php
        
              $sql = "SELECT fantasia, telefone, telefone2, contato FROM bdfrota.tbfornecedor WHERE status=1;";

              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
              while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                $nomefantasia = utf8_encode($row['fantasia']);
                $telefone = $row['telefone'];
                $telefone2 = $row['telefone2'];
                $contato = utf8_encode($row['contato']);

                print "<tr><td> $nomefantasia </td>
                           <td> $telefone </td>
                           <td> $telefone2 </td>
                           <td> $contato </td>
                           <td class='d-flex justify-content-between'> 
                            <a href='./editarfornecedor.php' style='text-decoration: none;'> <span class='material-symbols-outlined'> edit_note </span> </a>
                            <a href='./control/cancelarfornecedor.php' style='text-decoration: none;'> <span class='material-symbols-outlined'> delete </span> </a> 
                           </td>
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

  <script>
    //função data table para criar barra de rolagem
    $(document).ready(function () {
        $('#tabela1').DataTable({
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
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="src/js/scripts.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="src/js/datatables-simple-demo.js"></script> 
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</body>
</html>