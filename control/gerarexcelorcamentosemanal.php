<?php
session_start();
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
include("../conecta.php");

// Verificar se o usuário tem permissão
$perfil = $_SESSION['perfil'];
if ($perfil == null || $perfil == '0') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.history.back();</script>";
    exit;
}

// Receber parâmetros do formulário
$useg = $_POST["useg"];
$datam1 = $_POST["datam1"];
$gerente_filter = $_POST['gerente_filter'] ?? '';

// Construir filtro de gerente
$filtro_gerente_sql = '';
if (!empty($gerente_filter)) {
    $filtro_gerente_sql = " AND EXISTS (
        SELECT 1 FROM bdfrota.tbusuario u2
        JOIN bdfrota.tbsupervisor s2 ON u2.idtbsupervisor = s2.idtbsupervisor
        JOIN bdfrota.tbcoord c2 ON s2.idtbcoordenador = c2.idtbcoordenador
        JOIN bdfrota.tbgerente g2 ON c2.idtbgerente = g2.idtbgerente
        WHERE u2.matricula = s.matricula AND g2.matricula = '$gerente_filter'
    )";
}

header("Content-type: text/html; charset=utf-8");
?>
<!--**
  * gera excel - Orçamento Semanal
 *-->
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <title>Orçamento Semanal</title>
  </head>
  <body>
    <?php

    // Definimos o nome do arquivo que será exportado
    $arquivo = "Orcamento-Semanal-" . date('Y-m-d_H-i-s') . ".xls";

    // Determinar o período
    $u = DateTime::createFromFormat('Y-m-d', $useg);
    $d = DateTime::createFromFormat('Y-m-d', $datam1);
    $periodo = '';
    if ($u && $d) {
        $periodo = $u->format('d/m/Y') . ' até ' . $d->format('d/m/Y');
    } else {
        $periodo = $useg . ' até ' . $datam1;
    }

    // Nome do gerente se filtrado
    $nome_gerente_filtro = '';
    if (!empty($gerente_filter)) {
        $sql_nome_gerente = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$gerente_filter' AND status = 'Ativo'";
        $resultado_nome_gerente = mysql_query($sql_nome_gerente) or die(mysql_error());
        $row_nome_gerente = mysql_fetch_array($resultado_nome_gerente);
        $nome_gerente_filtro = $row_nome_gerente[0] ?? '';
    }

    // Criamos uma tabela HTML com o formato da planilha
    $html = "<table border='1' class='table table-bordered'>";
    
    // Cabeçalho informativo
    $html .= "<tr><td colspan='11' style='font-size: 16px; font-weight: bold; text-align: center; background-color: #4472C4; color: white;'>RELATÓRIO DE ORÇAMENTO SEMANAL</td></tr>";
    $html .= "<tr><td colspan='11' style='font-size: 12px; font-weight: bold; text-align: center; background-color: #D9E2F3;'>Período: $periodo</td></tr>";
    if ($nome_gerente_filtro) {
        $html .= "<tr><td colspan='11' style='font-size: 12px; font-weight: bold; text-align: center; background-color: #D9E2F3;'>Gerente: $nome_gerente_filtro</td></tr>";
    }
    $html .= "<tr><td colspan='11'>&nbsp;</td></tr>";
    
    // Cabeçalho da tabela
    $html .= "<tr style='background-color: #70AD47; color: white; font-weight: bold;'>
                <th>Matrícula</th>
                <th>Técnico</th>
                <th>Supervisor</th>
                <th>Coordenador</th>
                <th>Gerente</th>
                <th>Placa</th>
                <th>Cota Semanal 100%</th>
                <th>Cota Semanal 80%</th>
                <th>Cota Extra</th>
                <th>Valor Combustível</th>
                <th>KM Projetado Atual</th>
            </tr>";

    // Otimização: Buscar cota extra em uma única consulta
    $sql_cota_extra_all = "SELECT matricula, COALESCE(SUM(valorinserido), 0) as total_cota_extra 
                          FROM bdfrota.tbcotaextraac 
                          WHERE data >= '$useg 00:00:00' 
                          AND data <= '$datam1 23:59:59'
                          GROUP BY matricula";
    $resultado_cota_extra_all = mysql_query($sql_cota_extra_all) or die(mysql_error());
    $cota_extra_map = array();
    while ($row_ce = mysql_fetch_array($resultado_cota_extra_all)) {
        $cota_extra_map[$row_ce[0]] = $row_ce[1];
    }

    // Query otimizada com JOINs
    $sql = "SELECT 
            s.matricula,
            s.kmproj,
            s.kmosatual,
            s.orcsemanal,
            s.totalextra,
            s.kmproj - s.kmosatual as diferenca,
            s.valoraplicado,
            s.kmorcsem,
            s.slddscnt,
            s.sldprjtd,
            COALESCE(s.valorcomb, 0) as valorcomb,
            u.nome as nometec,
            u.idtbsupervisor,
            fs.nome as nomesup,
            fc.nome as nomecoordenador,
            fg.nome as nomegerente,
            cxc.ds_placa,
            v.unidade,
            atv.tipo as tipovel_desc,
            tv.km_litro_previsto,
            v.combustivel,
            tv2.km_litro_previsto as km_litro_vc,
            tv2.idtbtipoveiculo as id_tipo_vc
        FROM bdfrota.tbsaldo s
        LEFT JOIN bdfrota.tbusuario u ON s.matricula = u.matricula
        LEFT JOIN bdfrota.tbsupervisor sup ON u.idtbsupervisor = sup.idtbsupervisor
        LEFT JOIN bdaniel.tbfuncionario fs ON sup.matricula = fs.matricula
        LEFT JOIN bdfrota.tbcoord coord ON sup.idtbcoordenador = coord.idtbcoordenador
        LEFT JOIN bdaniel.tbfuncionario fc ON coord.matricula = fc.matricula AND fc.status = 'Ativo'
        LEFT JOIN bdfrota.tbgerente ger ON coord.idtbgerente = ger.idtbgerente
        LEFT JOIN bdaniel.tbfuncionario fg ON ger.matricula = fg.matricula AND fg.status = 'Ativo'
        LEFT JOIN bdfrota.tbcarrosxcolaborador cxc ON s.matricula = cxc.id_colaborador
        LEFT JOIN bdfrota.tbveiculo v ON cxc.ds_placa = v.placa
        LEFT JOIN bdfrota.tbatipovel atv ON v.tipovel = atv.idtbatipovel
        LEFT JOIN bdcombustivel.tbtipoveiculo tv ON v.tipovel = tv.idtbtipoveiculo
        LEFT JOIN bdcombustivel.tbtipoveiculo tv2 ON UPPER(atv.tipo) = UPPER(tv2.descricao) 
            AND UPPER(CASE WHEN v.combustivel = 'ALCOOL' THEN 'ETANOL' ELSE v.combustivel END) = UPPER(tv2.tipo_combustivel)
        WHERE s.data >= '$useg' 
        AND s.data <= '$datam1' 
        $filtro_gerente_sql
        GROUP BY s.matricula
        ORDER BY diferenca DESC";
    $resultado = mysql_query($sql) or die(mysql_error());

    $contador_registros = 0;

    while ($row = mysql_fetch_array($resultado)) {
        $row = array_map("utf8_decode", $row);
        $mattec = $row[0];
        $kmproj = $row[1];
        $kmos = $row[2];
        $cota_inicial_tecnico = $row[9]; // sldprjtd - Cota Inicial do Técnico
        $orcsem = $cota_inicial_tecnico;
        $orcsem80 = round(($cota_inicial_tecnico * 0.80), 2);
        
        // Buscar cota extra do array pré-carregado
        $totcot = isset($cota_extra_map[$mattec]) ? $cota_extra_map[$mattec] : 0;
        
        $valortotal = $row[6];
        $kmorcsem = $row[7];
        $kmorcsem80 = round($kmorcsem * 0.80, 2);
        $saldodesc = $row[8];
        $valorcomb_num = $row[10]; // Valor do combustível (numérico)
        $valorcomb = $row[10]; // Valor do combustível

        // Dados já vêm do JOIN
        $nometec = $row[11];
        $idsup = $row[12];
        $nomesup = $row[13];
        $nomecoordenador = $row[14] ?? 'N/A';
        $nomegerente = $row[15] ?? 'N/A';
        $placa = $row[16] ?? '';
        $unidade = $row[17] ?? '';
        $tipovel = $row[18] ?? '';
        $km_litro_previsto = $row[19] ?? 0;
        $combustivel = $row[20] ?? '';
        $km_litro_vc = $row[21] ?? 0;
        $id_tipo_vc = $row[22] ?? '';
        
        // Regra especial para veículos LEVE: desconsiderar combustível e usar valor fixo baseado na unidade
        if (strtoupper(trim($tipovel)) == 'LEVE') {
            if (strtoupper(trim($unidade)) == 'SP') {
                $km_litro_vc = 8;
            } else {
                $km_litro_vc = 10;
            }
        }
        
        // Total de combustível disponível = Cota Semanal 80% + Cota Extra
        $total_combustivel_disponivel = $orcsem80 + $totcot;
        
        // Calcular quantidade de litros total
        $qtd_litros = 0;
        if ($valorcomb_num > 0 && $total_combustivel_disponivel > 0) {
            $qtd_litros = $total_combustivel_disponivel / $valorcomb_num;
        }
        
        // Calcular KM Projetado Atual (Qtd. Litros Total × KM/L (V/C))
        $km_projetado_atual = 0;
        if ($km_litro_vc > 0 && $qtd_litros > 0) {
            $km_projetado_atual = $qtd_litros * $km_litro_vc;
        }

        if ($nomesup != '') {
            $html .= "<tr>
                        <td>'$mattec</td>
                        <td style=\"white-space: nowrap;\">$nometec</td>
                        <td>$nomesup</td>
                        <td>$nomecoordenador</td>
                        <td>$nomegerente</td>
                        <td>$placa</td>
                        <td style=\"text-align: right;\">R$ " . number_format($orcsem, 2, ',', '.') . "</td>
                        <td style=\"text-align: right;\">R$ " . number_format($orcsem80, 2, ',', '.') . "</td>
                        <td style=\"text-align: right;\">R$ " . number_format($totcot, 2, ',', '.') . "</td>
                        <td style=\"text-align: right;\">R$ " . number_format($valorcomb, 2, ',', '.') . "</td>
                        <td style=\"text-align: right;\">" . number_format($km_projetado_atual, 2, ',', '.') . "</td>
                      </tr>";
            
            $contador_registros++;
        }
    }

    // Linha de totais - apenas contador
    $html .= "<tr style='background-color: #FFC000; font-weight: bold;'>
                <td colspan='11' style='text-align: center;'>TOTAL DE REGISTROS: $contador_registros</td>
              </tr>";

    // Rodapé
    $html .= "<tr><td colspan='11'>&nbsp;</td></tr>";
    $html .= "<tr><td colspan='11' style='font-size: 10px; text-align: center;'>Relatório gerado em: " . date('d/m/Y H:i:s') . " por " . $_SESSION['nome'] . "</td></tr>";

    $html .= "</table>";

    // Configurações header para forçar o download
    header ("Expires: Mon, 07 Jul 2016 05:00:00 GMT");
    header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
    header ("Cache-Control: no-cache, must-revalidate");
    header ("Pragma: no-cache");
    header ("Content-type: application/x-msexcel");
    header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
    header ("Content-Description: PHP Generated Data" );
    
    // Envia o conteúdo do arquivo
    echo $html;
    ?>
  </body>
</html>
