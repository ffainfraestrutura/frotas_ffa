<?php
session_start();
include("../conecta.php");
$usuariof = $_POST['login'];
$pass = $_POST['pass'];
    $sql = "UPDATE bdfrota.tbusuario set senha = '$pass', pacesso = 1 WHERE usuario = '$usuariof'";
    $resultado = mysql_query($sql) or die (mysql_error());
    echo"<script language='javascript' type='text/javascript'>alert('Senha alterada com sucesso');window.location=\"../index.php\"</script>";
?>
