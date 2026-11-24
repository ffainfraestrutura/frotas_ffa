<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log.php";

include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$idtbfilial = $_POST['idtbfilial'];
$matr_autor = $_POST['matr_autor'];

//acredito que não preciso da variáveis abaixo, mas deixar guardadas para desencargo de consciência
/*$codigo = $_POST['codigo'];
$descricao = utf8_decode($_POST['descricao']);*/

//a query funciona, porém preciso de uma variável que represente a idtbfilialpara, por ela, validar o update

$sql2= "SELECT ativo FROM bdfrota.tbfilial WHERE idtbfilial= '$idtbfilial' ";

$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

while($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
	$ativo = $row2['ativo'];
}

if($ativo==1){
	//echo "<script language='javascript' type='text/javascript'> alert('Realmente deseja desativar essa filial?')	</script>";

	$sql1 = "UPDATE bdfrota.tbfilial SET ativo = 0 WHERE idtbfilial = '".$idtbfilial."'";
	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
	
	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	  $acao='Desativou filial';
	  $tipo = 'cadastro';
	  $logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
	  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";							

	echo "<script language='javascript' type='text/javascript'> alert('Cadastro de filial desativada com sucesso.');window.location=\"../listagemfiliais.php\"	</script>";
} else {
	//echo "<script language='javascript' type='text/javascript'> alert('Realmente deseja reativar essa filial?')	</script>";

	$sql1 = "UPDATE bdfrota.tbfilial SET ativo = 1 WHERE idtbfilial = '".$idtbfilial."';";

	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  	$acao='Ativou filial';
  	$tipo = 'cadastro';
  	$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
  	//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert('Cadastro de filial reativada com sucesso.');window.location=\"../listagemfiliais.php\"	</script>";
}

?>