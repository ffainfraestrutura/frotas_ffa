<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: application/json');
$json = file_get_contents('php://input');
$data = json_decode($json, true);

include "../conecta.php";
include_once "../func/log.php";

if (isset($data['id']) && isset($data['assinatura'])) {
    $id = $data['id'];
    $base64 = $data['assinatura'];
    $matricula = $data['matricula'];

    // Detecta o tipo da imagem pelo prefixo
    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
        $apiKey = "AIzaSyAGq81ITRhvO0fc_QZRD6hMoO4qv9URbXs";
        $urlgoogle = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=$apiKey";
        $payload = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => "Detect Face. Output a JSON list where each entry contains the 2D bounding box in 'box_2d', Face in 'label'. Return an empty list '[]' if no Face are present.",
                        ],
                        [
                            "inlineData" => [
                                "mimeType" => "image/jpeg",
                                "data" => preg_replace('#^data:image/\w+;base64,#i', '', $base64),
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $ch = curl_init($urlgoogle);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Erro na requisição: ' . curl_error($ch);
        } else {
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $data = json_decode($response, true);
            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $text = str_replace('`', "", $text);
            $text = str_replace('json', "", $text);
            $faces = json_decode($text, true);
            // Verifica se há pelo menos um item com 'label' igual a 'face'
            $hasFace = is_array($faces) && !empty(array_filter($faces, function ($item) {
                return isset($item['label']) && strtolower($item['label']) === 'face';
            }));

            if ($hasFace || $code != 200) {
                $ext = strtolower($matches[1]); // ex: png, jpeg, jpg

                // Remove o prefixo base64
                $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
                $base64 = str_replace(' ', '+', $base64); // Corrige espaços

                // Gera nome aleatório (no máximo 45 caracteres com extensão)
                $filename = substr(bin2hex(random_bytes(22)), 0, 40) . '.' . $ext;

                // Caminho para salvar (ajuste conforme seu projeto)
                $caminho = "../docs/assinaturaDesconto/" . $filename;

                // Decodifica e salva
                if (file_put_contents($caminho, base64_decode($base64))) {
                    $sql = "UPDATE bdfrota.tbdescontousoindevido SET aceite_colaborador = 1, horario_aceite_colaborador = current_timestamp(), situacao = 'Aguardando Desconto', url_photo_aceite = '$filename' where id = $id";
                    mysql_query($sql) or die(mysql_error());
                    if (mysql_affected_rows() > 0) {
                        $horario = date("Y-m-d H:i:s", time());
                        $logresult = enviarlog($horario, `Assinou o desconto por uso indevido de ID: $id`, '', $matricula, $matricula, '', '');

                        http_response_code(200);
                        echo json_encode([
                            'status' => 200,
                            'message' => 'Desconto assinado com sucesso.',
                            'arquivo' => $filename
                        ]);
                    } else {
                        http_response_code(400);
                        echo json_encode([
                            'status' => 400,
                            'message' => 'Nenhuma linha afetada'
                        ]);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'status' => 500,
                        'message' => 'Erro ao salvar a imagem.'
                    ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 400,
                    'message' => 'Face não detectada, por favor, centralize a face na imagem e tente novamente.'
                ]);
            }
        }
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 400,
            'message' => 'Formato da imagem inválido.'
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'status' => 400,
        'message' => 'Parâmetros incompletos.'
    ]);
}
?>