<?php
// Arquivo: ajax_get_saldo.php

header('Content-Type: application/json');
session_start();

if (!isset($_POST['matricula']) || !isset($_SESSION['matricula'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

// Inclui sua conexão MySQLi
include "conecta05.php"; 
include_once "./func/consultacartao.php";

$matricula_tec = $_POST['matricula'];
$response = ['success' => false];

$sql = "SELECT v.unidade, v.placa 
        FROM bdaniel.tbfuncionario b
        LEFT JOIN bdfrota.tbveiculo v ON b.matricula = v.matcond
        WHERE b.matricula = ?";

// Convertido para MySQLi
$stmt = mysqli_prepare($conexao05, $sql);
mysqli_stmt_bind_param($stmt, 's', $matricula_tec);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);


if ($data && !empty($data['placa'])) {
    $unidade = $data['unidade'];
    if($unidade != "RJ" && $unidade != "ES" && $unidade != ""){
        $codempresa = 2;
    }else{
        $codempresa = 1;
    }
    $placa = $data['placa'];
    $saldo_formatado = consultacartao($codempresa, $placa);

    if ($saldo_formatado && $saldo_formatado != 'Sem cartão vinculado') {
        $saldo_numerico = str_replace('R$ ', '', $saldo_formatado);
        $saldo_numerico = str_replace('.', '', $saldo_numerico);
        $saldo_numerico = str_replace(',', '.', $saldo_numerico);
        $response = [
            'success' => true,
            'saldo_formatado' => $saldo_formatado,
            'saldo_numerico' => floatval($saldo_numerico),
            'placa' => $placa,
            'codempresa' => $codempresa
        ];
    } else {
        $response = ['success' => true, 'saldo_formatado' => 'R$ 0,00', 'saldo_numerico' => 0, 'placa' => $placa];
    }
} else {
    $response = ['success' => true, 'saldo_formatado' => 'Sem veículo/placa', 'saldo_numerico' => 0];
}

echo json_encode($response);