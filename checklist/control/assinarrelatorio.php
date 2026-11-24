<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=utf-8');

include_once "../../func/log.php";
include("../../conecta.php");

$placa = $_POST['placa'];
$matricula = $_POST['matricula'];
$datavistoria = $_POST['datavistoria'];
$idtbvistoria = $_POST['idtbvistoria'];

$hoje = date('Y-m-d H:i:s');
$timestamp = date('YmdHis');

// Pasta onde o arquivo vai ser salvo
$uploadDir = '../docs_old/ass/';
$dirDbPath = '/docs_old/ass/';

// Configurações de upload
$maxSize = 32 * 1024 * 1024; // 32 MB
$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
$renomear = false;

$erros = [
    0 => 'Não houve erro',
    1 => 'O arquivo no upload é maior do que o limite do PHP',
    2 => 'O arquivo ultrapassa o limite de tamanho especificado no HTML',
    3 => 'O upload do arquivo foi feito parcialmente',
    4 => 'Não foi feito o upload do arquivo'
];

if (is_uploaded_file($_FILES['frontal']['tmp_name'])) {
    if ($_FILES['frontal']['error'] != 0) {
        die("Erro no upload: " . $erros[$_FILES['frontal']['error']]);
    }

    $extensao = strtolower(pathinfo($_FILES['frontal']['name'], PATHINFO_EXTENSION));

    if (!in_array($extensao, $allowedExts)) {
        die("Por favor, envie arquivos com as seguintes extensões: jpg, jpeg, png, gif ou pdf.");
    }

    if ($_FILES['frontal']['size'] > $maxSize) {
        die("O arquivo enviado é muito grande. Envie arquivos de até 32MB.");
    }

    // Geração do nome final do arquivo
    $nome_final = $renomear
        ? time() . '.jpg'
        : $matricula . "-" . $timestamp . "-assinatura." . $extensao;

    $caminhoCompleto = $uploadDir . $nome_final;
    $dirParaDb = $dirDbPath . $nome_final;

    $qualidade = 30;

    if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
        switch ($extensao) {
            case 'jpg':
            case 'jpeg':
                $imagemTmp = imagecreatefromjpeg($_FILES['frontal']['tmp_name']);
                break;
            case 'png':
                $imagemTmp = imagecreatefrompng($_FILES['frontal']['tmp_name']);
                break;
            case 'gif':
                $imagemTmp = imagecreatefromgif($_FILES['frontal']['tmp_name']);
                break;
        }

        if (!isset($imagemTmp)) {
            die("Erro ao processar a imagem.");
        }

        if (!imagejpeg($imagemTmp, $caminhoCompleto, $qualidade)) {
            echo $imagemTmp;
            echo $caminhoCompleto;
            echo $qualidade;
            die("Erro ao salvar a imagem compactada.");
        }
    } else {
        // Se for PDF, apenas mover
        if (!move_uploaded_file($_FILES['frontal']['tmp_name'], $caminhoCompleto)) {
            die("Erro ao mover o arquivo PDF.");
        }
    }

    // Atualiza no banco
    $sql = "UPDATE bdfrota.tbvistoria SET assinadocond = 1, dir = ? WHERE idtbvistoria = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'si', $dirParaDb, $idtbvistoria);
    mysqli_stmt_execute($stmt);

    // Log
    $acao = 'Assinou relatório';
    $tipo = 'checklist';
    enviarlognovo($hoje, $acao, '', $matricula, $tipo, $placa);

    echo "<script>
        alert('Relatório assinado com sucesso.');
        window.location.href = '../visualizarrelatorio.php?idtbvistoria={$idtbvistoria}';
    </script>";
    exit;
}
?>
