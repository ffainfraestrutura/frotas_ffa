<?php

/**
 * Monta a consulta do relatório de LOG a partir dos filtros da tela.
 *
 * A conexão mysql_* deve estar aberta antes da chamada desta função.
 */
function montarConsultaRelatorioLog($filtros)
{
    $dataInicial = mysql_real_escape_string($filtros['datai']);
    $dataFinal = mysql_real_escape_string($filtros['dataf']);

    $sql = "SELECT l.acao, l.data_e_hora, l.matricula, l.mat_autor, l.valor_novo, "
         . "(SELECT f.nome FROM bdaniel.tbfuncionario f WHERE f.matricula = l.matricula LIMIT 1) AS nome_funcionario, "
         . "(SELECT a.nome FROM bdaniel.tbfuncionario a WHERE a.matricula = l.mat_autor LIMIT 1) AS nome_autor "
         . "FROM bdfrota.tblog l "
         . "WHERE l.data_e_hora >= '$dataInicial 00:00:00' "
         . "AND l.data_e_hora <= '$dataFinal 23:59:59'";

    $filtrosParciais = array(
        'acao' => 'l.acao',
        'matricula' => 'l.matricula',
        'mat_autor' => 'l.mat_autor',
        'placa' => 'l.placa'
    );

    foreach ($filtrosParciais as $filtro => $coluna) {
        if ($filtros[$filtro] !== '') {
            $valor = mysql_real_escape_string($filtros[$filtro]);
            $sql .= " AND $coluna LIKE '%$valor%'";
        }
    }

    if ($filtros['tipo'] !== '') {
        $tipo = mysql_real_escape_string($filtros['tipo']);
        $sql .= " AND l.tipo = '$tipo'";
    }

    return $sql . " ORDER BY l.data_e_hora DESC";
}

