<?php
$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";

//$conexao=mysqlI_connect("$servidor","$usuario","$senha") or die (mysql_error());
$conexao=mysqli_connect("$servidor","$usuario","$senha") or die (mysqli_connect_error());

$db=mysql_select_db("bdindicador");
?>
