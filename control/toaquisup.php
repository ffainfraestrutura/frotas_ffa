<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
$dthj = date('Y-m-d ');
session_start();

// Validação de sessão
if(!isset($_SESSION['nome']) || !isset($_SESSION['usuario']) || !isset($_SESSION['matricula'])) {
  header("Location: ../index.php");
  exit;
}

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

include("../conecta.php");
include_once"../func/log.php";

// Validação dos dados POST
if(!isset($_POST['matricula']) || !isset($_POST['motivo']) || !isset($_POST['justificativa'])) {
  header("Location: ../toaquisup.php?erro=dados_incompletos");
  exit;
}

$matriculaf = $_POST['matricula'];
$justificativa = $_POST['justificativa'];
$motivo = $_POST["motivo"];
$latitude = $_POST["latitude"];
$longitude = $_POST["longitude"];

// Sanitização dos dados
$matriculaf = mysql_real_escape_string($matriculaf);
$justificativa = mysql_real_escape_string($justificativa);
$motivo = mysql_real_escape_string($motivo);
$latitude = mysql_real_escape_string($latitude);
$longitude = mysql_real_escape_string($longitude);

$accessToken = "pk.eyJ1IjoiZnJhbmNhY2ZhYmlvIiwiYSI6ImNsbGNuNHdzcTAyZGYzc28wOTBweTYxaDQifQ.rNTUHVPlUEITsHMYOZvxMA";
$apiUrlReverseGeocode = "https://api.mapbox.com/geocoding/v5/mapbox.places/$longitude,$latitude.json?access_token=$accessToken";

// Obter endereço via API
$response = file_get_contents($apiUrlReverseGeocode);
$data = json_decode($response, true);

// Validar resposta da API
if(isset($data['features'][0]['place_name'])) {
  $enderecolxl = $data['features'][0]['place_name'];
  $enderecolxl = str_replace("'", "", $enderecolxl);
  $enderecolxl = mysql_real_escape_string($enderecolxl);
} else {
  $enderecolxl = mysql_real_escape_string("Endereço não encontrado (Lat: $latitude, Long: $longitude)");
}

// Inserir no banco de dados
$sql = "INSERT INTO bdfrota.tbtoaquisup (matricula, motivo, obs, latitude, longitude, hora, data, endereco) 
        VALUES ('$matriculaf', '$motivo', '$justificativa', '$latitude', '$longitude', '$hoje', '$dthj', '$enderecolxl')";
$resultado = mysql_query($sql);

if($resultado) {
  // Log da ação
  $acao = 'supervisor criou rota to aqui';
  $flag = '2';
  $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');
  
  // Salvar a sessão antes de redirecionar
  session_write_close();
  
  // Redireciona para a mesma página com sucesso
  header("Location: ../toaquisup.php?sucesso=1&endereco=" . urlencode($enderecolxl));
  exit;
} else {
  // Salvar a sessão antes de redirecionar
  session_write_close();
  
  // Redireciona para a mesma página com erro
  header("Location: ../toaquisup.php?erro=banco_dados");
  exit;
}
?>
