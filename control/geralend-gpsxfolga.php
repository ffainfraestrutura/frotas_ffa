<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$diai = $_POST["diai"];
$diaf = $_POST["diaf"];
$matriculaf = $_POST["matriculatec"];
$nometec = $_POST["nometec"];
$placatec = $_POST["placatec"];
$matsup = $_POST["matsupf"];

include"../conecta.php";
include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");


$sql = "SELECT id, latitude, longitude FROM bdfrota.tbrelatoriogpsfalta where data > '$diai 00:00:00' and data < '$diaf 23:39:59' and matricula = '$matriculaf' and endereco = 'sem endereco' order by data";
$resultado = mysql_query($sql) or die (mysql_error());
      
while ($row = mysql_fetch_array($resultado)){

$id = $row[0];
$latitude = $row[1];
$longitude = $row[2];

$endereco = 'teste';

$sql2 = "UPDATE bdfrota.tbrelatoriogpsfalta SET endereco = '$endereco' where id = '$id'";
$resultado2 = mysql_query($sql2) or die (mysql_error());

}

echo "<form id='myForm' action='../detalhes-gpsxfolga.php' method='POST'>
                            <input type='hidden' name='matriculatec' value='$matriculaf'>
                            <input type='hidden' name='nometec' value='$nometec'>
                            <input type='hidden' name='placatec' value='$placatec'>
                            <input type='hidden' name='diaf' value='$diaf'>
                            <input type='hidden' name='diai' value='$diai'>
                            <input type='hidden' name='matsupf' value='$matsup'>
</form>'";



echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("myForm").submit();
    });
</script>';



?>