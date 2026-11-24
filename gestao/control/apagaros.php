<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");

include_once"../../func/log3.php";
include("../../conecta03.php");


$hoje=date('Y-m-d H:i:s');

//$idtbmanprev = utf8_decode($_POST['idtbmanprev']);
$idtbmanprev = $_POST['idtbmanprev'];
$placa=$_POST['placa'];
$matr_autor = $_POST['matr_autor'];
$escolha = $_POST['escolha'];

/*print "$idtbmanprev - $placa - $matr_autor - $escolha";*/

if($escolha == 0){
  echo "<script language='javascript' type='text/javascript'> alert('Processo cancelado.');window.location=\"http://ffasip.ddns.net:4242/frotas-teste/gestao/listagemmanutencaoaberta.php\"; </script>";

  //window.close();

}elseif($escolha == 1){
  $sql = "DELETE FROM bdfrota.tbmanprev WHERE idtbmanprev='$idtbmanprev';";

  $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

  //print $sql;

  echo "<script language='javascript' type='text/javascript'> alert('Registro de manutenção apagado com sucesso.');window.location=\"http://ffasip.ddns.net:4242/frotas/gestao/listagemmanutencaoaberta.php\";  </script>";

  $log = new Log($con);
  //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  $acao='Apagou registro de manutencao';
  $tipo = 'cadastro';
  $logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, $placa);

}

//window.location=\"../listagemveiculos.php\" 

?>