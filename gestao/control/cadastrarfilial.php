<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");

include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$codigo = $_POST['codigo'];
$descricao = utf8_decode($_POST['descricao']);
$matr_autor = $_POST['matr_autor'];

//echo $codigo, $descricao;

$sql2 = "SELECT * FROM bdfrota.tbfilial WHERE codigo = '$codigo';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_num_rows($resultado2);

if($row2== 0){
	$sql1 = "INSERT INTO bdfrota.tbfilial (codigo, descricao, ativo) VALUES ('".$codigo."', '".$descricao."', 1)";
	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	$acao='Cadastrou filial';
	$tipo = 'cadastro';
	$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');

	  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert ('Filial cadastrada com sucesso.');window.location=\"../listagemfiliais.php\"	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert('Código de Filial já cadastrado.');window.location=\"../listagemfiliais.php\"	</script>";	
}

mysqli_close($conexao);

?>