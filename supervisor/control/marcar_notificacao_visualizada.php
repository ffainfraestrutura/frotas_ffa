<?php
/**
 * Marca uma notificação de manutenção como visualizada
 * para que não seja exibida novamente ao usuário
 */

date_default_timezone_set('America/Sao_Paulo');
session_start();

include_once "../../conecta.php";

// Configurar resposta como JSON
header('Content-Type: application/json; charset=utf-8');

// Validar sessão
if (empty($_SESSION['matricula'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Sessão inválida ou expirada'
    ]);
    exit;
}

$matricula = $_SESSION['matricula'];
$idtbmanprev = isset($_POST['idtbmanprev']) ? intval($_POST['idtbmanprev']) : 0;

// Validar dados
if (empty($idtbmanprev)) {
    echo json_encode([
        'success' => false, 
        'message' => 'ID da manutenção não informado'
    ]);
    exit;
}

// Inserir ou atualizar registro de visualização
$sql = "INSERT INTO bdfrota.tbnotificacoes_visualizadas 
            (matricula, tipo_notificacao, referencia_id, data_visualizacao) 
        VALUES 
            ('$matricula', 'manutencao', '$idtbmanprev', NOW())
        ON DUPLICATE KEY UPDATE 
            data_visualizacao = NOW()";

$resultado = mysql_query($sql);

if ($resultado) {
    echo json_encode([
        'success' => true,
        'message' => 'Notificação marcada como visualizada',
        'data' => [
            'matricula' => $matricula,
            'idtbmanprev' => $idtbmanprev,
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao registrar visualização: ' . mysql_error()
    ]);
}
?>
