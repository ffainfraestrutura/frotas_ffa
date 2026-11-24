<?php
date_default_timezone_set('America/Sao_Paulo');

// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
}elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

$id = $_GET['id'];


include"../conecta03.php";
//include"conecta.php";
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
  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

  <title> Apagar Arquivo CNH </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js 
  <script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <style type="text/css">
    @media screen and (min-width: 992px) {
      .row {
        --bs-gutter-x: 0rem;
        flex-wrap: nowrap;
      }
    }
  </style>

</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include "./menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;" class="mb-2">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Apagar Arquivo CNH </h1>

        
        <div class="mt-2 d-flex col-12">  
          

            <!--div embed-->
            <div class="col-5">
            <?php 
              $sql="SELECT doc1, doc2 FROM bdfrota.tbcnh WHERE idtbcnh='$id';";
              $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
              $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
                $doc1 = $row['doc1'];
                $doc2 = $row['doc2'];

              if(!empty($doc2)){
                  $href = $doc2;
              }else{
                if(!empty($doc1)){
                  $href = $doc1;
                }
              }

              if(strpos($href, ".pdf")){
                print"<embed src='.$href' type='application/pdf' width=\"100%\" height='100%' />";
              }else{
                print"<img src='.$href' width=\"100%\" height='100%' />";
              }

            ?>
              
            </div>

            <div class="ms-4 col-4">
              <!-- div do formulario -->
              <form method="post" action="./control/apagararqcnh.php" class="pb-5 mb-5 m-auto" enctype="multipart/form-data">

              <label for='escolha' class="mt-2">Tem certeza que deseja apagar o último arquivo de CNH anexado?</label>
              <div>
                <?php
                  print"<input type='hidden' name='idtbcnh' value='$id'>
                  <input type='hidden' name='matr_autor' value='$matricula1'>
                  ";
                ?>

                <input type="radio" id="sim" name="escolha" value="1" checked />
                <label for="sim">Sim</label>
              </div>

              <div>
                <input type="radio" id="nao" name="escolha" value="0" />
                <label for="nao">Não</label>
              </div>
              <!-- div texto campos obrigatório -->
               <div class="mt-3 border-top border-dark" style="color: red;" class="pt-2">
                * Campos obrigatórios.
              </div>

              <!-- div botão -->
                <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
                  <div>
                    <button class="btn btn-success" type='submit'> Confirmar </button>
                  </div>

                  <div class="ms-5">
                    <button class="btn btn-danger" onclick='fecharTab()'> Cancelar </button>
                  </div>
                </div>
              </form>
              
            
          </div>

        </div>
      </div>

      <div id='popup' class="shadow border rounded p-5 m-6" hidden>
          <div class="d-flex justify-content-end">
            <button onclick="close()" id='close' class="btn btn-danger">&times;</button>
          </div>
        </div>
    </main>

    <footer class="mt-4 py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../src/js/scripts.js"></script>
  <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>-->
  <!--<script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
  <script src="../src/js/datatables-simple-demo.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>   
        
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->

  <script>
    //mascaras de input
    $(document).ready(function(){
      $('#cnh').mask('00000000000', {reverse: true});
      $('#pontos').mask('000', {reverse: true});
    });

    function fecharTab(){
      window.top.close();
    }
  </script>

</body>
</html>