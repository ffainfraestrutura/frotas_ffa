<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$marca = strtoupper($_POST['marca']);
$modelo = strtoupper($_POST['modelo']);
$matr_autor = $_POST['matr_autor'];

//echo $marca, $modelo;

$sql2 = "SELECT * FROM bdfrota.tbmodeloveic WHERE modelo = '$modelo';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_num_rows($resultado2);

$sql3 = "SELECT idtbmarcaveic FROM bdfrota.tbmarcaveic WHERE marca = '$marca';";
$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
    $idtbmarcaveic= utf8_encode($row3['idtbmarcaveic']);

if($row2== 0){
	$sql1 = "INSERT INTO bdfrota.tbmodeloveic (modelo, marca) VALUES ('$modelo', '$idtbmarcaveic');";
	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	$acao='Cadastrou modelo de veiculo';
	$tipo = 'cadastro';
	$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
	//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert ('Modelo cadastrado com sucesso.'); window.location=\"../cadastroveiculo.php\"	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert('Modelo já cadastrado.'); window.location=\"../cadastroveiculo.php\"	</script>";	
}

/*mysqli_close($conexao);*/

?>