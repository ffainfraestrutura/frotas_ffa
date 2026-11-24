<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');

//include_once"../../func/log.php";
include_once"../../func/log3.php";
include("../../conecta03.php");

$hoje= date('Y-m-d H:i:s');

$nome= $_POST['nome'];
$matricula = $_POST['matricula'];
$matr_autor = $_POST['frotas'];
$placa = $_POST['placa'];
$cartao = $_POST['cartao'];
$dataassoc = $_POST['dataassoc'];

if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} else {
  $placa = str_replace(" ", "", $placa);
}

$sql="SELECT placa FROM bdfrota.tbveiculo WHERE placa='$placa' ";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
$linha = mysqli_num_rows($resultado);

if($linha>0){

  /*$sql1="SELECT MAX(idtbcondutor) AS idtbcondutor FROM bdfrota.tbcondutor WHERE placaassoc='$placa' ";
  $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
    $idtbcondutor = $row1['idtbcondutor'];*/


  /*$sql2="SELECT datadissoc FROM bdfrota.tbcondutor WHERE idtbcondutor='$idtbcondutor' ";
  $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
  $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
    $datad = $row2['datadissoc'];

    $datad1= explode(" ", $datad);
    $datad2=$datad1[0];*/

  //print $nome."<br>".$matricula."<br>".$placa."<br>".$cartao."<br>".$dataassoc."<br>".$datad2;
  //idtbcondutor, nome, matricula, ativo, placaassoc, cartaoassoc, dataassoc, datadissoc

  /*$sql2="SELECT ativo FROM bdfrota.tbcondutor WHERE idtbcondutor='$idtbcondutor' ";
  $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
  $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
    $ativo = $row2['ativo'];*/

  $sql1="SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo='1' ";
  $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
  $row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
  $linha1 = mysqli_num_rows($resultado1);

  //if($datad2 != '0000-00-00'){
  //if($ativo != '1'){
  if($linha1<=0){

    $sql="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, cartaoassoc, dataassoc) VALUES ('$nome', '$matricula', '1', '$placa', '$cartao', '$dataassoc'); ";

    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

    $sql4 = "UPDATE bdfrota.tbveiculo SET matcond= '$matricula',  datamovimentacao='$hoje', statusvel='1', status='1' WHERE placa ='$placa' ";
    $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

    //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
    $log = new Log($con);
    $acao='Associou veiculo';
    $tipo = 'cadastro';
    $logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placa);

    //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";


    echo "<script> window.alert('Condutor vinculado com sucesso.');window.close();</script>";
    
    /*window.location=\"../listagemcondutores.php\"*/
  } else {
     echo "<script> window.alert('Placa já vinculada a outro condutor.');window.close();</script>";
  }

}else {
  echo "<script> window.alert('Placa não cadastrada no sistema.');window.close(); </script>";
}


//mysqli_close($con);
?>