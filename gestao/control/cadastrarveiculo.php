<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log3.php";
include("../../conecta03.php");

$hoje= date('Y-m-d H:i:s');

$placa = strtoupper($_POST['placa']);
$uf = $_POST['uf'];
//$aplicacaofrota = utf8_decode($_POST['aplicacaofrota']);

$aplicacaofrota = $_POST['aplicacaofrota'];

$marca = $_POST['marca'];
$modelo = $_POST['modelo'];

/*$versao = utf8_decode($_POST['versao']);
$categoria =  utf8_decode($_POST['categoria']);
$tipoveic =  utf8_decode($_POST['tipoveic']);
$cor =  utf8_decode($_POST['cor']);*/

$versao = $_POST['versao'];
$categoria =  $_POST['categoria'];
$tipoveic =  $_POST['tipoveic'];
$cor =  $_POST['cor'];

$zerokm = $_POST['zerokm'];
$anofabric = $_POST['anofabric'];
$anomodelo = $_POST['anomodelo'];

//$statusvel = utf8_decode($_POST['statusvel']);
$statusvel = $_POST['statusvel'];

$tipovel = $_POST['tipovel'];
$situacao = $_POST['situacao'];
$hodometroinicial = $_POST['hodometroinicial'];
$datamovimentacao = $_POST['datamovimentacao'];
$horamovimentacao = $_POST['horamovimentacao'];
$matcond = $_POST['matcond'];
$dtentrega = $_POST['dtentrega'];
$dtdevolucao = $_POST['dtdevolucao'];

$velmax = $_POST['velmax'];
$renavam = $_POST['renavam'];
$chassi = $_POST['chassi'];
$nummotor = $_POST['nummotor'];

//$combustivel = utf8_decode($_POST['combustivel']);
$combustivel = $_POST['combustivel'];

$tanque = $_POST['tanque'];
$motorizacao = $_POST['motorizacao'];
$nportas = $_POST['nportas'];
$npassageiros = $_POST['npassageiros'];
$calibragem = $_POST['calibragem'];
$aro = $_POST['aro'];
$qtdpneus = $_POST['qtdpneus'];
$qtdestepes = $_POST['qtdestepes'];
$qtdeixos = $_POST['qtdeixos'];
$gnv = $_POST['gnv'];
$gps = $_POST['gps'];
$tagpedagio = $_POST['tagpedagio'];
$airbag = $_POST['airbag'];
$rack = $_POST['rack'];

//$oficina= utf8_decode($_POST['oficina']);
$oficina= $_POST['oficina'];

$ncontloc = $_POST['ncontloc'];

$doccrlv = $_POST['doccrlv'];
$dtdisponivelloc = $_POST['dtdisponivelloc'];

//$gpsemp = utf8_decode($_POST['gpsemp']);
$gpsemp = $_POST['gpsemp'];

$status = $_POST['status'];
$hodometro = $_POST['hodometro'];

//$tipoposse =  utf8_decode($_POST['tipoposse']);
$tipoposse =  $_POST['tipoposse'];

$locador = $_POST['locador'];
$matr_autor = $_POST['matr_autor'];
$dtdevolucaoloc = $_POST['dtdevolucaoloc'];

$blindagem = $_POST['blindagem'];
$valaquisicao = $_POST['valaquisicao'];
//$obsveiculo = utf8_decode($_POST['obsveiculo']);
$obsveiculo = $_POST['obsveiculo'];

$baseffa = $_POST['baseffa']; //cnpj baseffa
$basegestao = $_POST['basegestao']; 
$centrocusto = $_POST['centrocusto'];

$dttermo = $_POST['dttermo'];
$dtdisp = $_POST['dtdisp'];

$unidade = $_POST['unidade'];


$dtmovimentacao = $datamovimentacao." ".$horamovimentacao;


//$dtmovimentacao = $hoje;

//tratando valores
$valaquisicao = explode(" ", $valaquisicao);
$valaquisicao = str_replace(".", " ", $valaquisicao[1]);
$valaquisicao = str_replace(",", ".", $valaquisicao);

if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} else {
  $placa = str_replace(" ", "", $placa);
}

if(strpos($hodometro, ".") !== false){
  $hodometro = str_replace(".", "", $hodometro);
}

if($statusvel == '5' || $statusvel== '18') {
  $matcond = '';
}

//fazer upload dos documentos
/*upload crlv*/
$_UP['pasta'] = '../docs/';
$crvl1= $_FILES['crlv']['name'];

if(!empty($crvl1)){
	$extensao = strtolower(end(explode('.', $_FILES['crlv']['name'])));
	$crvl='CRLV-'.$placa.".".$extensao;
	 
	move_uploaded_file($_FILES['crlv']['tmp_name'], $_UP['pasta'] . $crvl);
	$crlvc = $_UP['pasta'] . $crvl;
}

//fazer upload dos documentos
//upload crv
$crv1 =  $_FILES['crv']['name'];
if(!empty($crv1)){
	$extensao = strtolower(end(explode('.', $_FILES['crv']['name'])));
	$crv='CRV-'.$placa.".".$extensao;
	$crvc = $_UP['pasta'] . $crv;
	 
	move_uploaded_file($_FILES['crv']['tmp_name'], $_UP['pasta'] . $crv);
}

//fazer upload dos documentos
//upload ipva
$ipva1 = $_FILES['ipva']['name'];
if(!empty($ipva1)){
	$extensao = strtolower(end(explode('.', $_FILES['ipva']['name'])));
	$ipva='IPVA-'.$placa.".".$extensao;
	$ipvac = $_UP['pasta'] . $ipva;
	 
	move_uploaded_file($_FILES['ipva']['tmp_name'], $_UP['pasta'] . $ipva);
}


//echo $placa, $uf , $aplicacaofrota,$marca ,$modelo,$versao ,$categoria,$tipoveic ,$cor ,$zerokm ,$anofabric ,$anomodelo ,$velmax ,$renavam ,$chassi ,$nummotor ,$combustivel ,$tanque ,$motorizacao,$nportas ,$npassageiros ,$calibragem ,$aro ,$qtdpneus ,$qtdestepes ,$qtdeixos ,$gnv,$gps,$tagpedagio."<br>". $locador, $crlv;
//echo $crlv;

/*if($baseffa == 'FFA Rio de Janeiro' || $baseffa == 'FFA Campos dos Goytacazes' || $baseffa=='FFA Petropolis' || $baseffa=='FFA Teresopolis' || $baseffa == 'FFA Tres Rios' || $baseffa=='FFA Nova Friburgo' || $baseffa == 'FFA Macae' || $baseffa=='FFA Lagos'|| $baseffa=='FFA Vassouras'){
	$unidadef='RJ';
}elseif($baseffa=='FFA Cascavel' || $baseffa=='FFA Ponta Grossa' || $baseffa=='FFA Curitiba'){
	$unidadef='PR';
}elseif($baseffa=='FFA Sao Paulo'||$baseffa=='FFA SP'){
	$unidadef = 'SP';
}*/

$sql2 = "SELECT * FROM bdfrota.tbveiculo WHERE placa = '$placa';";
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
$row2 = mysqli_num_rows($resultado2);

if($row2 <= 0){
	$sql1 = "INSERT INTO bdfrota.tbveiculo (placa, uf, aplicacao, marca, modelo, versao, categoria, tipo, cor, zerokm, anofabr, anomodelo, velocmax, renavam, chassi, nummotor, combustivel, tanque, motorizacao, nportas, npassageiros, calibragem, aro, qtdpneus, qtdestepe, qtdeixos, gnv, gps,tagpedagio, status, hodometro, tipoposse, idlocador, statusvel, tipovel, situacao, hodometroinicial, datamovimentacao, matcond, dtentrega, dtdevolucao, gpsemp, doccrlv, airbag, rack, blindagem, oficina,ncontloc, dtdisponivelloc, dtdevolucaoloc, valaquisicao, obsveiculo, baseffa, unidade, basegestao, ccusto, dttermodisp,dtdesmobilizacao) VALUES ('$placa', '$uf', '$aplicacaofrota', '$marca', '$modelo', '$versao', '$categoria', '$tipoveic', '$cor', '$zerokm', '$anofabric', '$anomodelo', '$velmax', '$renavam', '$chassi', '$nummotor', '$combustivel', '$tanque', '$motorizacao', '$nportas', '$npassageiros', '$calibragem', '$aro', '$qtdpneus', '$qtdestepes', '$qtdeixos', '$gnv', '$gps', '$tagpedagio', '$status', '$hodometro', '$tipoposse', '$locador', '$statusvel', '$tipovel', '$situacao', '$hodometroinicial', '$dtmovimentacao', '$matcond', '$dtentrega', '$dtdevolucao', '$gpsemp', '$doccrlv', '$airbag', '$rack', '$blindagem', '$oficina', '$ncontloc', '$dtdisponivelloc', '$dtdevolucaoloc', '$valaquisicao', '$obsveiculo', '$baseffa', '$unidade', '$basegestao', '$centrocusto', '$dttermo', '$dtdesm')";
	$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

	/*$sql3 = "INSERT INTO bdfrota.tbveicdocs(placa, crlv, crv, cert_ipva) VALUES ('".$placa."', '".$crlvc."', '".$crvc."', '".$ipvac."'); ";

		$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));*/

	if($matcond != ''){
		$sql3="SELECT nome, codempresa, codfilial, ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matcond' ";
		$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
		$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
			$nomecond = $row3['nome'];
			$codempresa = $row3['codempresa'];
			$codfilial = $row3['codfilial'];
			$ccustofunc = $row3['ccusto'];

		/*$sql3a="SELECT Estado, nome FROM bdaniel.tbfilial WHERE codempresa ='$codempresa' AND codfilial='$codfilial' ";
		$resultado3a = mysqli_query($con, $sql3a) or die(mysqli_error($con));
		$row3a = mysqli_fetch_array($resultado3a, MYSQLI_BOTH);
			$estado = $row3['Estado'];
			$nomefilial = $row3['nome'];

		/*$sql3b = "UPDATE bdfrota.tbveiculo SET unidade='$estado', ccusto='$ccustofunc' WHERE placa='$placa' ";
		$resultado3b = mysqli_query($con, $sql3b) or die(mysqli_error($con));*/

		$sql4 = "INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc) VALUES ('$nomecond', '$matcond','1','$placa', '$hoje');";//$dtmovimentacao
		$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
	}

		//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
		$log = new Log($con);
		$acao='Cadastrou veiculo';
		$tipo = 'cadastro';
		$logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, $placa);
		//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	//echo utf8_decode("<script language='javascript' type='text/javascript'> window.alert ('Veículo cadastrado com sucesso.');window.close();	</script>");
	echo ("<script language='javascript' type='text/javascript'> window.alert ('Veículo cadastrado com sucesso.');window.close();	</script>");
} else {
	//echo utf8_decode("<script language='javascript' type='text/javascript'> window.alert('Veículo já cadastrado.');window.close();</script>");	
	echo ("<script language='javascript' type='text/javascript'> window.alert('Veículo já cadastrado.');window.close();</script>");	
}

//window.location=\"../listagemveiculos.php\"
mysqli_close($con);

?>