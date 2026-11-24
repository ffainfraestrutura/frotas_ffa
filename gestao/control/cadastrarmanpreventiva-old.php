<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$placa = $_POST['placa'];
$manutencao = $_POST['manutencao'];
$hodometro = $_POST['hodometro'];
$data = $_POST['data'];
$matr_autor = $_POST['matr_autor'];

$statusveic = utf8_decode($_POST['statusveic']);
$atualizadoem = $_POST['atualizadoem'];
$solicitante = utf8_decode($_POST['solicitante']);
$status = utf8_decode($_POST['status']);
$etapa = utf8_decode($_POST['etapa']);
$dataocorrencia = $_POST['dataocorrencia'];
$modelo = utf8_decode($_POST['modelo']);
//$Km = $_POST['Km'];
$fornman = utf8_decode($_POST['fornman']);
$descricao = utf8_decode($_POST['descricao']);
$ccusto = $_POST['ccusto'];
$condutor = utf8_decode($_POST['condutor']);
$oficina = utf8_decode($_POST['oficina']);
$dataagendamento = $_POST['dataagendamento'];
$prevsaida = $_POST['prevsaida'];
$dataentrada = $_POST['dataentrada'];
$dataretirada = $_POST['dataretirada'];
$tipopagamento = utf8_decode($_POST['tipopagamento']);
$reembolsoaprov = $_POST['reembolsoaprov'];
$valorreembolso = $_POST['valorreembolso'];
$valoroficina = $_POST['valoroficina'];
$valordesconto = $_POST['valordesconto'];
$valormaoobra = $_POST['valormaoobra'];
$valormaterial = $_POST['valormaterial'];
$valortransp = $_POST['valortransp'];
$outrosvalor = $_POST['outrosvalor'];
$descontarcond = $_POST['descontarcond'];
$datavencimento = $_POST['datavencimento'];
$datapagamento = $_POST['datapagamento'];
$formapagam = utf8_decode($_POST['formapagam']);
$condicaopag = utf8_decode($_POST['condicaopag']);
$numparc = $_POST['numparc'];
$valorparcela = $_POST['valorparcela'];
$dataprimparc = $_POST['dataprimparc'];
$protocolo = $_POST['protocolo'];
$dataconclusao = $_POST['dataconclusao'];
$placaanterior = $_POST['placaanterior'];

if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} else {
  $placa = str_replace(" ", "", $placa);
}

if(strpos($placaanterior, "-") !== false){
  $placaanterior = str_replace("-", "", $placaanterior);
} else {
  $placaanterior = str_replace(" ", "", $placaanterior);
}

if(strpos($hodometro, ".") !== false){
  $hodometro = str_replace(".", "", $hodometro);
}

//tratando valores
$valorreembolsoaux = explode(" ", $valorreembolso);
$valorreembolso = str_replace(".", "", $valorreembolsoaux[1]);
$valorreembolso = str_replace(",", ".", $valorreembolso);

$valoroficinaaux = explode(" ", $valoroficina);
$valoroficina = str_replace(".", "", $valoroficinaaux[1]);
$valoroficina = str_replace(",", ".", $valoroficina);

$valordescontoaux = explode(" ", $valordesconto);
$valordesconto = str_replace(".", "", $valordescontoaux[1]);
$valordesconto = str_replace(",", ".", $valordesconto);

$valtotalaux = explode(" ", $valtotal);
$valtotal = str_replace(".", "", $valtotalaux[1]);
$valtotal = str_replace(",", ".", $valtotal);

$valormaoobraaux = explode(" ", $valormaoobra);
$valormaoobra = str_replace(".", "", $valormaoobraaux[1]);
$valormaoobra = str_replace(",", ".", $valormaoobra);

$valormaterialaux = explode(" ", $valormaterial);
$valormaterial = str_replace(".", "", $valormaterialaux[1]);
$valormaterial = str_replace(",", ".", $valormaterial);

$valortranspaux = explode(" ", $valortransp);
$valortransp = str_replace(".", "", $valortranspaux[1]);
$valortransp = str_replace(",", ".", $valortransp);

$outrosvaloraux = explode(" ", $outrosvalor);
$outrosvalor = str_replace(".", "", $outrosvaloraux[1]);
$outrosvalor = str_replace(",", ".", $outrosvalor);

$valorparcelaaux = explode(" ", $valorparcela);
$valorparcela = str_replace(".", "", $valorparcelaaux[1]);
$valorparcela = str_replace(",", ".", $valorparcela);

/*$sql2="SELECT idtbmodeloveic FROM bdfrota.tbmodeloveic WHERE modelo='$modelo'; ";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
	$modelof = $row2[0];*/

//echo "ID $idtbmanprev, PLACA $placa, DATA $data, TIPO $manutencao,HODOMETRO/KM $hodometro, STATUSVEIC $statusveic, ATUALIZ $atualizadoem, SOLICITANTE $solicitante, STATUS MAN $status, ETAPA $etapa, DT OCOR $dataocorrencia, MODELO $modelo, FORNEC $fornman, DESC $descricao, CCUSTO $ccusto, COND $condutor,OFICINA $oficina,DT AGEND $dataagendamento, PREV SAIDA $prevsaida, DT ENT $dataentrada, DT RET $dataretirada, TIPO PAG $tipopagamento,REEMBOLSO $reembolsoaprov, VALOR REMB $valorreembolso, VAL OF $valoroficina, VAL DESC $valordesconto, VAL MO $valormaoobra, VAL MT $valormaterial, VAL TRANSP $valortransp,VAL OUTROS $outrosvalor, DESCONTADO $descontarcond, DT VENC $datavencimento, DT PAG $datapagamento, FORM PAG $formapagam, COND $condicaopag, NUM PARC $numparc, VALOR $valorparcela, DATA $dataprimparc, PROT $protocolo, DT CONCL $dataconclusao, PLACA ANT $placaanterior";

$sql1="SELECT placa FROM bdfrota.tbveiculo WHERE placa = '".$placa."'; ";
$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

if(mysqli_num_rows($resultado1) == 0){
	echo "<script language='javascript' type='text/javascript'> alert ('Placa de veículo não cadastrada no sistema.');window.location=\"../listagemmanutencao.php\"	</script>";
} else {

	$sql = "INSERT INTO bdfrota.tbmanprev (placa, data, tipo, hodometro, statusveic, atualizadoem, solicitante, status, etapa, dataocorrencia, modelo, fornman, descricao, ccusto, condutor, oficina, dataagendamento, prevsaida, dataentrada, dataretirada, tipopagamento, reembolsoaprov, valorreembolso, valoroficina, valordesconto, valormaoobra, valormaterial, valortransp, outrosvalor, descontarcond, datavencimento, datapagamento, formapagam, condicaopag, numparc, valorparcela, dataprimparc, protocolo, dataconclusao, placaanterior) VALUES ('$placa','$data', '$manutencao', '$hodometro', '$statusveic', '$atualizadoem', '$solicitante', '$status', '$etapa', '$dataocorrencia', '$modelo', '$fornman', '$descricao', '$ccusto', '$condutor', '$oficina', '$dataagendamento', '$prevsaida', '$dataentrada', '$dataretirada', '$tipopagamento', '$reembolsoaprov', '$valorreembolso', '$valoroficina', '$valordesconto', '$valormaoobra', '$valormaterial', '$valortransp', '$outrosvalor', '$descontarcond', '$datavencimento', '$datapagamento', '$formapagam', '$condicaopag', '$numparc', '$valorparcela', '$dataprimparc', '$protocolo', '$dataconclusao', '$placaanterior');";
	$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

if($statusveic == '5'){
    $sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$hoje', statusvel='$statusveic', hodometro='$hodometro', matcond='' WHERE placa ='$placa' ";
    $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));

    $sql5 = "UPDATE bdfrota.tbcondutor SET ativo='0', datadissoc='$hoje', statuscond='' WHERE idtbcondutor ='$idtbcondutor' ";
    $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
  }else{
  	$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$hoje', statusvel='$statusveic', hodometro='$hodometro' WHERE placa ='$placa' ";
  $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
  }
	$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$hoje', statusvel='$statusveic', hodometro='$hodometro' WHERE placa ='$placa' ";
  $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));

 $sqla="SELECT max(idtbcondutor) FROM bdfrota.tbcondutor WHERE placaassoc='$placa' ";
$resultadoa = mysqli_query($conexao, $sqla) or die(mysqli_error($conexao));
$rowa= mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
  $idtbcondutor=$rowa['idtbcondutor'];



  
/*if($status == 'CONCLUIDO'){
  if($statusveic == '2'){
    $sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$hoje', statusvel='$statusveic', hodometro='$hodometro', matcond='' WHERE placa ='$placa' ";
    $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));

    $sql5 = "UPDATE bdfrota.tbcondutor SET ativo='0', datadissoc='$hoje', statuscond='' WHERE idtbcondutor ='$idtbcondutor' ";
    $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
  }else{
    $sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$hoje', statusvel='$statusveic', hodometro='$hodometro' WHERE placa ='$placa' ";
    $resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
  }
  
}*/

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa) 
	$acao='Cadastrou man preventiva';
	$tipo = 'cadastro';
	$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');

	//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	//echo "<script language='javascript' type='text/javascript'> alert ('Manutenção de veículo cadastrada com sucesso.');window.location=\"http://ffasip.ddns.net:4545/emailmanutencao.php\"	</script>";

	echo "<script>window.alert('Manutenção de veículo cadastrada com sucesso.');
    window.location=\"http://ffasip.ddns.net:4242/frotas/gestao/listagemmanutencao.php\"; </script>";
}


?>