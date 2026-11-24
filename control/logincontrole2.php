<?php
session_start();
include("../conecta.php");
$usuariof = $_POST['login'];
$pass = $_POST['pass'];
    $sql = "SELECT nome, tipo FROM bdindicador.tbusuarioindicadores WHERE usuario = '$usuariof' AND senha = '$pass'";
    $resultado = mysql_query($sql) or die (mysql_error());
      if (mysql_num_rows($resultado)<=0){
        echo"<script language='javascript' type='text/javascript'>alert('Login e/ou senha incorretos');window.location=\"../ass.php\"</script>";
        die();
      }else{
        $row = mysql_fetch_array($resultado);
        $nome = $row['nome'];
        $tipo = $row['tipo'];
        $_SESSION['perfil'] = $tipo;
        $_SESSION['nome'] = $nome;
        $_SESSION['usuario'] = $usuariof;
        echo "<script>window.location=\"../assinatura.php\"</script>";
      }
?>