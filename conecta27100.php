<?php
$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";

$conexao=mysql_connect("$servidor","$usuario","$senha") or die (mysql_error());

$db=mysql_select_db("bdindicador");
?>
