<?php
// Funções de histórico de modificações - compatível com API mysql_* usada no projeto
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

/**
 * Grava um evento de histórico na tabela bdfrota.tb_historico_modificacoes
 * @param string $tabela Nome da tabela (sem prefixo do banco)
 * @param string $campoId Nome do campo ID (ex: idtbosrotalatlong)
 * @param string|int $registroId Valor do ID do registro modificado
 * @param string $acao 'INSERT'|'UPDATE'|'DELETE'
 * @param array|null $dadosAntes Snapshot associativo antes da alteração
 * @param array|null $dadosDepois Snapshot associativo depois da alteração
 */
function gravar_historico_rotas($tabela, $campoId, $registroId, $acao, $dadosAntes = null, $dadosDepois = null, $origem = null) {
    // pressupõe que ../conecta.php já foi incluído no contexto que chama a função
    $mat = isset($_SESSION['matricula']) ? $_SESSION['matricula'] : '';
    $nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : '';
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

    // Preparar JSONs (se necessário) e escapar
    $jsonAntes = ($dadosAntes !== null) ? mysql_real_escape_string(json_encode($dadosAntes, JSON_UNESCAPED_UNICODE)) : null;
    $jsonDepois = ($dadosDepois !== null) ? mysql_real_escape_string(json_encode($dadosDepois, JSON_UNESCAPED_UNICODE)) : null;

    $tabelaEsc = mysql_real_escape_string($tabela);
    $registroEsc = mysql_real_escape_string((string)$registroId);
    $acaoEsc = mysql_real_escape_string($acao);
    $matEsc = mysql_real_escape_string($mat);
    $nomeEsc = mysql_real_escape_string($nome);
    $ipEsc = mysql_real_escape_string($ip);
    $origemEsc = $origem !== null ? mysql_real_escape_string($origem) : mysql_real_escape_string('painel-suporte');

    // Inclui campoId dentro de observacoes para compatibilidade com esquema atual
    $observacoesArr = array();
    if (!empty($campoId)) {
        $observacoesArr['campo_id'] = $campoId;
    }
    $observacoesArr['gerado_por'] = 'gravar_historico_rotas';
    $observacoes = mysql_real_escape_string(json_encode($observacoesArr, JSON_UNESCAPED_UNICODE));

    // Montar INSERT compatível com a tabela existente:
    // (tabela, registro_id, acao, matricula_autor, nome_autor, data_hora, dados_anteriores, dados_novos, origem, ip_origem, observacoes, processado)
    $sql = "INSERT INTO bdfrota.tb_historico_modificacoes
      (tabela, registro_id, acao, matricula_autor, nome_autor, data_hora, dados_anteriores, dados_novos, origem, ip_origem, observacoes, processado)
      VALUES ";

    $values = "('$tabelaEsc', '$registroEsc', '$acaoEsc', '$matEsc', '$nomeEsc', NOW(), ";
    $values .= ($jsonAntes !== null ? "'$jsonAntes'" : "NULL") . ",";
    $values .= ($jsonDepois !== null ? "'$jsonDepois'" : "NULL") . ",";
    $values .= "'$origemEsc', '$ipEsc', ";
    $values .= ($observacoes !== null ? "'$observacoes'" : "NULL") . ", 0)";

    $sql .= $values;

    $res = @mysql_query($sql);
    if (!$res) {
        error_log('[historico_funcoes] Erro ao inserir historico: ' . mysql_error() . PHP_EOL . 'SQL: ' . $sql);
    }
}
?>
