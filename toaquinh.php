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




$latitude = isset($_GET["latitude"]) ? $_GET["latitude"] : "N/A";
$longitude = isset($_GET["longitude"]) ? $_GET["longitude"] : "N/A";


include"conecta.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");



$sql = "SELECT ccusto FROM bdaniel.tbfuncionario where matricula = '$matricula' and ccusto in ('CLARO - REDE EXTERNA','OPERAÇÃO - CLARO - MAN','OPERAÇÃO -CLARO -OSP GDE OBRAS','OPERAÇÃO -CLARO -OSP PEQ OBRAS','TIM RJ - IMPLANTAÇÃO','TIM RJ - REDE EXTERNA','TIM SP - IMPLANTAÇÃO','TIM SP - REDE EXTERNA','TIM SP IMPLANTAÇÃO', 'TIM SP- IMPLANTAÇAO')";
$resultado = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($resultado)>=1){

    $ccusto = 'sim';

}
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
       
    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">
    <main>

        <div class="container-fluid px-4">

    	
    		<h1 class="h1 pt-1"> Tô aqui! </h1>

    		<p class="fs-5 fw-bold">Técnico: <?php
             print"$nome"; 

            ?>  
            
             </p>

    		<form action="./control/toaqui.php" method="post" enctype='multipart/form-data'>
                <!--div solicitação-->
    			<div>
    <label class="form-label mb-0 fs-5" for="motivo">Motivo:</label>
    <div class="input-group mb-3">
        <span class="input-group-text">Motivo</span>
        <select class="form-select" name="motivo" id="motivo" required>
            <option value="">Selecione o motivo</option>
            <option value="base">Base</option>
            <option value="ponto de encontro">Ponto de Encontro</option>
            <option value="matriz">Matriz</option>
            <?php 
            if ($ccusto == 'sim') {
                echo "<option value='atividade'>Atividade</option>";
            }           
            ?>
            <!-- Adicione mais opções conforme necessário -->
        </select>
    </div>
</div>

<input type="hidden" value="<?php echo "$latitude";?>" name="latitude" />
<input type="hidden"value="<?php echo "$longitude";?>" name="longitude"  />

                <!--div justificativa-->
    			<div class="mb-3">
    				<label class="form-label mb-0 fs-5" for="ijust">Observação:</label>
    				<textarea step="0.01" rows="10" class="form-control" name="justificativa" placeholder="Escreva a justificativa" ></textarea>
    			</div>
<?php
print"<input type='hidden' name='matricula' value='$matricula'>";
?>


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