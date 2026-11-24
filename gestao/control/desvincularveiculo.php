<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');

include_once"../../func/log3.php";
include("../../conecta03.php");


$hoje= date('Y-m-d H:i:s');

$idtbcondutor = $_POST['idtbcondutor'];
$datadissoc = $_POST['datad'];
$placa = $_POST['placa'];
$matr_autor = $_POST['matr_autor'];
$matricula = $_POST['matricula'];

//print $idtbcondutor."<br>".$datadissoc."<br>";
/*$sql1="SELECT placaassoc FROM bdfrota.tbcondutor WHERE idtbcondutor = '".$idtbcondutor."'; ";
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
$placa = $row1[0];*/

//print "$idtbcondutor; $datadissoc; $matr_autor";

$sql="UPDATE bdfrota.tbcondutor SET datadissoc ='$datadissoc', ativo = 0 WHERE idtbcondutor='$idtbcondutor'; ";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$hoje', dtdevolucao='$datadissoc', matcond='', statusvel='2' WHERE placa ='$placa' ";
$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$log = new Log($con);
$acao ='Desvinculou veiculo';
$tipo = 'cadastro';
$logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placa);
//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

echo "<script> window.alert('Condutor disvinculado com sucesso.');window.close();</script>";
/*window.location=\"../listagemcondutores.php\"*/
?>