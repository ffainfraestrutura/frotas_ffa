<?php

date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$hoje = date('Y-m-d H:i:s');
$agora = date("H:i:s");

// Recebe dados do formulário
$placa = $_POST['placa'];
$matrvistoriador = $_POST['vistoriador'];
$datavistoria = $_POST['datavistoriaf'];
$matricula = $_POST['matriculac'];
$idinserido = isset($_POST['idinserido']) ? $_POST['idinserido'] : '';

// Conecta ao banco
include_once "../conecta.php";

// Busca o ID da vistoria
if (empty($idinserido)) {
    $sql1 = "SELECT MAX(idtbvistoria) FROM bdfrota.tbvistoria WHERE placa='$placa'";
    $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
    $row = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
    $idtbvistoria = $row[0];
} else {
    $idtbvistoria = $idinserido;
}

// ============================================================
// MARCA A VISTORIA COMO CONCLUÍDA (statusreg = 1)
// As fotos JÁ foram salvas via AJAX no checklistp2-foto.php
// Este arquivo apenas finaliza a vistoria
// ============================================================

$sqlUpdate = "UPDATE bdfrota.tbvistoria SET statusreg = 1 WHERE idtbvistoria = " . intval($idtbvistoria);
mysqli_query($con, $sqlUpdate);

// Registra no log (se a função existir)
$acao = 'Concluiu vistoria/checklist';
$tipo = 'checklist';
// Se você tiver a função de log, descomente a linha abaixo:
// include_once "../../func/log.php";
// enviarlognovo($hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa);

// Redireciona conforme o perfil
if ($perfil == '4' || $perfil == '5' || $perfil == '6') {
    // Perfil de vistoriador - vai para o relatório
    echo "<script>
        alert('Vistoria finalizada com sucesso!');
        window.location='../verrelatorio.php?id=$idtbvistoria';
    </script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2') {
    // Perfil de condutor - vai para gerar relatório
    echo "<script>
        alert('Vistoria finalizada com sucesso!');
        window.location='../gerandorelatorio.php?id=$idtbvistoria';
    </script>";
} else {
    // Caso padrão
    echo "<script>
        alert('Vistoria finalizada com sucesso!');
        window.location='../checklistinicioDEV.php';
    </script>";
}

?>
