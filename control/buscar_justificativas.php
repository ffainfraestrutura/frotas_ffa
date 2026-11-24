<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Inicia buffer de saída para capturar qualquer output indesejado
ob_start();

// Verifica se o usuário está logado
$nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : null;
$usuariof = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
$matricula = isset($_SESSION['matricula']) ? $_SESSION['matricula'] : null;
$perfil = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : null;

if ($perfil == null) {
    ob_end_clean();
    header("Content-type: application/json; charset=utf-8");
    echo json_encode(array('erro' => 'Você deve logar para ter acesso'), JSON_UNESCAPED_UNICODE);
    exit;
}

include "../conecta.php";

// Limpa o buffer e define o header
ob_end_clean();
header("Content-type: application/json; charset=utf-8");

// Define charset para UTF-8
mysql_query("SET NAMES 'utf8'");
mysql_query("SET character_set_connection=utf8");
mysql_query("SET character_set_client=utf8");
mysql_query("SET character_set_results=utf8");

// Recebe os filtros
$filtroMatricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';
$filtroPlaca = isset($_POST['placa']) ? trim($_POST['placa']) : '';
$filtroTipoAcao = isset($_POST['tipo_acao']) ? trim($_POST['tipo_acao']) : '';
$filtroDataInicio = isset($_POST['data_inicio']) ? trim($_POST['data_inicio']) : '';
$filtroDataFim = isset($_POST['data_fim']) ? trim($_POST['data_fim']) : '';

// Monta a query base
$sql = "SELECT 
            j.id,
            j.matricula_autor,
            DATE_FORMAT(j.data_hora, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada,
            j.data_hora,
            j.tipo_acao,
            j.justificativa,
            j.matricula_tecnico,
            j.placa,
            j.valor,
            f.nome AS nome_autor
        FROM bdfrota.tbjustificativa j
        LEFT JOIN bdaniel.tbfuncionario f ON j.matricula_autor = f.matricula
        WHERE 1=1";

// Adiciona filtros se informados
if ($filtroMatricula !== '') {
    $filtroMatricula = mysql_real_escape_string($filtroMatricula);
    $sql .= " AND j.matricula_tecnico LIKE '%$filtroMatricula%'";
}

if ($filtroPlaca !== '') {
    $filtroPlaca = mysql_real_escape_string($filtroPlaca);
    $sql .= " AND j.placa LIKE '%$filtroPlaca%'";
}

if ($filtroTipoAcao !== '') {
    $filtroTipoAcao = mysql_real_escape_string($filtroTipoAcao);
    $sql .= " AND j.tipo_acao = '$filtroTipoAcao'";
}

if ($filtroDataInicio !== '') {
    $filtroDataInicio = mysql_real_escape_string($filtroDataInicio);
    $sql .= " AND DATE(j.data_hora) >= '$filtroDataInicio'";
}

if ($filtroDataFim !== '') {
    $filtroDataFim = mysql_real_escape_string($filtroDataFim);
    $sql .= " AND DATE(j.data_hora) <= '$filtroDataFim'";
}

// Ordena por data mais recente
$sql .= " ORDER BY j.data_hora DESC LIMIT 500";

// Executa a query
$resultado = mysql_query($sql);

if (!$resultado) {
    echo json_encode(array('erro' => 'Erro ao buscar dados: ' . mysql_error()), JSON_UNESCAPED_UNICODE);
    exit;
}

// Verifica se há resultados
$numRows = mysql_num_rows($resultado);

if ($numRows == 0) {
    echo json_encode(array(), JSON_UNESCAPED_UNICODE);
    exit;
}

// Monta o array de resultados
$dados = array();

while ($row = mysql_fetch_array($resultado, MYSQL_ASSOC)) {
    $dados[] = array(
        'id' => isset($row['id']) ? $row['id'] : '',
        'matricula_autor' => isset($row['matricula_autor']) ? $row['matricula_autor'] : 'N/A',
        'nome_autor' => isset($row['nome_autor']) && !empty($row['nome_autor']) ? $row['nome_autor'] : 'N/A',
        'data_hora_formatada' => isset($row['data_hora_formatada']) ? $row['data_hora_formatada'] : '',
        'data_hora' => isset($row['data_hora']) ? $row['data_hora'] : '',
        'tipo_acao' => isset($row['tipo_acao']) ? $row['tipo_acao'] : '',
        'justificativa' => isset($row['justificativa']) ? utf8_encode($row['justificativa']) : 'N/A',
        'matricula_tecnico' => isset($row['matricula_tecnico']) && !empty($row['matricula_tecnico']) ? $row['matricula_tecnico'] : 'N/A',
        'placa' => isset($row['placa']) && !empty($row['placa']) ? $row['placa'] : 'N/A',
        'valor' => isset($row['valor']) ? $row['valor'] : '0.00'
    );
}

// Retorna os dados em JSON com encoding UTF-8
echo json_encode($dados, JSON_UNESCAPED_UNICODE);
?>
