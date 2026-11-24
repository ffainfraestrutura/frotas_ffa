<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
$dthj = date('Y-m-d ');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
include_once"../func/log.php";

$matriculaf = $_POST['matricula'];
$justificativa = $_POST['justificativa'];
$motivo = $_POST["motivo"];
$latitude = $_POST["latitude"];
$longitude = $_POST["longitude"];

$accessToken = "pk.eyJ1IjoiZnJhbmNhY2ZhYmlvIiwiYSI6ImNsbGNuNHdzcTAyZGYzc28wOTBweTYxaDQifQ.rNTUHVPlUEITsHMYOZvxMA"; // 
$apiUrlReverseGeocode = "https://api.mapbox.com/geocoding/v5/mapbox.places/$longitude,$latitude.json?access_token=$accessToken";
//echo "$apiUrlReverseGeocode";
$response = file_get_contents($apiUrlReverseGeocode);
$data = json_decode($response, true);
$enderecolxl = $data['features'][0]['place_name'];
$enderecolxl = str_replace("'", "", $enderecolxl);

echo "$enderecolxl <br>";

if ($matriculaf <> '') {
	$sql = "INSERT INTO bdfrota.tbtoaqui (matricula, motivo, obs, latitude, longitude, hora, data, endereco) values ('$matriculaf', '$motivo', '$justificativa', '$latitude', '$longitude', '$hoje', '$dthj', '$enderecolxl')";
	$resultado = mysql_query($sql) or die (mysql_error());

	$acao = 'criou rota to aqui';
	$flag = '2';
	$logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');
	echo"<script language='javascript' type='text/javascript'>alert('Endereço adicionado com sucesso!');window.location=\"../index.php\"</script>";
} else {
	echo"<script language='javascript' type='text/javascript'>alert('Erro no To Aqui, faça o login e use o To Aqui novamente');window.location=\"../index.php\"</script>";
}




        
?>
