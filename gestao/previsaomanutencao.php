<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
header("Content-Type: text/html; charset=utf-8");
session_start();
include_once "./controleacesso.php";
include_once "../conecta.php";
include_once "../func/log.php";

$filtroCoodenador_matricula = $_GET["filtroCoordenador"] ?? null;
$nome = $_SESSION['nome'] ?? '';
$usuariof = $_SESSION['usuario'] ?? '';
$matricula = $_SESSION['matricula'] ?? '';
$perfil = $_SESSION['perfil'] ?? null;
$filial = $_SESSION['filial'] ?? '';

if ($perfil == null) {
    echo "<script type='text/javascript'>alert('Você deve logar para ter acesso'); window.location=\"index.php\";</script>";
    exit;
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

if ($filtroCoodenador_matricula) {
    $filtroCoodenador_matricula = mysqli_real_escape_string($conn, $filtroCoodenador_matricula);
    $whereCoordenador = "AND tc.matricula = '$filtroCoodenador_matricula'";
} else {
    $whereCoordenador = "";
}

$matricula = mysqli_real_escape_string($conn, $matricula);

$sql_coordenadores = "
    SELECT tc.matricula, tf.nome
    FROM bdfrota.tbcoord tc
    JOIN bdaniel.tbfuncionario tf ON tc.matricula = tf.matricula
    ORDER BY tf.nome
";
$result_coordenadores = mysqli_query($conn, $sql_coordenadores);
$coordenadores = mysqli_fetch_all($result_coordenadores, MYSQLI_ASSOC);

$sql_registros = "SELECT 
    mh.placa,
    tf.matricula,
    tf.nome,
    tf.ccusto,
    mh.hodometro,
    mh.previsao_manutencao,
    mh.updated_at,
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

$result_registros = mysqli_query($conn, $sql_registros);
$registros = mysqli_fetch_all($result_registros, MYSQLI_ASSOC);
$hojeObj = new DateTime();

$totalVeiculos = count($registros);
$urgentes = 0;
$pendentesAgendamento = 0;
$encerrados = 0;

foreach ($registros as $r) {
    $isEnc = (!empty($r['data_fech']) || $r['status_manutencao'] === 'Encerrado');
    if ($isEnc) {
        $encerrados++;
    } else {
        if (!empty($r['previsao_manutencao'])) {
            $dtPrev = new DateTime($r['previsao_manutencao']);
            $diff = (int) $hojeObj->diff($dtPrev)->format("%r%a");
            if ($diff <= 0) {
                $urgentes++;
            }
        }
        if (empty($r['data_agendada_frota'])) {
            $pendentesAgendamento++;
        }
    }
}

// Percentuais para as barras de progresso
$pctUrgentes = $totalVeiculos > 0 ? round(($urgentes / $totalVeiculos) * 100) : 0;
$pctPendentes = $totalVeiculos > 0 ? round(($pendentesAgendamento / $totalVeiculos) * 100) : 0;
$pctEncerrados = $totalVeiculos > 0 ? round(($encerrados / $totalVeiculos) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Gestão de Manutenção de Frota | Portal FFA </title>

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.css"
    >
    <link href="../src/css/styles.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --brand-primary: #2563eb;
            --brand-primary-dark: #1d4ed8;
            --brand-success: #10b981;
            --brand-warning: #f59e0b;
            --brand-danger: #ef4444;
            --brand-info: #06b6d4;
            --surface: #ffffff;
            --surface-2: #f8fafc;
            --surface-3: #f1f5f9;
            --border: #e2e8f0;
            --border-soft: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
            --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06);
            --shadow-lg: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 13.5px;
            background-color: #f4f6f9;
            background-image:
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.04) 0px, transparent 50%);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* ===== HEADER HERO ===== */
        .page-hero {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: var(--radius-lg);
            padding: 26px 30px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 22px;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -60%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.35) 0%, transparent 70%);
            border-radius: 50%;
        }

        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: 30%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .page-hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-hero h3 {
            font-weight: 700;
            letter-spacing: -0.02em;
            margin: 0 0 4px 0;
            font-size: 1.55rem;
        }

        .page-hero p {
            color: rgba(255, 255, 255, 0.7);
            margin: 0;
            font-size: 13px;
        }

        .hero-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #fff;
            margin-right: 16px;
            flex-shrink: 0;
        }

        .btn-hero {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* ===== KPI CARDS ===== */
        .kpi-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            background: var(--surface);
            padding: 20px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--kpi-accent, var(--brand-primary));
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--kpi-accent, var(--brand-primary));
        }

        .kpi-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .kpi-label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .kpi-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.03em;
            color: var(--text-primary);
            font-variant-numeric: tabular-nums;
        }

        .kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
            background: var(--kpi-bg, rgba(37, 99, 235, 0.1));
            color: var(--kpi-accent, var(--brand-primary));
        }

        .kpi-progress {
            height: 5px;
            background: var(--surface-3);
            border-radius: 3px;
            overflow: hidden;
            margin-top: 12px;
        }

        .kpi-progress-bar {
            height: 100%;
            border-radius: 3px;
            background: var(--kpi-accent, var(--brand-primary));
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .kpi-meta {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
            font-weight: 500;
        }

        .kpi-primary { --kpi-accent: #2563eb; --kpi-bg: rgba(37, 99, 235, 0.1); }
        .kpi-danger  { --kpi-accent: #ef4444; --kpi-bg: rgba(239, 68, 68, 0.1); }
        .kpi-warning { --kpi-accent: #f59e0b; --kpi-bg: rgba(245, 158, 11, 0.12); }
        .kpi-success { --kpi-accent: #10b981; --kpi-bg: rgba(16, 185, 129, 0.1); }

        /* ===== MAIN CARD ===== */
        .modern-card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .modern-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(to bottom, #ffffff, #fbfcfe);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .modern-card-header h5 {
            font-weight: 700;
            font-size: 15px;
            margin: 0;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ===== FILTER BAR ===== */
        .filter-group {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-label {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 6px;
            display: block;
        }

        .form-select, .form-control {
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            font-size: 13px;
            padding: 8px 12px;
            transition: all 0.15s ease;
            background-color: var(--surface);
            color: var(--text-primary);
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .btn-modern {
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
        }

        .btn-modern.btn-primary {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
            border: none;
        }

        .btn-modern.btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .btn-modern.btn-outline {
            background: transparent;
            border-color: var(--border);
            color: var(--text-secondary);
        }

        .btn-modern.btn-outline:hover {
            background: var(--surface-2);
            border-color: #cbd5e1;
            color: var(--text-primary);
        }

        /* ===== TABLE ===== */
        .table-wrapper {
            padding: 4px 8px 12px;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 13px;
        }

        .custom-table thead th {
            background-color: var(--surface-2) !important;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10.5px;
            letter-spacing: 0.6px;
            border-bottom: 1px solid var(--border) !important;
            border-top: none !important;
            padding: 12px 14px !important;
            white-space: nowrap;
            position: sticky;
            top: 0;
        }

        .custom-table thead th:first-child {
            border-top-left-radius: var(--radius-sm);
        }

        .custom-table thead th:last-child {
            border-top-right-radius: var(--radius-sm);
        }

        .custom-table tbody tr {
            border-bottom: 1px solid var(--border-soft);
            transition: background-color 0.12s ease;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .custom-table tbody tr:last-child {
            border-bottom: none;
        }

        .custom-table tbody td {
            padding: 12px 14px !important;
            vertical-align: middle;
            border: none !important;
            color: var(--text-secondary);
        }

        /* Placa Badge */
        .badge-placa {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, #1e293b, #334155);
            color: #fff;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-block;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.2);
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Driver Cell */
        .driver-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .driver-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: var(--brand-primary-dark);
            font-weight: 700;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            letter-spacing: -0.3px;
        }

        .driver-info strong {
            display: block;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 13px;
            line-height: 1.3;
        }

        .driver-info small {
            color: var(--text-muted);
            font-size: 11px;
        }

        /* Badges */
        .status-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 5px 11px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .badge-urgente {
            background-color: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .badge-atencao {
            background-color: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }

        .badge-ok {
            background-color: #f0fdf4;
            color: #16a34a;
            border-color: #bbf7d0;
        }

        .badge-neutro {
            background-color: #f1f5f9;
            color: #64748b;
            border-color: #e2e8f0;
        }

        .badge-status-aberto {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .badge-status-encerrado {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .badge-status-sem {
            background-color: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* Dates & Numbers */
        .text-numeric {
            font-variant-numeric: tabular-nums;
            font-weight: 600;
            color: var(--text-primary);
        }

        .text-unit {
            font-size: 10.5px;
            color: var(--text-muted);
            font-weight: 500;
            margin-left: 2px;
        }

        /* Date Editor */
        .date-editor {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .date-display {
            font-weight: 700;
            color: var(--brand-primary);
            font-variant-numeric: tabular-nums;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .date-display:hover {
            background: rgba(37, 99, 235, 0.08);
        }

        .date-display.is-empty {
            color: var(--text-muted);
            font-weight: 500;
        }

        .btn-icon {
            width: 28px;
            height: 28px;
            border-radius: 7px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            padding: 0;
            cursor: pointer;
            font-size: 11px;
        }

        .btn-icon:hover {
            background: var(--surface-2);
            color: var(--brand-primary);
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        .btn-icon.btn-save {
            background: var(--brand-success);
            border-color: var(--brand-success);
            color: #fff;
        }

        .btn-icon.btn-save:hover {
            background: #059669;
            border-color: #059669;
            color: #fff;
        }

        .tooltip-observacao .tooltip-inner {
            max-width: 320px;
            text-align: left;
            white-space: pre-wrap;
            font-size: 12px;
            background-color: #0f172a;
            padding: 10px 12px;
            border-radius: 8px;
            line-height: 1.5;
        }

        .tooltip-observacao.bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #0f172a;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 12px;
        }

        /* ===== FOOTER ===== */
        .modern-footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 18px 24px;
            margin-top: 30px;
        }

        .modern-footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 10px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        /* ===== DATATABLES CUSTOM ===== */
        .datatable-wrapper .datatable-top,
        .datatable-wrapper .datatable-bottom {
            padding: 8px 20px;
        }

        .datatable-wrapper .datatable-input,
        .datatable-wrapper .datatable-selector {
            border-radius: var(--radius-sm) !important;
            border: 1px solid var(--border) !important;
            padding: 7px 12px !important;
            font-size: 13px !important;
            transition: all 0.15s ease;
        }

        .datatable-wrapper .datatable-input:focus,
        .datatable-wrapper .datatable-selector:focus {
            border-color: var(--brand-primary) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
            outline: none;
        }

        .datatable-wrapper .datatable-info,
        .datatable-wrapper .datatable-dropdown label {
            font-size: 12.5px;
            color: var(--text-muted);
        }

        .datatable-wrapper .datatable-pagination ul {
            gap: 3px;
            display: flex;
        }

        .datatable-wrapper .datatable-pagination a,
        .datatable-wrapper .datatable-pagination button {
            border-radius: 7px !important;
            border: 1px solid var(--border) !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            padding: 5px 10px !important;
            color: var(--text-secondary) !important;
            transition: all 0.15s ease;
            background: var(--surface);
        }

        .datatable-wrapper .datatable-pagination a:hover,
        .datatable-wrapper .datatable-pagination button:hover {
            background: var(--surface-2) !important;
            color: var(--brand-primary) !important;
        }

        .datatable-wrapper .datatable-pagination .datatable-active a,
        .datatable-wrapper .datatable-pagination .datatable-active button {
            background: var(--brand-primary) !important;
            color: #fff !important;
            border-color: var(--brand-primary) !important;
        }

        .datatable-wrapper .datatable-sorter::before,
        .datatable-wrapper .datatable-sorter::after {
            opacity: 0.4;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.4s ease both;
        }

        .animate-in:nth-child(1) { animation-delay: 0.02s; }
        .animate-in:nth-child(2) { animation-delay: 0.06s; }
        .animate-in:nth-child(3) { animation-delay: 0.1s; }
        .animate-in:nth-child(4) { animation-delay: 0.14s; }

        /* Responsive */
        @media (max-width: 768px) {
            .page-hero { padding: 20px; }
            .page-hero h3 { font-size: 1.2rem; }
            .kpi-value { font-size: 1.5rem; }
            .modern-card-header { padding: 16px; }
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">

                <!-- HERO HEADER -->
                <div class="page-hero animate-in">
                    <div class="page-hero-content">
                        <div class="d-flex align-items-center">
                            <div class="hero-icon">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </div>
                            <div>
                                <h3>Gestão de Manutenção de Frota</h3>
                                <p>Acompanhe prazos de revisão, chamados técnicos e agendamentos operacionais.</p>
                            </div>
                        </div>
                        <button class="btn-hero" onclick="exportarExcel()">
                            <i class="fa-solid fa-file-excel"></i>
                            Exportar Relatório
                        </button>
                    </div>
                </div>

                <!-- KPI CARDS -->
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card kpi-primary animate-in">
                            <div class="kpi-header">
                                <div>
                                    <div class="kpi-label">Frota Monitorada</div>
                                    <div class="kpi-value"><?= $totalVeiculos ?></div>
                                </div>
                                <div class="kpi-icon">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                            </div>
                            <div class="kpi-meta">Veículos ativos no monitoramento</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card kpi-danger animate-in">
                            <div class="kpi-header">
                                <div>
                                    <div class="kpi-label">Manutenção Urgente</div>
                                    <div class="kpi-value"><?= $urgentes ?></div>
                                </div>
                                <div class="kpi-icon">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>
                            </div>
                            <div class="kpi-progress">
                                <div class="kpi-progress-bar" style="width: <?= $pctUrgentes ?>%"></div>
                            </div>
                            <div class="kpi-meta"><?= $pctUrgentes ?>% da frota</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card kpi-warning animate-in">
                            <div class="kpi-header">
                                <div>
                                    <div class="kpi-label">Pendente Agendamento</div>
                                    <div class="kpi-value"><?= $pendentesAgendamento ?></div>
                                </div>
                                <div class="kpi-icon">
                                    <i class="fa-solid fa-calendar-minus"></i>
                                </div>
                            </div>
                            <div class="kpi-progress">
                                <div class="kpi-progress-bar" style="width: <?= $pctPendentes ?>%"></div>
                            </div>
                            <div class="kpi-meta"><?= $pctPendentes ?>% sem data agendada</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="kpi-card kpi-success animate-in">
                            <div class="kpi-header">
                                <div>
                                    <div class="kpi-label">Manutenções Concluídas</div>
                                    <div class="kpi-value"><?= $encerrados ?></div>
                                </div>
                                <div class="kpi-icon">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                            </div>
                            <div class="kpi-progress">
                                <div class="kpi-progress-bar" style="width: <?= $pctEncerrados ?>%"></div>
                            </div>
                            <div class="kpi-meta"><?= $pctEncerrados ?>% finalizadas</div>
                        </div>
                    </div>
                </div>

                <!-- MAIN CARD -->
                <div class="modern-card animate-in">
                    <div class="modern-card-header">
                        <h5>
                            <i class="fa-solid fa-list-check text-primary"></i>
                            Registros de Manutenção
                        </h5>
                        <div class="filter-group">
                            <div style="min-width: 260px;">
                                <label class="filter-label">Filtrar por Coordenador</label>
                                <select class="form-select" name="filtroCoordenador" id="filtroCoordenador">
                                    <option value="">Todos os Coordenadores</option>
                                    <?php foreach ($coordenadores as $coordenador): ?>
                                        <option value="<?= $coordenador['matricula'] ?>"
                                            <?= $filtroCoodenador_matricula == $coordenador['matricula'] ? "selected" : "" ?>>
                                            <?= $coordenador['nome'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button class="btn-modern btn-primary" onclick="aplicarFiltro()">
                                <i class="fa-solid fa-filter"></i>
                                Filtrar
                            </button>
                            <button class="btn-modern btn-outline" onclick="limparFiltro()">
                                <i class="fa-solid fa-rotate-left"></i>
                                Limpar
                            </button>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <div class="table-responsive">
                            <table class="table align-middle datatable custom-table" id="tabelaManutencao">
                                <thead>
                                    <tr>
                                        <th>Placa</th>
                                        <th>Motorista</th>
                                        <th>Centro de Custo</th>
                                        <th>Hodômetro</th>
                                        <th>Faixa Km</th>
                                        <th>Média Diária</th>
                                        <th>Data Prevista</th>
                                        <th>Última atualização</th>
                                        <th>Status Manutenção</th>
                                        <th>Status Chamado</th>
                                        <th>Data Gerência</th>
                                        <th>Data Frota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($registros as $registro): ?>
                                        <?php
                                        $isEncerrado = (!empty($registro['data_fech']) || $registro['status_manutencao'] === 'Encerrado');

                                        $dataUltimaAtualizacao = !empty($registro['updated_at'])
                                            ? (new DateTime($registro['updated_at']))->format('d/m/Y')
                                            : '-';

                                        if ($isEncerrado) {
                                            $dataFormatada = '-';
                                            $badgeClass = 'badge-neutro';
                                            $textoDias = 'Concluído';
                                            $tooltipTexto = 'Manutenção concluída / Aguardando novos dados de leitura';
                                            $dataOrderValue = '9999-12-31';
                                            $diasRestantes = null;
                                        } elseif (!empty($registro['previsao_manutencao'])) {
                                            $dataPrevista = new DateTime($registro['previsao_manutencao']);
                                            $intervalo = $hojeObj->diff($dataPrevista);
                                            $diasRestantes = (int) $intervalo->format("%r%a");

                                            $dataFormatada = $dataPrevista->format('d/m/Y');
                                            $dataOrderValue = $dataPrevista->format('Y-m-d');
                                            $tooltipTexto = '';
                                            if ($diasRestantes <= 0) {
                                                $badgeClass = "badge-urgente";
                                                $textoDias = "Urgente";
                                            } elseif ($diasRestantes <= 15) {
                                                $badgeClass = "badge-atencao";
                                                $textoDias = $diasRestantes . " dias";
                                            } else {
                                                $badgeClass = "badge-ok";
                                                $textoDias = $diasRestantes . " dias";
                                            }
                                        } else {
                                            $dataFormatada = '-';
                                            $textoDias = 'Sem previsão';
                                            $badgeClass = 'badge-neutro';
                                            $tooltipTexto = '';
                                            $dataOrderValue = '';
                                            $diasRestantes = null;
                                        }

                                        switch ($registro['status_manutencao']) {
                                            case 'Aberto':
                                                $statusBadgeClass = 'badge-status-aberto';
                                                $statusIcon = 'fa-circle-dot';
                                                break;
                                            case 'Encerrado':
                                                $statusBadgeClass = 'badge-status-encerrado';
                                                $statusIcon = 'fa-circle-check';
                                                break;
                                            default:
                                                $statusBadgeClass = 'badge-status-sem';
                                                $statusIcon = 'fa-circle-minus';
                                                break;
                                        }

                                        $observacao = htmlspecialchars((!empty($registro['id_chamado']) ? 'Chamado #' . $registro['id_chamado'] . ' — ' : '') . ($registro['resposta'] ?? 'Sem observação registrada'), ENT_QUOTES);

                                        // Avatar initials
                                        $initials = '';
                                        if (!empty($registro['nome'])) {
                                            $parts = explode(' ', trim($registro['nome']));
                                            $initials = strtoupper(substr($parts[0], 0, 1));
                                            if (count($parts) > 1) {
                                                $initials .= strtoupper(substr(end($parts), 0, 1));
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="badge-placa"><?= $registro['placa'] ?></span>
                                            </td>

                                            <td>
                                                <div class="driver-cell">
                                                    <div class="driver-avatar"><?= $initials ?></div>
                                                    <div class="driver-info">
                                                        <strong><?= $registro['nome'] ?></strong>
                                                        <small>Mat. <?= $registro['matricula'] ?></small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <span class="fw-medium"><?= $registro['ccusto'] ?></span>
                                            </td>

                                            <td data-order="<?= $registro['hodometro'] ?>">
                                                <span class="text-numeric"><?= number_format($registro['hodometro'], 0, ',', '.') ?></span><span class="text-unit">km</span>
                                            </td>

                                            <td data-order="<?= $registro['faixa_km_atual'] ?>">
                                                <span class="text-numeric"><?= number_format($registro['faixa_km_atual'], 0, ',', '.') ?></span><span class="text-unit">km</span>
                                            </td>

                                            <td data-order="<?= $registro['km_medio_diario'] ?>">
                                                <span class="text-numeric"><?= number_format($registro['km_medio_diario'], 1, ',', '.') ?></span><span class="text-unit">km/dia</span>
                                            </td>

                                            <td data-order="<?= htmlspecialchars($dataOrderValue, ENT_QUOTES, 'UTF-8') ?>">
                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($dataFormatada ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                                            </td>

                                            <td data-order="<?= !empty($registro['updated_at']) ? (new DateTime($registro['updated_at']))->format('Y-m-d') : '' ?>">
                                                <span class="fw-medium text-dark"><?= htmlspecialchars($dataUltimaAtualizacao, ENT_QUOTES, 'UTF-8') ?></span>
                                            </td>

                                            <td>
                                                <?php if ($isEncerrado): ?>
                                                    <span class="status-badge badge-ok">
                                                        <i class="fa-solid fa-check-circle"></i>
                                                        Concluído
                                                    </span>
                                                <?php elseif ($diasRestantes !== null && $diasRestantes <= 0): ?>
                                                    <span class="status-badge badge-urgente">
                                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                                        Urgente
                                                    </span>
                                                <?php elseif ($diasRestantes !== null && $diasRestantes <= 15): ?>
                                                    <span class="status-badge badge-atencao">
                                                        <i class="fa-solid fa-clock"></i>
                                                        <?= $textoDias ?>
                                                    </span>
                                                <?php elseif ($diasRestantes !== null): ?>
                                                    <span class="status-badge badge-ok">
                                                        <i class="fa-solid fa-circle-check"></i>
                                                        <?= $textoDias ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge badge-neutro">
                                                        <i class="fa-solid fa-minus"></i>
                                                        Sem previsão
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <span class="status-badge <?= $statusBadgeClass ?>"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-custom-class="tooltip-observacao" title="<?= $observacao ?>">
                                                    <i class="fa-solid <?= $statusIcon ?>"></i>
                                                    <?= $registro['status_manutencao'] ?>
                                                </span>
                                            </td>

                                            <?php
                                            $dtGerencia = !empty($registro['data_agendada_gerencia'])
                                                ? (new DateTime($registro['data_agendada_gerencia']))->format('d/m/Y')
                                                : '-';
                                            ?>
                                            <td data-order="<?= !empty($registro['data_agendada_gerencia']) ? (new DateTime($registro['data_agendada_gerencia']))->format('Y-m-d') : '' ?>">
                                                <span class="fw-medium text-dark"><?= $dtGerencia ?></span>
                                            </td>

                                            <?php
                                            $dtFrota = !empty($registro['data_agendada_frota'])
                                                ? (new DateTime($registro['data_agendada_frota']))->format('d/m/Y')
                                                : '-';
                                            $temData = !empty($registro['data_agendada_frota']);
                                            ?>
                                            <td data-placa="<?= $registro['placa'] ?>"
                                                data-faixa="<?= $registro['faixa_km_atual'] ?>"
                                                data-order="<?= $temData ? (new DateTime($registro['data_agendada_frota']))->format('Y-m-d') : '' ?>">
                                                <div class="date-editor">
                                                    <span class="date-display <?= !$temData ? 'is-empty' : '' ?>">
                                                        <?php if ($temData): ?>
                                                            <i class="fa-regular fa-calendar-check" style="font-size: 11px; opacity: 0.7;"></i>
                                                        <?php else: ?>
                                                            <i class="fa-regular fa-calendar" style="font-size: 11px; opacity: 0.5;"></i>
                                                        <?php endif; ?>
                                                        <?= $dtFrota ?>
                                                    </span>

                                                    <input type="date"
                                                        class="form-control form-control-sm d-none input-data"
                                                        style="max-width: 140px; padding: 4px 8px; font-size: 12px;"
                                                        value="<?= $temData ? (new DateTime($registro['data_agendada_frota']))->format('Y-m-d') : '' ?>">

                                                    <button class="btn-icon btn-editar" title="Editar data">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </button>

                                                    <button class="btn-icon btn-save btn-salvar d-none" title="Salvar">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <footer class="modern-footer">
            <div class="modern-footer-content">
                <div class="footer-brand">
                    <i class="fa-solid fa-truck-fast text-primary"></i>
                    <span>FFA Infraestrutura</span>
                </div>
                <div>
                    &copy; <?= date('Y') ?> — Todos os direitos reservados
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.8/js/dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="exportar_previsao_manutencao.js"></script>

    <script>
        function aplicarFiltro() {
            let coord = document.getElementById('filtroCoordenador').value;
            window.location.href = "?filtroCoordenador=" + coord;
        }

        function limparFiltro() {
            window.location.href = window.location.pathname;
        }

        // function exportarExcel() {
        //     let tabela = document.querySelector("table");
        //     let wb = XLSX.utils.table_to_book(tabela, { sheet: "Manutenção" });
        //     XLSX.writeFile(wb, "gestao_manutencao_frota.xlsx");
        // }

        document.addEventListener('DOMContentLoaded', function () {
            let dataTable = null;
            const tabela = document.querySelector('#tabelaManutencao');

            if (tabela) {
                dataTable = new DataTable(tabela, {
                    searching: true,
                    paging: true,
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    autoWidth: false,
                    order: [],
                    columnDefs: [
                        { targets: 5, type: 'string' },
                        { targets: [6, 9], orderable: true }
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/2.3.3/i18n/pt-BR.json',
                        search: '',
                        searchPlaceholder: '🔍 Buscar na tabela...',
                        lengthMenu: '_MENU_ registros por página'
                    },
                    drawCallback: function () {
                        initTooltips();
                    }
                });

                function initTooltips() {
                    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
                        return;
                    }

                    tabela.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                        const existing = bootstrap.Tooltip.getInstance(el);

                        if (existing) {
                            existing.dispose();
                        }

                        new bootstrap.Tooltip(el);
                    });
                }

                initTooltips();
            }

            document.addEventListener('click', function (e) {
                // EDITAR
                if (e.target.closest('.btn-editar')) {
                    const td = e.target.closest('td');
                    if (!td) return;

                    const display = td.querySelector('.date-display');
                    const input = td.querySelector('.input-data');
                    const btnEditar = td.querySelector('.btn-editar');
                    const btnSalvar = td.querySelector('.btn-salvar');

                    if (!display || !input || !btnEditar || !btnSalvar) return;

                    display.classList.add('d-none');
                    input.classList.remove('d-none');
                    input.focus();
                    btnEditar.classList.add('d-none');
                    btnSalvar.classList.remove('d-none');
                }

                // SALVAR
                if (e.target.closest('.btn-salvar')) {
                    const td = e.target.closest('td');
                    if (!td) return;

                    const input = td.querySelector('.input-data');
                    if (!input) return;

                    const novaData = input.value;

                    if (!novaData) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atenção',
                            text: 'Selecione uma data válida antes de salvar',
                            confirmButtonColor: '#2563eb'
                        });
                        return;
                    }

                    const placa = td.dataset.placa;
                    const faixa_km = td.dataset.faixa;

                    Swal.fire({
                        title: 'Salvando...',
                        text: 'Aguarde um momento',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    fetch('./control/salvar_data_manutencao.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            placa: placa,
                            faixa_km: faixa_km,
                            data: novaData
                        })
                    })
                        .then(res => {
                            if (!res.ok) {
                                throw new Error('Erro HTTP ' + res.status);
                            }

                            return res.json();
                        })
                        .then(data => {
                            Swal.close();

                            if (data.success) {
                                const [ano, mes, dia] = novaData.split('-');
                                const dataFormatada = `${dia}/${mes}/${ano}`;

                                const display = td.querySelector('.date-display');
                                display.innerHTML =
                                    '<i class="fa-regular fa-calendar-check" style="font-size: 11px; opacity: 0.7;"></i> ' +
                                    dataFormatada;

                                display.classList.remove('is-empty', 'd-none');
                                td.setAttribute('data-order', novaData);

                                input.classList.add('d-none');
                                td.querySelector('.btn-editar').classList.remove('d-none');
                                td.querySelector('.btn-salvar').classList.add('d-none');

                                if (dataTable) {
                                    // Atualiza o cache da célula sem perder a página atual.
                                    dataTable.cell(td).invalidate('dom').draw(false);
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso',
                                    text: 'Data agendada atualizada!',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: data.message || 'Erro ao salvar alteração',
                                    confirmButtonColor: '#2563eb'
                                });
                            }
                        })
                        .catch(err => {
                            Swal.close();

                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Falha de conexão com o servidor',
                                confirmButtonColor: '#2563eb'
                            });

                            console.error(err);
                        });
                }
            });
        });
    </script>
</body>

</html>