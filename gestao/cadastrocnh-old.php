<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
} /*elseif ($perfil <> '5'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

$matriculafunc = $_POST['matricula'];

include"../conecta2.php";
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

  <title> Cadastro de CNH </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

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
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Cadastro de CNH </h1>

        <!-- div do formulario -->
        
          <form method="post" action="./control/cadastrarcnh.php" style="width: 70%;" class="pb-5 mb-5 m-auto">

            <!-- div identificação -->
            <!--mt-2 col-sm-12 d-flex justify-content-start-->
            <div class="row">

              <!-- div nome -->
              <?php 
              $sql = "SELECT nome, matricula FROM bdaniel.tbfuncionario WHERE matricula = '$matriculafunc';";

              $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                  $nomefunc = utf8_encode($row['nome']);
                  $matriculafunc = $row['matricula'];
                }

                print "<input type='hidden' name='matr_autor' value='$matricula1'>
                <div class='d-flex flex-column col-sm-7'>
                  <label class='form-label mt-2' for='nome'>Nome: <span style='color: red;'>*</span></label>
                  <input class='form-control' style='width: 100%;' type='text' name='nome' id='nome' placeholder='Nome' value='$nomefunc' required>
                </div>

                <!-- div matricula -->
                <div class='ms-3 d-flex flex-column col-sm-3'>
                  <label class='form-label mt-2' for='matricula'>Matricula: <span style='color: red;'>*</span> </label>
                  <input class='form-control' style='width: 100%;' type='text' name='matricula' id='matricula'  value='$matriculafunc'  placeholder='Matrícula' maxlength='6'  required>
                </div>";
              ?>            
            </div>

            <!-- div infos cnh -->
            <!--mt-2 col-sm-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div cnh-->
              <div class="col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="cnh">Número da CNH: <span style="color: red;">*</span> </label>
                <input class="form-control" type="text" name="cnh" id='cnh' required placeholder="000000000000" onKeyPress="if(this.value.length==12) return false;" required>
              </div>

              <!-- div validade-->
              <div class="ms-3 col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="validade">Validade: <span style="color: red;">*</span> </label>
                <input class="form-control" type="date" name="validade" id="validade" required>
              </div>

              <!-- div UF-->
              <div class="ms-3 col-sm-1 d-flex flex-column">
               
                <p>U.F.: <span style="color: red;">*</span></p>
                <select class="form-select" name="uf" required>
                  <option value="AC">AC</option> 
                  <option value="AL">AL</option>
                  <option value="AP">AP</option> 
                  <option value="AM">AM</option>  
                  <option value="BA">BA</option> 
                  <option value="CE">CE</option> 
                  <option value="DF">DF</option> 
                  <option value="ES">ES</option> 
                  <option value="GO">GO</option> 
                  <option value="MA">MA</option> 
                  <option value="MT">MT</option> 
                  <option value="MS">MS</option> 
                  <option value="MG">MG</option> 
                  <option value="PA">PA</option> 
                  <option value="PB">PB</option> 
                  <option value="PR">PR</option> 
                  <option value="PE">PE</option> 
                  <option value="PI">PI</option> 
                  <option value="RJ">RJ</option> 
                  <option value="RN">RN</option>
                  <option value="RS">RS</option> 
                  <option value="RO">RO</option> 
                  <option value="RR">RR</option> 
                  <option value="SC">SC</option> 
                  <option value="SP">SP</option>
                  <option value="SE">SE</option>  
                  <option value="TO">TO</option> 
                </select>
              </div>

              <!-- div categoria-->
              <div class="ms-3 col-sm-2 d-flex flex-column">
                <label class="form-label mt-2" for="categoria">Categoria: <span style="color: red;">*</span> </label>
                <input class="form-control" type="text"  name="categoria" id="categoria" required>
              </div>

            </div>

            <!-- div infos cnh -->
            <!--pb-2 mt-2 col-sm-12 d-flex justify-content-start-->
            <div class="row">
              <!-- div pontos-->
              <div class="col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="pontos">Pontos:</label>
                <input class="form-control" type="text" onKeyPress="if(this.value.length==3)"  name="pontos" id="pontos">
              </div>

              <!-- div consulta-->
              <div class="ms-3 col-sm-3 d-flex flex-column">
                <label class="form-label mt-2" for="consulta">Consulta ao DETRAN: <span style="color: red;">*</span> </label>
                <input class="form-control" type="date"  name="consulta" id="consulta" required>
              </div>

              <!-- div suspensa -->
              <div class="ms-3 mt-2 col-sm-3 d-flex">
                <!-- div select suspensa -->
                <div class="altcli pe-0 col-sm-5 mt-2">
                  Suspensa: <span style="color: red;">*</span>
                  <select name="suspensa" class="mb-3 form-select" aria-label="Default select example" required>
                    <option value='0'> Não </option>
                    <option value='1'> Sim </option>
                  </select>  
                </div>
              </div>            
          </div>           

          <!-- div texto campos obrigatório -->
          <div class="border-top border-dark" style="color: red;" class="pt-2">
            * Campos obrigatórios.
          </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar cadastro </button>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./listagemcnh2.php'> Cancelar cadastro </a>
              </div>
            </div>
          </form>
        
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
  </div><!--div para fechar a div que fica aberta no menu.php-->



<script>
    //mascaras de input
    $(document).ready(function(){
      $('#cnh').mask('000000000000', {reverse: true});
      $('#pontos').mask('000', {reverse: true});

      //formatação do texto no input
      $("#nome").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });

      $("#categoria").on("input", function(){
        $(this).val($(this).val().toUpperCase());
      });
    });
  </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

</body>
</html>