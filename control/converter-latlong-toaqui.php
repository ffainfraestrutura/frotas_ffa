<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '1') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

include "conecta.php";
include_once "./func/log.php";

$accessToken = "pk.eyJ1IjoiZnJhbmNhY2ZhYmlvIiwiYSI6ImNsbGNuNHdzcTAyZGYzc28wOTBweTYxaDQifQ.rNTUHVPlUEITsHMYOZvxMA";
$mensagem = '';
$resultadoConversao = null;

function consultarMapbox($apiUrlReverseGeocode)
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

function converterCoordenadasParaEndereco($latitude, $longitude, $accessToken)
{
    if ($latitude == '' || $longitude == '') {
        return array('erro' => 'Informe latitude e longitude.');
    }

    $latitudeNormalizada = str_replace(',', '.', trim($latitude));
    $longitudeNormalizada = str_replace(',', '.', trim($longitude));

    if (!is_numeric($latitudeNormalizada) || !is_numeric($longitudeNormalizada)) {
        return array('erro' => 'Latitude ou longitude inválida. Use apenas números, ponto ou vírgula decimal.');
    }

    if ($latitudeNormalizada < -90 || $latitudeNormalizada > 90 || $longitudeNormalizada < -180 || $longitudeNormalizada > 180) {
        return array('erro' => 'Latitude ou longitude fora do intervalo válido.');
    }

    $apiUrlReverseGeocode = "https://api.mapbox.com/geocoding/v5/mapbox.places/" . $longitudeNormalizada . "," . $latitudeNormalizada . ".json?access_token=" . $accessToken;
    $consulta = consultarMapbox($apiUrlReverseGeocode);

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

    $endereco = str_replace("'", '', $data['features'][0]['place_name']);

    return array(
        'endereco' => $endereco,
        'latitude' => $latitudeNormalizada,
        'longitude' => $longitudeNormalizada
    );
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idtbtoaqui = isset($_POST['idtbtoaqui']) ? $_POST['idtbtoaqui'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
    $salvar = isset($_POST['salvar']) ? $_POST['salvar'] : '0';

    $resultadoConversao = converterCoordenadasParaEndereco($latitude, $longitude, $accessToken);

    if (isset($resultadoConversao['endereco']) && $salvar == '1' && $idtbtoaqui != '') {
        $endereco = mysql_real_escape_string($resultadoConversao['endereco']);
        $idtbtoaqui = mysql_real_escape_string($idtbtoaqui);
        $sqlUpdate = "UPDATE bdfrota.tbtoaqui SET endereco = '$endereco' WHERE idtbtoaqui = '$idtbtoaqui'";
        $resultadoUpdate = mysql_query($sqlUpdate);

        if ($resultadoUpdate) {
            $hoje = date('Y-m-d H:i:s');
            enviarlog($hoje, 'converteu latitude longitude to aqui', '2', $matricula, $matricula, '', '');
            $mensagem = 'Endereço convertido e salvo no To Aqui selecionado.';
        } else {
            $mensagem = 'Endereço convertido, mas não foi possível salvar: ' . mysql_error();
        }
    }
}

$sqlPendentes = "SELECT idtbtoaqui, matricula, motivo, obs, latitude, longitude, hora, data, endereco
                FROM bdfrota.tbtoaqui
                WHERE aceite = 0
                AND latitude <> ''
                AND longitude <> ''
                AND (endereco IS NULL OR endereco = '')
                ORDER BY data DESC, hora DESC
                LIMIT 100";
$resultadoPendentes = mysql_query($sqlPendentes) or die(mysql_error());
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Converter Latitude/Longitude - To Aqui</title>
    <link href="src/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body class="sb-nav-fixed vsc-initialized">
    <?php include "menu.php"; ?>
    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2">Converter Latitude/Longitude - To Aqui</h1>

                <?php if ($mensagem != '') { ?>
                    <div class="alert alert-info"><?php echo $mensagem; ?></div>
                <?php } ?>

                <?php if (is_array($resultadoConversao)) { ?>
                    <?php if (isset($resultadoConversao['endereco'])) { ?>
                        <div class="alert alert-success">
                            <strong>Endereço detectado:</strong><br>
                            <?php echo htmlspecialchars($resultadoConversao['endereco']); ?>
                        </div>
                    <?php } elseif (isset($resultadoConversao['erro'])) { ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($resultadoConversao['erro']); ?></div>
                    <?php } ?>
                <?php } ?>

                <div class="card mb-4">
                    <div class="card-header">Conversão manual</div>
                    <div class="card-body">
                        <form method="post" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="latitudeManual">Latitude</label>
                                <input type="text" class="form-control" name="latitude" id="latitudeManual" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="longitudeManual">Longitude</label>
                                <input type="text" class="form-control" name="longitude" id="longitudeManual" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">Converter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">To Aqui pendentes com coordenada e sem endereço</div>
                    <div class="card-body table-responsive">
                        <table class="table table-striped table-bordered" style="font-size: 12px; width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Matrícula</th>
                                    <th>Motivo</th>
                                    <th>OBS</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Data/Hora</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysql_fetch_array($resultadoPendentes)) { ?>
                                    <tr>
                                        <td><?php echo $row['idtbtoaqui']; ?></td>
                                        <td><?php echo $row['matricula']; ?></td>
                                        <td><?php echo htmlspecialchars($row['motivo']); ?></td>
                                        <td><?php echo htmlspecialchars($row['obs']); ?></td>
                                        <td><?php echo $row['latitude']; ?></td>
                                        <td><?php echo $row['longitude']; ?></td>
                                        <td><?php echo $row['data'] . ' ' . $row['hora']; ?></td>
                                        <td>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="idtbtoaqui" value="<?php echo $row['idtbtoaqui']; ?>">
                                                <input type="hidden" name="latitude" value="<?php echo $row['latitude']; ?>">
                                                <input type="hidden" name="longitude" value="<?php echo $row['longitude']; ?>">
                                                <input type="hidden" name="salvar" value="1">
                                                <button type="submit" class="btn btn-outline-success btn-sm">Converter e salvar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
</body>
</html>