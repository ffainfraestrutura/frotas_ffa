<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '3') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

$data = date('Y-m-d');
include "../conecta.php";
include_once "../func/log.php";
header("Content-type: text/html; charset=utf-8");

// Define período da semana (mesma lógica do aprovarcotaDEVS.php)
$useg = date('Y-m-d', strtotime('last monday'));
$datam1 = date("Y-m-d", strtotime("-1 day"));

// Mantém as variáveis antigas para compatibilidade
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

$sql2 = "SELECT valor, orcrecebido FROM bdfrota.tbgerente where matricula = '$matricula'";
echo $sql2;
$resultado2 = mysql_query($sql2) or die(mysql_error());
$row2 = mysql_fetch_array($resultado2);
$saldo = $row2[0];
$orccomp = $row2[1];
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
    <title>Aprovar Cota Escalonada</title>
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
            setTimeout(function() {
                button.form.submit(); // Submete o formulário
            }, 100);
            
            // 8. Retorna false para prevenir comportamento padrão
            return false;
        }
    </script>

</head>


<body class="sb-nav-fixed vsc-initialized">

    <!--nav-->
    <?php include "../menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">


                <h1 class="h1 pt-2 pb-2"> Aprovação de Cota </h1>
                <h6>Saldo Atual: <?= $saldo ?></h6>



                <!--div tabela - entrar dados do sistema-->
                <div id="containertab">
                    <div class="btn-group d-flex justify-content-center mb-4 mt-4" role="group"
                        aria-label="Grupo de Botões">
                        <!-- <button type="button" class="btn btn-primary mx-2" onclick="endOS()">Rota OS</button>
                        <button type="button" class="btn btn-secondary mx-2" onclick="endGPS()">Rota GPS</button>
                        <button type="button" class="btn btn-success mx-2" onclick="endAniel()">Endereço Aniel</button>
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
                                <th>Placa</th>
                                <!--<th>Tipo de Veículo</th>-->
                                <!--<th>KM/L Previsto</th>-->
                                <!--<th>Qtd. Litros</th>-->
                                <th>Status</th>
                                <!--<th>Km Proj. Inicial</th>-->
                                <th>Km Proj. Atual</th>
                                <th>Km Trab.</th>
                                <th>Orc. Inicial</th>
                                <!--<th>Orc. Atual</th>-->
                                <th>Sld. Remanej.</th>
                                <th>Fd. Frota</th>
                                <th>Sld. Cartão</th>
                                <th>Tot. Extra</th>
                                <th>Data</th>
                                <th>Justificativa Técnico</th>
                                <th>Justificativa Supervisor</th>
                                <th>Solicitação</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php
                            // ============================================
                            // MEGA QUERY OTIMIZADA - BUSCA TUDO DE UMA VEZ
                            // Substitui 3 loops aninhados por 1 query única
                            // ============================================
                            
                            $sql_otimizado = "
                                SELECT 
                                    u.matricula AS matriculatec,
                                    f.nome AS nometec,
                                    f.codempresa,
                                    ROUND(s.saldo, 2) AS saldo,
                                    s.idtbsaldo,
                                    ROUND(s.orcsemanal, 2) AS orcsemanal,
                                    ROUND(s.kmproj, 0) AS kmproj,
                                    ROUND(s.kmosatual, 0) AS kmosatual,
                                    s.slddscnt AS valdesc,
                                    ROUND(s.kmorcsem, 0) AS kmorcsem,
                                    ROUND(s.valoraplicado, 2) AS valoraplicado,
                                    ROUND(s.sldprjtd, 2) AS sldprjtd,
                                    COALESCE(s.valorcomb, 0) AS valorcomb,
                                    v.placa,
                                    v.unidade,
                                    v.combustivel,
                                    atv.tipo AS tipovel_desc,
                                    tv2.km_litro_previsto AS km_litro_vc,
                                    p.justificativa,
                                    ROUND(p.valor, 2) AS valor,
                                    p.idtbpedidostec AS id,
                                    p.data AS dataehora,
                                    p.justsup,
                                    ROUND(p.valsup, 2) AS novoorccomp_raw,
                                    COALESCE(
                                        (SELECT SUM(ce.valorinserido) 
                                         FROM bdfrota.tbcotaextraac ce
                                         WHERE ce.matricula = u.matricula 
                                         AND ce.data >= '$segunda 00:00:00' 
                                         AND ce.data <= '$domingo 23:59:59'), 
                                    0) AS totalextra
                                FROM bdfrota.tbusuario u
                                INNER JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                                INNER JOIN bdfrota.tbsupervisor sup ON u.idtbsupervisor = sup.idtbsupervisor
                                INNER JOIN bdfrota.tbcoord coord ON sup.idtbcoordenador = coord.idtbcoordenador
                                INNER JOIN bdfrota.tbgerente ger ON coord.idtbgerente = ger.idtbgerente
                                LEFT JOIN (
                                    SELECT s1.* 
                                    FROM bdfrota.tbsaldo s1
                                    INNER JOIN (
                                        SELECT matricula, MAX(idtbsaldo) AS max_id
                                        FROM bdfrota.tbsaldo
                                        GROUP BY matricula
                                    ) s2 ON s1.matricula = s2.matricula AND s1.idtbsaldo = s2.max_id
                                ) s ON u.matricula = s.matricula
                                LEFT JOIN bdfrota.tbveiculo v ON u.matricula = v.matcond
                                LEFT JOIN bdfrota.tbatipovel atv ON v.tipovel = atv.idtbatipovel
                                LEFT JOIN bdcombustivel.tbtipoveiculo tv2 ON UPPER(atv.tipo) = UPPER(tv2.descricao) 
                                    AND UPPER(CASE WHEN v.combustivel = 'ALCOOL' THEN 'ETANOL' ELSE v.combustivel END) = UPPER(tv2.tipo_combustivel)
                                LEFT JOIN bdfrota.tbpedidostec p ON u.matricula = p.matricula 
                                    AND p.flag = 0 
                                    AND p.data >= '$hoje 00:00:00' 
                                    AND p.data <= '$hoje 23:59:59' 
                                    AND p.escalonado IN ('1','2')
                                WHERE ger.matricula = '$matricula'
                                    AND v.placa IS NOT NULL
                                    AND v.placa <> ''
                                    AND p.idtbpedidostec IS NOT NULL
                                ORDER BY u.matricula, p.data DESC
                            ";
                            
                            $resultado_otimizado = mysql_query($sql_otimizado) or die(mysql_error());
                            
                            // Agora percorre os resultados (1 loop simples ao invés de 3 aninhados!)
                            while ($row = mysql_fetch_array($resultado_otimizado)) {
                                
                                // Extrai os dados
                                $matriculatec = $row['matriculatec'];
                                $nometec = $row['nometec'];
                                $codempresa = $row['codempresa'];
                                
                                $saldotec = str_replace(".", ",", $row['saldo']);
                                $idtbsaldo = $row['idtbsaldo'];
                                $orcsemanal = str_replace(".", ",", $row['orcsemanal']);
                                $kmproj = $row['kmproj'];
                                $kmosatual = $row['kmosatual'];
                                $totalextra = $row['totalextra'];
                                $valdesc = $row['valdesc'];
                                $kmorcsem = $row['kmorcsem'];
                                $valoraplicado = str_replace(".", ",", $row['valoraplicado']);
                                $sldprjtd = str_replace(".", ",", $row['sldprjtd']);
                                
                                $placa = $row['placa'];
                                $unidade = $row['unidade'];
                                $combustivel = $row['combustivel'];
                                $tipovel = $row['tipovel_desc'] ?? '';
                                $km_litro_vc = $row['km_litro_vc'] ?? 0;
                                $valorcomb_num = $row['valorcomb'];
                                
                                $justificativa = $row['justificativa'];
                                $novoorccomp = $row['novoorccomp_raw'];
                                if ($novoorccomp == '' || $novoorccomp == null) {
                                    $novoorccomp = $row['valor'];
                                }
                                $novoorccomp = str_replace(".", ",", $novoorccomp);
                                $id = $row['id'];
                                $dataehora = $row['dataehora'];
                                $justsup = $row['justsup'];
                                
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
                               // $km_litro_previsto_fmt = number_format($km_litro_previsto, 2, ',', '.');
                                $qtd_litros_fmt = number_format($qtd_litros, 2, ',', '.');
                                $km_projetado_atual_fmt = number_format($km_projetado_atual, 2, ',', '.');
                                // ===== FIM DO CÁLCULO =====
                                
                                // Continua com a lógica da API (mantém igual)
                                if ($unidade != 'RJ' && $unidade != 'ES') {
                                    $curl = curl_init();
                                    curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes');
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                    curl_setopt($curl, CURLOPT_POST, TRUE);
                                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                        'Content-Type: application/json',
                                        'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
                                    ));
                                    curl_setopt($curl, CURLOPT_HEADER, 0);

                                                    $body = '{
                                                            "contratoCliente": "524073",
                                                            "numeroCartao": null,
                                                            "placa": "' . $placa . '",
                                                            "statusCartao": "A"
                                                        }';
                                                    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                                                    $result = json_decode(curl_exec($curl)); // retorna objetos
                                                    if (count($result)) {
                                                        foreach ($result as $itens) {
                                                            $situacao = $itens->statusCartao;

                                                            if ($situacao == 'A') {
                                                                $saldocartao = $itens->saldo;
                                                                $saldoct = $saldocartao;
                                                                $saldocartao = str_replace(".", ",", $saldocartao);
                                                            }
                                                        }
                                                    }

                                                } else {

                                                    $curl = curl_init();
                                                    curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
                                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                                    curl_setopt($curl, CURLOPT_POST, TRUE);
                                                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                                        'Content-Type: application/json',
                                                        'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
                                                    ));
                                                    curl_setopt($curl, CURLOPT_HEADER, 0);

                                                    $body = '{
                                        "codigoCliente": 183062,
                                        "codigoProduto": 4,
                                        "numeroCartao": null,
                                        "especieCartao": null,
                                        "numeroMatricula": null,
                                        "nomeCompleto": null,
                                        "placaVeiculo": "' . $placa . '",
                                        "numeroFrota": null,
                                        "codigoTipoFrota": null,
                                        "codigoFamiliaVeiculo": null,
                                        "codigoVeiculoModelo": null,
                                        "codigoCidade": null,
                                        "codigoClienteTCVeiculoResponsavel": null,
                                        "nomeResponsavel": null,
                                        "codigoGrupo": 4,
                                        "codigoTipoCombustivel": null,
                                        "informacoesAdicionais": [
                                            {
                                            "labelIA": null,
                                            "codigoIA": null,
                                            "descricaoIA": null
                                            }
                                        ],
                                        "situacaoCartao": "T",
                                        "ordem": "C",
                                        "quebra": null,
                                        "tipoMotorista": null,
                                        "ativacao": null,
                                        "tipoCartao": null,
                                        "controlaHodometro": null,
                                        "controlaHorimetro": null,
                                        "situacaoVeiculo": null,
                                        "trackOnLine": null
                                        }';

                                                    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                                                    $result = json_decode(curl_exec($curl));

                                                    if (count($result->itens)) {

                                                        foreach ($result->itens as $itens) {

                                                            $situacao = $itens->situacao;

                                                            if ($situacao == 'A') {
                                                                $saldocartao = $itens->saldo;
                                                                $saldoct = $saldocartao;
                                                                $saldocartao = str_replace(".", ",", $saldocartao);
                                                            }

                                                        }
                                                    }

                                                }

                                                /*$servidor5="10.17.0.5";
                                                $usuario5="blvp";
                                                $senha5="@F3r7n6c2";
                                                $db5 = "bdfrota";
                                                $conexao5=mysqli_connect("$servidor5","$usuario5","$senha5","$db5") or die (mysqli_connect_error());

                                                $sql5 = "SELECT ROUND((max(kmtotal) - min(kmtotal)),2) as kmsem FROM bdfrota.tbgps where placa = '$placa' and datagps >= '$segunda' group by placa";

                                                $resultado5 = mysqli_query($conexao5, $sql5) or die(mysqli_error($conexao5));
                                                $row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
                                                $kmsem = $row5[0];
                                                if ($kmsem > 2500) {
                                                    $kmsem = 0;
                                                }*/

                                                // Determina o início da semana (segunda-feira)
                                                // A semana vai de segunda a domingo
                                                $hoje_numero = date('N'); // 1=segunda, 2=terça... 6=sábado, 7=domingo
                                
                                                if ($hoje_numero == 1) {
                                                    $useg_temp = date('Y-m-d'); // Se hoje for segunda-feira, $useg é igual a hoje
                                                } else {
                                                    $useg_temp = date('Y-m-d', strtotime('last monday')); // Obtém a segunda-feira da semana atual
                                                }

                                // Calcular status usando o KM Projetado Atual (mesma lógica do aprovarcotaDEVS.php)
                                if ($saldoct >= 40) {
                                    $status = 'Proibido';
                                } elseif ($km_projetado_atual == 0 && $kmosatual == 0) {
                                    // Se ambos estão zerados, considera como "Devido" (não há dados suficientes para julgar)
                                    $status = 'Devido';
                                } elseif (($km_projetado_atual * 0.75) > $kmosatual) {
                                    $status = 'Indevido';
                                } else {
                                    $status = 'Devido';
                                }

                                // Exceções de placas específicas (mesma lógica do aprovarcotaDEVS.php)
                                if ($placa == 'SQY0I35' or $placa == 'SQV8G36' or $placa == 'SQY0I37' or $placa == 'RKF8H29' or $placa == 'SQW8G79' or $placa == 'SQW8G73') {
                                    $status = 'Devido';
                                }

                                                //remanejamento positivo
                                                $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1;";
                                                $resultado4 = mysql_query($sql4) or die(mysql_error());
                                                $row4 = mysql_fetch_array($resultado4);
                                                $remanejpositivo = $row4[0];

                                                //remanejamento negativo
                                                $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=0;";
                                                $resultado5 = mysql_query($sql5) or die(mysql_error());
                                                $row5 = mysql_fetch_array($resultado5);
                                                $remanejnegativo = $row5[0];

                                                $saldoremanej = $remanejpositivo - $remanejnegativo;

                                                $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where  matricula='$matriculatec' AND data_e_hora BETWEEN  '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";
                                                $resultado6 = mysql_query($sql6) or die(mysql_error());
                                                $row6 = mysql_fetch_array($resultado6);
                                                $ffrota = $row6[0];
                                                $ffrota = str_replace(".", ",", $ffrota);


                                                print "                        
                                <tr>
                                    <td>
                                        <input type='radio' name='idtbpedido' id='$id' value='$id'>
                                        </label>
                                    </td>
                                    <td>$matriculatec</td>
                                    <td style=\"white-space: nowrap;\">$nometec</td>
                                    <td>$placa</td>";
                            // Colunas auxiliares comentadas (disponíveis para conferência se necessário)
                            // print "<td>$tipovel</td>";
                            // print "<td>$km_litro_previsto_fmt</td>";
                            // print "<td style='white-space: nowrap;'>$qtd_litros_fmt L</td>";

                                                // Exibir status com coloração (mesma lógica do aprovarcotaDEVS.php)
                                                if ($status == 'Devido') {
                                                    print "<td class='text-success'>$status</td>";
                                                } elseif ($status == 'Indevido') {
                                                    print "<td class='text-warning'>$status</td>";
                                                } elseif ($status == 'Proibido') {
                                                    print "<td class='text-danger'>$status</td>";
                                                } else {
                                                    print "<td>$status</td>";
                                                }

                            // Coluna KM Proj. Inicial comentada (mantida para referência)
                            // print "<td>$kmorcsem</td>";
                            print "<td>$km_projetado_atual_fmt</td>";

                                                // Usar KM Projetado Atual para colorir a célula de KM Trabalhado (mesma lógica do aprovarcotaDEVS.php)
                                                if (($km_projetado_atual * 0.75) > $kmosatual) {
                                                    print "<td class='bg-danger text-white'>$kmosatual</td>";
                                                } else {
                                                    print "<td class='bg-success text-white'>$kmosatual</td>";
                                                }

                                                print "
                                    <td style=\"white-space: nowrap;\">R$ $orcsemanal</td>
                                    <!--<td style=\"white-space: nowrap;\">R$ $valoraplicado</td>-->
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
                            <td style='font-size: 9px;'>$justsup</td>
                            <td>$novoorccomp</td>";

                                                if ($status == 'Proibido') {

                                                    print "<td><div class=\"btn-group\">
                            <form action='control/aprovarcotaescalonada.php' method='post' class='d-flex'>
                                
                                    <input class='form-control' type='text' name='valor' id='valor' placeholder='Insira novo valor' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>
                                    
                                    <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-success'
                                            title='Aprovar pedido (com ou sem alteração de valor)'
                                            data-bs-toggle='tooltip'
                                            data-bs-placement='top'>
                                        <span class='material-symbols-outlined'>done</span>
                                        </button>
                            <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='decisao' value='2'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input type='hidden' name='valor_ant' value='$saldocartao'>
                            <input type='hidden' name='valor_desc' value='$valdesc'>
                            <input type='hidden' name='orcsem' value='$orcsemanal'>
                            <input type='hidden' name='kmproj' value='$km_projetado_atual'>
                            <input type='hidden' name='kmos' value='$kmosatual'>
                            <input type='hidden' name='kmsem' value=''>
                            <input type='hidden' name='totextra' value='$totalextra'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                            </form>

                            <form action='control/aprovarcotaescalonada.php' method='post'>
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

                                                    print "<td><div class=\"btn-group\">
                            <form action='control/aprovarcotaescalonada.php' method='post' class='d-flex'>
                                <input class='form-control' type='text' name='valor' id='valor' placeholder='Insira novo valor' style=\"min-width: 100px !important;width: 78%;\" onkeyup='formatarMoeda(this)'>
                                    
                                    <button type='button' onclick='return submitApproval(this);' class='btn btn-outline-success'
                                            title='Aprovar pedido (com ou sem alteração de valor)'
                                            data-bs-toggle='tooltip'
                                            data-bs-placement='top'>
                                        <span class='material-symbols-outlined'>done</span>
                                        </button>
                            <input type='hidden' name='orcpedido' value ='$novoorccomp'>  
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='justsup' value='$justsup'>
                            <input type='hidden' name='decisao' value='2'>
                            <input type='hidden' name='id' value='$id'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input type='hidden' name='valor_ant' value='$saldocartao'>
                            <input type='hidden' name='valor_desc' value='$valdesc'>
                            <input type='hidden' name='orcsem' value='$orcsemanal'>
                            <input type='hidden' name='kmproj' value='$km_projetado_atual'>
                            <input type='hidden' name='kmos' value='$kmosatual'>
                            <input type='hidden' name='kmsem' value=''>
                            <input type='hidden' name='totextra' value='$totalextra'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='extra'>
                            <input class='dataplantao' type='hidden' name='dataplantao' value =''>
                            </form>

                            <form action='control/aprovarcotaescalonada.php' method='post'>
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
                            } // FIM do loop único otimizado
                            ?>

                        </tbody>
                    </table>
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

    </div>

    <script>
        //função data table para criar barra de rolagem
        $(document).ready(function () {
            $('#datatablesSimple').DataTable({
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
                var url = '../detalhes-kmoscotaind-sup.php'; // URL de destino

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
                var url = 'http://ffasip.ddns.net:4545/gerarelgpsrodado-sup.php'; // URL de destino

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
                var url = '../detalhes-endcasa-sup.php'; // URL de destino

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
                var url = '../detalhes-extratokmproj-sup.php'; // URL de destino

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
                var url = '../detalhes-enderecoforatoa-sup.php'; // URL de destino

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="../assets/demo/chart-area-demo.js"></script>
    <script src="../assets/demo/chart-bar-demo.js"></script>
    <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="../src/js/datatables-simple-demo.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
</body>

</html>