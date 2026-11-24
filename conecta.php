<?php
/*$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";

$conexao=mysql_connect("$servidor","$usuario","$senha") or die (mysql_error());

$db=mysql_select_db("bdfrota");*/

$servidor="10.17.0.5";
$usuario="blvp";
$senha="@F3r7n6c2";

$conexao=mysql_connect("$servidor","$usuario","$senha") or die (mysql_error());
$conn = mysqli_connect($servidor, $usuario, $senha, "bdfrota"); 
$db=mysql_select_db("bdfrota");

// Conexao adicional para consultas DEV de presenca (BdPonto).
$servidorBdPonto = '10.17.0.2';
$usuarioDbBdPonto = 'blvp';
$senhaDbBdPonto = '@F3r7n6c2';
$bancoBdPonto = 'BdPonto';
$databaseNameBdPonto = $bancoBdPonto;
$connBdPonto = mysqli_connect($servidorBdPonto, $usuarioDbBdPonto, $senhaDbBdPonto, $bancoBdPonto);

//echo "Atualização no sistema, aguarde um momento...";

?>
