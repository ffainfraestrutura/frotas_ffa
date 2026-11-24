<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Executa o mesmo controle de acesso da tela antes de consultar os dados.
ob_start();
require_once __DIR__ . '/controleacesso.php';
$saidaControleAcesso = trim(ob_get_clean());
if ($saidaControleAcesso !== '' || empty($_SESSION['perfil'])) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Você não tem permissão para exportar este relatório.');
}

require_once __DIR__ . '/../conecta.php';
$filtroCoordenador = $_GET['filtroCoordenador'] ?? '';
if (!is_string($filtroCoordenador)) {
    http_response_code(400);
    exit('Filtro de coordenador inválido.');
}
$whereCoordenador = $filtroCoordenador !== ''
    ? "AND tc.matricula = '" . mysqli_real_escape_string($conn, $filtroCoordenador) . "'"
    : '';
$sql = "SELECT
    mh.placa,
    tf.matricula,
    tf.nome,
    tf.ccusto,
    mh.hodometro,
    mh.previsao_manutencao,
    mh.km_medio_diario,
    am.data_agendada_gerencia,
    am.data_agendada_frota,
    mh.faixa_km_atual,
    CASE
        WHEN tc_chamado.idtbchamados IS NULL THEN 'Sem chamado'
        WHEN tc_chamado.data_fech IS NOT NULL THEN 'Encerrado'
        ELSE 'Aberto'
    END AS status_manutencao,
    tc_chamado.idtbchamados AS id_chamado,
    tc_chamado.data_abert,
    tc_chamado.data_fech,
    tc_chamado.resposta
FROM
    bdfrota.metricas_hodometro mh
    LEFT JOIN agendamento_manutencao am
        ON am.faixa_km = mh.faixa_km_atual
        AND am.placa = mh.placa
    JOIN bdfrota.tbveiculo tv
        ON mh.placa = tv.placa
    LEFT JOIN bdaniel.tbfuncionario tf
        ON tf.matricula = tv.matcond
    LEFT JOIN bdfrota.tbusuario tu
        ON tu.matricula = tf.matricula
    LEFT JOIN bdfrota.tbsupervisor ts
        ON tu.idtbsupervisor = ts.idtbsupervisor
    LEFT JOIN bdfrota.tbcoord tc
        ON ts.idtbcoordenador = tc.idtbcoordenador
    LEFT JOIN bdfrota.tbchamados tc_chamado
        ON tc_chamado.placa = mh.placa
        AND REPLACE(
            SUBSTRING_INDEX(tc_chamado.tipomanuprev, 'DE ', -1),
            '.', ''
        ) = mh.faixa_km_atual
    LEFT JOIN bdfrota.tbastatusvel tsv
        ON tsv.idtbastatusvel = tv.statusvel
    WHERE tsv.status NOT LIKE '%DESMOBILI%'
    " . $whereCoordenador . "
    GROUP BY placa
    ORDER BY
        CASE
            WHEN tc_chamado.data_fech IS NOT NULL THEN '9999-12-31'
            ELSE mh.previsao_manutencao
        END ASC";
$resultado = mysqli_query($conn, $sql);
if ($resultado === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Não foi possível consultar os dados para exportação.');
}

$linhasExcel = array(array(
    'Placa', 'Motorista', 'Centro de Custo', 'Hodômetro', 'Faixa Km',
    'Média Diária', 'Data Prevista', 'Status Manutenção', 'Status Chamado',
    'Data Gerência', 'Data Frota'
));
$hoje = new DateTime();
while ($registro = mysqli_fetch_assoc($resultado)) {
    $encerrado = !empty($registro['data_fech']) || $registro['status_manutencao'] === 'Encerrado';
    $dataPrevistaTexto = '-';
    $statusManutencao = 'Sem previsão';
    if ($encerrado) {
        $statusManutencao = 'Concluído';
    } elseif (!empty($registro['previsao_manutencao'])) {
        $dataPrevista = new DateTime($registro['previsao_manutencao']);
        $dias = (int) $hoje->diff($dataPrevista)->format('%r%a');
        $dataPrevistaTexto = $dataPrevista->format('d/m/Y');
        $statusManutencao = $dias <= 0 ? 'Urgente' : $dias . ' dias';
    }
    $linhasExcel[] = array(
        (string) $registro['placa'],
        $registro['nome'] . ' — Mat. ' . $registro['matricula'],
        (string) $registro['ccusto'],
        number_format($registro['hodometro'], 0, ',', '.') . ' km',
        number_format($registro['faixa_km_atual'], 0, ',', '.') . ' km',
        number_format($registro['km_medio_diario'], 1, ',', '.') . ' km',
        $dataPrevistaTexto,
        $statusManutencao,
        (string) $registro['status_manutencao'],
        !empty($registro['data_agendada_gerencia'])
            ? (new DateTime($registro['data_agendada_gerencia']))->format('d/m/Y') : '-',
        !empty($registro['data_agendada_frota'])
            ? (new DateTime($registro['data_agendada_frota']))->format('d/m/Y') : '-'
    );
}
mysqli_free_result($resultado);
$dadosExcel = json_encode($linhasExcel, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
if ($dadosExcel === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Não foi possível preparar os dados para exportação.');
}
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Exportar previsão de manutenção</title>
</head>
<body>
    <p id="statusExportacao" role="status">Preparando o Excel...</p>
    <button type="button" id="baixarExcel">Baixar novamente</button>
    <p><a href="previsaomanutencao.php">Voltar ao relatório</a></p>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script>
        function exportarExcel() {
            try {
                const linhas = <?= $dadosExcel ?>;
                const planilha = XLSX.utils.aoa_to_sheet(linhas);
                const arquivo = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(arquivo, planilha, 'Manutenção');
                XLSX.writeFile(arquivo, 'gestao_manutencao_frota.xlsx');
                document.getElementById('statusExportacao').textContent =
                    'Excel gerado. Se o download não iniciar, clique em Baixar novamente.';
            } catch (erro) {
                document.getElementById('statusExportacao').textContent =
                    'Não foi possível gerar o Excel. Tente novamente.';
            }
        }
        document.getElementById('baixarExcel').addEventListener('click', exportarExcel);
        exportarExcel();
    </script>
</body>
</html>
