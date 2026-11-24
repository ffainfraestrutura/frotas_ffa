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
include("../conecta02.php");
include_once"../func/log.php";

$matriculaf = $_POST['matriculatec'];

$sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
//echo "$sql";
$resultado = mysql_query($sql) or die (mysql_error());
$row = mysql_fetch_array($resultado);
$idtbsupervisor = $row[0];

if(empty($idtbsupervisor)){
    echo"<script language='javascript' type='text/javascript'>alert('Erro: Supervisor não encontrado! ');window.location=\"../transferirtec100326.php\"</script>";
    exit;
}

$sql = "UPDATE bdfrota.tbusuario SET idtbsupervisor = '$idtbsupervisor' where matricula = '$matriculaf'";
//echo "$sql";

// UPDATE no servidor A (principal)
$resultado = mysql_query($sql) or die (mysql_error());
// UPDATE no servidor B (secundário)
$resultado_b = mysqli_query($conn, $sql) or die (mysqli_error($conn));

$acao = 'Transferiu tecnico';
$flag = '7';
$logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

echo"<script language='javascript' type='text/javascript'>alert('Transferencia concluida em ambos os servidores!');window.location=\"../transferirtec100326.php\"</script>";

    





        
?>
