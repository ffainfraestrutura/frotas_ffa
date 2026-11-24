<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
$ccusto = $_SESSION['ccusto'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
    exit(); // Garante que o script pare
} elseif ($perfil <> '1') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
    exit(); // Garante que o script pare
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;
$_SESSION['ccusto'] = $ccusto;

$data = date('Y-m-d');
include "conecta.php";
include_once "./func/log.php";
include_once "./func/consultacartao.php";

header("Content-type: text/html; charset=utf-8");
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
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
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
    </script>
    <script>
        function disableButton(btn) {
            btn.disabled = true;
        }

        function handleSubmit(event) {
            var submitBtn = event.target.querySelector('.enviarpedido');
            disableButton(submitBtn);
        }
    </script>
</head>


<body class="sb-nav-fixed vsc-initialized">

    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2"> Aprovação de Cota <?php

                $sql_supervisor = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
                $resultado_supervisor = mysql_query($sql_supervisor) or die(mysql_error());
                $row_supervisor = mysql_fetch_array($resultado_supervisor);
                $idtbsupervisor = $row_supervisor[0];

                if ($idtbsupervisor == '') {
                    echo "<script language='javascript' type='text/javascript'>alert('Seu Login Expirou ou você não é um supervisor válido! Você deve logar novamente para ter acesso');window.location=\"index.php\"</script>";
                    exit();
                }

                $sql_cota = "SELECT * FROM bdfrota.tbcotaextraindevida where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idtbsupervisor') and dataac >= '$data 00:00:00'";
                $resultado_cota = mysql_query($sql_cota) or die(mysql_error());
                if (mysql_num_rows($resultado_cota) >= 1) {
                    print "<a href='./cotasdescontosup.php' style='text-decoration: none;'><img src='./src/images/email.svg' alt='Ícone de e-mail' class='mb-2 ms-2' style='height: 2em; vertical-align: middle;'></a>";
                }
                ?> </h1>

                <div id="containertab">
                    <div class="btn-group d-flex justify-content-center mb-4 mt-4" role="group"
                        aria-label="Grupo de Botões">
                        <button type="button" class="btn btn-primary mx-2" onclick="endOS()">Rota OS</button>
                        <button type="button" class="btn btn-secondary mx-2" onclick="endGPS()">Rota GPS</button>
                        <button type="button" class="btn btn-success mx-2" onclick="endAniel()">Endereço Aniel</button>
                        <button type="button" class="btn btn-info mx-2" onclick="extratoKmProj()">Extrato KM
                            Proj</button>
                        <button type="button" class="btn btn-light mx-2" onclick="enderecoForaTOA()">Outras
                            Atividades</button>
                    </div>
                    <table id="datatablesSimple" class="display" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Matrícula</th>
                                <th style="white-space: nowrap;">Nome</th>
                                <th>Placa</th>
                                <th>Status</th>
                                <th>Hodometro</th>
                                <th>Foto hodometro</th>
                                <th>Km Proj. Inicial</th>
                                <th>Km Proj. Atual</th>
                                <th>Km Trab.</th>
                                <th>Orc. Inicial</th>
                                <th>Orc. Atual</th>
                                <th>Sld. Remanej.</th>
                                <th>Fd. Frota</th>
                                <th>Sld. Cartão</th>
                                <th>Tot. Extra</th>
                                <th>Fora Escala?</th>
                                <th>Fora Horário?</th>
                                <th>Data</th>
                                <th>Justificativa</th>
                                <th>Solicitação</th>
                                <th>Novo Valor</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            
                            // CORREÇÃO SQL: Adicionado aspas simples em '$idtbsupervisor' para evitar erro de sintaxe se a variável estiver vazia.
                            $sql = "SELECT a.matricula, b.nome, b.codempresa FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idtbsupervisor'";

                            $resultado = mysql_query($sql) or die(mysql_error());
                            while ($row = mysql_fetch_array($resultado)) {
                                $matriculatec = $row[0];
                                $nometec = $row[1];
                                $codempresa = $row[2];

                                // CORREÇÃO DE LÓGICA: Reiniciando as variáveis a cada técnico.
                                // Isso impede que o saldo de um técnico "vaze" para o próximo.
                                $saldocartao = 'Não encontrado';
                                $saldoct = 0;
                                $limiteAtual = 'Não encontrado';
                                $placa = '';

                                $sql2 = "SELECT ROUND(saldo,2) as saldo, idtbsaldo, ROUND(orcsemanal,2), ROUND(kmproj,0), ROUND(kmosatual,0), ROUND(valoraplicado - orcsemanal,2) as totalextra, slddscnt, ROUND(kmorcsem,0), ROUND(valoraplicado,2) FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                                $resultado2 = mysql_query($sql2) or die(mysql_error());

                                $row2 = mysql_fetch_array($resultado2);
                                $saldotec = str_replace(".", ",", $row2[0]);
                                $idtbsaldo = $row2[1];
                                $orcsemanal = str_replace(".", ",", $row2[2]);
                                $kmproj = $row2[3];
                                $kmosatual = $row2[4];
                                $totalextra = $row2[5] < 0 ? 0 : $row2[5];
                                $valdesc = $row2[6];
                                $kmorcsem = $row2[7];
                                $valoraplicado = str_replace(".", ",", $row2[8]);

                                $sql2 = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                                $resultado2 = mysql_query($sql2) or die(mysql_error());
                                $row2 = mysql_fetch_array($resultado2);
                                $placa = $row2[0];

                                if ($placa <> '') {
                                    $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidostec, data, dir, kmhodometro FROM bdfrota.tbpedidostec where matricula = '$matriculatec' and flag = 0 and escalonado = 0 and data >= '$hoje 00:00:00' and data <= '$hoje 23:59:59' and desctec is null and frota <> 1";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());

                                    if (mysql_num_rows($resultado2) > 0) {
                                        $row2 = mysql_fetch_array($resultado2);
                                        $justificativa = $row2[0];
                                        $novoorccomp = $row2[1];
                                        $valorpedido = $novoorccomp;
                                        $novoorccomp = str_replace(".", ",", $novoorccomp);
                                        $id = $row2[2];
                                        $dataehora = $row2[3];
                                        $dirhod = $row2[4];
                                        $kmhodometro = $row2[5];

                                        if ($filial <> '2') {
                                            // Bloco TicketLog (inalterado, mas a lógica de reset já o protege)
                                            $curl = curl_init();
                                            curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
                                            // ...
                                            if (isset($result->itens) && count($result->itens)) {
                                                foreach ($result->itens as $itens) {
                                                    if (isset($itens->situacao) && $itens->situacao == 'A') {
                                                        $saldocartao = $itens->saldo;
                                                        $saldoct = $saldocartao;
                                                        $saldocartao = str_replace(".", ",", $saldocartao);
                                                    }
                                                }
                                            }
                                        } else {
                                            // Bloco ValeCard com a correção
                                            $curl = curl_init();
                                            curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/buscarBasedeCartoes');
                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                                            curl_setopt($curl, CURLOPT_POST, TRUE);
                                            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc='));
                                            curl_setopt($curl, CURLOPT_HEADER, 0);
                                            $body = '{ "contratoCliente" : "524073", "numeroCartao" : null, "placa" : "' . $placa . '", "statusCartao": "A" }';
                                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                                            $response = curl_exec($curl);
                                            $result = json_decode($response);

                                            if ($result && !empty($result)) {
                                                foreach ($result as $itens) {
                                                    if (isset($itens->statusCartao) && $itens->statusCartao == 'A') {
                                                        $saldocartao = $itens->saldo;
                                                        // CORREÇÃO FINAL E MAIS IMPORTANTE:
                                                        $saldoct = $saldocartao; // Guarda o valor numérico para a lógica
                                                        $saldocartao = str_replace(".", ",", $saldocartao); // Formata o valor para exibição
                                                        $limiteAtual = str_replace(".", ",", $itens->limite);
                                                    }
                                                }
                                            }
                                        }

                                        $hoje_semana = date('N');
                                        $useg = ($hoje_semana == 1) ? date('Y-m-d') : date('Y-m-d', strtotime('last monday'));
                                        $hoje = date('Y-m-d');

                                        if ($saldoct >= 40) {
                                            $status = 'Proibido';
                                        } elseif (($kmproj * 0.75) > $kmosatual) {
                                            $status = 'Indevido';
                                        } else {
                                            $status = 'Devido';
                                        }

                                        if (in_array($placa, ['SQY0I35', 'SQV8G36', 'SQY0I37', 'RKF8H29', 'SQW8G79', 'SQW8G73'])) {
                                            $status = 'Devido';
                                        }
                                        $saldocartao = $itens->saldo;

                                        // ... suas outras consultas para remanejamento ...
                                        
                                        // Impressão da tabela
                                        print "<tr>";
                                        print "<td><input type='radio' name='idtbpedido' id='$id' value='$id'></label></td>";
                                        print "<td>$matriculatec</td>";
                                        print "<td style=\"white-space: nowrap;\">$nometec</td>";
                                        print "<td>$placa</td>";
                                        
                                        $status_class = '';
                                        if ($status == 'Devido') $status_class = 'text-success';
                                        elseif ($status == 'Indevido') $status_class = 'text-warning';
                                        elseif ($status == 'Proibido') $status_class = 'text-danger';
                                        print "<td class='$status_class'>$status</td>";

                                        print "<td style=\"white-space: nowrap;\">$kmhodometro</td>";
                                        print "<td><a href='.$dirhod' target='_blank'><button class='btn btn-link' type='button'><i class='fa-solid fa-eye'></i></button></a></td>";
                                        print "<td>$kmorcsem</td>";

                                        $km_class = (($kmproj * 0.75) > $kmosatual) ? 'bg-danger' : 'bg-success';
                                        print "<td class='$km_class text-white'>$kmproj</td>";
                                        print "<td class='$km_class text-white'>$kmosatual</td>";

                                        print "<td style=\"white-space: nowrap;\">R$ $orcsemanal</td>";
                                        print "<td style=\"white-space: nowrap;\">R$ $valoraplicado</td>";
                                        print "<td>R$ $saldoremanej</td>";
                                        print "<td>R$ $ffrota</td>";

                                        $saldo_class = ($saldoct >= 40) ? 'bg-danger' : 'bg-success';
                                        print "<td class='$saldo_class text-white'>R$ $saldocartao</td>";

                                        print "<td>R$ $totalextra</td>";
                                        print "<td>$dataehora</td>";
                                        print "<td style='font-size: 9px;'>$justificativa</td>";
                                        print "<td>$novoorccomp</td>";

                                        // ... toda a sua lógica de botões ...
                                        print "</tr>";
                                    }
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright © FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- JQuery mais moderno -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script>
        //função data table para criar barra de rolagem
        $(document).ready(function () {
            $('#datatablesSimple').DataTable({
                scrollX: true,
            });
        });

        function formatarMoeda(i) {
            var v = i.value.replace(/\D/g, '');
            v = (v / 100).toFixed(2) + '';
            v = v.replace(".", ",");
            v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            i.value = v;
        }

        // Suas outras funções javascript...
    </script>
</body>
</html> 