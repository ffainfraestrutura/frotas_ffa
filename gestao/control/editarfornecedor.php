<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log.php";
include("../../conecta2.php");


$hoje = date('Y-m-d H:i:s');

$idtbfornecedor = $_POST['idtbfornecedor'];

$nomefant= utf8_decode($_POST['nomefant']);
$razsocial=utf8_decode($_POST['razsocial']);
$cnpj = $_POST['cnpj'];
$iestadual = $_POST['iestadual'];
$imunicipal = $_POST['imunicipal'];
$tipo = $_POST['tipo'];
$telefone1 = $_POST['telefone1'];
$telefone2 = $_POST['telefone2'];
$email = $_POST['email'];
$contato = utf8_decode($_POST['contato']);
$endereco = utf8_decode($_POST['endereco']);
$complemento = utf8_decode($_POST['complemento']);
$cep = $_POST['cep'];
$bairro = utf8_decode($_POST['bairro']);
$cidade = utf8_decode($_POST['cidade']);
$estado = $_POST['estado'];
$matr_autor = $_POST['matr_autor'];

//print $nomefant;
if(strpos($nomefant, "'") !== false){
	$nomefant = str_replace("'", "", $nomefant);
}

//print $nomefant;

$sql1 = "UPDATE bdfrota.tbfornecedor SET tipo = '".$tipo."', fantasia = '".$nomefant."', razaosocial = '".$razsocial."', telefone = '".$telefone1."', telefone2 = '".$telefone2."', email = '".$email."', contato = '".$contato."', endereco = '".$endereco."', complemento = '".$complemento."', bairro = '".$bairro."', cidade = '".$cidade."', estado = '".$estado."', CNPJ = '".$cnpj."', inscestadual = '".$iestadual."', inscmunicipal = '".$imunicipal."', cep = '".$cep."' WHERE idtbfornecedor = '".$idtbfornecedor."';";

//$sql1 = "UPDATE bdfrota.tbfornecedor SET fantasia = '$nomefant' WHERE idtbfornecedor = '$idtbfornecedor';";

$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Editou fornecedor';
$tipo = 'cadastro';
$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
//echo "$hoje, $acao, '', $matr_autor, $tipo, ''";

echo "<script language='javascript' type='text/javascript'> alert('Cadastro de fornecedor editado com sucesso.');window.location=\"../listagemfornecedores.php\"	</script>";

?>