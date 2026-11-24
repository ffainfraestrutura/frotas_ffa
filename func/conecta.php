<?php
$servidor="10.17.0.5";
$usuario="blvp";
$senha="@F3r7n6c2";

$conexao=mysql_connect("$servidor","$usuario","$senha") or die (mysql_error());

$db=mysql_select_db("bdindicador");
?>
