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
if ($matricula == '' or $matricula == null) {
    echo"<script language='javascript' type='text/javascript'>alert('ERRO na matricula, você precisa acessar novamente o sistema');window.location=\"../atividadecomplementarclaro.php\"</script>";
}

$tecnico = $_POST["tecnico"];
$dataos = $_POST["data"];
$hora = $_POST["hora"];
$endereco1 = $_POST["endereco1"];
$numero = $_POST["numero"];
$bairro = $_POST["bairro"];
$cidade = $_POST["cidade"];
$estado = $_POST["estado"];
$motivo1 = $_POST["motivo1"];

$enderecocomp = "$endereco1, $numero, $bairro - $cidade, $estado";

$sql = "SELECT nomeaux FROM bdaniel.tbfuncionario where matricula = '$tecnico' and nomeaux is not null";
$resultado = mysql_query($sql) or die (mysql_error());
if (mysql_num_rows($resultado)<=0){
  $sql = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$tecnico'";
  $resultado = mysql_query($sql) or die (mysql_error());
}
$row = mysql_fetch_array($resultado);
$provedor = $row[0];


$address = "$enderecocomp"; // Substitua pelo endereço desejado
$accessToken = "pk.eyJ1IjoiZnJhbmNhY2ZhYmlvIiwiYSI6ImNsbGNuNHdzcTAyZGYzc28wOTBweTYxaDQifQ.rNTUHVPlUEITsHMYOZvxMA"; // Substitua pelo seu token do Mapbox

// URL da API de geocodificação do Mapbox
$apiUrllxl = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . urlencode($address) . ".json?access_token=" . $accessToken;


// Fazer a requisição à API de geocodificação
$response = file_get_contents($apiUrllxl);

$geocodeData = json_decode($response, true);

$enderecocomp = utf8_decode($enderecocomp);
// Verificar se a resposta é válida e se há resultados
if (!empty($geocodeData['features']) && isset($geocodeData['features'][0]['geometry']['coordinates'])) {
    $latitude = $geocodeData['features'][0]['geometry']['coordinates'][1];
    $longitude = $geocodeData['features'][0]['geometry']['coordinates'][0];

   
} else {


    $sql2 = "INSERT INTO bdfrota.tberroosrota (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag) 
                   VALUES('$endereco', '$cidade', '$provedor', '$dataos', '$eta', '$fim', 'CASA', '0')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

    echo"<script language='javascript' type='text/javascript'>alert('Não foi possível converter o endereço em coordenadas.');window.location=\"../atividadecomplementartim.php\"</script>";

    
}

if ($latitude == '' or $longitude == '') {
  
$sql2 = "INSERT INTO bdfrota.tberroosrota (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag) 
                   VALUES('$endereco', '$cidade', '$provedor', '$dataos', '$eta', '$fim', 'CASA', '0')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

    echo"<script language='javascript' type='text/javascript'>alert('Não foi possível converter o endereço em coordenadas.');window.location=\"../atividadecomplementartim.php\"</script>";


}


if ($latitude >= -26 && $latitude <= -20 && $longitude >= -50 && $longitude <= -40) {

$apiUrlReverseGeocode = "https://api.mapbox.com/geocoding/v5/mapbox.places/$longitude,$latitude.json?access_token=$accessToken";

$response = file_get_contents($apiUrlReverseGeocode);

// Verificar se a solicitação foi bem-sucedida
if ($response !== false) {
    // Converter a resposta JSON em um array associativo
    $data = json_decode($response, true);

    // Verificar se há resultados de geocodificação
    if (!empty($data['features'])) {
        // O endereço estará no primeiro resultado (índice 0)
        $enderecolxl = $data['features'][0]['place_name'];
        
        $sql2 = "INSERT INTO bdfrota.tbosrota (provedor, endereco, data, eta, fim, matricula, operador, foratoa, motivo, latitude, longitude) values ('$provedor', '$enderecolxl', '$dataos', '$hora', '$hora', '$tecnico', '$matricula', '1', '$motivo1', '$latitude', '$longitude')";

$resultado2 = mysql_query($sql2) or die (mysql_error());
$ultimoIDInserido = mysql_insert_id();

        $acao = 'Adicionou Endereco';
        $flag = '3';
        $logresult = enviarlog($hoje, $acao, $flag, $tecnico, $matricula, '', '');

        echo"<script language='javascript' type='text/javascript'>alert('Endereço adicionado com sucesso');window.location=\"../atividadecomplementartimvolta.php?ID=$ultimoIDInserido\"</script>";

    } else {
        $sql2 = "INSERT INTO bdfrota.tberroosrota (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag) 
                   VALUES('$endereco', '$cidade', '$provedor', '$dataos', '$eta', '$fim', 'CASA', '0')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

    echo"<script language='javascript' type='text/javascript'>alert('Não foi possível converter o endereço em coordenadas.');window.location=\"../atividadecomplementartim.php\"</script>";
    }
} else {
    $sql2 = "INSERT INTO bdfrota.tberroosrota (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag) 
                   VALUES('$endereco', '$cidade', '$provedor', '$dataos', '$eta', '$fim', 'CASA', '0')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

    echo"<script language='javascript' type='text/javascript'>alert('Não foi possível converter o endereço em coordenadas.');window.location=\"../atividadecomplementartim.php\"</script>";
}

    
} else {
      $sql2 = "INSERT INTO bdfrota.tberroosrota (endereco, cidade, provedor, data, eta, fim, numero_de_ordem, flag) 
                   VALUES('$endereco', '$cidade', '$provedor', '$dataos', '$eta', '$fim', 'CASA', '0')";

        $resultado2 = mysql_query($sql2) or die (mysql_error());

    echo"<script language='javascript' type='text/javascript'>alert('Não foi possível converter o endereço em coordenadas.');window.location=\"../atividadecomplementartim.php\"</script>";
}





?>
