<?php
// Desabilita exibição de erros para não quebrar o JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

session_start();

// Limpa qualquer output anterior
ob_start();

header('Content-Type: application/json; charset=utf-8');

// Verificar se o usuário está logado
if (!isset($_SESSION['matricula'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

include "../conecta.php";

// Limpa qualquer warning do conecta.php
ob_end_clean();

include_once __DIR__ . '/historico_funcoes.php'; // caminho absoluto do diretório atual

// Receber dados do POST
$id = isset($_POST['id']) ? $_POST['id'] : '';
$enderecoCompleto = isset($_POST['endereco']) ? $_POST['endereco'] : '';
$eta = isset($_POST['eta']) ? $_POST['eta'] : '';
$os = isset($_POST['os']) ? $_POST['os'] : '';
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
$kmrota = isset($_POST['kmrota']) ? $_POST['kmrota'] : '';
$tabela = isset($_POST['tabela']) ? $_POST['tabela'] : '';

// Validações básicas
if (empty($id) || empty($tabela)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos']);
    exit;
}

// Remover o prefixo do banco se vier junto
$tabelaSemBanco = str_replace('bdfrota.', '', $tabela);

// Determinar qual tabela e campo ID usar
if ($tabelaSemBanco == 'tbosrotalatlong') {
    $campoId = 'idtbosrotalatlong';
} else {
    $campoId = 'idtbosrota';
}

// Capturar snapshot antes da atualização
$sqlAntes = "SELECT * FROM bdfrota.$tabelaSemBanco WHERE $campoId = '$id' LIMIT 1";
$resAntes = mysql_query($sqlAntes);
$rowAntes = $resAntes ? mysql_fetch_assoc($resAntes) : null;
$dadosAntes = $rowAntes; // garantir variável usada na gravação de histórico
$jsonAntes = $rowAntes ? mysql_real_escape_string(json_encode($rowAntes, JSON_UNESCAPED_UNICODE)) : null;

// Tentar separar endereço completo em partes (simplificado)
// Formato esperado: "Rua X, Bairro Y, Cidade Z" ou similar
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

// Escapar strings para prevenir SQL injection
$endereco = mysql_real_escape_string($endereco);
$bairro = mysql_real_escape_string($bairro);
$cidade = mysql_real_escape_string($cidade);
$eta = mysql_real_escape_string($eta);
$os = mysql_real_escape_string($os);
$latitude = mysql_real_escape_string($latitude);
$longitude = mysql_real_escape_string($longitude);
$kmrota = mysql_real_escape_string($kmrota);

// Montar a query de atualização
$sql = "UPDATE bdfrota.$tabelaSemBanco SET 
    endereco = '$endereco',
    bairro = '$bairro',
    cidade = '$cidade',
    eta = '$eta',
    numero_de_ordem = '$os',
    latitude = '$latitude',
    longitude = '$longitude',
    kmrota = '$kmrota',
    processado = 0,
    operador = '".mysql_real_escape_string($_SESSION['matricula'])."'
    WHERE $campoId = '$id'";

// Logging para depuração
error_log('[salvar_rota] SQL update: ' . $sql);

// Executar a query
$resultado = mysql_query($sql);

if (!$resultado) {
    error_log('[salvar_rota] Erro ao executar UPDATE: ' . mysql_error() . ' -- SQL: ' . $sql);
}

// Verificar resultado da atualização
if ($resultado) {
    // Log da operação
    $matriculaOp = $_SESSION['matricula'];
    $nomeOp = $_SESSION['nome'];
    $sqlLog = "INSERT INTO bdfrota.tblog (matricula, nome, acao, data) 
               VALUES ('$matriculaOp', '$nomeOp', 'Editou rota ID: $id na tabela $tabelaSemBanco', NOW())";
    mysql_query($sqlLog);
    
    // Buscar snapshot depois da alteração
    $sqlAfter = "SELECT * FROM bdfrota.$tabelaSemBanco WHERE $campoId = '".mysql_real_escape_string($id)."'";
    $resAfter = mysql_query($sqlAfter);
    $dadosDepois = mysql_fetch_assoc($resAfter);

    // Gravar histórico na tabela tb_historico_modificacoes
    $dadosAntesJson = mysql_real_escape_string(json_encode($dadosAntes, JSON_UNESCAPED_UNICODE));
    $dadosDepoisJson = mysql_real_escape_string(json_encode($dadosDepois, JSON_UNESCAPED_UNICODE));
    $ip_origem = mysql_real_escape_string($_SERVER['REMOTE_ADDR'] ?? 'N/A');
    $observacoes = mysql_real_escape_string("Edição de rota via painel - OS: $os");
    
    $sqlHistorico = "INSERT INTO bdfrota.tb_historico_modificacoes 
                    (tabela, registro_id, acao, matricula_autor, nome_autor, data_hora, 
                     dados_anteriores, dados_novos, origem, ip_origem, observacoes, processado) 
                    VALUES 
                    ('bdfrota.$tabelaSemBanco', '$id', 'UPDATE', '$matriculaOp', '$nomeOp', NOW(), 
                     '$dadosAntesJson', '$dadosDepoisJson', 'WEB', '$ip_origem', '$observacoes', 0)";
    
    $resultHistorico = mysql_query($sqlHistorico);
    
    if (!$resultHistorico) {
        error_log('[salvar_rota] Erro ao salvar histórico: ' . mysql_error());
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Rota atualizada com sucesso',
        'id' => $id
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao atualizar: ' . mysql_error()
    ]);
}

mysql_close();
?>
