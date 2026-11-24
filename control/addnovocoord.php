<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos estão definidos
    if (isset($_POST['matsup'])) {
        // Obtém os valores dos campos
        $matsup = $_POST['matsup'];

        $sql = "SELECT * FROM bdfrota.tbusuario WHERE matricula = '$matsup '";
    	$resultado = mysql_query($sql) or die (mysql_error());
      	if (mysql_num_rows($resultado)<>'0'){ 
      		$sql = "UPDATE bdfrota.tbusuario SET perfil = '2' WHERE matricula = '$matsup '";
    		$resultado = mysql_query($sql) or die (mysql_error());
      	} else {
      		echo"<script language='javascript' type='text/javascript'>alert('Matricula sem cadastro no sistema');window.location=\"../painel-suporte/addnovosup.php\"</script>";
      	}


      	$sql = "SELECT * FROM bdfrota.tbcoord WHERE matricula = '$matsup '";
    	$resultado = mysql_query($sql) or die (mysql_error());
      	if (mysql_num_rows($resultado)<>'0'){ 
    		echo"<script language='javascript' type='text/javascript'>alert('Coordenador já existe');window.location=\"../painel-suporte/addnovosup.php\"</script>";
      	} else {
      		$sql = "INSERT INTO bdfrota.tbcoord (matricula, valor, orcrecebido, totcotaextra, saldo) VALUES ('$matsup', '0', '0', '0', '0')";
    		$resultado = mysql_query($sql) or die (mysql_error());
      		echo"<script language='javascript' type='text/javascript'>alert('Coordenador Adicionado com sucesso');window.location=\"../painel-suporte/addnovosup.php\"</script>";
      	}

        // Redireciona para outra página após o processamento (se necessário)
        // header("Location: outra_pagina.php");
        // exit;
    } else {
        // Campos não definidos
        echo"<script language='javascript' type='text/javascript'>alert('Erro no envio dos dados');window.location=\"../painel-suporte/addnovosup.php\"</script>";
    }
} else {
    // Formulário não enviado por POST
    
        echo"<script language='javascript' type='text/javascript'>alert('Erro no envio dos dados');window.location=\"../painel-suporte/addnovosup.php\"</script>";
}
?>
