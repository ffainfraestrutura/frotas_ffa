<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log.php";
include("../../conecta2.php");



$hoje= date('Y-m-d H:i:s');

$idtbcondutor = $_POST['idtbcondutor'];
$matr_autor = $_POST['matr_autor'];
//print $idtbcondutor."<br>";

$sql2= "SELECT ativo, matricula FROM bdfrota.tbcondutor WHERE idtbcondutor = '".$idtbcondutor."';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

while($row = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
	$ativo = $row['ativo'];
	$matricula= $row['matricula'];
}

//print $ativo;

if($ativo==1){
	echo "<script language='javascript' type='text/javascript'> alert('Realmente deseja desativar esse condutor?')	</script>";

	$sql1 = "UPDATE bdfrota.tbcondutor SET ativo = 0 WHERE idtbcondutor = '".$idtbcondutor."'";

	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	/*$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$hoje', matcond='' WHERE placa ='$placa' ";
  	$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));*/

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  $acao='Desativou condutor';
  $tipo = 'cadastro';
  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, '');
  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert('Cadastro de condutor desativado com sucesso.');window.location=\"../listagemcondutores.php\"	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert('Realmente deseja reativar esse condutor?')	</script>";

	$sql1 = "UPDATE bdfrota.tbcondutor SET ativo = 1 WHERE idtbcondutor = '".$idtbcondutor."';";

	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  $acao='Ativou condutor';
  $tipo = 'cadastro';
  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, '');
  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert('Cadastro de condutor reativado com sucesso.');window.location=\"../listagemcondutores.php\"	</script>";
}

?>