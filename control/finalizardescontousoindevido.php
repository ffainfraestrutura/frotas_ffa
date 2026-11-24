<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json');

$json = file_get_contents('php://input');
$data = json_decode($json, true);

include "../conecta.php";
include_once "../func/log.php";

if (isset($data['id']) && isset($data['matricula'])) {
    $id = $data['id'];
    $matricula = $data['matricula'];
    $sql = "UPDATE bdfrota.tbdescontousoindevido SET finalizado_por = $matricula, data_hora_finalizacao = current_timestamp(), situacao = 'Finalizado' where id = $id";
    mysql_query($sql) or die(mysql_error());
    if (mysql_affected_rows() > 0) {
        $horario = date("Y-m-d H:i:s", time());
        $logresult = enviarlog($horario, `Finalizou o Desconto por uso indevido de ID: $id`, '', '', $matricula, '', '');

        http_response_code(200);
        echo json_encode([
            'status' => 200,
            'message' => 'Desconto finalizado com sucesso.'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => 'Houve um erro ao finalizar o desconto.'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 400,
        'message' => 'Parâmetros incompletos.'
    ]);
}