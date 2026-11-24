<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");


$hoje=date('Y-m-d H:i:s');

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

$sql1 = "UPDATE bdfrota.tbcnh SET numcnh ='".$cnh."' , validade = '".$validade."', uf = '".$uf."', categoria = '".$categoria."', matricula = '".$matricula."' , pontos = '".$pontos."', consulta = '".$consulta."', suspensa = '".$suspensa."' WHERE matricula = '".$matricula."'";
$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  $acao='Editou cnh';
  $tipo = 'cadastro';
  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placa);
  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

echo "<script language='javascript' type='text/javascript'> alert('Cadastro de CNH editado com sucesso.');window.location=\"../listagemgeralcondutores.php\"	</script>";
?>