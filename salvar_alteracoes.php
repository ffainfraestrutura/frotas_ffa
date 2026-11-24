<?php
include "conecta.php";

$matriculas = $_POST['matricula'];
$enderecos = $_POST['endereco'];
$cidades = $_POST['cidade'];
$dias = $_POST['dia'];
$horas = $_POST['hora'];
$osArray = $_POST['os'];
$latitudes = $_POST['latitude'];
$longitudes = $_POST['longitude'];
$kms = $_POST['km'];
$ids = $_POST['ids'];
$tables = $_POST['tables'];
$deletedRows = $_POST['deletedRows'];

// Processando as linhas excluídas
if ($deletedRows) {
    $deletedEntries = explode(';', $deletedRows);
    foreach ($deletedEntries as $entry) {
        if ($entry) {
            list($id, $table) = explode(',', $entry);
            $idColumn = $table == 'bdfrota.tbosrota' ? 'idtbosrota' : 'idtbosrotalatlong';
            $sql = "DELETE FROM $table WHERE $idColumn = '$id'";
            mysql_query($sql) or die(mysql_error());
        }
    }
}

// Inserindo novas linhas
foreach ($matriculas as $index => $matricula) {
    $endereco = $enderecos[$index];
    $cidade = $cidades[$index];
    $dia = $dias[$index];
    $hora = $horas[$index];
    $os = $osArray[$index];
    $latitude = $latitudes[$index];
    $longitude = $longitudes[$index];
    $km = $kms[$index];
    $table = $tables[$index];

    // Verifica se é uma linha nova (sem id associado)
    if (empty($ids[$index])) {
        $sql = "INSERT INTO $table (matricula, endereco, cidade, data, eta, numero_de_ordem, latitude, longitude, kmrota) VALUES ('$matricula', '$endereco', '$cidade', '$dia', '$hora', '$os', '$latitude', '$longitude', '$km')";
        mysql_query($sql) or die(mysql_error());
    }
}

header("Location: ./detalhes-kmos-edicao.php");
?>
