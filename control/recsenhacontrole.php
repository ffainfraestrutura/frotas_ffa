<?php
session_start();
include("../conecta.php");
$usuariof = $_POST['login'];
$pass = $_POST['pass'];
$cpf = $_POST['cpf'];

$sql = "SELECT cpf FROM bdaniel.tbfuncionario WHERE matricula = '$usuariof'";
    $resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
     
    if ($cpf == $row[0]) {
    	$sql = "UPDATE bdfrota.tbusuario set senha = '$pass' WHERE usuario = '$usuariof'";
    $resultado = mysql_query($sql) or die (mysql_error());
    echo"<script language='javascript' type='text/javascript'>alert('Senha alterada com sucesso');window.location=\"../index.php\"</script>";
    } else {
    	 echo"<script language='javascript' type='text/javascript'>alert('CPF e Matricula divergentes');window.location=\"../recsenha.php\"</script>";
    }

?>
