<?php
header('Content-Type: application/json; charset=utf-8');
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$matriculaLogada = isset($_SESSION['matricula']) ? $_SESSION['matricula'] : '';

include("../conecta.php");
include_once "../func/log.php";

function responderJson($sucesso, $mensagem, $dados = array())
{
    echo json_encode(array_merge(array(
        'sucesso' => $sucesso,
        'mensagem' => $mensagem
    ), $dados));
    exit();
}

$accessToken = "pk.eyJ1Ijoic3Vwb3J0ZWRlc2Vudm9sdmltZW50byIsImEiOiJjbXNnN2g4cHYwM25nMnlvdDk4NWl0ajF5In0.iM2qaq-C46aD0MF_KeU4fw";

function consultarMapboxToAqui($apiUrlReverseGeocode)
{
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrlReverseGeocode);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Frotas-ToAqui/1.0'));

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError != '') {
            return array('erro' => 'Não foi possível consultar a API de geocodificação via cURL: ' . $curlError);
        }

        if ($httpCode == 401) {
            return array('erro' => 'A API de geocodificação retornou HTTP 401. Token Mapbox inválido, expirado ou sem permissão.');
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            return array('erro' => 'A API de geocodificação retornou HTTP ' . $httpCode . '.');
        }

        return array('response' => $response);
    }

    $context = stream_context_create(array(
        'http' => array(
            'method' => 'GET',
            'timeout' => 30,
            'header' => "User-Agent: Frotas-ToAqui/1.0\r\n"
        ),
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false
        )
    ));

    $response = file_get_contents($apiUrlReverseGeocode, false, $context);
    if ($response === false) {
        $erroPhp = error_get_last();
        $detalhe = isset($erroPhp['message']) ? $erroPhp['message'] : 'sem detalhe retornado pelo PHP';
        return array('erro' => 'Não foi possível consultar a API de geocodificação via file_get_contents: ' . $detalhe);
    }

    return array('response' => $response);
}

function converterCoordenadasParaEnderecoToAqui($latitude, $longitude, $accessToken)
{
    if ($accessToken == '') {
        return array('erro' => 'Token Mapbox não configurado. Configure MAPBOX_ACCESS_TOKEN ou MAPBOX_TOKEN no portal.');
    }

    if ($latitude == '' || $longitude == '') {
        return array('erro' => 'Este To Aqui não possui latitude e longitude para converter.');
    }

    $latitudeNormalizada = str_replace(',', '.', trim($latitude));
    $longitudeNormalizada = str_replace(',', '.', trim($longitude));

    if (!is_numeric($latitudeNormalizada) || !is_numeric($longitudeNormalizada)) {
        return array('erro' => 'Latitude ou longitude inválida para este To Aqui.');
    }

    if ($latitudeNormalizada < -90 || $latitudeNormalizada > 90 || $longitudeNormalizada < -180 || $longitudeNormalizada > 180) {
        return array('erro' => 'Latitude ou longitude fora do intervalo válido para este To Aqui.');
    }

    $apiUrlReverseGeocode = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . $longitudeNormalizada . "," . $latitudeNormalizada . ".json?access_token=" . $accessToken;
    $consulta = consultarMapboxToAqui($apiUrlReverseGeocode);

    if (isset($consulta['erro'])) {
        return array('erro' => $consulta['erro']);
    }

    $data = json_decode($consulta['response'], true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        return array('erro' => 'A API retornou uma resposta inválida: ' . json_last_error_msg());
    }

    if (empty($data['features']) || !isset($data['features'][0]['place_name'])) {
        return array('erro' => 'A API não retornou endereço para essas coordenadas.');
    }

    return array('endereco' => str_replace("'", '', $data['features'][0]['place_name']));
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    responderJson(false, 'Método inválido.');
}

$idtbtoaqui = isset($_POST['idtbtoaqui']) ? trim($_POST['idtbtoaqui']) : '';
if ($idtbtoaqui == '' || !is_numeric($idtbtoaqui)) {
    responderJson(false, 'ID do To Aqui inválido.');
}

$idtbtoaquiSql = mysql_real_escape_string($idtbtoaqui);
$sql = "SELECT idtbtoaqui, matricula, latitude, longitude, endereco FROM bdfrota.tbtoaqui WHERE idtbtoaqui = '$idtbtoaquiSql' LIMIT 1";
$resultado = mysql_query($sql);

if (!$resultado) {
    responderJson(false, 'Erro ao buscar To Aqui: ' . mysql_error());
}

if (mysql_num_rows($resultado) == 0) {
    responderJson(false, 'To Aqui não encontrado.');
}

$row = mysql_fetch_assoc($resultado);
if (trim($row['endereco']) != '') {
    responderJson(true, 'Endereço já estava preenchido.', array('endereco' => $row['endereco']));
}

$conversao = converterCoordenadasParaEnderecoToAqui($row['latitude'], $row['longitude'], $accessToken);

if (isset($conversao['erro'])) {
    responderJson(false, $conversao['erro']);
}

$endereco = mysql_real_escape_string($conversao['endereco']);
$sqlUpdate = "UPDATE bdfrota.tbtoaqui SET endereco = '$endereco' WHERE idtbtoaqui = '$idtbtoaquiSql'";
$resultadoUpdate = mysql_query($sqlUpdate);

if (!$resultadoUpdate) {
    responderJson(false, 'Endereço convertido, mas não foi possível salvar: ' . mysql_error());
}

$hoje = date('Y-m-d H:i:s');
enviarlog($hoje, 'converteu endereco to aqui no aceite', '2', $row['matricula'], $matriculaLogada, '', '');

responderJson(true, 'Endereço convertido e salvo com sucesso.', array('endereco' => $conversao['endereco']));
?>
