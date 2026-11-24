<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Verificar se o usuário está logado
if (!isset($_SESSION['matricula'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

include "../conecta.php";
include_once __DIR__ . '/historico_funcoes.php'; // incluir função de histórico

// Receber dados do POST
$matricula = isset($_POST['matricula']) ? $_POST['matricula'] : '';
$enderecoCompleto = isset($_POST['endereco']) ? $_POST['endereco'] : '';
$data = isset($_POST['data']) ? $_POST['data'] : '';
$eta = isset($_POST['eta']) ? $_POST['eta'] : '';
$os = isset($_POST['os']) ? $_POST['os'] : '';
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
$kmrota = isset($_POST['kmrota']) ? $_POST['kmrota'] : '0';
$tabela = isset($_POST['tabela']) ? $_POST['tabela'] : '';

// Validações básicas
if (empty($matricula) || empty($data) || empty($tabela)) {
    echo json_encode(['success' => false, 'message' => 'Dados obrigatórios faltando (matrícula, data, tabela)']);
    exit;
}

// Remover o prefixo do banco se vier junto
$tabelaSemBanco = str_replace('bdfrota.', '', $tabela);

// Determinar qual tabela usar
if ($tabelaSemBanco != 'tbosrotalatlong' && $tabelaSemBanco != 'tbosrota') {
    echo json_encode(['success' => false, 'message' => 'Tabela inválida']);
    exit;
}

// Tentar separar endereço completo em partes
$partes = explode(',', $enderecoCompleto);
$endereco = trim($partes[0] ?? '');
$bairro = trim($partes[1] ?? '');
$cidade = trim($partes[2] ?? '');

// Se não houver vírgulas, colocar tudo no campo endereço
if (count($partes) == 1) {
    $endereco = trim($enderecoCompleto);
    $bairro = '';
    $cidade = '';
}

// Buscar informações do técnico para obter o provedor
$sqlTec = "SELECT nome, ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
$resultadoTec = mysql_query($sqlTec);
$rowTec = mysql_fetch_array($resultadoTec);
$nomeTecnico = $rowTec[0] ?? '';
$ccusto = $rowTec[1] ?? '';

// Determinar provedor baseado no centro de custo
$provedor = '';
if (strpos($ccusto, 'TIM') !== false) {
    $provedor = 'TIM';
} elseif (strpos($ccusto, 'CLARO') !== false) {
    $provedor = 'CLARO';
} elseif (strpos($ccusto, 'LIGGA') !== false) {
    $provedor = 'LIGGA';
} elseif (strpos($ccusto, 'IHS') !== false) {
    $provedor = 'IHS';
}

// Escapar strings para prevenir SQL injection
$matricula = mysql_real_escape_string($matricula);
$endereco = mysql_real_escape_string($endereco);
$bairro = mysql_real_escape_string($bairro);
$cidade = mysql_real_escape_string($cidade);
$data = mysql_real_escape_string($data);
$eta = mysql_real_escape_string($eta);
$os = mysql_real_escape_string($os);
$latitude = mysql_real_escape_string($latitude);
$longitude = mysql_real_escape_string($longitude);
$kmrota = mysql_real_escape_string($kmrota);
$provedor = mysql_real_escape_string($provedor);

// Montar a query de inserção
$sql = "INSERT INTO bdfrota.$tabelaSemBanco 
    (endereco, bairro, cidade, provedor, data, eta, numero_de_ordem, matricula, 
     latitude, longitude, kmrota, foratoa, operador, dataenvio, processado) 
    VALUES 
    ('$endereco', '$bairro', '$cidade', '$provedor', '$data $eta', '$eta', '$os', '$matricula', 
     '$latitude', '$longitude', '$kmrota', 1, '".mysql_real_escape_string($_SESSION['matricula'])."', NOW(), 0)";

error_log('[adicionar_rota] SQL insert: ' . $sql);

// Executar a query
$resultado = mysql_query($sql);

if (!$resultado) {
    error_log('[adicionar_rota] Erro ao executar INSERT: ' . mysql_error() . ' -- SQL: ' . $sql);
}

if ($resultado) {
    $novoId = mysql_insert_id();
    
    // Buscar snapshot do registro inserido
    if ($tabelaSemBanco == 'tbosrotalatlong') {
        $sqlNew = "SELECT * FROM bdfrota.tbosrotalatlong WHERE idtbosrotalatlong = '".mysql_real_escape_string($novoId)."'";
        $campoId = 'idtbosrotalatlong';
    } else {
        $sqlNew = "SELECT * FROM bdfrota.$tabelaSemBanco WHERE idtbosrota = '".mysql_real_escape_string($novoId)."'";
        $campoId = 'idtbosrota';
    }
    $resNew = mysql_query($sqlNew);
    $dadosNovo = mysql_fetch_assoc($resNew);
    
    // Gravar histórico (antes = null)
    gravar_historico_rotas($tabelaSemBanco, $campoId, $novoId, 'INSERT', null, $dadosNovo);
    
    // Log da operação
    $matriculaOp = $_SESSION['matricula'];
    $nomeOp = $_SESSION['nome'];
    $sqlLog = "INSERT INTO bdfrota.tblog (matricula, nome, acao, data) 
               VALUES ('$matriculaOp', '$nomeOp', 'Adicionou nova rota (ID: $novoId) para matrícula $matricula na tabela $tabelaSemBanco', NOW())";
    mysql_query($sqlLog);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Rota adicionada com sucesso',
        'id' => $novoId
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao adicionar: ' . mysql_error()
    ]);
}

mysql_close();
?>
