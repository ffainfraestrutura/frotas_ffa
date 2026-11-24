<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

if ($perfil == null){
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

$autoinfra = $_POST['autoinfra'];
$placa = $_POST['placa'];

//print "$autoinfra - $placa ";
$sql = "SELECT * FROM bdfrota.tbmovidatramite where autoinfra= '$autoinfra' AND placa='$placa';";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row = mysqli_fetch_array($resultado, MYSQLI_BOTH);

$nome= $row['nome'];
$cpf= $row['cpf'];
$placa=$row['placa'];
$autoinfra=$row['autoinfra'];
//$valor = $row['valor'];
$dtcons = $row['dtcons'];

$locadora= $row['locadora'];
$idmulta = $row['idmulta'];

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
	$dataInfra = $row1['datainfracao'];
	$descricao = utf8_encode($row1['descricaoinfra']);
	$enderecoinfra = utf8_encode($row1['endereco']);
	$cidadeinfra = utf8_encode($row1['municipio']);
	$orgao = utf8_encode($row5['orgao']);

//tratando data
$dataInfra1 = explode(" ", $dataInfra);
$dataInfra2 = explode("-", $dataInfra1[0]);
$dataInfraf = $dataInfra2[2]."/".$dataInfra2[1]."/".$dataInfra2[0]." ".$dataInfra1[1];

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
$datahf = $dia."/".$mes."/".$ano;

$sql4 = "SELECT numcnh FROM bdfrota.tbcnh WHERE matricula='$matricula'; ";
$resultado4 =  mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
$row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
	$cnh = $row4['numcnh'];

$sql5 = "SELECT rg, cpf, endereco, bairro, cidade, estado, cep, matricula FROM bdaniel.tbfuncionario WHERE nome = '$nome' /*AND status<>'Demitido'*/; ";
$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
	$rg = $row5['rg'];
	$cpf = $row5['cpf'];
	$endereco = utf8_encode($row5['endereco']);
	$bairro = utf8_encode($row5['bairro']);
	$cidade = utf8_encode($row5['cidade']);
	$estado = $row5['estado'];
	$cep = $row5['cep'];
	$matricula = $row5['matricula'];

$sql6 = "SELECT renavam, chassi FROM bdfrota.tbveiculo WHERE placa='$placa'; ";
$resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
$row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
	$chassi = $row6['chassi'];
	$renavam = $row6['renavam'];




?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
	  <meta charset="utf-8" />
	  <meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
	  <meta name="description" content="FFA" />
	  <meta name="author" content="FFA" />
	  <link rel="icon" type="image/png" href="../src/images/favicon.png"/>

	  <title> Assinar recibos </title>

	  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
	  <link href="../src/css/styles2.css" rel="stylesheet" />
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

	  <style type="text/css">
	  	.bold{
	  		font-weight: bold;
	  	}
	  </style>
	</head>
	<body class="sb-nav-fixed">
		<?php include "menu-teste.php"; ?>

		<div id="layoutSidenav_content">

			<main style="width: 100%;" class="mb-2">
				<div class="border border-dark ms-2 me-2 mt-2">
					<h4 class='h4 pt-3 pb-2 col-12 text-center'> RECIBO DE ADIANTAMENTO SALARIAL </h4>

					<?php
						print"
						<div class='m-auto p-1'>
							<p>Nome do Empregador: FFA INFRAESTRUTURA E SERVIÇOS LTDA<br>
							CNPJ n.º $cnpj</p>					
						</div>

						<div class='mt-2 m-auto p-1'>
							<p>Nome do Empregado: $nome
							<br>
							CPF: $cpf</p>					
						</div>

						<div class='mt-2 m-auto p-1 '>
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
							<br><br>
							$cidadeass, $dia de $mesd de $ano.</p>					
						</div>"
					?>
				</div>
				

				<div class='mt-5'>
					<img src="../src/images/logo-localiza.png">
					<h5 class='h5 pt-3 pb-2 col-12 text-center'> TERMO DE RESPONSABILIDADE POR INFRAÇÃO DE TRÂNSITO </h5>

					<?php
					print"
					<div class='mt-2 ms-2 m-auto border border-dark p-1 me-2'>
						<table>
							<tr>
								<td>Contrato nº:</td>
								<td>RIO0007/21</td>
							</tr>

							<tr>
								<td>Cliente:</td>
								<td>FFA INFRAESTRUTURA E SERVICOS LTDA<br>RUA TANAGRA, nº42 - OLARIA<br>CEP: 21031560 - RIO DE JANEIRO - RJ - BRASIL</td>
							</tr>

							<tr>
								<td>Placa/Modelo:</td>
								<td>$placa/$modelo</td>
							</tr>

						</table>				
					</div>

					<div class='mt-2 ms-2 m-auto border border-dark p-1 me-2'>
						<p>INFORMAÇÕES DO CONDUTOR QUE SERÁ INDICADO</p>
						<p>Nome do Condutor: $nome 	CPF: $cpf</p>
						<p>Nº Registro Carteira Nacional de Habilitação: $cnh 	UF(CNH): $ufcnh</p>
						<p>RG: $rg 	Órgão Emissor: $emissorrg	UF(RG): $ufrg</p>

						<p>O Usuário / condutor identificado acima declara que estava em posse do carro placa $placa no dia $dataInfraf, momento do cometimento da infração nº AIT $autoinfra, emitida pelo(a) $orgao.</p>

						<p>Data: $datahf</p>

						<p class='border-top border-dark'>   </p>

						<p class='text-center'>(Idêntica à assinatura da CNH)</p>

						<p>Considerando a ciência do Usuário quanto ao Contrato de Aluguel e Gestão de Frota nº xxxxxxx firmado entre LOCALIZA FLEET S/A, CNPJ 02.286.479/0001-08, com sede em Belo Horizonte/MG Localiza Gestão de Frotas, e o Cliente (“Contrato”), o Usuário / condutor declara que:
						<br>
						1) Assume a responsabilidade pela infração supracitada cometida na condução do Carro alugado e pela pontuação decorrente desta infração, nos termos do artigo 5º e seus parágrafos, da Resolução 619/16 do CONTRAN, e da cláusula 1.7.1 do Contrato.
						<br>
						2) Autoriza a Localiza Fleet a assinar o termo de apresentação do condutor / infrator das multas de trânsito que envolvam o Carro alugado, nos termos do artigo 257, parágrafos 1º, 3º, 7º e 8º, do Código Brasileiro de Trânsito, e da cláusula 1.7.1 do Contrato.
						<br>
						3) É preposto autorizado pelo Cliente a conduzir os Carros alugados nos termos do Contrato.
						<br>
						4) Não havendo identificação do infrator e sendo o veículo de propriedade de pessoa jurídica, será lavrada nova multa ao proprietário do veículo, mantida a originada pela infração, cujo valor é o da multa multiplicada pelo número de infrações iguais cometidas no período de doze meses.
						<br>
						5) O preposto responsabiliza-se nas esferas cível, administrativa e penal, pela veracidade das informações e dos documentos fornecidos.
						<br>
						6) A identificação do condutor infrator só surtirá efeito se estiver corretamente preenchida, assinada e acompanhada de cópia legível do documento de habilitação, além de documento que comprove a assinatura do condutor infrator, quando esta não constar do referido documento.</p>
					</div>";
					?>
				</div>

				<div class="mt-2 d-flex justify-content-center">
					<form method='post' action='./assinarrecibolocadora.php'> 
          	<?php print"
            <input type='hidden' name='placa' value='$placa'/> 
            <input type='hidden' name='autoinfra' value='$autoinfra'/> "
            ?>
            <button type='submitt' class="btn btn-success"> Assinar</button> 
	        </form>

					
				</div>

			</main>
		</div>
	</body>
</html>

<?php
/*

// Instanciation of inherited class



$pdf->MultiCell(177,5,utf8_decode("\n\n\n\n\n\n                              ______________________________________________________\n                                                                   Assinatura do Empregado \n\n\n"),1,1);
$pdf->Output();

/* \n\nValor desconto: $valdesconto1*/
?>

