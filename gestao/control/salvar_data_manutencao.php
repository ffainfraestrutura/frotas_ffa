<?php
header('Content-Type: application/json');
include_once "../../conecta.php";

// Pega JSON
$input = json_decode(file_get_contents("php://input"), true);

$placa = $input['placa'] ?? null;
$faixa_km = $input['faixa_km'] ?? null;
$data = $input['data'] ?? null;

// Valida  o b sica
if (!$placa || !$faixa_km || !$data) {
    echo json_encode([
        "success" => false,
        "message" => "Dados inv lidos"
    ]);
    exit;
}

// Sanitiza  o
$placa = mysqli_real_escape_string($conn, $placa);
$faixa_km = mysqli_real_escape_string($conn, $faixa_km);
$data = mysqli_real_escape_string($conn, $data);

// Verifica se j  existe registro
$sql_check = "
    SELECT id 
    FROM agendamento_manutencao 
    WHERE placa = '$placa' 
    AND faixa_km = '$faixa_km'
";

$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    // UPDATE
    $sql = "
        UPDATE agendamento_manutencao 
        SET data_agendada_frota = '$data'
        WHERE placa = '$placa' 
        AND faixa_km = '$faixa_km'
    ";
} else {
    // INSERT
    $sql = "
        INSERT INTO agendamento_manutencao 
        (placa, faixa_km, data_agendada_frota)
        VALUES ('$placa', '$faixa_km', '$data')
    ";
}

// Executa
if (mysqli_query($conn, $sql)) {
    echo json_encode([
        "success" => true,
        "message" => "Data salva com sucesso"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao salvar",
        "erro_sql" => mysqli_error($conn)
    ]);
}