<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil == '0') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

// Verificar se o formulário foi submetido
$mostrar_dados = isset($_POST['useg']) || isset($_POST['gerente_filter']);

$useg = $_POST["useg"] ?? '';
if ($useg == '') {
    $useg = date('Y-m-d', strtotime('last monday'));
    $datam1 = date("Y-m-d", strtotime("-1 day"));
} else {
    $datam1 = date('Y-m-d', strtotime($useg . "+6 days"));
}

// Filtro por gerente
$gerente_filter = $_POST['gerente_filter'] ?? '';
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


include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Orcamento Semanal DEV</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-3 mb-3"> Orçamento Semanal</h1>
                
                <div id="collapseExample4" class="mb-4" style="width: 70%;">
                    <form action="./detalhes-kmproj-kmosDEV.php" method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inputGroupSelect04" class="form-label">Período:</label>
                                <select class="form-select" id="inputGroupSelect04" name="useg">
                                    <?php
                                    $sql = "SELECT distinct(data) from bdfrota.tbsaldo order by data desc";
                                    $resultado = mysql_query($sql) or die(mysql_error());

                                    while ($row = mysql_fetch_array($resultado)) {
                                        $dataa = $row[0];
                                        $usega = explode(" ", $dataa);
                                        $usegf = $usega[0];
                                        $datam1f = date('Y-m-d', strtotime($usegf . "+6 days"));

                                        $dataesca = explode("-", $usegf);
                                        $dataescf1 = $dataesca[2] . "/" . $dataesca[1] . "/" . $dataesca[0];

                                        $dataesca = explode("-", $datam1f);
                                        $dataescf2 = $dataesca[2] . "/" . $dataesca[1] . "/" . $dataesca[0];

                                        $selected = ($usegf == $useg) ? 'selected' : '';
                                        print "<option value='$usegf' $selected>$dataescf1 até $dataescf2</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="gerenteFilter" class="form-label">Gerente:</label>
                                <select class="form-select" id="gerenteFilter" name="gerente_filter">
                                    <option value="">Todos os Gerentes</option>
                                    <?php
                                    // Buscar todos os gerentes ativos
                                    $sql_gerentes = "SELECT DISTINCT g.matricula, f.nome 
                                                   FROM bdfrota.tbgerente g
                                                   JOIN bdaniel.tbfuncionario f ON g.matricula = f.matricula
                                                   WHERE f.status = 'Ativo'
                                                   ORDER BY f.nome";
                                    $resultado_gerentes = mysql_query($sql_gerentes) or die(mysql_error());
                                    
                                    $gerente_selecionado = $_POST['gerente_filter'] ?? '';
                                    
                                    while ($row_gerente = mysql_fetch_array($resultado_gerentes)) {
                                        $matricula_gerente = $row_gerente[0];
                                        $nome_gerente = $row_gerente[1];
                                        $selected = ($matricula_gerente == $gerente_selecionado) ? 'selected' : '';
                                        print "<option value='$matricula_gerente' $selected>$nome_gerente</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <input type="submit" class="btn btn-success" value="Filtrar">
                                <input type="button" class="btn btn-outline-secondary" value="Limpar Filtros" onclick="window.location.href='detalhes-kmproj-kmosDEV.php'">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card mb-4 mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voltar para Dashboard Inicial
                        </a>
                        <button id="exportExcel" class="btn btn-success">
                            <i class="fas fa-file-excel"></i>
                            Exportar Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <!--tabela para entrada de dados-->
                        <table id='datatablesSimple' class='table table-bordered' style="font-size: 12px !important;">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Técnico</th>
                                    <th>Supervisor</th>
                                    <th>Coordenador</th>
                                    <th>Gerente</th>
                                    <th>Placa</th>
                                    <!--<th>Unidade</th>-->
                                    <!--<th>Tipo de Veículo</th>-->
                                    <!--<th>Tipo Combustível</th>-->
                                    <!--<th>Veículo/Combustível</th>-->
                                    <!--<th>ID Tipo V/C</th>-->
                                    <!--<th>KM/L Previsto</th>-->
                                    <!--<th>KM/L (V/C)</th>-->
                                    <!--<th>Qtd. Litros</th>-->
                                    <th>Cota Semanal 100%</th>
                                    <th>Cota Semanal 80%</th>
                                    <!--<th>KM Proj.</th>-->
                                    <th>Cota Extra</th>
                                    <th>Valor Combustível</th>
                                    <!--<th>KM Atual</th>-->
                                    <th>KM Projetado Atual</th>
                                    <th>Deslocamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                
                                if (!$mostrar_dados) {
                                    echo "<tr><td colspan='16' class='text-center'><div class='alert alert-info m-3'>Por favor, selecione um período e clique em <strong>Filtrar</strong> para exibir os dados.</div></td></tr>";
                                } else {

                                if (strftime('t') >= 21) {

                                    $mesini = date('m');
                                    $anoini = date('y');
                                    if (date('m') == 12) {
                                        $mesfim = 1;
                                        $anofim = date('y', strtotime('next year'));
                                    } else {

                                        $mesfim = date('m', strtotime('next month'));
                                        $anofim = date('y');
                                    }

                                } else {

                                    $mesfim = date('m');
                                    $anofim = date('y');
                                    if (date('m') == 1) {
                                        $mesini = 12;
                                        $anoini = date('y', strtotime('last year'));
                                    } else {
                                        $mesini = date('m', strtotime('last month'));
                                        $anoini = date('y');
                                    }

                                }

                                $diah = strftime('t');
                                $ontem = $diah - 1;

                                $dataini = "$anoini-$mesini-21";
                                $datafim = "$anofim-$mesfim-$ontem";


                                if (strftime('%A') == "segunda") {

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

                                    // Query otimizada com JOINs para trazer todos os dados de uma vez
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

                                    while ($row = mysql_fetch_array($resultado)) {

                                        $mattec = $row[0];
                                        $kmproj = $row[1];
                                        $kmos = $row[2];
                                        $diff = $kmproj - $kmos;
                                        $cota_inicial_tecnico = $row[9]; // sldprjtd - Cota Inicial do Técnico
                                        $orcsem = $cota_inicial_tecnico;
                                        $orcsem80 = round(($cota_inicial_tecnico * 0.80), 2);
                                        
                                        // Buscar cota extra do array pré-carregado
                                        $totcot = isset($cota_extra_map[$mattec]) ? $cota_extra_map[$mattec] : 0;
                                        
                                        $valortotal = $row[6];
                                        $kmorcsem = $row[7];
                                        $kmorcsem80 = round($kmorcsem * 0.80, 2);
                                        $saldodesc = $row[8];
                                        $valorcomb = number_format($row[10], 2, ',', '.');
                                        
                                        // Calcular KM Projetado Atual usando (Cota Semanal 80% + Cota Extra)
                                        // Fórmula: ((Cota Semanal 80% + Cota Extra) / Valor Combustível) × KM/L Previsto
                                        $valorcomb_num = $row[10]; // Valor numérico para cálculo
                                        
                                        // Total de combustível disponível = Cota Semanal 80% + Cota Extra
                                        $total_combustivel_disponivel = $orcsem80 + $totcot;
                                        
                                        // Calcular quantidade de litros total
                                        $qtd_litros = 0;
                                        if ($valorcomb_num > 0 && $total_combustivel_disponivel > 0) {
                                            $qtd_litros = $total_combustivel_disponivel / $valorcomb_num;
                                        }
                                        $qtd_litros_fmt = number_format($qtd_litros, 2, ',', '.');

                                        // Dados já vêm do JOIN
                                        $nometec = $row[11];
                                        $idsup = $row[12];
                                        $nomesup = $row[13];
                                        $nomecoordenador = $row[14] ?? 'N/A';
                                        $nomegerente = $row[15] ?? 'N/A';
                                        $placa = $row[16];
                                        $unidade = $row[17] ?? '';
                                        $tipovel = $row[18] ?? '';
                                        $km_litro_previsto = $row[19] ?? 0;
                                        $km_litro_previsto_fmt = number_format($km_litro_previsto, 2, ',', '.');
                                        $combustivel = $row[20] ?? '';
                                        $km_litro_vc = $row[21] ?? 0;
                                        
                                        // Regra especial para veículos LEVE: desconsiderar combustível e usar valor fixo baseado na unidade
                                        if (strtoupper(trim($tipovel)) == 'LEVE') {
                                            if (strtoupper(trim($unidade)) == 'SP') {
                                                $km_litro_vc = 8;
                                            } else {
                                                $km_litro_vc = 10;
                                            }
                                        }
                                        
                                        $km_litro_vc_fmt = number_format($km_litro_vc, 2, ',', '.');
                                        $id_tipo_vc = $row[22] ?? '';
                                        
                                        // Substituir ALCOOL por ETANOL para exibição
                                        $combustivel_exibicao = $combustivel;
                                        if (strtoupper($combustivel) == 'ALCOOL') {
                                            $combustivel_exibicao = 'ETANOL';
                                        }
                                        
                                        // Concatenar tipo de veículo e combustível (nesta ordem)
                                        $veiculo_combustivel = trim($tipovel . ' ' . $combustivel_exibicao);
                                        
                                        // Calcular KM Projetado Atual (Qtd. Litros Total × KM/L (V/C))
                                        $km_projetado_atual = 0;
                                        if ($km_litro_vc > 0 && $qtd_litros > 0) {
                                            $km_projetado_atual = $qtd_litros * $km_litro_vc;
                                        }
                                        $km_projetado_atual_fmt = number_format($km_projetado_atual, 2, ',', '.');

                                        // Conexão para GPS (mantida pois é outro servidor)
                                        $kmgps = 0;
                                        if ($placa) {
                                            $servidor5 = "10.17.0.5";
                                            $usuario5 = "blvp";
                                            $senha5 = "@F3r7n6c2";
                                            $db5 = "bdfrota";
                                            $conexao5 = mysqli_connect("$servidor5", "$usuario5", "$senha5", "$db5") or die(mysqli_connect_error());

                                            $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$useg' and datagps <= '$datam1' group by placa";
                                            $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
                                            $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                                            $kmgps = $row5[0];
                                            mysqli_close($conexao5);
                                        }

                                        if ($nomesup <> '') {
                                            print "<tr><td>$mattec</td>
              <td>$nometec</td>
              <td>$nomesup</td>
              <td>$nomecoordenador</td>
              <td>$nomegerente</td>
              <td>$placa</td>
              <!--<td>$unidade</td>-->
              <!--<td>$tipovel</td>-->
              <!--<td>$combustivel_exibicao</td>-->
              <!--<td>$veiculo_combustivel</td>-->
              <!--<td>$id_tipo_vc</td>-->
              <!--<td>$km_litro_previsto_fmt</td>-->
              <!--<td>$km_litro_vc_fmt</td>-->
              <!--<td style='white-space:  nowrap;'>$qtd_litros_fmt L</td>-->
              <td style='white-space:  nowrap;'>R$ $orcsem</td>
              <td style='white-space:  nowrap;'>R$ $orcsem80</td>
              <!--<td style='white-space:  nowrap;'>$kmorcsem80</td>-->
              <td style='white-space:  nowrap;'>R$ $totcot</td>
              <td style='white-space:  nowrap;'>R$ $valorcomb</td>
              <!--<td style='white-space:  nowrap;'>$kmos</td>-->
              <td style='white-space:  nowrap;'>$km_projetado_atual_fmt</td>
              <td><button class='btn btn-primary btn-sm' onclick=\"window.open('/frotas/painel-suporte/rotaspmatric.php?mattec=$mattec&diai=$useg', '_blank')\"><i class='fas fa-external-link-alt'></i> Detalhes</button></td>
              </tr> ";
                                        }

                                    }

                                } else {

                                    $domingo = date('Y-m-d', strtotime('last sunday'));
                                    $hoje = date('Y-m-d');

                                    $datetime1 = new DateTime($domingo);
                                    $datetime2 = new DateTime($hoje);
                                    $interval = $datetime1->diff($datetime2);
                                    $differenceInDays = $interval->format('%a');

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

                                    // Query otimizada com JOINs para trazer todos os dados de uma vez
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

                                    while ($row = mysql_fetch_array($resultado)) {

                                        $mattec = $row[0];
                                        $kmproj = $row[1];
                                        $kmos = $row[2];
                                        $diff = $kmproj - $kmos;
                                        $cota_inicial_tecnico = $row[9]; // sldprjtd - Cota Inicial do Técnico
                                        $orcsem = $cota_inicial_tecnico;
                                        $orcsem80 = round(($cota_inicial_tecnico * 0.80), 2);
                                        
                                        // Buscar cota extra do array pré-carregado
                                        $totcot = isset($cota_extra_map[$mattec]) ? $cota_extra_map[$mattec] : 0;
                                        
                                        $valortotal = $row[6];
                                        $kmorcsem = $row[7];
                                        $kmorcsem80 = round($kmorcsem * 0.80, 2);
                                        $saldodesc = $row[8];
                                        $valorcomb = number_format($row[10], 2, ',', '.');
                                        
                                        // Calcular KM Projetado Atual usando (Cota Semanal 80% + Cota Extra)
                                        // Fórmula: ((Cota Semanal 80% + Cota Extra) / Valor Combustível) × KM/L Previsto
                                        $valorcomb_num = $row[10]; // Valor numérico para cálculo
                                        
                                        // Total de combustível disponível = Cota Semanal 80% + Cota Extra
                                        $total_combustivel_disponivel = $orcsem80 + $totcot;
                                        
                                        // Calcular quantidade de litros total
                                        $qtd_litros = 0;
                                        if ($valorcomb_num > 0 && $total_combustivel_disponivel > 0) {
                                            $qtd_litros = $total_combustivel_disponivel / $valorcomb_num;
                                        }
                                        $qtd_litros_fmt = number_format($qtd_litros, 2, ',', '.');

                                        // Dados já vêm do JOIN
                                        $nometec = $row[11];
                                        $idsup = $row[12];
                                        $nomesup = $row[13];
                                        $nomecoordenador = $row[14] ?? 'N/A';
                                        $nomegerente = $row[15] ?? 'N/A';
                                        $placa = $row[16];
                                        $unidade = $row[17] ?? '';
                                        $tipovel = $row[18] ?? '';
                                        $km_litro_previsto = $row[19] ?? 0;
                                        $km_litro_previsto_fmt = number_format($km_litro_previsto, 2, ',', '.');
                                        $combustivel = $row[20] ?? '';
                                        $km_litro_vc = $row[21] ?? 0;
                                        
                                        // Regra especial para veículos LEVE: desconsiderar combustível e usar valor fixo baseado na unidade
                                        if (strtoupper(trim($tipovel)) == 'LEVE') {
                                            if (strtoupper(trim($unidade)) == 'SP') {
                                                $km_litro_vc = 8;
                                            } else {
                                                $km_litro_vc = 10;
                                            }
                                        }
                                        
                                        $km_litro_vc_fmt = number_format($km_litro_vc, 2, ',', '.');
                                        $id_tipo_vc = $row[22] ?? '';
                                        
                                        // Substituir ALCOOL por ETANOL para exibição
                                        $combustivel_exibicao = $combustivel;
                                        if (strtoupper($combustivel) == 'ALCOOL') {
                                            $combustivel_exibicao = 'ETANOL';
                                        }
                                        
                                        // Concatenar tipo de veículo e combustível (nesta ordem)
                                        $veiculo_combustivel = trim($tipovel . ' ' . $combustivel_exibicao);
                                        
                                        // Calcular KM Projetado Atual (Qtd. Litros Total × KM/L (V/C))
                                        $km_projetado_atual = 0;
                                        if ($km_litro_vc > 0 && $qtd_litros > 0) {
                                            $km_projetado_atual = $qtd_litros * $km_litro_vc;
                                        }
                                        $km_projetado_atual_fmt = number_format($km_projetado_atual, 2, ',', '.');

                                        // Conexão para GPS (mantida pois é outro servidor)
                                        $kmgps = 0;
                                        if ($placa) {
                                            $servidor5 = "10.17.0.5";
                                            $usuario5 = "blvp";
                                            $senha5 = "@F3r7n6c2";
                                            $db5 = "bdfrota";
                                            $conexao5 = mysqli_connect("$servidor5", "$usuario5", "$senha5", "$db5") or die(mysqli_connect_error());

                                            $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$domingo' and datagps <= '$hoje' group by placa";
                                            $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
                                            $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                                            $kmgps = $row5[0];
                                            mysqli_close($conexao5);
                                        }

                                        if ($nomesup <> '') {
                                            print "<tr><td>$mattec</td>
              <td>$nometec</td>
              <td>$nomesup</td>
              <td>$nomecoordenador</td>
              <td>$nomegerente</td>
              <td>$placa</td>
              <!--<td>$unidade</td>-->
              <!--<td>$tipovel</td>-->
              <!--<td>$combustivel_exibicao</td>-->
              <!--<td>$veiculo_combustivel</td>-->
              <!--<td>$id_tipo_vc</td>-->
              <!--<td>$km_litro_previsto_fmt</td>-->
              <!--<td>$km_litro_vc_fmt</td>-->
              <!--<td style='white-space:  nowrap;'>$qtd_litros_fmt L</td>-->
              <td style='white-space:  nowrap;'>R$ $orcsem</td>
              <td style='white-space:  nowrap;'>R$ $orcsem80</td>
              <!--<td style='white-space:  nowrap;'>$kmorcsem80</td>-->
              <td style='white-space:  nowrap;'>R$ $totcot</td>
              <td style='white-space:  nowrap;'>R$ $valorcomb</td>
              <!--<td style='white-space:  nowrap;'>$kmos</td>-->
              <td style='white-space:  nowrap;'>$km_projetado_atual_fmt</td>
              <td><button class='btn btn-primary btn-sm' onclick=\"window.open('/frotas/painel-suporte/rotaspmatric.php?mattec=$mattec&diai=$useg', '_blank')\"><i class='fas fa-external-link-alt'></i> Detalhes</button></td>
              </tr> ";
                                        }

                                    }
                                }
                                
                                } // fim do if ($mostrar_dados)

                                ?>

                            </tbody>
                        </table>
                        
                        <!-- Formulário oculto para exportação Excel -->
                        <form id="excelForm" action="./control/gerarexcelorcamentosemanal.php" method="post" style="display: none;">
                            <input type="hidden" name="useg" value="<?php echo htmlspecialchars($useg, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="datam1" value="<?php echo htmlspecialchars($datam1, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="gerente_filter" value="<?php echo htmlspecialchars($gerente_filter, ENT_QUOTES, 'UTF-8'); ?>">
                        </form>

                    </div>
                </div>
            </div>

        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <!--<div>
                <a href="#">Privacy Policy</a>
                                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>!-->
                </div>
            </div>
        </footer>

    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    crossorigin="anonymous"></script>
<script src="src/js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
<script src="src/js/datatables-simple-demo.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

<!--script para criar data table-->
<script>
    //função iniciar data table 
    $(document).ready(function () {
        $('#datatablesSimple').DataTable();
    });
    
    // Script para exportação Excel
    document.getElementById('exportExcel').addEventListener('click', function() {
        document.getElementById('excelForm').submit();
    });
</script>
</html>
<!-- VERSÃO COM NOVO CALCULO DE KM PROJETADO -->