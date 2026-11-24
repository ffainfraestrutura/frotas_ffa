<?php
session_start();
$usuariof = $_SESSION['usuario'];
$matriculasup = $_SESSION['matriculasup'];
include("../conecta.php");

$matriculafunc = $_POST["matriculafunc"];

$sql = "UPDATE bdaniel.tbsupfunc SET matsup = '".$matriculasup."' where matfunc=".$matriculafunc."";
$resultado = mysql_query($sql) or die (mysql_error());

echo"<script language='javascript' type='text/javascript'>alert('Opereção realizada com sucesso!');window.location=\"../transferencia.php\"</script>";



?>