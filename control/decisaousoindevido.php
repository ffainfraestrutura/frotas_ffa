<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json'); // Define o tipo de retorno como JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);
include "../conecta.php";
include_once "../func/log.php";
if (isset($data['matricula']) && isset($data['aval']) && isset($data['justificativa']) && isset($data['id']) && isset($data['valor'])) {
    $id = $data['id'];
    $matricula = $data['matricula'];
    $aval = $data['aval'];
    $justificativa = $data['justificativa'];
    $valor_desconto = $data['valor'];
    if ($aval == 1) {
        $situacao = "Aguardando assinatura do colaborador";
    } else {
        $situacao = "Gestor negou o desconto";
    }
    $sql = "UPDATE bdfrota.tbdescontousoindevido SET matricula_coordenador = '$matricula', aval_coordenador = '$aval', valor_desconto = '$valor_desconto', justificativa_coordenador = '$justificativa', situacao = '$situacao', bloqueio = 0 WHERE id = '$id'";
    if (mysql_query($sql)) {
        if (mysql_affected_rows()) {
            $horario = date("Y-m-d H:i:s", time());
            $logresult = enviarlog($horario, `Avaliou o desconto por uso indevido de ID: $id`, $aval, '', $matricula, '', '');
            http_response_code(200); // OK
            echo json_encode([
                'status' => 200,
                'message' => 'Justificativa registrada com sucesso.'
            ]);
        } else {
            http_response_code(204); // No Content (nenhuma linha afetada)
            echo json_encode([
                'status' => 204,
                'message' => 'Nenhuma alteração realizada.'
            ]);
        }
    } else {
        http_response_code(500); // Erro interno
        echo json_encode([
            'status' => 500,
            'message' => 'Erro ao atualizar o banco de dados: ' . mysql_error()
        ]);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode([
        'status' => 400,
        'message' => 'Parâmetros incompletos ou inválidos.'
    ]);
}

?>