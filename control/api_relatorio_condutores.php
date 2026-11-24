<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

require "../func/funcoes.php";

session_start();

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] == '0') {
    http_response_code(403);
    echo json_encode([
        'erro' => 'Sem permissão'
    ]);
    exit;
}


$dados = relatorioCondutoresPresenca();

$supervisores = [];

foreach ($dados as $item) {
    if (!empty($item['supervisor_nome'])) {
        $supervisores[$item['supervisor_nome']] = true;
    }
}

header('Content-Type: application/json');

echo json_encode([
    'totalInativos' => count($dados),
    'totalSupervisores' => count($supervisores),
    'dataRelatorio' => date('d/m/Y'),
    'dados' => $dados
]);