<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
/*$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;*/

include"conecta.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
  <meta name="description" content="FFA" />
  <meta name="author" content="FFA" />
  <link rel="icon" type="image/png" href="src/images/favicon.png"/>

  <title> Listagem CNH - Inicial</title>
  
  <!-- Vendor CSS-->
  <link href="src/vendor/select2/select2.min.css" rel="stylesheet" media="all">
  <link href="src/vendor/datepicker/daterangepicker.css" rel="stylesheet" media="all">

  <!-- Bootstrap core CSS -->
  <link href="src/index_files/bootstrap.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <!--<script src="src/index_files/ie-emulation-modes-warning.js.download"></script>-->
  <script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>

  <!--link Material Symbols - simbolos do input hidden-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
  <!--nav-->
      <?php include"./menu.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;">
      <div class="container-fluid px-4" style="width: 80%;">
        <h1 class="h1 pt-2 pb-2"> Listagem CNH </h1>

        <!--div listagem funcionarios-->
        <div style="width: 100%;" class="m-auto">
          <table id="tabela1" class="table table-striped">
            <thead>
              <th> Matrícula </th>
              <th> Nome </th>
              <th> </th>
              <th> </th>
            </thead>

            <tfoot>
              <th> Matrícula </th>
              <th> Nome </th>
              <th> </th>
              <th> </th>
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

  <script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>

  <script type="text/javascript">
    $(document).ready(function(){
      $('#tabela1').DataTable({
        processing: false,
        ajax: "./control/fetch_records4.php",
        columns: [
          {data: 'idtbfuncionario'},
          {data: 'matricula'},
          {data: 'nome'},
          {data: "idtbfuncionario" , render : function ( data, type, row, meta ) {
            return type === 'display'  ?
              '<form method="post" action="./cadastrocnh.php"><input type="hidden" name="idtbfuncionario" id="nome" value='+data+'><button style="border: none; background-color: transparent;" type="submit" title="Cadastrar CNH">  <span class="material-symbols-outlined"> check_circle </span> </button></form>' : data;          
            }
          },
          {data: "idtbfuncionario" , render : function ( data, type, row, meta ) {
            return type === 'display'  ?
              '<form method="post" action="./editarcnh.php"><input type="hidden" name="idtbfuncionario" id="nome" value='+data+'><button style="border: none; background-color: transparent;" type="submit" title="Editar CNH"> <span class="material-symbols-outlined"> edit_note </span> </button></form>' : data;
           }}
        ],
         
       });
     }
      );

  </script>
  
  <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="src/js/scripts.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="src/js/datatables-simple-demo.js"></script> 
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

</body>
</html>