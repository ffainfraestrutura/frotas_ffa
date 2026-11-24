<?php
include("../conecta.php");

$matricula = $_GET['matricula'];
$sql = "SELECT dataaceite FROM bdfrota.tbmemorando WHERE matricula = '$matricula' AND dataaceite IS NOT NULL";
$resultado = mysql_query($sql) or die(mysql_error());

if (mysql_num_rows($resultado) > 0) {
    echo json_encode(["assinado" => true]); // Termo assinado
} else {
    echo json_encode(["assinado" => false]); // Termo não assinado
}
?>
