<?php
// Configurações de localidade e timezone
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

include "conectanovo.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Processa o upload do arquivo
    $uploadedFile = $_FILES['file']['tmp_name'];
    $fileType = $_FILES['file']['type'];

    try {
        // Lê o arquivo Excel
        $spreadsheet = IOFactory::load($uploadedFile);
        $worksheet = $spreadsheet->getActiveSheet();

        // Processa cada linha
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            // Supondo que a estrutura da planilha seja:
            // Coluna A: Matricula, Coluna B: Valor
            $matricula = $data[0];
            $valor = $data[1];

            // Obtém a placa vinculada à matrícula
            $sql2 = "SELECT placa FROM bdfrota.tbveiculo WHERE matcond = '$matricula'";
            $resultado2 = mysqli_query($conexao, $sql2);

            if ($resultado2 && $row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)) {
                $placa = $row2['placa'];

                // Realiza chamadas à API apenas se a placa for encontrada
                if (!empty($placa)) {
                    $numeroCartao = fetchCardDetails($placa);
                    if ($numeroCartao) {
                        updateCardLimit($numeroCartao, $valor);
                        echo "Limite atualizado para a placa $placa (Matrícula: $matricula) com valor R$ $valor<br>";
                    } else {
                        echo "Cartão não encontrado para a placa $placa<br>";
                    }
                } else {
                    echo "Placa não encontrada para a matrícula $matricula<br>";
                }
            }
        }
    } catch (Exception $e) {
        echo "Erro ao processar o arquivo: " . $e->getMessage();
    }
}

// Função para buscar detalhes do cartão
function fetchCardDetails($placa)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
    ]);

    $body = json_encode([
        "codigoCliente" => 183062,
        "placaVeiculo" => $placa,
        "situacaoCartao" => "T",
        "ordem" => "C"
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $result = json_decode(curl_exec($curl), true);
    curl_close($curl);

    if (isset($result['itens'][0]['numeroCartao'])) {
        return $result['itens'][0]['numeroCartao'];
    }
    return null;
}

// Função para atualizar o limite do cartão
function updateCardLimit($numeroCartao, $valor)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA='
    ]);

    $body = json_encode([
        "codigoCliente" => 183062,
        "cartoes" => [
            [
                "numeroCartao" => $numeroCartao,
                "valorLimite" => $valor,
                "valorLimiteProxPeriodo" => $valor,
                "mensagem" => "Controle Combustivel"
            ]
        ]
    ]);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

    $result = json_decode(curl_exec($curl), true);
    curl_close($curl);

    return $result;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualizar Limites</title>
</head>
<body>
    <h1>Atualizar Limites de Cartões</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="file">Selecione o arquivo Excel:</label>
        <input type="file" name="file" id="file" accept=".xlsx, .xls" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
