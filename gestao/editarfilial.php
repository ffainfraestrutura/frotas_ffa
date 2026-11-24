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

  if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  } elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  } elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  }

  $_SESSION['perfil'] = $perfil;
  $_SESSION['matricula'] = $matricula1;
  $_SESSION['nome'] = $nome1;
  $_SESSION['usuario'] = $usuariof;
  $_SESSION['tipo']= $tipo;

  $idtbfilial = $_POST['idtbfilial'];

  //include"../conecta.php";
  include"../conecta2.php";
  //include_once"./func/log.php";
  header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

    <title> Editar Filial </title>
    
    <link href="../src/css/styles.css" rel="stylesheet" />

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    
  </head>
  <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    <!--nav-->
    <?php include"menu-teste.php"; ?>

    <div id="layoutSidenav_content">

      <main style="width: 100%;">

        <div class="container-fluid px-4" style="width: 100%;"> 
          <h1 class="h1 pt-2 pb-2 m-auto text-center"> Editar Filial </h1>

          <!-- div do formulario -->
          <div>
            <form method="post" action="./control/editarfilial.php" style="width: 80%;" class="m-auto">
              <?php 
              $sql = "SELECT * FROM bdfrota.tbfilial WHERE idtbfilial= '$idtbfilial';";
                
              //$sql = "SELECT * FROM bdfrota.tbfilial WHERE idtbfilial= 1;";

              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
            
              while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                $idtbfilial = $row['idtbfilial'];
                $codigo = $row['codigo'];
                $descricao = $row['descricao'];
                $ativo = $row['ativo'];                
              }

              print"

                <!-- div cadastro-->
                <div class='d-flex input-group-sm col-sm-8 m-auto justify-content-between'>

                  <input type='hidden' name='idtbfilial' id='nome' value='$idtbfilial'>
                  <input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>

                  <label class='form-label mt-2' for='codigo'>Código:<span style='color: red;'>*</span> </label>
                  <input class='ms-1 form-control' style='min-width: 100px; width: 80%;' type='text' name='codigo' id='codigo' onkeypress='return event.charCode >= 48 && event.charCode <= 57' maxlength='3' value='$codigo' required>
                </div>

                <!-- div descrição-->
                <div class='mt-2 d-flex input-group-sm col-sm-8 m-auto justify-content-between border-bottom border-dark pb-4'>
                  <label class='form-label mt-2' for='descricao'>Descrição:<span style='color: red;'>*</span> </label>
                  <input class='form-control' style='width: 80%;' type='text' name='descricao' id='descricao' value='$descricao' required>
                </div>";
              ?>

              <!-- div texto campos obrigatório -->
              <div style="color: red;" class="pt-2 col-sm-8 m-auto">
                <p>* Campos obrigatórios.<p>
              </div>

              <!-- div botão -->
              <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
                <div>
                  <button class="btn btn-success" type='submit'> Confirmar edição </button>
                </div>

                <div class="ms-5">
                  <a class="btn btn-danger" href='./listagemfiliais.php'> Descartar edição </a>
                </div>
              </div>
            </form>
          </div>
        </div>
      </main>

      <footer class="py-4 bg-light mt-auto" style="position: fixed; bottom: 0; start: 0; width: 100%;">
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

    <!-- jquery -->
    <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

    <!-- masks -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    <!-- fontawesome -->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- bootstrap 5.1.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    
    <!-- script local -->
    <script src="../src/js/scripts.js"></script>    
    
    <!-- script local -->
    <script type="text/javascript">
      $(document).ready(function(){
        //formatação do texto no input
        /*$("#descricao").on("input", function(){
          $(this).val($(this).val().toUpperCase());
        });*/
      });

    </script>

  </body>
</html>