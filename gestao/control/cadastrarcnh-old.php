<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$nome= utf8_decode($_POST['nome']);
$matricula=$_POST['matricula'];
$cnh = $_POST['cnh'];
$validade = $_POST['validade'];
$uf = $_POST['uf'];
$categoria = $_POST['categoria'];
$pontos = $_POST['pontos'];
$consulta = $_POST['consulta'];
$suspensa = $_POST['suspensa'];
$matr_autor = $_POST['matr_autor'];

//echo $nome, $matricula, $cnh, $validade, $uf, $categoria, $pontos, $consulta, $suspensa;

$sql2 = "SELECT * FROM bdfrota.tbcnh WHERE matricula = '$matricula';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_num_rows($resultado2);

if($row2 == 0){
	$sql1 = "INSERT INTO bdfrota.tbcnh (numcnh, validade, uf, categoria, matricula, pontos, consulta, suspensa) VALUES ('$cnh', '$validade', '$uf', '$categoria', '$matricula', '$pontos', '$consulta', '$suspensa');";
	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	  //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	  $acao='Cadastrou cnh';
	  $tipo = 'cadastro';
	  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, '');

	  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";
	  
	echo "<script language='javascript' type='text/javascript'> alert('CNH cadastrada com sucesso.');window.location=\"../listagemcnh2.php\"	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert('CNH já cadastrada.');window.location=\"../listagemcnh2.php\"	</script>";	
}
?>