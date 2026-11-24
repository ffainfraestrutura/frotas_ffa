<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

$matriculaf = $_POST['matriculatec'];
$id = $_POST['id'];
$idtbsaldo = $_POST['idtbsaldo'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor_ant = $_POST["valor_ant"];
$valor_ants = str_replace(",", ".", $valor_ant);
if ($valor_ants >= 40) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"./aprovacaocota.php\"</script>";

            }
$orcpedido = $_POST["orcpedido"];
$tipocota = $_POST["tipocota"];
$valor_desc = $_POST["valor_desc"];
$orcsem = $_POST["orcsem"];
$kmproj = $_POST["kmproj"];
$kmos = $_POST["kmos"];
$kmsem = $_POST["kmsem"];
$totextra = $_POST["totextra"];
$foraescala = $_POST["foraescala"];
$forahorario = $_POST["forahorario"];
$kmprojini = $_POST["kmprojini"];
$orcsematu = $_POST["orcsematu"];



                            
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
        <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
<!-- jquery e masks -->
  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">
    <main>

        <div class="container-fluid px-4">

    	
    		<h1 class="h1 pt-1"> Cota Indevida </h1>

    		<p class="fs-5 fw-bold">KM Projetado (<?php print"$kmproj"; ?>) é maior que KM OS (<?php print"$kmos"; ?>)</p>

    		<form action="./control/aprovacaocotaindevida.php" method="post" enctype='multipart/form-data'>
  <!--div justificativa-->
    			<div class="mb-3">


<div class='mt-3 col-sm-12 d-flex m-auto justify-content-start align-items-start'>
              
              <div class='col-4 mt-2 align-items-center'>
                <label class='col-form-label' for='descontocombustivel'> Deve descontar o valor do colaborar? <span style='color: red;'>*</span> </label>
                <div class='ms-2 d-flex' style= 'width: 40%; flex-wrap: wrap;'>
                  <div class='ms-1'>
                    <input type='radio' id='sim' name='descontocombustivel' value='sim' required>
                    <label for='sim'>Sim</label>
                  </div>
                  <div class='ms-3'>
                    <input class='ms-1' type='radio' id='nao' name='descontocombustivel' value='nao'>
                    <label for='nao'>Não</label>
                  </div>
                </div>
              </div>

              
              <div class='col-3 mt-3 align-items-center' id='valdescontocombustivel' hidden>
                <label class='form-label' for='valdescontocombustivel'> Quanto descontar? <span style='color: red;'>*</span> </label>
                <input type='text' class='form-control' name='valdescontocombustivel' id='valdescontocombustivel2' onkeyup='formatarMoeda(this)'>
              </div>
            </div>";


    				<label class="form-label mb-0 fs-5" for="ijust">Justificativa:</label>
    				<textarea step="0.01" rows="10" class="form-control" name="justificativa" placeholder="Escreva a justificativa" required="true"></textarea>
    			</div>
<?php
print"                      <input type='hidden' name='valor' value ='$valor'> 
                            <input type='hidden' name='orcpedido' value ='$orcpedido'>  
                            <input type='hidden' name='matriculatec' value='$matriculaf'>
                            <input type='hidden' name='decisao' value='2'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input type='hidden' name='valor_ant' value='$valor_ant'>
                            <input type='hidden' name='valor_desc' value='$valor_desc'>
                            <input type='hidden' name='orcsem' value='$orcsem'>
                            <input type='hidden' name='kmproj' value='$kmproj'>
                            <input type='hidden' name='kmos' value='$kmos'>
                            <input type='hidden' name='kmsem' value='$kmsem'>
                            <input type='hidden' name='totextra' value='$totextra'>
                            <input type='hidden' name='foraescala' value='$foraescala'>
                            <input type='hidden' name='forahorario' value='$forahorario'>
                            <input type='hidden' name='kmprojini' value='$kmprojini'>
                            <input type='hidden' name='orcsematu' value='$orcsematu'>

                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>";
?>
 
                <!--div botão confirmar-->
    			<div class="mt-4" id='inputGroupFileAddon04'>
    				  <button class='btn btn-success' type='submit'>Confirmar</button>
    			</div>
    			
    		</form>
            <?php
$useg = date('Y-m-d', strtotime('last monday'));
$hoje = date('Y-m-d');
$sqlind = "SELECT * FROM `bdfrota-teste`.tbrelatoriogpsfalta where matricula = '$matriculaf' and data >= '$useg' and data <= '$hoje'";
$resultadoind = mysql_query($sqlind) or die (mysql_error());
if (mysql_num_rows($resultadoind)>=1){

print"
<div class='mt-4'>
<form action='./gpsxfolga-sup.php' method='post' target = '_blank'>
                                        
                            <input type='hidden' name='matriculatec' value='$matriculaf'>
                            <input type='hidden' name='diaf' value='$hoje'>
                            <input type='hidden' name='diai' value='$useg'>
                            <input type='hidden' name='detcota' value='sim'>
                            
                            
Existe deslocamento fora da escala para esse funcionário <input type='submit' class='btn btn-sm btn-danger' value='DETALHES'> </form></div>
";

}

$sqlind = "SELECT * FROM `bdfrota-teste`.tbrelatoriogpsmovind where matricula = '$matriculaf' and data >= '$useg' and data <= '$hoje'";
$resultadoind = mysql_query($sqlind) or die (mysql_error());

if (mysql_num_rows($resultadoind)>=1){

print"
<div class='mt-4'>
<form action='./gpsxhora-sup.php' method='post' target = '_blank'>
                                        
                            <input type='hidden' name='matriculatec' value='$matriculaf'>
                            <input type='hidden' name='diaf' value='$hoje'>
                            <input type='hidden' name='diai' value='$useg'>
                            <input type='hidden' name='detcota' value='sim'>
                            
                            
Existe deslocamento fora do horário para esse funcionário <input type='submit' class='btn btn-sm btn-danger' value='DETALHES'> </form></div>
";


}


            ?>
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
                </div>!-->
            </div>
        </div>
    </footer>
</div>
            

    	<script>
            //função para formatar valor do input - moeda
            function formatarMoeda(i) {
                var v = i.value.replace(/\D/g,'');
                v = (v/100).toFixed(2) + '';
                v = v.replace(".", ",");
                v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                i.value = v;
            }
    	</script>

    	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
<script>window.jQuery || document.write('<script src="src/vendor/jquery/jquery.min.js"><\/script>')</script>
          <script type="text/javascript">
    $(document).ready(function () {     

      //descontocombustivel
      $('input:radio[name="descontocombustivel"]').change(function() {
        if ($(this).val() == 'sim') {
          $("#valdescontocombustivel").attr("hidden", false);
          $("#valdescontocombustivel2").attr("required", true);
          $(this).attr("checked", true);
          
          $("#m2").attr("hidden", true); $(this).attr("checked", true);
          
        } else if ($(this).val() == 'nao') {
          $("#valdescontocombustivel").attr("hidden", true);
          $("#valdescontocombustivel2").attr("required", false);
          $(this).attr("checked", true);
          
          $("#m1").attr("hidden", true);$(this).attr("checked", true);
        }       
      });
  });

    
</script>
    </body>

</html>