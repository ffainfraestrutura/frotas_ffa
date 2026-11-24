<?php
// Desabilita exibição de erros para não quebrar o JSON
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Limpa qualquer output anterior
ob_start();

include "../conecta.php";

// Limpa qualquer warning do conecta.php
ob_end_clean();

header("Content-type: application/json; charset=utf-8");

// Log para debug
error_log("[buscar_historico_rotas] Iniciando busca - POST: " . print_r($_POST, true));


try {
    // Captura os filtros do POST
    $matricula = isset($_POST['matricula']) ? mysql_real_escape_string($_POST['matricula']) : '';
    $tabela = isset($_POST['tabela']) ? mysql_real_escape_string($_POST['tabela']) : '';
    $acao = isset($_POST['acao']) ? mysql_real_escape_string($_POST['acao']) : '';
    $dataInicio = isset($_POST['data_inicio']) ? mysql_real_escape_string($_POST['data_inicio']) : '';
    $dataFim = isset($_POST['data_fim']) ? mysql_real_escape_string($_POST['data_fim']) : '';
    $registro_id = isset($_POST['registro_id']) ? mysql_real_escape_string($_POST['registro_id']) : '';

    // Monta a query base
    $sql = "SELECT 
                id,
                tabela,
                registro_id,
                acao,
                matricula_autor,
                nome_autor,
                DATE_FORMAT(data_hora, '%d/%m/%Y %H:%i:%s') as data_hora_formatada,
                data_hora,
                dados_anteriores,
                dados_novos,
                origem,
                ip_origem,
                observacoes,
                processado
            FROM bdfrota.tb_historico_modificacoes 
            WHERE 1=1";

    // Adiciona os filtros
    if (!empty($matricula)) {
        $sql .= " AND matricula_autor = '$matricula'";
    }

    if (!empty($tabela)) {
        $sql .= " AND tabela LIKE '%$tabela%'";
    }

    if (!empty($acao)) {
        $sql .= " AND acao = '$acao'";
    }

    if (!empty($registro_id)) {
        $sql .= " AND registro_id = '$registro_id'";
    }

    if (!empty($dataInicio)) {
        $sql .= " AND DATE(data_hora) >= '$dataInicio'";
    }

    if (!empty($dataFim)) {
        $sql .= " AND DATE(data_hora) <= '$dataFim'";
    }

    // Ordena por data mais recente
    $sql .= " ORDER BY data_hora DESC LIMIT 100";

    // Log da query para debug
    error_log("[buscar_historico_rotas] SQL: " . $sql);

    // Executa a query
    $resultado = mysql_query($sql);

    if (!$resultado) {
        $erro = mysql_error();
        error_log("[buscar_historico_rotas] Erro MySQL: " . $erro);
        throw new Exception('Erro ao buscar histórico: ' . $erro . ' | SQL: ' . $sql);
    }

    // Log do número de resultados
    $numRows = mysql_num_rows($resultado);
    error_log("[buscar_historico_rotas] Registros encontrados: " . $numRows);

    // Monta o array de resultados
    $dados = array();
    while ($row = mysql_fetch_assoc($resultado)) {
        // Decodifica os JSONs para melhor visualização
        $dadosAnteriores = '';
        $dadosNovos = '';
        
        if (!empty($row['dados_anteriores'])) {
            $jsonAnt = json_decode($row['dados_anteriores'], true);
            if ($jsonAnt) {
                // Extrai campos relevantes
                $dadosAnteriores = 'OS: ' . ($jsonAnt['numero_de_ordem'] ?? 'N/A') . 
                                 ' | Endereço: ' . ($jsonAnt['endereco'] ?? '') . 
                                 ' | ETA: ' . ($jsonAnt['eta'] ?? '');
            }
        }
        
        if (!empty($row['dados_novos'])) {
            $jsonNov = json_decode($row['dados_novos'], true);
            if ($jsonNov) {
                // Extrai campos relevantes
                $dadosNovos = 'OS: ' . ($jsonNov['numero_de_ordem'] ?? 'N/A') . 
                            ' | Endereço: ' . ($jsonNov['endereco'] ?? '') . 
                            ' | ETA: ' . ($jsonNov['eta'] ?? '');
            }
        }

        $dados[] = array(
            'id' => $row['id'],
            'tabela' => str_replace('bdfrota.', '', $row['tabela']),
            'registro_id' => $row['registro_id'],
            'acao' => $row['acao'],
            'matricula_autor' => $row['matricula_autor'],
            'nome_autor' => $row['nome_autor'],
            'data_hora_formatada' => $row['data_hora_formatada'],
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'origem' => $row['origem'],
            'ip_origem' => $row['ip_origem'],
            'observacoes' => $row['observacoes'],
            'processado' => $row['processado']
        );
    }

    // Log antes de retornar
    error_log("[buscar_historico_rotas] Retornando " . count($dados) . " registros");
    
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);


} catch (Exception $e) {
    error_log("[buscar_historico_rotas] EXCEPTION: " . $e->getMessage());
    echo json_encode(array(
        'erro' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ), JSON_UNESCAPED_UNICODE);
}
?>
