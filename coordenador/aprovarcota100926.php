<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

$data = date('Y-m-d');
include_once "./controleacesso.php";
include_once "../conecta.php";
include_once "../func/log.php";
include_once "../func/consultacartao.php";

header("Content-type: text/html; charset=utf-8");

// Define período da semana (mesma lógica do detalhes-kmproj-kmosDEVS.php)
$useg = date('Y-m-d', strtotime('last monday'));
$datam1 = date("Y-m-d", strtotime("-1 day"));

// Mantém as variáveis antigas para compatibilidade com outras partes do código
if (strftime('%A') == "domingo") {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}

$sql2 = "SELECT valor, orcrecebido FROM bdfrota.tbcoord where matricula = '$matricula'";
$resultado2 = mysql_query($sql2) or die(mysql_error());
$row2 = mysql_fetch_array($resultado2);
$saldo = round($row2[0], 2);
$orccomp = $row2[1];
$hoje_numero = date('N');
if ($hoje_numero == 1) {
    $useg = date('Y-m-d'); // Se hoje for segunda-feira, $useg é igual a hoje
} else {
    $useg = date('Y-m-d', strtotime('last monday')); // Obtém a segunda-feira da semana atual
}
$hoje = date('Y-m-d');
//$resultado_cartao = consultarCartoes();
//$resultado_cartao = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Aprovar Cota</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!-- vendor jquery js -->
    <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

    <!--data tables - css e js-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>

    <script language="javascript">
        function moeda(a, e, r, t) {
            let n = ""
                , h = j = 0
                , u = tamanho2 = 0
                , l = ajd2 = ""
                , o = window.Event ? t.which : t.keyCode;
            if (13 == o || 8 == o)
                return !0;
            if (n = String.fromCharCode(o),
                -1 == "0123456789".indexOf(n))
                return !1;
            for (u = a.value.length,
                h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
                ;
            for (l = ""; h < u; h++)
                -1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
            if (l += n,
                0 == (u = l.length) && (a.value = ""),
                1 == u && (a.value = "0" + r + "0" + l),
                2 == u && (a.value = "0" + r + l),
                u > 2) {
                for (ajd2 = "",
                    j = 0,
                    h = u - 3; h >= 0; h--)
                    3 == j && (ajd2 += e,
                        j = 0),
                        ajd2 += l.charAt(h),
                        j++;
                for (a.value = "",
                    tamanho2 = ajd2.length,
                    h = tamanho2 - 1; h >= 0; h--)
                    a.value += ajd2.charAt(h);
                a.value += r + l.substr(u - 2, u)
            }
            return !1
        }

        // ============================================
        // FUNÇÃO PARA PREVENIR DUPLO CLIQUE
        // ============================================
        function submitApproval(button) {
            // 1. Verifica se o botão já foi clicado (está desabilitado)
            if (button.disabled) {
                return false; // Já foi clicado, não faz nada
            }

            // 2. Desabilita o botão imediatamente
            button.disabled = true;

            // 3. Salva o conteúdo original do botão
            var originalContent = button.innerHTML;

            // 4. Muda o texto para "Processando..."
            button.innerHTML = 'Processando...';

            // 5. Feedback visual: diminui opacidade
            button.style.opacity = '0.6';

            // 6. Feedback visual: muda o cursor
            button.style.cursor = 'not-allowed';

            // 7. Aguarda um pouco para garantir que as mudanças visuais apareçam
            setTimeout(function () {
                button.form.submit(); // Submete o formulário
            }, 100);

            // 8. Retorna false para prevenir comportamento padrão
            return false;
        }
    </script>
</head>


<body class="sb-nav-fixed vsc-initialized">

    <!--nav-->
    <?php include_once "../menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">


                <h1 class="h1 pt-2 pb-2"> Aprovação de Cota</h1>
                <h6>Saldo atual: <?= $saldo ?></h6>

                <div class="card p-4">
                    <h2 class="h2 pt-2 pb-2">Técnicos</h2>
                    <?php

                    // Busca o ccusto do coordenador logado
                    $sql_ccusto_coord = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
                    $resultado_ccusto = mysql_query($sql_ccusto_coord) or die(mysql_error());
                    $row_ccusto = mysql_fetch_assoc($resultado_ccusto);
                    $ccusto_coordenador = $row_ccusto['ccusto'];

                    $sql_alert = "SELECT 
                                    d.nome, DATE_FORMAT(d.data_sol, '%d/%m/%Y') AS data
                                FROM
                                    bdaniel.tbdisciplina d
                                        JOIN
                                    bdaniel.tbfuncionario f ON f.matricula = d.matricula
                                        JOIN
                                    bdaniel.tbsupfunc sf ON sf.matfunc = d.matricula
                                        JOIN
                                    bdaniel.tbsupervisor s ON s.matricula = sf.matsup
                                        JOIN
                                    bdaniel.tbcoordenador c ON s.idtbcoordenador = c.idtbcoordenador
                                        JOIN
                                    bdaniel.tbfuncionario fc ON fc.matricula = c.matricula
                                WHERE
                                    d.status = 'Emitida'
                                        AND c.matricula = '$matricula'
                                        AND narrativa LIKE '%USO INDEVIDO%'
                                        AND motivo = 6
                                        AND f.status = 'ATIVO'
                                        AND f.ccusto = fc.ccusto
                                        AND f.ccusto = '$ccusto_coordenador'";

                    $resultado_alert = mysql_query($sql_alert) or die(mysql_error());
                    $alertas = [];

                    while ($row = mysql_fetch_assoc($resultado_alert)) {
                        $alertas[] = $row;
                    }

                    $sql = "SELECT 
                            *
                        FROM
                            bdfrota.tbcotaextraindevida ci
                                JOIN
                            bdfrota.tbusuario u ON ci.matricula = u.matricula
                                JOIN
                            bdfrota.tbsupervisor s ON u.idtbsupervisor = s.idtbsupervisor
                                JOIN
                            bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador 
                            WHERE c.matricula = '$matricula' 
                            AND dataac >= '$data 00:00:00'";
                    $resultado = mysql_query($sql) or die(mysql_error());

                    $row = mysql_fetch_array($resultado);
                    if (mysql_num_rows($resultado) >= 1) {
                        print "<a href='../cotasdescontosup.php' style='text-decoration: none;'>
                <img src='./src/images/email.svg' alt='Ícone de e-mail' class='mb-2 ms-2' style='height: 2em; vertical-align: middle;'>
            </a>";
                    }
                    ?> </h1>



                    <!--div tabela - entrar dados do sistema-->
                    <div id="containertab">
                        <div class="btn-group d-flex justify-content-center mb-4 mt-4" role="group"
                            aria-label="Grupo de Botões">
                            <!-- <button type="button" class="btn btn-primary mx-2" onclick="endOS()">Rota OS</button>
                            <button type="button" class="btn btn-secondary mx-2" onclick="endGPS()">Rota GPS</button>
                            <button type="button" class="btn btn-success mx-2" onclick="endAniel()">Endereço
                                Aniel</button>
                            <button type="button" class="btn btn-info mx-2" onclick="extratoKmProj()">Extrato KM
                                Proj</button>
                            <button type="button" class="btn btn-light mx-2" onclick="enderecoForaTOA()">Outras
                                Atividades</button> -->
                        </div>
                        <table id="datatablesSimple" class="display" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Matrícula</th>
                                    <th style="white-space: nowrap;">Nome</th>
                                    <th style="white-space: nowrap;">Supervisor</th>
                                    <th>Placa</th>
                                    <!--<th>Tipo de Veículo</th>-->
                                    <!--<th>KM/L Previsto</th>-->
                                    <!--<th>Qtd. Litros</th>-->
                                    <th>Status</th>
                                    <th>Hodometro</th>
                                    <th>Foto hodometro</th>
                                    <!--<th>Km Proj. Inicial</th>-->
                                    <th>Km Proj. Atual</th>
                                    <th>Km Trab.</th>
                                    <th>Orc. Inicial</th>
                                    <th>Orc. Atual</th>
                                    <th>Sld. Remanej.</th>
                                    <th>Fd. Frota</th>
                                    <th>Sld. Cartão</th>
                                    <th>Tot. Extra</th>
                                    <th>Data</th>
                                    <th>Justificativa</th>
                                    <th>Solicitação</th>
                                    <th>Novo Valor</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php

                                $remanejamentoPorMatricula = [];
                                $sqlRemanejamento = "SELECT 
                                                            matricula,
                                                            SUM(CASE WHEN tipo = 1 THEN valor ELSE 0 END) AS remanejpositivo,
                                                            SUM(CASE WHEN tipo = 0 THEN valor ELSE 0 END) AS remanejnegativo,
                                                            SUM(CASE WHEN tipo = 1 AND perfil_autor = 4 THEN valor ELSE 0 END) AS ffrota
                                                        FROM bdfrota.tbremanejamento
                                                        WHERE data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59'
                                                        GROUP BY matricula";
                                $resultadoRemanejamento = mysql_query($sqlRemanejamento) or die(mysql_error());
                                while ($rowRemanejamento = mysql_fetch_assoc($resultadoRemanejamento)) {
                                    $remanejamentoPorMatricula[$rowRemanejamento['matricula']] = $rowRemanejamento;
                                }

                                $sql = "SELECT 
                                            tpt.justificativa,
                                            ROUND(tpt.valor, 2) AS valor,
                                            tpt.idtbpedidostec,
                                            tpt.data,
                                            tpt.dir,
                                            tpt.kmhodometro,
                                            tf.matricula,
                                            tf.nome,
                                            tf.codempresa,
                                            ROUND(ts.saldo, 2) AS saldo,
                                            idtbsaldo,
                                            ROUND(ts.orcsemanal, 2),
                                            ROUND(ts.kmproj, 0),
                                            ROUND(ts.kmosatual, 2) AS kmosatual,
                                            ts.slddscnt,
                                            ROUND(ts.kmorcsem, 0),
                                            ROUND(ts.valoraplicado, 2),
                                            ROUND(ts.sldprjtd, 2),
                                            ROUND(COALESCE(ts.valorcomb, 0), 2),
                                            ROUND(COALESCE(tca.total_cota, 0), 2),
                                            v.placa,
                                            v.unidade,
                                            v.tipovel,
                                            tv.km_litro_previsto,
                                            tv.descricao,
                                            fsup.nome AS nome_supervisor
                                        FROM
                                            bdfrota.tbpedidostec tpt
                                                JOIN
                                            bdfrota.tbusuario tu ON tpt.matricula = tu.matricula
                                                JOIN
                                            bdaniel.tbfuncionario tf ON tu.matricula = tf.matricula
                                                JOIN
                                            bdfrota.tbsupervisor tsup ON tu.idtbsupervisor = tsup.idtbsupervisor
                                                JOIN
                                            bdaniel.tbfuncionario fsup ON tsup.matricula = fsup.matricula
                                                JOIN
                                            bdfrota.tbcoord tc ON tsup.idtbcoordenador = tc.idtbcoordenador
                                                LEFT JOIN
                                            bdfrota.tbsaldo ts ON tu.matricula = ts.matricula
                                                AND ts.idtbsaldo = (
                                                    SELECT ts2.idtbsaldo
                                                    FROM bdfrota.tbsaldo ts2
                                                    WHERE ts2.matricula = tu.matricula
                                                    AND ts2.data >= (SELECT 
                                                        CASE
                                                            WHEN WEEKDAY(CURDATE()) = 0 THEN CURDATE()
                                                            WHEN WEEKDAY(CURDATE()) = 6 THEN DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                                                            ELSE DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                                                        END
                                                    )
                                                    ORDER BY ts2.idtbsaldo DESC
                                                    LIMIT 1
                                                )
                                                LEFT JOIN
                                            (SELECT 
                                                matricula, SUM(valorinserido) AS total_cota
                                            FROM
                                                bdfrota.tbcotaextraac
                                            WHERE
                                                data BETWEEN '$useg 00:00:00' AND '$datam1 23:59:59'
                                            GROUP BY matricula) tca ON tca.matricula = tpt.matricula
                                                JOIN
                                            bdfrota.tbveiculo v ON v.matcond = tpt.matricula
                                                LEFT JOIN
                                            bdcombustivel.tbtipoveiculo tv ON v.tipovel = tv.idtbtipoveiculo
                                        WHERE
                                            tc.matricula = '$matricula' AND tpt.flag = 0
                                                AND tpt.escalonado = 0
                                                AND tpt.data >= '$hoje 00:00:00'
                                                AND tpt.data <= '$hoje 23:59:59'
                                                AND tpt.desctec IS NULL
                                                AND tpt.frota <> 1";

                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $justificativa = $row[0];
                                    $novoorccomp = $row[1];
                                    $novoorccomp = str_replace(".", ",", $novoorccomp);
                                    $valorpedido = $novoorccomp;
                                    $id = $row[2];
                                    $dataehora = $row[3];
                                    $dirhod = $row[4];
                                    $kmhodometro = $row[5];
                                    $matriculatec = $row[6];
                                    $nometec = $row[7];
                                    $codempresa = $row[8];
                                    $saldotec = $row[9];
                                    $saldotec = str_replace(".", ",", $saldotec);
                                    $idtbsaldo = $row[10];
                                    $orcsemanal = $row[11];
                                    $orcsemanal = str_replace(".", ",", $orcsemanal);
                                    $kmproj = $row[12];
                                    $kmosatual = $row['kmosatual'];
                                    $valdesc = $row[14];
                                    $kmorcsem = $row[15];
                                    $valoraplicado = $row[16];
                                    $valoraplicado = str_replace(".", ",", $valoraplicado);
                                    $sldprjtd = $row[17];
                                    $sldprjtd_fmt = str_replace(".", ",", $sldprjtd);
                                    $valorcomb_num = $row[18] ?? 0;

                                    $totalextra = $row[19];

                                    if ($totalextra < 0) {
                                        $totalextra = 0;
                                    }

                                    $placa = $row[20];
                                    $unidade = $row[21];
                                    $tipovel_id = $row[22];
                                    $km_litro_vc = $row[23] ?? 0;
                                    // Busca descrição do tipo de veículo
                                    $tipovel = $row[24] ?? '';
                                    $nome_supervisor = $row['nome_supervisor'] ?? '';
                                    $nome_supervisor_exibicao = $nome_supervisor;
                                    if (strlen($nome_supervisor_exibicao) > 25) {
                                        $nome_supervisor_exibicao = substr($nome_supervisor_exibicao, 0, 25) . '...';
                                    }
                                    $nome_supervisor_title = htmlspecialchars($nome_supervisor, ENT_QUOTES, 'UTF-8');
                                    $nome_supervisor_exibicao = htmlspecialchars($nome_supervisor_exibicao, ENT_QUOTES, 'UTF-8');

                                    // ===== CALCULAR KM PROJETADO ATUAL (usando Cota Semanal 80% + Cota Extra) =====
                                    // Total de cota extra da semana
                                    $totcot = $totalextra;

                                    // Orçamento inicial (sldprjtd) - Cota Inicial do Técnico
                                    $orc_inicial_num = str_replace(",", ".", $sldprjtd);

                                    // Cota Semanal 80%
                                    $orcsem80 = round(($orc_inicial_num * 0.80), 2);

                                    // Total de combustível disponível = Cota Semanal 80% + Cota Extra
                                    $total_combustivel_disponivel = $orcsem80 + $totcot;

                                    // Calcular quantidade de litros total
                                    $qtd_litros = 0;
                                    if ($valorcomb_num > 0 && $total_combustivel_disponivel > 0) {
                                        $qtd_litros = $total_combustivel_disponivel / $valorcomb_num;
                                    }

                                    // Regra especial para veículos LEVE: desconsiderar combustível e usar valor fixo baseado na unidade
                                    if (strtoupper(trim($tipovel)) == 'LEVE') {
                                        if (strtoupper(trim($unidade)) == 'SP') {
                                            $km_litro_vc = 8;
                                        } else {
                                            $km_litro_vc = 10;
                                        }
                                    }

                                    // Calcular KM Projetado Atual (Qtd. Litros Total × KM/L (V/C))
                                    $km_projetado_atual = 0;
                                    if ($km_litro_vc > 0 && $qtd_litros > 0) {
                                        $km_projetado_atual = $qtd_litros * $km_litro_vc;
                                    }
                                    // Formatar valores para exibição
                                    $km_litro_previsto_fmt = number_format($km_litro_vc, 2, ',', '.');
                                    $qtd_litros_fmt = number_format($qtd_litros, 2, ',', '.');
                                    $km_projetado_atual_fmt = number_format($km_projetado_atual, 2, ',', '.');
                                    // ===== FIM DO CÁLCULO =====
                                
                                    $saldoct = 0;
                                    try {
                                        $saldocartao = 0;
                                        $saldoct = 0;
                                        $saldocartao = consultacartao($codempresa, $placa);
                                       if (isset($resultado_cartao[$codempresa])) {
                                            if (isset($resultado_cartao[$codempresa][$placa])) {
                                                $saldoct = $resultado_cartao[$codempresa][$placa]['saldo'];
                                                $saldocartao = number_format($saldoct, 2, ',', '.');
                                            }
                                        }
                                    } catch (Exception $e) {
                                        $saldocartao = 'Erro na consulta';
                                        $saldoct = 0;
                                        error_log("Erro ao consultar cartão para placa $placa: " . $e->getMessage());
                                    }

                                    // Calcular status usando o KM Projetado Atual (e não o KM Proj. Inicial)
                                    //if ($saldoct >= 40) {
                                    // Linha inserida temporária para melhorar performance da tela
                                    //  alterado para > 100 em 160426 para evitar que casos com cotas aceitas baixas sejam classificados como "Proibido" sem considerar o KM Projetado Atual
                                    if ($totalextra > 100) {
                                        $status = 'Proibido';
                                    } elseif ($km_projetado_atual == 0 && $kmosatual == 0) {
                                        // Se ambos estão zerados, considera como "Devido" (não há dados suficientes para julgar)
                                        $status = 'Devido';
                                    } elseif (($km_projetado_atual * 0.75) > $kmosatual) {
                                        $status = 'Devido';
                                    } else {
                                        $status = 'Devido';
                                    }

                                    if ($placa == 'SQY0I35' or $placa == 'SQV8G36' or $placa == 'SQY0I37' or $placa == 'RKF8H29' or $placa == 'SQW8G79' or $placa == 'SQW8G73') {
                                        $status = 'Devido';
                                    }

                                    $dadosRemanejamento = $remanejamentoPorMatricula[$matriculatec] ?? null;
                                    $remanejpositivo = $dadosRemanejamento ? $dadosRemanejamento['remanejpositivo'] : 0;
                                    $remanejnegativo = $dadosRemanejamento ? $dadosRemanejamento['remanejnegativo'] : 0;
                                    $saldoremanej = $remanejpositivo - $remanejnegativo;
                                    $ffrota = $dadosRemanejamento ? $dadosRemanejamento['ffrota'] : 0;
                                    $ffrota = str_replace(".", ",", $ffrota);


                                    print "                        
                            <tr>
                                <td><input type='radio' name='idtbpedido' id='$id' value='$id'>
                                    </label></td>
                                <td>$matriculatec</td>
                                <td style=\"white-space: nowrap;\">$nometec</td>
                                <td style=\"white-space: nowrap;\" title='$nome_supervisor_title'>$nome_supervisor_exibicao</td>
                                <td>$placa</td>";
                                    // Colunas auxiliares comentadas (disponíveis para conferência se necessário)
                                    // print "<td>$tipovel</td>";
                                    // print "<td>$km_litro_previsto_fmt</td>";
                                    // print "<td style='white-space: nowrap;'>$qtd_litros_fmt L</td>";
                                    //$status = 'Devido';
                                    if ($status == 'Devido') {
                                        print "<td class='text-success'>$status</td>";
                                    } elseif ($status == 'Indevido') {
                                        print "<td class='text-warning'>$status</td>";
                                    } elseif ($status == 'Proibido') {
                                        print "<td class='text-danger'>$status</td>";
                                    } else {
                                        print "<td>$status</td>";
                                    }

                                    print "<td style=\"white-space: nowrap;\">$kmhodometro</td>";
                                    print "<td><a href='../$dirhod' target='_blank'><button class='btn btn-link' type='submit'><i class='fa-solid fa-eye'></i></button></a></td>";
                                    // Coluna KM Proj. Inicial comentada (mantida para referência)
                                    // print "<td>$kmorcsem</td>";
                                    print "<td>$km_projetado_atual_fmt</td>";

                                    // Usar KM Projetado Atual para colorir a célula de KM Trabalhado
                                    if (($km_projetado_atual * 0.75) > $kmosatual) {
                                        print "<td class='bg-danger text-white'>$kmosatual</td>";
                                    } else {
                                        print "<td class='bg-success text-white'>$kmosatual</td>";
                                    }

                                    print "<td style=\"white-space: nowrap;\">R$ $orcsemanal</td>
                            <td style=\"white-space: nowrap;\">R$ $valoraplicado</td>
                            <td>R$ $saldoremanej</td>
                            <td>R$ $ffrota</td>";

                                    if ($saldoct >= 40) {
                                        print "<td class='bg-danger text-white'>R$ $saldocartao</td>";
                                    } else {
                                        print "<td class='bg-success text-white'>R$ $saldocartao</td>";
                                    }

                                    print "<td>R$ $totalextra</td>";

                                    print "<td>$dataehora</td>
                            <td style='font-size: 9px;'>$justificativa</td>
                            <td>$novoorccomp</td>";

                                    if ($status == 'Proibido') {

                                        print "<td>
                                <div class=\"btn-group\">
                                    <form action='./control/escalonarpedido.php' method='post' class='d-flex'>
                                        <button type='button' onclick='return submitApproval(this);' class='btn btn-warning'>Escalonar
                                        </button>
                                        <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                                        <input type='hidden' name='matriculatec' value='$matriculatec'>
                                        <input type='hidden' name='decisao' value='2'>
                                        <input type='hidden' name='id' value='$id'>
                                        <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                        <input type='hidden' name='valor_ant' value='$saldocartao'>
                                        <input type='hidden' name='valor_desc' value='$valdesc'>
                                        <input type='hidden' name='orcsem' value='$orcsemanal'>
                                        <input type='hidden' name='kmproj' value='$kmproj'>
                                        <input type='hidden' name='kmos' value='$kmosatual'>
                                        <input type='hidden' name='kmsem' value='$kmsem'>
                                        <input type='hidden' name='totextra' value='$totalextra'>
                                        <input type='hidden' name='kmprojini' value='$kmorcsem'>
                                        <input type='hidden' name='orcsematu' value='$valoraplicado'>
                                        <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                        <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                                    </form>

                                    <form action='./control/aprovarcota.php' method='post'>
                                        <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-danger'
                                                title='Negar pedido de cota'
                                                data-bs-toggle='tooltip'
                                                data-bs-placement='top'>
                                        <span class='material-symbols-outlined'>close</span>
                                    </button>
                                        <input type='hidden' name='matriculatec' value='$matriculatec'>
                                        <input type='hidden' name='decisao' value='1'>
                                        <input type='hidden' name='id' value='$id'>
                                        <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                        <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                    </form>

                                </div>
                                </td></tr>";

                                    } elseif ($status == 'Indevido') {
                                        $sql6 = "SELECT sum(valorinserido) FROM bdfrota.tbcotaextraac where dataaceite >= '$segunda' and matricula = '$matricula' and cota_validade = 1";
                                        $resultado6 = mysql_query($sql6) or die(mysql_error());
                                        $row6 = mysql_fetch_array($resultado6);
                                        $totctxi = $row6[0];
                                        $valtotalind = $valorpedido + $totctxi;



                                        $sqlaux = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matriculatec'";
                                        $resultadoaux = mysql_query($sqlaux) or die(mysql_error());
                                        $rowaux = mysql_fetch_array($resultadoaux);
                                        $ccusto = $rowaux[0];

                                        // Valor máximo padrão
                                        $max = 51;

                                        // Verifica se o centro de custo indica rede externa ou similar
                                        if (
                                            stripos(trim($ccusto), 'rede externa') !== false ||
                                            stripos(trim($ccusto), 'implanta') !== false ||
                                            stripos(trim($ccusto), 'obra') !== false
                                        ) {
                                            $max = 81;
                                        }

                                        // Verifica se o valor ultrapassa o máximo definido
                                        if ($novoorccomp > $max) {
                                            print "<td><div class=\"btn-group\">
              <form action='./control/escalonarpedido.php' method='post' class='d-flex'>
              <button class='btn btn-warning'>Escalonar
                                        </button>";
                                        } else {
                                            if ($valtotalind > 150) {

                                                print "<td><div class=\"btn-group\">
                            <form action='./control/cotaindevidafrota.php' method='post' class='d-flex'>";
                                            } else {
                                                print "<td><div class=\"btn-group\">
                            <form action='./justificarcotaindevida.php' method='post' class='d-flex'>";
                                            }
                                            print "<input class='form-control' type='text' name='valor' id='valor' placeholder='R$ $novoorccomp' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>
                                    
                                    <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-success' 
                                            title='Aprovar pedido (com ou sem alteração de valor)'
                                            data-bs-toggle='tooltip'
                                            data-bs-placement='top'>
                                        <span class='material-symbols-outlined'>done</span>
                                        </button>";
                                        }
                                        print "
                            <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='2'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input type='hidden' name='valor_ant' value='$saldocartao'>
                            <input type='hidden' name='valor_desc' value='$valdesc'>
                            <input type='hidden' name='orcsem' value='$orcsemanal'>
                            <input type='hidden' name='kmproj' value='$kmproj'>
                            <input type='hidden' name='kmos' value='$kmosatual'>
                            <input type='hidden' name='kmsem' value='$kmsem'>
                            <input type='hidden' name='totextra' value='$totalextra'>
                            <input type='hidden' name='kmprojini' value='$kmorcsem'>
                            <input type='hidden' name='orcsematu' value='$valoraplicado'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                            </form>

                            <form action='./control/aprovarcota.php' method='post'>
                                        <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-danger'
                                                title='Negar pedido de cota'
                                                data-bs-toggle='tooltip'
                                                data-bs-placement='top'>
                                        <span class='material-symbols-outlined'>close</span>
                                    </button> 
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='1'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            </form>

                                </div>

                            </td></tr>";


                                    } else {


                                        $sqlaux = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matriculatec'";
                                        $resultadoaux = mysql_query($sqlaux) or die(mysql_error());
                                        $rowaux = mysql_fetch_array($resultadoaux);
                                        $ccusto = $rowaux[0];

                                        // Valor máximo padrão
                                        $max = 51;

                                        // Verifica se o centro de custo indica rede externa ou similar
                                        if (
                                            stripos(trim($ccusto), 'rede externa') !== false ||
                                            stripos(trim($ccusto), 'implanta') !== false ||
                                            stripos(trim($ccusto), 'obra') !== false
                                        ) {
                                            $max = 81;
                                        }

                                        // Verifica se o valor ultrapassa o máximo definido
                                        if ($novoorccomp > $max) {
                                            print "<td><div class=\"btn-group\">
              <form action='./control/escalonarpedido.php' method='post' class='d-flex'>
              <button type='button' onclick='return submitApproval(this);' class='btn btn-warning'>Escalonar
                                        </button>";
                                        } else {
                                            print "<td><div class=\"btn-group\">
              <form action='./control/aprovarcota.php' method='post' class='d-flex'>
              <input class='form-control' type='text' name='valor' id='valor' placeholder='Insira novo valor' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>
                                    
                                    <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-success enviarpedido'
                                            title='Aprovar pedido (com ou sem alteração de valor)'
                                            data-bs-toggle='tooltip'
                                            data-bs-placement='top'>
                                        <span class='material-symbols-outlined'>done</span>";
                                        }

                                        print "
                                
                                    
                                        </button>
                            <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='2'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input type='hidden' name='valor_ant' value='$saldocartao'>
                            <input type='hidden' name='valor_desc' value='$valdesc'>
                            <input type='hidden' name='orcsem' value='$orcsemanal'>
                            <input type='hidden' name='kmproj' value='$kmproj'>
                            <input type='hidden' name='kmos' value='$kmosatual'>
                            <input type='hidden' name='kmsem' value='$kmsem'>
                            <input type='hidden' name='totextra' value='$totalextra'>
                            <input type='hidden' name='kmprojini' value='$kmorcsem'>
                            <input type='hidden' name='orcsematu' value='$valoraplicado'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                            </form>

                            <form action='./control/aprovarcota.php' method='post'>
                                        <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-danger'
                                                title='Negar pedido de cota'
                                                data-bs-toggle='tooltip'
                                                data-bs-placement='top'>
                                        <span class='material-symbols-outlined'>close</span>
                                    </button> 
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='1'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            </form>

                                </div>

                            </td></tr>";

                                    }
                                }
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card p-4 mt-4">
                    <h2 class="h2 pt-2 pb-2">Supervisores</h2>
                    <div id="containertab">
                        <table id="datatablesSimple2" class="display" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th style="white-space: nowrap;"> Nome</th>
                                    <th>Placa</th>
                                    <th>Sld. Cartão</th>
                                    <th>Fd. Frota</th>
                                    <th>Tot. Extra</th>
                                    <th>Data e Hora</th>
                                    <th>Justificativa</th>
                                    <th>Solicitação</th>
                                    <th>Novo Valor</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php

                                $sql = "SELECT idtbcoordenador FROM bdfrota.tbcoord where matricula = '$matricula'";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                $row = mysql_fetch_array($resultado);
                                $idtbcoordenador = $row[0];

                                $sql = "SELECT a.matricula, a.valor, b.nome, b.codempresa FROM bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbcoordenador = '$idtbcoordenador'";
                                $resultado = mysql_query($sql) or die(mysql_error());
                                while ($row = mysql_fetch_array($resultado)) {
                                    $matriculasup = $row[0];
                                    $saldosup = $row[1];
                                    $saldosup = str_replace(".", ",", $saldosup);
                                    $nome = $row[2];
                                    $codempresa = $row[3];
                                    if ($codempresa == 2) {
                                        $filial = 'SP';
                                    } else {
                                        $filial = 'RJ';
                                    }
                                    $sql2 = "SELECT totcotaextra FROM bdfrota.tbsupervisor where matricula = '$matriculasup'";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);
                                    $orccomp = $row2[0];
                                    if ($row2[0] == '' or $row2[0] == null) {
                                        $orccomp == '0,00';
                                    }

                                    $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidossupcota, data FROM bdfrota.tbpedidossupcota where matricula = '$matriculasup' and flag = 0";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());

                                    if (mysql_num_rows($resultado2) > 0) {

                                        while ($row2 = mysql_fetch_array($resultado2)) {
                                            $justificativa = $row2[0];
                                            $novoorccomp = $row2[1];
                                            $novoorccomp = str_replace(".", ",", $novoorccomp);
                                            $id = $row2[2];
                                            $dataehora = $row2[3];

                                            $sql3 = "SELECT placa, unidade FROM bdfrota.tbveiculo where matcond = '$matriculasup'";
                                            $resultado3 = mysql_query($sql3) or die(mysql_error());
                                            $row3 = mysql_fetch_array($resultado3);
                                            $placa = $row3[0];
                                            $unidade = $row[1];

                                            $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculasup' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";
                                            $resultado6 = mysql_query($sql6) or die(mysql_error());
                                            $row6 = mysql_fetch_array($resultado6);
                                            $ffrota = $row6[0];
                                            $ffrota = str_replace(".", ",", $ffrota);

                                            $saldoct_sup = 0;
                                            try {
                                                // Determina a operadora baseada na unidade
                                                $saldocartao = 0;
                                                $saldoct_sup = 0;
                                                $saldocartao = consultacartao($codempresa, $placa);
                                                /*if (isset($resultado_cartao[$codempresa])) {
                                                    if (isset($resultado_cartao[$codempresa][$placa])) {
                                                        $saldoct_sup = $resultado_cartao[$codempresa][$placa]['saldo'];
                                                        $saldocartao = number_format($saldoct_sup, 2, ',', '.');
                                                    }
                                                } */

                                            } catch (Exception $e) {
                                                $saldocartao = 'Erro na consulta';
                                                $saldoct_sup = 0;
                                                error_log("Erro ao consultar cartão para supervisor $matriculasup: " . $e->getMessage());
                                            }

                                            print "                        
                                    <tr>
                                        <td>$matriculasup</td>
                                        <td style=\"white-space: nowrap;\">$nome</td>
                                        <td>$placa</td>
                                        <td>$saldocartao</td>
                                        <td>R$ $ffrota</td>
                                        <td>R$ $orccomp</td>
                                        <td>$dataehora</td>
                                        <td style='font-size: 9px;'>$justificativa</td>
                                        <td>R$ $novoorccomp</td>
                                        <td><div class=\"btn-group\">

                                            <form action='./control/aprovarcotasupervisor.php' method='post'>
                                    
                                                <div class='input-group mb-3'>             
                                                    <input type='text' 
                                                           class='form-control' 
                                                           placeholder='Insira novo valor se preferir' 
                                                           aria-label='Valor a aprovar' 
                                                           aria-describedby='button-addon2' 
                                                           name='valor' 
                                                           onkeyup='formatarMoeda(this);'>
                                                    <div class='input-group-append'>
                                                        <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-success' 
                                                                id='button-addon2'
                                                                title='Aprovar pedido (com ou sem alteração de valor)'
                                                                data-bs-toggle='tooltip'
                                                                data-bs-placement='top'>
                                                            <span class='material-symbols-outlined'>done</span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                                                <input type='hidden' name='matriculasup' value='$matriculasup'>
                                                <input type='hidden' name='decisao' value='2'>
                                                <input type='hidden' name='id' value='$id'>
                                                <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                <input type='hidden' name='valor_ant' value='$saldocartao'>
                                                <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                                <input class='dataplantao' type='hidden' name='dataplantao' value ='extra'>
                                            </form>

                                            <form action='./control/aprovarcotasupervisor.php' method='post'>
                                                <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-danger'
                                                        title='Negar pedido de cota'
                                                        data-bs-toggle='tooltip'
                                                        data-bs-placement='top'>
                                                <span class='material-symbols-outlined'>close</span>
                                                </button> 
                                                <input type='hidden' name='matriculasup' value='$matriculasup'>
                                                <input type='hidden' name='decisao' value='1'>
                                                <input type='hidden' name='id' value='$id'>
                                                <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                                                <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                                            </form>

                                            </div>
                                        </td>";


                                        }
                                    }
                                }

                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>

        <div class="modal fade" id="modalAlerta" tabindex="-1" aria-labelledby="modalAlertaLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-dark">
                        <h5 class="modal-title" id="modalAlertaLabel">
                            <i class="fa-solid fa-triangle-exclamation"></i> Atenção
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body" id="modalAlertaBody">
                        <!-- Conteúdo será preenchido dinamicamente -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendi</button>
                    </div>
                </div>
            </div>
        </div>


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

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const alertas = <?php echo json_encode($alertas); ?>;

            if (alertas.length > 0) {
                let index = 0;

                function mostrarProximoAlerta() {
                    if (index >= alertas.length) return;

                    const alerta = alertas[index];
                    const body = document.getElementById("modalAlertaBody");
                    body.innerHTML = `Foi identificado que uma medida disciplinar por <b>USO INDEVIDO</b> foi atribuido ao colaborador <b>${alerta.nome}</b>. <br>Por favor, compareça no departamento pessoal junto ao colaborador.`;

                    const modal = new bootstrap.Modal(document.getElementById('modalAlerta'));
                    modal.show();

                    // Espera o fechamento do modal antes de mostrar o próximo
                    document.getElementById('modalAlerta').addEventListener('hidden.bs.modal', function handler() {
                        index++;
                        document.getElementById('modalAlerta').removeEventListener('hidden.bs.modal', handler);
                        mostrarProximoAlerta();
                    });
                }

                mostrarProximoAlerta();
            }
        });
    </script>
    <script>
        function endOS() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;

                }
            }

            if (corSelecionada) {
                var url = '../../frotas/detalhes-kmoscotaind-sup.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function endGPS() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;

                }
            }

            if (corSelecionada) {
                var url = 'https://ffa.painel-telecom.com:4545/gerarelgpsrodado-sup.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
                alert('Relatório está sendo gerado, por favor, aguarde');
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function endAniel() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;

                }
            }

            if (corSelecionada) {
                var url = '../../frotas/detalhes-endcasa-sup.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function extratoKmProj() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;

                }
            }

            if (corSelecionada) {
                var url = '../../frotas/detalhes-extratokmproj-sup.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }

        function enderecoForaTOA() {
            var radios = document.getElementsByName('idtbpedido');
            var corSelecionada;

            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    corSelecionada = radios[i].value;

                }
            }

            if (corSelecionada) {
                var url = '../../frotas/detalhes-enderecoforatoa-sup.php'; // URL de destino

                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', url);
                form.setAttribute('target', '_blank'); // Abre em uma nova aba

                var input = document.createElement('input');
                input.setAttribute('type', 'hidden');
                input.setAttribute('name', 'idtbpedido');
                input.setAttribute('value', corSelecionada);

                form.appendChild(input);

                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Selecione um item antes de continuar');
            }
        }
    </script>
    <script>
        //função data table para criar barra de rolagem
        $(document).ready(function () {
            $('#datatablesSimple').DataTable({
                scrollX: true,
            });
            $('#datatablesSimple2').DataTable({
                scrollX: true,
            });
        });

        //função para formatar valor do input - moeda
        function formatarMoeda(i) {
            var v = i.value.replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace(".", ",");
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            i.value = v;
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
</body>

</html>