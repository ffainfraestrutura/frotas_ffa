<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

$data = date('Y-m-d');
include_once "./controleacesso.php";
include_once "../conecta.php";
include_once "../func/log.php";

header("Content-type: text/html; charset=utf-8");

// Determina o início da semana (segunda-feira)
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
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Análise de Cota Indevida</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    
    <style>
        .card-analise {
            border-left: 4px solid #ffc107;
        }
        .valor-destaque {
            font-weight: bold;
            font-size: 1.1em;
        }
        .badge-calculo {
            font-size: 0.9em;
            margin: 2px;
        }
        .alert-indevido {
            background-color: #fff3cd;
            border-color: #ffc107;
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">

    <!--nav-->
    <?php include_once "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2">
                    <i class="fas fa-exclamation-triangle text-warning"></i> 
                    Análise de Cotas Indevidas
                </h1>
                
                <div class="alert alert-indevido mb-4" role="alert">
                    <h5><i class="fas fa-info-circle"></i> Critério de Indevido</h5>
                    <p class="mb-0">
                        Uma cota é considerada <strong>INDEVIDA</strong> quando: 
                        <code>(Km Projetado × 0.75) > Km Trabalhado</code>
                    </p>
                    <p class="mb-0 mt-2">
                        <small>Ou seja, quando o técnico rodou menos de 75% do esperado na semana.</small>
                    </p>
                    <p class="mb-0 mt-2">
                        <strong>Esta página mostra apenas os pedidos PENDENTES com status INDEVIDO.</strong>
                    </p>
                </div>

                <div class="card p-4">
                    <h2 class="h2 pt-2 pb-2">Pedidos de Cota Indevida - Análise Detalhada</h2>
                    <p class="text-muted">
                        Período: <?= date('d/m/Y', strtotime($segunda)) ?> até <?= date('d/m/Y', strtotime($domingo)) ?>
                    </p>

                    <div id="containertab">
                        <table id="datatablesSimple" class="display" style="font-size: 12px;">
                            <thead>
                                <tr>
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
                                    <th>Data</th>
                                    <th>Justificativa</th>
                                    <th>Solicitação</th>
                                    <th>Análise Indevido</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                // Buscar técnicos do coordenador
                                $sql = "SELECT u.matricula, f.nome, f.codempresa 
                                        FROM bdfrota.tbusuario u
                                        JOIN bdaniel.tbfuncionario f ON u.matricula = f.matricula
                                        JOIN bdfrota.tbsupervisor s ON u.idtbsupervisor = s.idtbsupervisor
                                        JOIN bdfrota.tbcoord c ON s.idtbcoordenador = c.idtbcoordenador
                                        WHERE c.matricula = '$matricula'";

                                $resultado = mysql_query($sql) or die(mysql_error());
                                
                                while ($row = mysql_fetch_array($resultado)) {
                                    $matriculatec = $row[0];
                                    $nometec = $row[1];
                                    $codempresa = $row[2];

                                    // Buscar dados do saldo - MAIS RECENTE DA SEMANA ATUAL
                                    $sql2 = "SELECT 
                                                ROUND(saldo, 2) AS saldo,
                                                idtbsaldo,
                                                ROUND(orcsemanal, 2),
                                                ROUND(kmorcsem, 2),
                                                ROUND(kmproj, 2),
                                                ROUND(kmosatual, 2),
                                                slddscnt,
                                                ROUND(valoraplicado, 2)
                                            FROM bdfrota.tbsaldo
                                            WHERE DATE(data) >= (
                                                SELECT 
                                                    CASE 
                                                        WHEN WEEKDAY(CURDATE()) = 0 THEN CURDATE()
                                                        WHEN WEEKDAY(CURDATE()) = 6 THEN DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                                                        ELSE DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY)
                                                    END
                                            )
                                            AND DATE(data) <= CURDATE()
                                            AND matricula = '$matriculatec'
                                            ORDER BY data DESC, idtbsaldo DESC
                                            LIMIT 1;";
                                    
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);

                                    $saldotec = $row2[0];
                                    $saldotec = str_replace(".", ",", $saldotec);
                                    $idtbsaldo = $row2[1];
                                    $orcsemanal = $row2[2];
                                    $orcsemanal = str_replace(".", ",", $orcsemanal);
                                    $kmorcsem = $row2[3];
                                    $kmproj = $row2[4];
                                    $kmosatual = $row2[5];
                                    $valdesc = $row2[6];
                                    $valoraplicado = $row2[7];
                                    $valoraplicado = str_replace(".", ",", $valoraplicado);

                                    // Buscar Total Extra da semana
                                    $sql_totalextra = "SELECT COALESCE(SUM(valorinserido), 0) 
                                                       FROM bdfrota.tbcotaextraac 
                                                       WHERE matricula = '$matriculatec' 
                                                       AND data >= '$segunda 00:00:00' 
                                                       AND data <= '$domingo 23:59:59'";
                                    $resultado_totalextra = mysql_query($sql_totalextra) or die(mysql_error());
                                    $row_totalextra = mysql_fetch_array($resultado_totalextra);
                                    $totalextra = $row_totalextra[0];

                                    if ($totalextra < 0) {
                                        $totalextra = 0;
                                    }

                                    // Buscar placa
                                    $sql2 = "SELECT placa,unidade FROM bdfrota.tbveiculo where matcond = '$matriculatec'";
                                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                                    $row2 = mysql_fetch_array($resultado2);
                                    $placa = $row2[0];
                                    $unidade = $row2[1];

                                    if ($placa <> '') {
                                        // Buscar pedidos pendentes (flag = 0)
                                        $sql2 = "SELECT justificativa, ROUND(valor,2) as valor, idtbpedidostec, data, dir, kmhodometro 
                                                FROM bdfrota.tbpedidostec 
                                                WHERE matricula = '$matriculatec' 
                                                AND flag = 0 
                                                AND escalonado = 0 
                                                AND data >= '$hoje 00:00:00' 
                                                AND data <= '$hoje 23:59:59' 
                                                AND desctec is null 
                                                AND frota <> 1";
                                        $resultado2 = mysql_query($sql2) or die(mysql_error());

                                        if (mysql_num_rows($resultado2) > 0) {
                                            while ($row3 = mysql_fetch_array($resultado2)) {
                                                $justificativa = $row3[0];
                                                $novoorccomp = $row3[1];
                                                $valorpedido = $novoorccomp;
                                                $novoorccomp = str_replace(".", ",", $novoorccomp);
                                                $id = $row3[2];
                                                $dataehora = $row3[3];
                                                $dirhod = $row3[4];
                                                $kmhodometro = $row3[5];

                                                // Buscar saldo do cartão (simplificado)
                                                $saldocartao = 0;
                                                $saldoct = 0;

                                                // Determinar status
                                                $hoje_numero = date('N');
                                                if ($hoje_numero == 1) {
                                                    $useg = date('Y-m-d');
                                                } else {
                                                    $useg = date('Y-m-d', strtotime('last monday'));
                                                }

                                                if ($totalextra > 0) {
                                                    $status = 'Proibido';
                                                } elseif ($kmproj == 0 && $kmosatual == 0) {
                                                    // Se ambos estão zerados, considera como "Devido"
                                                    $status = 'Devido';
                                                } elseif (($kmproj * 0.75) > $kmosatual) {
                                                    $status = 'Indevido';
                                                } else {
                                                    $status = 'Devido';
                                                }

                                                // Exceções
                                                if ($placa == 'SQY0I35' or $placa == 'SQV8G36' or $placa == 'SQY0I37' or $placa == 'RKF8H29' or $placa == 'SQW8G79' or $placa == 'SQW8G73') {
                                                    $status = 'Devido';
                                                }

                                                // Só mostrar se for INDEVIDO
                                                if ($status == 'Indevido') {
                                                    // Buscar remanejamentos
                                                    $sql4 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where matricula='$matriculatec' AND data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1;";
                                                    $resultado4 = mysql_query($sql4) or die(mysql_error());
                                                    $row4 = mysql_fetch_array($resultado4);
                                                    $remanejpositivo = $row4[0];

                                                    $sql5 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where matricula='$matriculatec' AND data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59' AND tipo=0;";
                                                    $resultado5 = mysql_query($sql5) or die(mysql_error());
                                                    $row5 = mysql_fetch_array($resultado5);
                                                    $remanejnegativo = $row5[0];

                                                    $saldoremanej = $remanejpositivo - $remanejnegativo;

                                                    $sql6 = "SELECT SUM(valor) FROM bdfrota.tbremanejamento where matricula='$matriculatec' AND data_e_hora BETWEEN '$useg 00:00:00' AND '$data 23:59:59' AND tipo=1 AND perfil_autor=4; ";
                                                    $resultado6 = mysql_query($sql6) or die(mysql_error());
                                                    $row6 = mysql_fetch_array($resultado6);
                                                    $ffrota = $row6[0];
                                                    $ffrota = str_replace(".", ",", $ffrota);

                                                    // Calcular valores para análise
                                                    $km_75_percent = $kmproj * 0.75;
                                                    $diferenca_km = $km_75_percent - $kmosatual;
                                                    $percentual_rodado = 0;
                                                    if ($kmproj > 0) {
                                                        $percentual_rodado = ($kmosatual / $kmproj) * 100;
                                                    }

                                                    print "<tr>
                                <td>$matriculatec</td>
                                <td style=\"white-space: nowrap;\">$nometec</td>
                                <td>$placa</td>";
                                                    
                                                    print "<td class='text-warning'><b>$status</b></td>";

                                                    print "<td style=\"white-space: nowrap;\">$kmhodometro</td>";
                                                    print "<td><a href='.$dirhod' target='_blank'><button class='btn btn-link' type='submit'><i class='fa-solid fa-eye'></i></button></a></td>
                            <td>$kmorcsem</td>";

                                                    if (($kmproj * 0.75) > $kmosatual) {
                                                        print "<td class='bg-danger text-white'>$kmproj</td>
                                <td class='bg-danger text-white'>$kmosatual</td>";
                                                    } else {
                                                        print "<td class='bg-success text-white'>$kmproj</td>
                                <td class='bg-success text-white'>$kmosatual</td>";
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

                                                    // Coluna de Análise Indevido
                                                    print "<td style='font-size: 10px;'>
                                <b>Por que está INDEVIDO:</b><br>
                                <span>Km Proj: " . number_format($kmproj, 0, ',', '.') . " km</span><br>
                                <span>75% Proj: " . number_format($km_75_percent, 0, ',', '.') . " km</span><br>
                                <span>Km Trab: " . number_format($kmosatual, 0, ',', '.') . " km</span><br>
                                <span>Faltou: " . number_format($diferenca_km, 0, ',', '.') . " km</span><br>
                                <span>% Rodado: " . number_format($percentual_rodado, 1, ',', '.') . "%</span><br>
                                <small><b>Regra:</b> ($kmproj × 0.75) = " . number_format($km_75_percent, 0, ',', '.') . " > $kmosatual</small>
                            </td>";

                                                    print "</tr>";
                                                }
                                            }
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card de Explicação -->
                <div class="card mt-4 p-4 card-analise">
                    <h3 class="h3">Como Interpretar os Dados</h3>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h5><i class="fas fa-calculator text-primary"></i> Cálculos</h5>
                            <ul>
                                <li><strong>Km Proj. (A):</strong> Quilometragem projetada para a semana</li>
                                <li><strong>Km Trab. (B):</strong> Quilometragem efetivamente trabalhada</li>
                                <li><strong>75% do Proj. (C):</strong> A × 0.75 (limite mínimo esperado)</li>
                                <li><strong>Diferença (C-B):</strong> Quanto faltou para atingir os 75%</li>
                                <li><strong>% Rodado:</strong> (B ÷ A) × 100</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-lightbulb text-warning"></i> Quando é Indevido?</h5>
                            <div class="alert alert-warning">
                                <p class="mb-2">
                                    <strong>Condição:</strong> Se <code>C > B</code>, ou seja, se <code>(A × 0.75) > B</code>
                                </p>
                                <p class="mb-0">
                                    <strong>Exemplo:</strong><br>
                                    Km Proj = 1000 km<br>
                                    75% = 750 km<br>
                                    Km Trab = 600 km<br>
                                    <span class="badge bg-danger">750 > 600 = INDEVIDO</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3">
                        <h5><i class="fas fa-flag text-success"></i> Exceções (Sempre "Devido")</h5>
                        <p>As seguintes placas são sempre consideradas "Devido", independente do cálculo:</p>
                        <span class="badge bg-secondary badge-calculo">SQY0I35</span>
                        <span class="badge bg-secondary badge-calculo">SQV8G36</span>
                        <span class="badge bg-secondary badge-calculo">SQY0I37</span>
                        <span class="badge bg-secondary badge-calculo">RKF8H29</span>
                        <span class="badge bg-secondary badge-calculo">SQW8G79</span>
                        <span class="badge bg-secondary badge-calculo">SQW8G73</span>
                    </div>
                </div>

            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>

    <script>
        // Função para inicializar DataTable
        $(document).ready(function () {
            $('#datatablesSimple').DataTable({
                scrollX: true,
                order: [[0, 'asc']], // Ordenar por matrícula
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/pt-BR.json'
                }
            });
        });
    </script>
</body>
</html>
