
<?php
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include_once"../../conecta.php";

// Verifica se foi enviado um arquivo
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // Caminho onde deseja salvar o arquivo
    $placa = $_POST['placa'];
    $matrvistoriador = $_POST['vistoriador'];
    $datavistoria = $_POST['datavistoriaf'];
    $matricula = $_POST['matriculac'];
    $idinserido = $_POST['idinserido'];
    $datacerta = str_replace("-", "", $datavistoria);
    $datacerta = str_replace(" ", "", $datacerta);
    $datacerta = str_replace(":", "", $datacerta);

    $uploadDir = '../docs_old/';
    $uploadFile = $uploadDir.$placa."-".$datacerta."-frontal.jpg";

    // Move o arquivo enviado para o destino desejado
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {

        $sql = "UPDATE bdfrota.tbvistoriafotos SET frontal = '/docs_old/$placa-$datacerta-frontal.jpg' WHERE idtbvistoria = '$idinserido'";
        
        $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

        if (mysqli_affected_rows($con) > 0) {
            echo 'OK';
        } else {
            echo 'Falha ao salvar o arquivo.';
        }

        
    } else {
        echo 'Falha ao salvar o arquivo.';
    }
} else {
    echo 'Erro ao receber o arquivo.';
}
?>