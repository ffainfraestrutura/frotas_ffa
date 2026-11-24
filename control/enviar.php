<?php
session_start();
$usuariof = $_SESSION['usuario'];
$matriculasup = $_SESSION['matriculasup'];
include("../conecta.php");
$data = date('Y-m-d');


$sql = "SELECT matfunc FROM bdaniel.tbsupfunc where matsup = '".$matriculasup."'";
$resultado = mysql_query($sql) or die (mysql_error());
while($row = mysql_fetch_array($resultado)){
  $matriculafunc = $row[0];
  $sqla = "SELECT idtbfuncionario, nome FROM bdaniel.tbfuncionario where matricula = '".$matriculafunc."'";
  $resultadoa = mysql_query($sqla) or die (mysql_error());
  $rowa = mysql_fetch_array($resultadoa);
  $id = $rowa[0];
  $matricula = $_POST["matricula$id"];
  $situacau = $_POST["situacau$id"];

  $situacau = utf8_decode($situacau);

  $sqlt = "SELECT * FROM bdaniel.tbpresenca where matricula = '".$matriculafunc."' AND data='".$data." 00:00:00'";
  $resultadot = mysql_query($sqlt) or die (mysql_error());
  if (mysql_num_rows($resultadot) != 0){ 
    echo"<script language='javascript' type='text/javascript'>alert('ERRO: Presenças já inseridas hoje');window.location=\"../index.php\"</script>";
  }


  $sqla = "INSERT INTO bdaniel.tbpresenca (matricula, situacau, data)
  VALUES ('".$matricula."','".$situacau."','".$data."')";
  $resultadoa = mysql_query($sqla) or die (mysql_error());
  echo "$sqla";

}

//echo"<script language='javascript' type='text/javascript'>alert('Presenças enviadas com sucesso');window.location=\"../index.php\"</script>";

?>