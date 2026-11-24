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
include_once"../func/log.php";

$matriculaf = $_POST["matricula"];
$cotadiaria = $_POST["cotadiaria"];
$cotadiaria = str_replace(",", ".", $cotadiaria);
$valantigo = $_POST["valantigo"];

$sql2 = "SELECT * FROM bdfrota.tbcotaindividual where matricula = '$matriculaf'";
                $resultado2 = mysql_query($sql2) or die (mysql_error());
                    if (mysqli_num_rows($resultado2) <= 0) {
        $sql2 = "INSERT INTO bdfrota.tbcotaindividual (matricula, cotadiaria) VALUES ('$matriculaf','$cotadiaria')";
$resultado2 = mysql_query($sql2) or die (mysql_error());
    } else {
               $sql2 = "UPDATE bdfrota.tbcotaindividual set cotadiaria = '$cotadiaria' where matricula = '$matriculaf'";
$resultado2 = mysql_query($sql2) or die (mysql_error());
    }




        $acao = 'Alterou cota diária';
        $flag = '1';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valantigo, $cotadiaria);


    echo"<script language='javascript' type='text/javascript'>alert('Nova cota adicionada com sucesso');window.location=\"../../cotadiaria.php\"</script>";


?>
