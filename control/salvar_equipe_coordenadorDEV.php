<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
include("../conecta.php");
include("../conecta02.php");
include_once "../func/log.php";

// Configurar header para JSON
header('Content-Type: application/json; charset=utf-8');

// Verificar se o usuário está logado
if (!isset($_SESSION['matricula']) || !isset($_SESSION['perfil'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sessão expirada. Por favor, faça login novamente.'
    ]);
    exit();
}

// Verificar se é coordenador
if ($_SESSION['perfil'] != '2') {
    echo json_encode([
        'success' => false,
        'message' => 'Apenas coordenadores podem realizar esta operação.'
    ]);
    exit();
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método de requisição inválido.'
    ]);
    exit();
}

// Verificar ação
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action !== 'salvar_equipe_coordenador') {
    echo json_encode([
        'success' => false,
        'message' => 'Ação inválida.'
    ]);
    exit();
}

// Receber dados
$coordenador_id = isset($_POST['coordenador_id']) ? intval($_POST['coordenador_id']) : 0;
$coordenador_matricula = isset($_POST['coordenador_matricula']) ? $_POST['coordenador_matricula'] : '';
$supervisor_id = isset($_POST['supervisor_id']) ? intval($_POST['supervisor_id']) : 0;
$supervisor_matricula = isset($_POST['supervisor_matricula']) ? $_POST['supervisor_matricula'] : '';
$tecnicos = isset($_POST['tecnicos']) ? $_POST['tecnicos'] : [];

// Validações
if ($coordenador_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do coordenador inválido.'
    ]);
    exit();
}

if ($supervisor_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'ID do supervisor inválido.'
    ]);
    exit();
}

if (empty($tecnicos) || !is_array($tecnicos)) {
    echo json_encode([
        'success' => false,
        'message' => 'Nenhum técnico selecionado.'
    ]);
    exit();
}

// Verificar se o coordenador logado é o mesmo do POST
$matricula_logada = $_SESSION['matricula'];
$sql_verifica = "SELECT idtbcoordenador FROM bdfrota.tbcoord WHERE matricula = '$matricula_logada' AND idtbcoordenador = '$coordenador_id'";
$resultado_verifica = mysql_query($sql_verifica);

if (mysql_num_rows($resultado_verifica) == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Você não tem permissão para gerenciar esta equipe.'
    ]);
    exit();
}

try {
    // Iniciar transação (simulada com múltiplas queries)
    $erros = [];
    $sucessos = 0;
    
    // 1. Atualizar o supervisor para vincular ao coordenador
    $sql_update_supervisor = "UPDATE bdfrota.tbsupervisor 
                              SET idtbcoordenador = '$coordenador_id' 
                              WHERE idtbsupervisor = '$supervisor_id'";
    
    // UPDATE no servidor A (principal)
    $resultado_a = mysql_query($sql_update_supervisor);
    // UPDATE no servidor B (secundário)
    $resultado_b = mysqli_query($conn, $sql_update_supervisor);
    
    if (!$resultado_a || !$resultado_b) {
        $erros[] = "Erro ao atualizar supervisor no servidor " . (!$resultado_a ? "A" : "B");
    } else {
        // Log da ação
        $hoje = date('Y-m-d H:i:s');
        $acao = "Vinculou supervisor (ID: $supervisor_id) ao coordenador";
        $flag = '2';
        enviarlog($hoje, $acao, $flag, $supervisor_matricula, $matricula_logada, '', '');
    }
    
    // 2. Atualizar cada técnico para vincular ao supervisor
    foreach ($tecnicos as $matricula_tecnico) {
        $matricula_tecnico = mysql_real_escape_string($matricula_tecnico);
        
        // Atualizar na tabela tbusuario
        $sql_update_tecnico = "UPDATE bdfrota.tbusuario 
                               SET idtbsupervisor = '$supervisor_id' 
                               WHERE matricula = '$matricula_tecnico' AND perfil = 0";
        
        // UPDATE no servidor A (principal)
        $resultado_a = mysql_query($sql_update_tecnico);
        // UPDATE no servidor B (secundário)
        $resultado_b = mysqli_query($conn, $sql_update_tecnico);
        
        if ($resultado_a && $resultado_b) {
            $sucessos++;
            
            // Log da ação
            $hoje = date('Y-m-d H:i:s');
            $acao = "Vinculou técnico ao supervisor (ID: $supervisor_id)";
            $flag = '2';
            enviarlog($hoje, $acao, $flag, $matricula_tecnico, $matricula_logada, '', '');
        } else {
            $erros[] = "Erro ao atualizar técnico $matricula_tecnico no servidor " . (!$resultado_a ? "A" : "B");
        }
    }
    
    // Verificar resultados
    if (count($erros) > 0) {
        echo json_encode([
            'success' => false,
            'message' => "Alguns erros ocorreram:\n" . implode("\n", $erros) . "\n\nTécnicos atualizados com sucesso: $sucessos"
        ]);
    } else {
        // Buscar nomes para a mensagem
        $sql_nome_sup = "SELECT f.nome FROM bdfrota.tbsupervisor s, bdaniel.tbfuncionario f 
                         WHERE s.matricula = f.matricula AND s.idtbsupervisor = '$supervisor_id'";
        $resultado_nome_sup = mysql_query($sql_nome_sup);
        $row_nome_sup = mysql_fetch_array($resultado_nome_sup);
        $nome_supervisor = $row_nome_sup[0];
        
        echo json_encode([
            'success' => true,
            'message' => "Equipe atualizada com sucesso em ambos os servidores!\n\n" .
                        "Supervisor: $nome_supervisor\n" .
                        "Técnicos vinculados: $sucessos\n\n" .
                        "As alterações já estão em vigor nos dois servidores."
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar requisição: ' . $e->getMessage()
    ]);
}

// Fechar conexão
mysql_close();
?>
