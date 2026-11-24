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
$tipo= $_SESSION['tipo'];

if($matricula1 == ''){
  $matricula1 = $_GET['mat'];
}

if($perfil == ''){
  $perfil = $_GET['perfil'];
}

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../inde.php\"</script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2'|| $perfil == '3'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}elseif ($matricula1 == '160030'||$matricula1 == '410109'||$matricula1 == '500422'||$matricula1 == '501285'||$matricula1 == '410039'||$matricula1 == '501640'||$matricula1 == '500459' || $matricula1=='411425' || $matricula1 == '003931'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

include"../conecta03.php";
//include"conecta.php";
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

  <title> Cadastro de Modelo de Veículo </title>

  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!--link Material Symbols - simbolos do input-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

  <!-- vendor jquery js -->
  <!--<script type='text/javascript' src='../src/vendor/jquery/jquery-3.2.1.min.js'></script>
  <script type='text/javascript' src='../src/vendor/jquery/jquery.min.js'></script>-->

  <!--data tables - css e js-->
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
  <link href="../src/css/styles.css" rel="stylesheet" />
  <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

  <!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

  <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/plentz/jquery-maskmoney@master/dist/jquery.maskMoney.min.js"></script>
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

  <!--nav-->
  <?php include"menu-teste.php"; ?>

  <div id="layoutSidenav_content">

    <main style="width: 100%;">

      <div class="container-fluid px-4" style="width: 100%;"> 
        <h1 class="h1 pt-2 pb-2 m-auto text-center"> Cadastro Modelo de Veículo </h1>

        <!-- div do formulario -->
        <div>
          <form method="post" action="./control/cadastrarmodeloveic.php" style="width: 80%;" class="m-auto">
            <?php print"<input type='hidden' name='matr_autor' id='matr_autor' value='$matricula1'>"; ?>
            <!-- div cadastro-->
            <!--row-->
            
            <div class="m-auto d-flex col-6 m-auto justify-content-between">
              <label class="form-label mt-2" for="codigo">Modelo:<span style="color: red;">*</span> </label>
              <input class="ms-1 form-control" style="min-width: 100px; width: 80%;" type="text" name="modelo" id="modelo" required>
            </div>

            <!-- div descrição-->
            <div class="m-auto mt-2 d-flex col-6 m-auto justify-content-between border-bottom border-dark pb-4">
              <!--<label class="form-label mt-2" for='descricao'>Marca:<span style="color: red;">*</span> </label>
              <input class="form-control" style="width: 80%;" type="text" name="marca" id="marca" required>-->
              <!--<div class="d-flex flex-column" style="width: 80%;">-->
                <p class="mb-0 col-2"> Marca: <span style="color: red;">*</span> </p>
                <select name="marca" id='marca' class="mb-3 form-select" required>
                  <option value="">TODAS</option>
                  <?php
                    $i = 0;

                    //$sql = "SELECT * FROM bdfrota.tbmarcaveic WHERE idtbmarcaveic<>'16'  ORDER BY marca";
                    $sql="SELECT * FROM bdfrota.tbmarcaveic where idtbmarcaveic not in (16,35,18,34,39,1059,30,32,1057,29,22,27,25,28,20,24,17,33,31) and idtbmarcaveic<>1 AND idtbmarcaveic not between 40 and 1055 AND marca<>'' ORDER BY marca";
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                    
                    while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                      $marca = utf8_encode($row['marca']);
                      $idtbmarcaveic = $row['idtbmarcaveic'];
                      
                      $addmatriz = [$idtbmarcaveic => $i];
                      foreach($addmatriz as $key => $value)
                      {
                        $idselect[$key] = $value;
                      };
                      $i++;
                      
                      print "<option id='$idselect[$idtbmarcaveic]' value='$idtbmarcaveic'> $marca </option>";
                    }
                   ?>
                </select>
              </div>
            </div>

            <!-- div texto campos obrigatório -->
            <div style="color: red;" class="pt-2 col-6 m-auto">
              <p>* Campos obrigatórios.<p>
            </div>

            <!-- div botão -->
            <div class="mt-3 pb-2 d-flex col-sm-12 justify-content-center">
              <div>
                <button class="btn btn-success" type='submit'> Confirmar cadastro </button>
              </div>

              <div class="ms-5">
                <a class="btn btn-danger" href='./cadastroveiculo.php'> Cancelar cadastro </a>
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

  </div><!--div para fechar a div que fica aberta no menu.php-->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>

<script type="text/javascript">
  $("#modelo").on("input", function(){
    $(this).val($(this).val().toUpperCase());
  });

  $("#marca").on("input", function(){
    $(this).val($(this).val().toUpperCase());
  });
  /*onclick="window.close();"*/
</script>

</body>
</html>