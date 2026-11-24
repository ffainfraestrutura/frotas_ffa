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
    <main>

        <div class="container-fluid px-4">

    	
    		<h1 class="h1 pt-1"> Solicitação de combustível </h1>

    		<p class="fs-5 fw-bold">Técnico: <?php echo"$nome"; ?></p>

    		<form action="./control-tecnico.php" method="post" enctype='multipart/form-data'>
                <!--div solicitação-->
    			<div>
    				<label class="form-label mb-0 fs-5" for="ivalor">Solicitação:</label>
    				<div class="input-group mb-3">
    					<span class="input-group-text">R$: </span>
    					<input class="form-control" type="text" name="valor" id="ivalor" required="true" placeholder="Informe o valor" onkeyup="formatarMoeda()">
    				</div>
    			</div>





                <!--div justificativa-->
    			<div class="mb-3">
    				<label class="form-label mb-0 fs-5" for="ijust">Justificativa:</label>
    				<textarea step="0.01" rows="10" class="form-control" name="justificativa" placeholder="Escreva a justificativa" required="true"></textarea>
    			</div>
<?php
print"<input type='hidden' name='matricula' value='$matricula'>";
?>
    <label for="inputGroupFile04" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;" viewBox="0 0 16 16">
  <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
  <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
</svg>  Foto Hodômetro
</label>
  <input type='file' class='form-control' id='inputGroupFile04' aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='arquivo' style='display: none;'required>

                <!--div botão confirmar-->
    			<div class="mt-4" id='inputGroupFileAddon04'>
    				  <button class='btn btn-success' type='submit'>Confirmar</button>
    			</div>
    			
    		</form>
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
            //função para formatar
    		function formatarMoeda() {
        	var elemento = document.getElementById('ivalor');
       		var valor = elemento.value;
        

        	valor = valor + '';
        	valor = parseInt(valor.replace(/[\D]+/g, ''));
        	valor = valor + '';
        	valor = valor.replace(/([0-9]{2})$/g, ",$1");

        	if (valor.length > 6) {
            	valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
        	}

        	elemento.value = valor;
        	if(valor == 'NaN') elemento.value = '';
        
    		}
    	</script>

    	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="src/js/datatables-simple-demo.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    </body>

</html>