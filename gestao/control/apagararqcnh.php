<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");

include_once"../../func/log3.php";
include("../../conecta03.php");


$hoje=date('Y-m-d H:i:s');

$id= utf8_decode($_POST['idtbcnh']);
$escolha=$_POST['escolha'];
$matr_autor=$_POST['matr_autor'];

//print "$id, $escolha";

$sql="SELECT doc1, doc2, matricula FROM bdfrota.tbcnh WHERE idtbcnh='$id';";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
$row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
  $doc1 = $row['doc1'];
  $doc2 = $row['doc2'];
  $matricula = $row['matricula'];

if($escolha == 1){
  if(!empty($doc2)){  
    $sql1 = "UPDATE bdfrota.tbcnh SET doc2='' WHERE idtbcnh = '$id'";
  
  }else{
    $sql1 = "UPDATE bdfrota.tbcnh SET  doc1='' WHERE idtbcnh = '$id'";
  }

  $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

  //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  $log = new Log($con);
  $acao='Apagou arquivo de cnh';
  $tipo = 'cadastro';
  $logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, '');
  
  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";
  //print $sql1;

  echo "<script language='javascript' type='text/javascript'> alert('Cadastro de CNH editado com sucesso.');window.close();   </script>";

}else{

  echo "<script language='javascript' type='text/javascript'>alert('Sem alteração.'); window.close();  </script>";
  
}




//echo "<script language='javascript' type='text/javascript'> alert('Cadastro de CNH editado com sucesso.');window.location=\"../listagemgeralcondutores.php\"	</script>";
?>