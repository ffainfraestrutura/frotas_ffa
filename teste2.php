<?php
// Dados que serão enviados no corpo da requisição
$data = [
    "contratoCliente" => "524073",
    "dataInicial" => "2025-07-02",
    "dataFinal" => "2025-07-03",
    "tipotransacao" => "TODAS"
];

// Converte o array em JSON (se a API espera JSON)
$jsonData = json_encode($data);

// Inicializa o cURL
$ch = curl_init();

// Configurações da requisição
curl_setopt($ch, CURLOPT_URL, "https://kissapi.valecard.com.br/core/services/frotaCliente/buscartransacoesfrota");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

// Define o tipo de conteúdo como JSON
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
	'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
    'Content-Length: ' . strlen($jsonData)
]);

// Executa a requisição
$response = curl_exec($ch);

// Verifica se houve erro
if (curl_errno($ch)) {
    echo 'Erro na requisição: ' . curl_error($ch);
} else {
    // Exibe a resposta da API
    echo 'Resposta da API: ' . json_encode($response);
}

// Fecha a conexão
curl_close($ch);
?>