<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

//$nomefant = mysql_real_escape_string($_POST['nomefant']);
//$nomefant1= utf8_decode($nomefant);
$nomefant= utf8_decode($_POST['nomefant']);
//$razsocial = mysql_real_escape_string($_POST['razsocial']);
//$razsocial1=utf8_decode($razsocial);
$razsocial=utf8_decode($_POST['razsocial']);
$cnpj = $_POST['cnpj'];
$iestadual = $_POST['iestadual'];
$imunicipal = $_POST['imunicipal'];
$status = $_POST['status'];
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

//echo $nomefant, $razsocial, $cnpj, $iestadual, $imunicipal, $status, $tipo, $telefone1, $telefone2, $email, $contato, $endereco, $complemento, $cep, $bairro, $cidade, $estado;

$sql2 = "SELECT * FROM bdfrota.tbfornecedor WHERE CNPJ = '$cnpj';";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
$row2 = mysqli_num_rows($resultado2);

if($row2 == 0){
	$sql1 = "INSERT INTO bdfrota.tbfornecedor(status, tipo, fantasia, razaosocial, telefone, telefone2, email, contato, endereco, complemento, bairro, cidade, estado, CNPJ, inscestadual, inscmunicipal, cep) VALUES ('1', '".$tipo."', '".$nomefant."', '".$razsocial."', '".$telefone1."', '".$telefone2."', '".$email."','".$contato."', '".$endereco."','".$complemento."', '".$bairro."', '".$cidade."', '".$estado."', '".$cnpj."', '".$iestadual."', '".$imunicipal."', '".$cep."');";
	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	$acao='Cadastrou fornecedor';
	$tipo = 'cadastro';
	$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');

	//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert('Fornecedor cadastrado com sucesso.');window.location=\"../listagemfornecedores.php\"	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert('Fornecedor já cadastrado.');window.location=\"../listagemfornecedores.php\"	</script>";	
}
?>