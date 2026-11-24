<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log.php";
include("../../conecta2.php");


$hoje= date('Y-m-d H:i:s');

$idtbfornecedor = $_POST['idtbfornecedor'];
$matr_autor = $_POST['matr_autor'];

//a query funciona, porém preciso de uma variável que represente a idtbfornecedor para, por ela, validar o update
//todos os campos devem estar preenchidos, porque só tem placeholder; se salvar dessa forma, vai o input vazio pro banco

$sql2= "SELECT status FROM bdfrota.tbfornecedor WHERE idtbfornecedor = '".$idtbfornecedor."'";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

while($row = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
	$status = $row['status'];
}


if($status==1){
	//echo "<script language='javascript' type='text/javascript'> alert('Realmente deseja desativar esse fornecedor?')	</script>";

	$sql1 = "UPDATE bdfrota.tbfornecedor SET status = 0 WHERE idtbfornecedor = '".$idtbfornecedor."'";

	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	  $acao ='Desativou fornecedor';
	  $tipo = 'cadastro';
	  $logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
	  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert('Cadastro de fornecedor desativado com sucesso.');window.location=\"../listagemfornecedores.php\"	</script>";
} else {
	//echo "<script language='javascript' type='text/javascript'> alert('Realmente deseja reativar esse fornecedor?')	</script>";

	$sql1 = "UPDATE bdfrota.tbfornecedor SET status = 1 WHERE idtbfornecedor = '".$idtbfornecedor."'";

	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	  $acao='Ativou fornecedor';
	  $tipo = 'cadastro';
	  $logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
	  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert('Cadastro de fornecedor reativado com sucesso.');window.location=\"../listagemfornecedores.php\"	</script>";
}

?>