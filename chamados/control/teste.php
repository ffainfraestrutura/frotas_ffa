<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje= date('Y-m-d H:i:s');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");
include("../../conecta2.php");

$autor=$_POST['autor'];
$ccusto1 = $_POST['ccusto1'];

print $autor."<br>".$ccusto1;



?>