<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
}*/
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
    </head>

    <body class="sb-nav-fixed">

    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">
                <main>

<div class="container-fluid px-4">

    	
    		<h1 class="h1 mt-1"> Consumo de Combustível </h1>

    		<form action=" " method="post">
                <!--div tipo -->
    			<div>
    				<label class="form-label mb-0 fs-5" for="ivalor">Tipo:</label>
    				<div class="input-group mb-3">
    					<input class="form-control" type="text" name="tipo" id="itipo" required="true" placeholder="Informe o tipo">
    				</div>
    			</div>

                <!--div consumo litro-->
    			<div class="mb-3">
    				<label class="form-label mb-0 fs-5" for="ijust">Consumo:</label>
    				<input class="form-control" type="text" name="cons" id="icons" required="true" placeholder="Consumo em litros">
    			</div>

                <!--div valor do combustível-->
                <div>
                    <label class="form-label mb-0 fs-5" for="ivalor">Valor:</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">R$: </span>
                        <input class="form-control" type="text" name="valor" id="ivalor" required="true" placeholder="Informe o valor do combustível/litro" onkeyup="formatarMoeda()">
                    </div>
                </div>

                <!--div tipo do combustível-->
    			<div class="mb-2">
                    <p class="form-label mb-0 fs-5">Tipo de combustível: </p>
    				<select class="form-select" aria-label="Default select example">
                        <option value="1">Gasolina</option>
                        <option value="2">Diesel</option>
                        <option value="3">Álcool</option>
                    </select>
    			</div>

                <!--div botão confirmar-->
                <div>
                    <button class="btn btn-success" type="submit"> Confirmar </button>
                </div>
    			
    		</form>

    	</main>
</div>

        <script>
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
    </body>

</html>