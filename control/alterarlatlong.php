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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupere os valores enviados pelo formulário
    $mattec = $_POST['mattec'];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];
    $idtbos = $_POST["idtbos"];
    $id = $_POST["id"];
    $tabela = $_POST["tabela"];

    // Faça o que quiser com os dados, por exemplo, exiba-os
    echo "Matrícula do Técnico: $mattec<br>";
    echo "Latitude: $latitude<br>";
    echo "Longitude: $longitude<br>";

    $sql = "UPDATE $tabela SET latitude = '$latitude', longitude = '$longitude' where $id = '$idtbos'";
    echo "$sql";
    $resultado = mysql_query($sql) or die (mysql_error());

    echo"<script language='javascript' type='text/javascript'>alert('Alteração concluida!');window.location=\"../detalhes-kmos-zero.php\"</script>";
} else {
    echo "O formulário não foi enviado corretamente.";
}
?>
