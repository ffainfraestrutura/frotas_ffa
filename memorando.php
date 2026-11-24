<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$idpedidotec = $_POST['idpedidotec'];
$valdesc = $_POST['valdesc'];
$id = $_POST['id'];


include"conecta.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
  <script type="text/javascript">
              
  $(document).ready(function () {
    setInterval(function(){
      var doc01 = document.getElementById("inputGroupFile04");

      if(doc01.value=="") {
        $("#inputGroupFileAddon04").addClass('d-none');
      }else{
        $("#inputGroupFileAddon04").removeClass('d-none');
      }
    });
  });



        </script>     
    </head>

    <body class="sb-nav-fixed vsc-initialized">
        <!--nav-->
        <?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<div class='container-fluid px-4'><div class='card mb-4 mt-4 shadow-lg border-0 rounded-lg'><div class='card-body'>
<h3>COMUNICADO</h3>
<p>Srs Colaboradores condutores de veículos fornecidos pela FFA,</p>
<p>Conforme política de utilização de veículo assinado por Vossa Senhoria, na qualidade de condutor de um dos veículos listados na frota da FFA, vimos por meio deste reafirmar o documento anteriormente assinado, que informa o seguinte:</p>
<p>Os veículos da Frota da FFA devem ser utilizados EXCLUSIVAMENTE para fins laborativos, sendo PROIBIDO seu uso para fins pessoais e/ou fora dos horários e dias de trabalho. </p>
<p>Em hipótese alguma, o veículo poderá ser utilizado para fins particulares, ou de qualquer outra natureza que não atendam aos interesses da FFA Infraestrutura e Serviços. O uso do veículo em assuntos não relacionados a empresa, remete o infrator a FALTA GRAVE, sujeito a medidas administrativas disciplinares, bem como como descontos relativos ao uso indevido de combustíveis, desgaste de peças entre outros.</p>
<p>Informamos que a partir de 10/10/2023, a FFA implantará um monitoramento em TEMPO REAL de todos os veículos fornecidos pela empresa com intuito de manter a segurança, conscientizar os empregados quanto à responsabilidade de uso dos veículos e também buscando diminuir os possíveis descontos para o colaborador quanto a manutenção, combustível indevido e outros eventos fortuitos. </p>
<p>Desta forma, os veículos identificados em uso FORA DOS HORÁRIOS E DIAS DE TRABALHO estarão passiveis de bloqueio através da Central de Monitoramento, onde todos os custos de retorno do veículo serão descontados do condutor.</p>
<h6>Você deve tirar foto e depois apertar o botão confirmar para o aceite</h6>
<form method='post' id='deacordo' action='./control/memorando.php' enctype='multipart/form-data'>
<div class='input-group'>
    <label for="inputGroupFile04" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
  <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
  <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
</svg>  Assinar digitalmente
</label>
  <input type='file' class='form-control' id='inputGroupFile04' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='arquivo' style='display: none;'required>
  <?php

    print"<input type='hidden' name='idpedidotec' value='$idpedidotec'>
                            <input type='hidden' name='valdesc' value='$valdesc'>
                            <input type='hidden' name='id' value='$id'>";


  ?>
  <button class='btn btn-success' type='submit' id='inputGroupFileAddon04' style="margin-left: 25px;">Confirmar</button>

</form>

</div></div></div>
<div class='container-fluid px-0'><div class='card mb-4 mt-4 shadow-lg border-0 rounded-lg'><div class='card-body m-auto'>
<form method="post" action='./control/semacordo.php' style="text-align: center;">
    <button type="submit" class="btn btn-link d-flex align-items-center justify-content-center">
        <h4 class="text-black m-0 text-center">Não aceitar e voltar</h4>
    </button>
    <?php

    print"<input type='hidden' name='idpedidotec' value='$idpedidotec'>
                            <input type='hidden' name='valdesc' value='$valdesc'>
                            <input type='hidden' name='id' value='$id'>";


  ?>
</form>
</div></div></div>
</main>
                <footer class="py-4 bg-light mt-auto">
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
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    </body>
</html>
