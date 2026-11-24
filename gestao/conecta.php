<?php
$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";

$conexao=mysql_connect("$servidor","$usuario","$senha") or die (mysql_error());

$db=mysql_select_db("bdindicador");

// Define o charset para UTF-8
mysql_set_charset('utf8', $conexao);
?>
