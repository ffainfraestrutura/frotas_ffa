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

    // 1. Consulta o status atual
    $query = mysql_query("SELECT bloqueio FROM bdfrota.tbdescontousoindevido WHERE id = '$id'");
    if ($query && mysql_num_rows($query)) {
        $row = mysql_fetch_assoc($query);
        $bloqueioAtual = (int)$row['bloqueio'];
        $novoStatus = $bloqueioAtual === 1 ? 0 : 1;
        $acao = $novoStatus === 1 ? 'Bloqueou' : 'Desbloqueou';

        // 2. Atualiza o valor invertido
        $sql = "UPDATE bdfrota.tbdescontousoindevido SET bloqueio = $novoStatus WHERE id = '$id'";
        if (mysql_query($sql)) {
            if (mysql_affected_rows()) {
                $horario = date("Y-m-d H:i:s");
                enviarlog($horario, "$acao o t�cnico por uso indevido. ID: $id", '', '', $matricula, '', '');
                http_response_code(200);
                echo json_encode([
                    'status' => 200,
                    'message' => "$acao com sucesso.",
                    'bloqueio' => $novoStatus
                ]);
            } else {
                http_response_code(204);
                echo json_encode([
                    'status' => 204,
                    'message' => 'Nenhuma altera��o realizada.'
                ]);
            }
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 500,
                'message' => 'Erro ao atualizar o banco de dados: ' . mysql_error()
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode([
            'status' => 404,
            'message' => 'Registro n�o encontrado.'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 400,
        'message' => 'Par�metros incompletos.'
    ]);
}
