<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");


$hoje = date('Y-m-d H:i:s');

$idtbfilial = $_POST['idtbfilial'];
$codigo = $_POST['codigo'];
$descricao = utf8_decode($_POST['descricao']);
$matr_autor = $_POST['matr_autor'];

//a query funciona, porém preciso de uma variável que represente a idtbfilial para, por ela, validar o update

$sql1 = "UPDATE bdfrota.tbfilial SET codigo = '".$codigo."', descricao = '".$descricao."' WHERE idtbfilial = '".$idtbfilial."';";

$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Editou filial';
$tipo = 'cadastro';
$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
//print "$hoje, $acao, '', $matr_autor, $tipo, $placa";

echo "<script language='javascript' type='text/javascript'> alert ('Cadastro de filial editado com sucesso.');window.location=\"../listagemfiliais.php\"	</script>";

?>