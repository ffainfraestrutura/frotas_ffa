<?php
$placa = 'TEH3I26';           // ✅ Parâmetro editável
$valor = 1;                   // ✅ Valor editável

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc='
    ],
    CURLOPT_POSTFIELDS => json_encode([     // ✅ JSON correto
        "contratoCliente" => "524073",
        "veiculoId" => null,
        "placa" => $placa,
        "numeroCartao" => null,
        "valorAlteracao" => (float)$valor,   // ✅ float
        "tipoAlteracao" => "adicionar",
        "periodo" => "atual",
        "motivo" => "Sistema Combustivel",
        "modulo" => "abastecimento"
    ]),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 25
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$erro = curl_error($curl);

curl_close($curl);  // ✅ Sempre fechar

// ✅ Resultado completo
echo "HTTP Code: $httpCode\n";
echo "Erro cURL: " . ($erro ?: 'Nenhum') . "\n";
echo "Resposta: " . $response . "\n";

if (!$erro && $httpCode === 200) {
    $result = json_decode($response);
    echo "✅ SUCESSO!\n";
    print_r($result);  // Ver estrutura da resposta
} else {
    echo "❌ FALHOU\n";
}
?>
