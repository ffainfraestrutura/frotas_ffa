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
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

include"conecta.php";
//include_once"./func/log.php";
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

  <!-- Vendor CSS-->
  <link href="src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

  <!-- Bootstrap core CSS -->
  <link href="src/index_files/bootstrap.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <!--<script src="src/index_files/ie-emulation-modes-warning.js.download"></script>-->
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!-- vendor jquery js -->
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

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
                <th>ID: </th>
                <th>Nome Fantasia: </th>
                <th>Telefone 1: </th>
                <th>Telefone 2: </th>
                <th>Contato: </th>
                <th> </th>
              </tr>
            </thead>

            <tfoot>
              <tr>
                <th>ID: </th>
                <th>Nome Fantasia: </th>
                <th>Telefone 1: </th>
                <th>Telefone 2: </th>
                <th>Contato: </th>
                <th> </th>
              </tr>
            </tfoot>  
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

  <script type='text/javascript' src='/var/www/html/presenca/src/vendor/bootstrap/js/bootstrap.min.js'></script>

  <script>window.jQuery || document.write('<script src="src/vendor/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function(){
      var tabela = $('#tabela1').DataTable({
         "processing": false,
         "ajax": "control/fetch_records.php",
         "columns": [
         {data: 'idtbfornecedor'},
         {data: 'fantasia'},
         {data: 'telefone'},
         {data: 'telefone2'},
         {data: 'contato'},
         {data: "idtbfornecedor" , render : function ( data, type, row, meta ) {
           return type === 'display'  ?
            '<form method="post" action="./control/editarfornecedor.php"><input type="hidden" name="idtbocorrencia" id="nome" value='+data+'><button class="btntransf btn--radius-2 btn--green" type="submit">Alterar</button>' :
           data;
         }}
         ],
         //"order": [[ 2, "asc" ]], "lengthMenu": [[50, 100, 200, -1], [50, 100, 200, "Todos"]
       });
     }
      );
      /*$(document).ready(
        function(){
          setInterval(
            function(){
              $('#example').DataTable().ajax.reload();
            },1000);
        }
        );*/
  </script>

</body>
</html>