<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');

$matricula = $_POST['matricula'];
$fotoTempPath = $_FILES['arquivo']['tmp_name'];
$fotoDestPath = "../docs/{$matricula}-memorando.jpg"; // Caminho padrão para salvar a nova foto
$fotoOldPath  = "../docs/{$matricula}-memorando-old.jpg"; // Caminho para a foto antiga renomeada

include("../conecta.php");
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");

// Função para chamar o serviço Flask e detectar se há uma pessoa na imagem
function detectaPessoaNaImagem($filePath) {
    $flaskUrl = "http://10.17.0.2:5000/detect"; // Ajuste se necessário
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $flaskUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Determina o MIME type do arquivo
    $fileType = mime_content_type($filePath);
    // Cria um CURLFile para o arquivo
    $cfile = new CURLFile($filePath, $fileType, basename($filePath));
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['image' => $cfile]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return ['error' => "Erro cURL: " . $error_msg];
    }
    curl_close($ch);
    $result = json_decode($response, true);
    return $result;
}

// Verifica se já existe uma foto para a matrícula
if (file_exists($fotoDestPath)) {
    if (file_exists($fotoOldPath)) {
        unlink($fotoOldPath); // Remove a foto antiga, se existir
    }
    rename($fotoDestPath, $fotoOldPath); // Renomeia a foto atual para -old
}

// Move a nova foto para o diretório
if (move_uploaded_file($fotoTempPath, $fotoDestPath)) {
    // Chama o serviço Flask para detectar pessoa na imagem
    $detec = detectaPessoaNaImagem($fotoDestPath);

    // Se houver erro na detecção ou se não for detectada nenhuma pessoa
    if (isset($detec['error']) || !isset($detec['pessoa_detectada']) || $detec['pessoa_detectada'] !== true) {
        // Remove a foto recém gravada, pois não atende ao requisito
        if(file_exists($fotoDestPath)) {
            unlink($fotoDestPath);
        }
        echo "<script>alert('Nenhuma pessoa foi detectada na foto. Tente novamente.');</script>";
        echo "<script>window.location='../memorando.php';</script>";
        exit;
    }

    // Atualiza o banco de dados
    $sqlCheck = "SELECT * FROM bdfrota.tbmemorando WHERE matricula = '$matricula'";
    $resultadoCheck = mysql_query($sqlCheck) or die(mysql_error());

    if (mysql_num_rows($resultadoCheck) > 0) {
        // Se já existe um registro, atualiza
        $sqlUpdate = "UPDATE bdfrota.tbmemorando SET dataaceite = '$hoje', doc = '$fotoDestPath' WHERE matricula = '$matricula'";
        mysql_query($sqlUpdate) or die(mysql_error());
    } else {
        // Caso contrário, insere um novo registro
        $sqlInsert = "INSERT INTO bdfrota.tbmemorando (matricula, dataaceite, doc) VALUES ('$matricula', '$hoje', '$fotoDestPath')";
        mysql_query($sqlInsert) or die(mysql_error());
    }
    echo "<script>alert('Aceito com sucesso');</script>";
    echo "<script>window.location='../tecnico.php?matricula=$matricula';</script>";
} else {
    echo "<script>alert('Erro no envio da foto, tente novamente.');</script>";
    echo "<script>window.location='../tecnico.php?matricula=$matricula';</script>";
}
?>
