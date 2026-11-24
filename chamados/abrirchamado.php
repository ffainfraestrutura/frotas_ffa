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
} elseif ($perfil == '' ){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

include_once"../func/log3.php";
include"../conecta03.php";
//include_once"../func/log.php";
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
        <link rel="icon" type="image/png" href="../src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../src/css/styles.css" rel="stylesheet" />
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous">
        </script>
        <!-- jquery e masks -->
          <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

          <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"./menu-teste.php"; ?>

<div id="layoutSidenav_content">
    <main>

        <div class="container-fluid px-4">

    	
    		<h1 class="h1 pt-1"> Abrir Chamado </h1> 
            <?php
            if($perfil == 0){
                print"
                <p class='fs-5 fw-bold'>Técnico: $nome </p>";
            } elseif($perfil == 1){
                print"
                <p class='fs-5 fw-bold'>Supervisor: $nome </p>";
            } elseif($perfil == 2){
                print"
                <p class='fs-5 fw-bold'>Coordenador: $nome </p>";
            }


            //$sql1="SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador WHERE id_colaborador='$matricula'; ";
            $sql1="SELECT placa FROM bdfrota.tbveiculo WHERE matcond='$matricula'; ";
            $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
            $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
                $placa = $row1[0];
            
            $sql2="SELECT telefone FROM bdfrota.tbusuario WHERE matricula='$matricula'; ";
                $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                    $telefone = $row2[0];
    		
            ?>

    		<form action="./control/abrirchamado.php" method="post" enctype='multipart/form-data'>
                <?php
                    print"<input type='hidden' name='nome' value='$nome'>
                        <input type='hidden' name='matricula' value='$matricula'>";
                 ?>
                <!--div telefone, placa, hodometro-->
                <div>
        			<div>
        				<label class="form-label mb-0 fs-5" for="telefone">Informe seu telefone para contato:<span style='color: red;'>*</span></label>
        				<input class="form-control" type="text" name="telefone" id="telefone" required="true" placeholder="Informe o Telefone" <?php print "value='$telefone'"; ?>>		
        			</div>

                    <div>
                    <?php

                    print"
                        <label class='form-label mb-0 fs-5' for='placa'>Placa:<span style='color: red;'>*</span></label>
                        <input class='form-control' type='text' name='placa' id='placa' required='true' placeholder='PLACA' value='$placa'> ";
                    ?>     
                    </div>

                    <div>
                        <label class="form-label mb-0 fs-5" for="hodometro">Hodômetro:<span style='color: red;'>*</span></label>
                        <input class="form-control" type="text" name="hodometro" id="hodometro" required="true" placeholder="Km do Hodômetro">      
                    </div>
                </div>

                <!-- div centro de custo -->
                <div>
                    <label class="form-label mb-0 fs-5" for="ccusto" style="width: 100%;">Selecione o centro de custo abaixo:</label>
                    <select class="form-select" id="ccusto" name="ccusto">
                <?php
                    $sql="SELECT DISTINCT(ccusto) FROM bdaniel.tbfuncionario WHERE ccusto<>'' AND status = 'Ativo' ORDER BY ccusto;";
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                    $sql2 = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
                    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                    while($row2=mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
                        $centro=$row2['ccusto'];
                        //print "<option> $centro </option>";
                    }

                    while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                    
                        $ccusto = $row['ccusto'];
                       
                        $selected = ($centro == $ccusto) ? "selected=\"selected\"" : null;
                        print"<option value='$ccusto' $selected> $ccusto </option>";
                    }
                    
                ?>
                    </select>
                </div>

                
                <div>
                    <label class="form-label mb-0 fs-5" for="tipochamado" style="width: 100%;">Qual tipo de chamado deseja realizar?<span style='color: red;'>*</span></label>
                    <select class="form-select" id="tipochamado" name="tipochamado">
                        <option value=''>Tipo de chamado...</option>
                        <option value='Comunicado de Sinistro'>Comunicado de Sinistro</option>
                        <option value='Solicitação de Manutenção Preventiva'>Solicitação de Manutenção Preventiva</option>
                        <option value='Solicitação de Manutenção Corretiva'>Solicitação de Manutenção Corretiva</option>
                        <!--<option value='Solicitação de Cota Extra'>Solicitação de Cota Extra</option> -->
                        <option value='Solicitação de Guincho'>Solicitação de Guincho</option>    
                    </select>
                </div>

                <div id="tiposinistrod">
                    <label class="form-label mb-0 fs-5" for="tiposinistro" style="width: 100%;">Qual tipo de sinistro deseja comunicar?</label>
                <select class="form-select" id="tiposinistro" name="tiposinistro">
                    <option value=''>Tipo de sinistro...</option>
                    <option value='ROUBO/FURTO'>ROUBO/FURTO</option>
                    <option value='COLISÃO'>COLISÃO</option>
                </select>
                </div>

                <div id="orientacao">
                    <label class="form-label mb-0 fs-5" for="orientsini" style="width: 100%;">Orientações relacionadas ao sinistro:
*<br>
   &nbsp;&nbsp;- Todo sinistro deve ser comunicado a frota imediatamente.<br>
   &nbsp;&nbsp;- É imprescindível a abertura do Boletim de Ocorrencia ou E-brat nas primeiras 12 horas do sinistro.<br>
   &nbsp;&nbsp;- Toda a documentação deve ser enviada a Frota imediatamente.<br>
   &nbsp;&nbsp;- O colaborador deve comparecer a frota imediatamente. </label>
                  <input class="form-check-input" type="checkbox" value="sim" id="corientesini" name='corientesini'>
  <label class="form-check-label" for="corientesini">
    Ciente
  </label>
                </div>


                <div id="manuprevd">
                    <label class="form-label mb-0 fs-5" for="manuprev" style="width: 100%;">Qual Manutenção Preventiva deseja solicitar?</label>
                <select class="form-select" id="manuprev" name="manuprev">
                    <option value=''>Tipo de revisão...</option>
                    <option value='REVISÃO DE 10.000'>REVISÃO DE 10.000</option>
                    <option value='REVISÃO DE 20.000'>REVISÃO DE 20.000</option>
                    <option value='REVISÃO DE 30.000'>REVISÃO DE 30.000</option>
                    <option value='REVISÃO DE 40.000'>REVISÃO DE 40.000</option>
                    <option value='REVISÃO DE 50.000'>REVISÃO DE 50.000</option>
                    <option value='REVISÃO DE 60.000'>REVISÃO DE 60.000</option>
                    <option value='REVISÃO DE 70.000'>REVISÃO DE 70.000</option>
                    <option value='REVISÃO DE 80.000'>REVISÃO DE 80.000</option>
                    <option value='REVISÃO DE 90.000'>REVISÃO DE 90.000</option>
                    <option value='REVISÃO DE 100.000'>REVISÃO DE 100.000</option>
                </select>
                </div>

                <div id="prevcord">
                    <label class="form-label mb-0 fs-5" for="prevcor" style="width: 100%;">Além da revisão preventiva, tem alguma solicitação de manutenção corretiva?</label>
                <select class="form-select" id="prevcor" name="prevcor">
                    <option value=''>Escolha</option>
                    <option value='Sim'>Sim</option>
                    <option value='Não'>Não</option>
                </select>
                </div>


                <div id="manucord">
                    <label class="form-label mb-0 fs-5" for="manucor">Descreva aqui a sua solicitação de manutenção corretiva </label>
                    <input class="form-control" type="text" name="manucor" id="manucor"  placeholder="Descreva...">      
                </div>

                <div id="guincho">
                    <div>
                        <label class="form-label mb-0 fs-5" for="endereco">Informe o endereço:<span style='color: red;'>*</span> </label>
                        <input class="form-control" type="text" name="enderecog" id="enderecog"  placeholder="Endereço">
                    </div>        
                </div>

                <!--div botão confirmar-->
    			<div class="mt-4 mb-2 text-center" id='botao'>
    				  <button class='btn btn-success' type='submit'>Confirmar</button>
    			</div>
    			
    		</form>

            <!--div campos obrigatórios -->
            <div class="mt-4 mb-2">
                <p><span style="color: red;">*</span> Campos obrigatórios.</p>
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
                </div>!-->
            </div>
        </div>
    </footer>
</div>
            

    	<script type="text/javascript">
              
$(document).ready(function () {
    //máscaras
    $('#telefone').mask('(00) 00000-0000');
    $('#hodometro').mask('000.000.000');

    //formatação de placa
      $('#placa').mask('AAA0U00', {
        translation: {
            'A': {
                pattern: /[A-Za-z]/
            },
            'U': {
                pattern: /[A-Za-z0-9]/
            },
        },
        onKeyPress: function (value, e, field, options) {
            // Convert to uppercase
            e.currentTarget.value = value.toUpperCase();

            // Get only valid characters
            let val = value.replace(/[^\w]/g, '');

            // Detect plate format
            let isNumeric = !isNaN(parseFloat(val[4])) && isFinite(val[4]);
            let mask = 'AAA 0U00';
            if(val.length > 4 && isNumeric) {
                mask = 'AAA-0000';
            }
            $(field).mask(mask, options);
        }
      });

  setInterval(function(){
    var tipochamado = document.getElementById("tipochamado"),
    tiposinistro = document.getElementById("tiposinistrod"),
    orientacao = document.getElementById("orientacao"),
    manuprev = document.getElementById("manuprevd"),
    prevcor = document.getElementById("enderecod"),
    manucor = document.getElementById("manucord");
    checkBox = document.getElementById("corientesini");
    prevcor2= document.getElementById("prevcor");
    guincho = document.getElementById("guincho");



    if(tipochamado.value=="Comunicado de Sinistro"){
      $("#manuprevd").addClass('d-none');
      $("#prevcord").addClass('d-none');
      $("#manucord").addClass('d-none');
      $("#tiposinistrod").removeClass('d-none');
      $("#orientacao").removeClass('d-none');
      $("#guincho").addClass('d-none');
      
      if (checkBox.checked) {
          //console.log("O checkbox está marcado!");
          $("#botao").removeClass('d-none');
        } else {
          //console.log("O checkbox não está marcado!");
          $("#botao").addClass('d-none');
      }
      
    }else if (tipochamado.value=="Solicitação de Manutenção Preventiva") {
      $("#tiposinistrod").addClass('d-none');
      $("#orientacao").addClass('d-none');
      $("#manuprevd").removeClass('d-none');
      $("#prevcord").removeClass('d-none');
      $("#guincho").addClass('d-none');
      
      if(prevcor2.value=="Sim"){
        $("#manucord").removeClass('d-none');
        $("#botao").removeClass('d-none');

      } else {
        $("#manucord").addClass('d-none');
        $("#botao").removeClass('d-none');
      }
    }else if (tipochamado.value=="Solicitação de Manutenção Corretiva") {
      $("#tiposinistrod").addClass('d-none');
      $("#orientacao").addClass('d-none');
      $("#manuprevd").addClass('d-none');
      $("#prevcord").addClass('d-none');
      $("#manucord").removeClass('d-none');
      $("#botao").removeClass('d-none');
      $("#guincho").addClass('d-none');
      
    }else if (tipochamado.value=="Solicitação de Guincho") {
      $("#tiposinistrod").addClass('d-none');
      $("#orientacao").addClass('d-none');
      $("#manuprevd").addClass('d-none');
      $("#prevcord").addClass('d-none');
      $("#manucord").addClass('d-none');
      $("#botao").removeClass('d-none');
      //$("#cotaextra").addClass('d-none');
      $("#guincho").removeClass('d-none');

      $("#enderecog").addClass("required");

    }else{
      $("#tiposinistrod").addClass('d-none');
      $("#orientacao").addClass('d-none');
      $("#manuprevd").addClass('d-none');
      $("#prevcord").addClass('d-none');
      $("#manucord").addClass('d-none');
      $("#botao").addClass('d-none');
      $("#guincho").addClass('d-none');
    }

  });
});



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
        <script src="../src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="../src/js/datatables-simple-demo.js"></script>
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->
    </body>

</html>