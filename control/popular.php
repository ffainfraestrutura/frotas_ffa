<?php
session_start();
$usuariof = $_SESSION['usuario'];
$matriculasup = $_SESSION['matriculasup'];
include("../conecta.php");
$data = date('2022-01-01');

$i = 1;

while ($i <= 31) {
  
  $sql = "SELECT matfunc FROM bdaniel.tbsupfunc where matsup = '000052'";
  $resultado = mysql_query($sql) or die (mysql_error());
  while($row = mysql_fetch_array($resultado)){
  $matriculafunc = $row[0];
  $sitaux = rand(1, 4);

  if ($sitaux == 1) {
    $situacau = 'atestado';
  } elseif ($sitaux == 2) {
    $situacau = 'falta';
  } elseif ($sitaux == 3) {
    $situacau = 'folga';
  } else {
    $situacau = 'presente';
  }

  $sqla = "INSERT INTO bdaniel.tbpresenca (matricula, situacau, data)
  VALUES ('".$matriculafunc."','".$situacau."','".$data."')";
  $resultadoa = mysql_query($sqla) or die (mysql_error());
  echo "$sqla <br>";

}
  $data = date('Y-m-d', strtotime($data .' +1 day'));
  $i = $i + 1;
}


//echo"<script language='javascript' type='text/javascript'>alert('Presenças enviadas com sucesso');window.location=\"../index.php\"</script>";

?>