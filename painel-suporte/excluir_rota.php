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

// Receber dados do POST
$id = isset($_POST['id']) ? $_POST['id'] : '';
$tabela = isset($_POST['tabela']) ? $_POST['tabela'] : '';

// Validações básicas
if (empty($id) || empty($tabela)) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos (ID ou tabela faltando)']);
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

// Escapar ID para prevenir SQL injection
$id = mysql_real_escape_string($id);

// Buscar informações do registro antes de excluir (para log)
$sqlBusca = "SELECT matricula, numero_de_ordem, data FROM bdfrota.$tabelaSemBanco WHERE $campoId = '$id'";
$resultadoBusca = mysql_query($sqlBusca);
$dadosRegistro = mysql_fetch_array($resultadoBusca);

if (!$dadosRegistro) {
    echo json_encode(['success' => false, 'message' => 'Registro não encontrado']);
    exit;
}

$matriculaRota = $dadosRegistro[0];
$osRota = $dadosRegistro[1];
$dataRota = $dadosRegistro[2];

// Montar a query de exclusão
$sql = "DELETE FROM bdfrota.$tabelaSemBanco WHERE $campoId = '$id'";
error_log('[excluir_rota] SQL delete: ' . $sql);

// Executar a query
$resultado = mysql_query($sql);

if (!$resultado) {
    error_log('[excluir_rota] Erro ao executar DELETE: ' . mysql_error() . ' -- SQL: ' . $sql);
}

// Verificar resultado da exclusão
if ($resultado) {
    // Gravar histórico de exclusão (dados antes já obtidos em $dadosRegistro)
    include_once __DIR__ . '/historico_funcoes.php';
    // garantir que $campoId foi definido corretamente para esta tabela
    if ($tabelaSemBanco == 'tbosrotalatlong') {
        $campoIdUse = 'idtbosrotalatlong';
    } else {
        $campoIdUse = 'idtbosrota';
    }
    gravar_historico_rotas($tabelaSemBanco, $campoIdUse, $id, 'DELETE', $dadosRegistro, null);

    // Log da operação
    $matriculaOp = $_SESSION['matricula'];
    $nomeOp = $_SESSION['nome'];
    $sqlLog = "INSERT INTO bdfrota.tblog (matricula, nome, acao, data) 
               VALUES ('$matriculaOp', '$nomeOp', 'Excluiu rota ID: $id (Mat: $matriculaRota, OS: $osRota, Data: $dataRota) da tabela $tabelaSemBanco', NOW())";
    mysql_query($sqlLog);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Rota excluída com sucesso',
        'id' => $id
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Erro ao excluir: ' . mysql_error()
    ]);
}

mysql_close();
?>
