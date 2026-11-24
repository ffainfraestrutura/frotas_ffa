<?php
include "conecta.php";

$matricula = $_GET['matricula'];
$response = array();

$sql = "SELECT motivo, obs, hora, endereco, idtbtoaqui, latitude, longitude FROM bdfrota.tbtoaqui where matricula = '$matricula' and aceite = 0 order by data";
$resultado = mysql_query($sql) or die (mysql_error());
while($row = mysql_fetch_array($resultado)){
    $response[] = array(
        "motivo" => $row[0],
        "obs" => $row[1],
        "hora" => $row[2],
        "endereco" => $row[3],
        "idtbtoaqui" => $row[4],
        "latitude" => $row[5],
        "longitude" => $row[6]
    );
}

echo json_encode($response);
?>