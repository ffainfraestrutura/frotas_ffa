<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
} /*elseif ($tipo <> '0' || $tipo<>'1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}*/

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']= $tipo;

$data = date('Y-m-d');

include"../conecta2.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

$autoinfra = $_GET['autoinfra'];
$placa = $_GET['placa'];

//print "$autoinfra - $placa ";
$sql = "SELECT * FROM bdfrota.tbmovidatramite where autoinfra= '$autoinfra' AND placa='$placa';";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row = mysqli_fetch_array($resultado, MYSQLI_BOTH);

$id = $row['idtbmovidatramite'];
$nome= $row['nome'];
$cpf= $row['cpf'];
$placa=$row['placa'];
$autoinfra=$row['autoinfra'];
//$valor = $row['valor'];
$dtcons = $row['dtcons'];
$reciboass = $row['reciboass'];

$locadora= $row['locadora'];
$idmulta = $row['idmulta'];

$matmulta = $row['matricula'];

if($idmulta !=''){
	$sql1 = "SELECT * FROM bdfrota.tbmulta where idtbmulta= '$idmulta';";
}else{
	$sql1 = "SELECT * FROM bdfrota.tbmulta where placa= '$placa' AND autoinfracao = '$autoinfra';";
}

$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
	$valor1= $row1['valor'];
	$valdesconto1= $row1['valdesconto'];
	$taxaadm1=$row1['taxaadm'];
	$juros=$row1['juros'];
	$valortotal = $row1['valtotal'];
	$valparcelas= $row1['valparcelas'];
	$filial = $row1['filial'];

if($locadora==''){
	$sql2="SELECT idlocador FROM bdfrota.tbveiculo WHERE placa='$placa';";
	$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
	$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
		$locadora= $row2['idlocador'];
}

$sql2="SELECT cnpj, Estado FROM bdaniel.tbfilial WHERE nome='$filial'; ";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
	$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
		$cnpj= $row2['cnpj'];
		$estadofilial = $row2['Estado'];

if($filial ==''){
	$sql3="SELECT unidade FROM bdfrota.tbveiculo WHERE placa='$placa';";
	$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
	$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
		$estadofilial= $row3['unidade'];
}


if($filial == 'FFA Sao Paulo' || $filial=='SP' || $filial=='FFA SP'){
	$cnpj='08.375.450/0005-02';
	$cidadeass='SÃO PAULO/SP';
}else{
	$cnpj='08.375.450/0001-70';
	$cidadeass='RIO DE JANEIRO/RJ';
}

if($estadofilial == 'SP'){
	if(empty($cnpj)){
		$cnpj='08.375.450/0005-02';
	}
	
	$cidadeass='SÃO PAULO/SP';

}elseif($estadofilial == 'RJ'){
	if(empty($cnpj)){
		$cnpj='08.375.450/0001-70';
	}

	$cidadeass='RIO DE JANEIRO/RJ';

}elseif($estadofilial == 'PR'){
	if(empty($cnpj)){
		$cnpj='08.375.450/0017-38';
	}

	$cidadeass='CURITIBA/PR';
}

if($taxaadm1 == '' || $taxaadm1 == '0'){
	if($locadora == '2' || $locadora == '9' || $locadora == '16' || $locadora == '17' || $locadora == '19'){//movida
		$taxaadm1 = round((0.15*$valor1),2);
	} elseif($locadora == '3' || $locadora == '4' || $locadora == '14' || $locadora == '15'){//localiza
		$taxaadm1 = '25.00';
	} elseif($locadora == '6' || $locadora == '8'){
		$taxaadm1 = round((0.2*$valor1),2);
		
	}elseif($locadora == '1'){
		$taxaadm1 = '20.70';
		
	}
}

//if($valortotal == ''){
	$valortotal = ($valor1+$taxaadm1)-$valdesconto1;
//}

if($taxaadm1 == ''){
	$taxaadm1 = '0.00';
}

//tratando valor
$valor1=str_replace('.', ',', $valor1);
$valortotal=str_replace('.', ',', $valortotal);
$valdesconto1=str_replace('.', ',', $valdesconto1);

$taxaadm1=str_replace('.', ',', $taxaadm1);

$preco = explode(',',$valortotal);
$centavos = $preco[1];
$real = $preco[0];

if(strlen($centavos) == 1){
	$valortotal= $valortotal."0";
}


if(strpos($taxaadm1, ",") === false){
	$taxaadm1 = $taxaadm1.",00";
}

if(strpos($valortotal, ",") === false){
	$valortotal = $valortotal.",00";
}

$hoje = date('Y-m-d');
$datah = explode("-", $hoje);
$ano = $datah[0];
$mes = $datah[1];

switch ($mes) {
	case 1:
  	$mesd = 'janeiro';
  	break;

	case 2:
		$mesd = 'fevereiro';
		break;

	case 3:
		$mesd = 'março';
		break;

	case 4:
		$mesd = 'abril';
		break;

	case 5:
		$mesd = 'maio';
		break;

	case 6:
		$mesd = 'junho';
		break;

	case 7:
		$mesd = 'julho';
		break;

	case 8:
		$mesd = 'agosto';
		break;

	case 9:
		$mesd = 'setembro';
		break;

	case 10:
		$mesd = 'outubro';
		break;

	case 11:
		$mesd = 'novembro';
		break;

	case 12:
		$mesd = 'dezembro';
		break;

	default:
  	$mesd='';
}

$dia = $datah[2];

if($reciboass<>'sim'){
	echo "<script language='javascript' type='text/javascript'> alert('Recibo não assinado.'); window.location=\"./multascondutor.php\"; </script>";
}

 
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
	  <meta charset="utf-8" />
	  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
	  <meta name="description" content="FFA" />
	  <meta name="author" content="FFA" />
	  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

	  <title> Condutor - Início </title>

	  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
	  <link href="../src/css/styles.css" rel="stylesheet" />
	  <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

	  <!--link Material Symbols - simbolos do input-->
	  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

	  <!-- jquery e masks -->
	  <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

	  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

	  <!--data tables - css e js-->
	  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
	  <link href="../src/css/styles.css" rel="stylesheet" />
	  <!--<script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>-->
	  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
	  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
	  <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-eu.js"></script>
	  <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script>
	</head>
	<body class="sb-nav-fixed">
		<?php include "menu-teste.php"; ?>

		<div id="layoutSidenav_content">

			<main style="width: 100%;" class="mb-2">
				<h4 class='h4 pt-3 pb-2 col-12 text-center'> RECIBO DE ADIANTAMENTO SALARIAL </h4>

				<?php
				print"
				<div class='ms-2 m-auto border border-dark p-1 me-2'>
					<p>Nome do Empregador: FFA INFRAESTRUTURA E SERVIÇOS LTDA<br>
					CNPJ n.º $cnpj</p>					
				</div>

				<div class='mt-2 ms-2 m-auto border border-dark p-1 me-2'>
					<p>Nome do Empregado: $nome
					<br>
					CPF: $cpf</p>					
				</div>

				<div class='mt-2 ms-2 m-auto border border-dark p-1 me-2'>
					<h6>Adiantamento Salarial para pagamento de MULTA DO VEÍCULO  $placa</h6>
					<p>
					Auto de infração: $autoinfra
					<br>
					Valor da multa: R$ $valor1
					<br>
					Valor Taxa Administração Locadora: R$ $taxaadm1
					<br><br>
					Valor desconto: $valdesconto1
					<br>Total: R$ $valortotal
					<br><br>
					Declaro, para todos os efeitos, ter recebido a título de \"Adiantamento Salarial para pagamento de multa\", a importância de R$ $valortotal em espécie, e  em consonância com o disposto no art. 462, caput, da CLT, tenho a  plena ciência de que o respectivo valor será descontado, pelo  empregador, quando do pagamento da minha remuneração mensal relativa à folha de pagamento.
					
					$cidadeass, $dia de $mesd de $ano.</p>

					<br><br>

					<img style='width: 200px;' class='border-bottom border-dark' src='../../assinaturas/docs/colass/$matmulta.png'/>
					<br>
					Assinatura do condutor
					</p>
				</div>";
				?>

				<div class="mt-2 text-center">
					<div class="mb-2">
						<a href="./multascondutor.php" class="btn btn-danger"> Voltar para página inicial </a>
					</div>

					<div>

						<?php
						$link = '../pdf/fpdf/reciboffaass.php';

						print"
						<form method='post' action='$link' target='_blank'>
              <input type='hidden' name='placa' value='$placa'/>
              <input type='hidden' name='autoinfracao' value='$autoinfra'/>
              <input type='hidden' name='id' value='$id'/>
              
              <button class='btn btn-success' type='submit'> Baixar versão PDF </button>
            </form>"; ?>
					</div>
          
	        
	      </div>			

			</main>
		</div>
	</body>
</html>


