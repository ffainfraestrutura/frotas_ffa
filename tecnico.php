<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '0') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['form_token'] = bin2hex(random_bytes(32));

include "conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");

$sql = "SELECT * FROM bdfrota.tbmulta WHERE etapa LIKE 'AGUARDANDO ASSINATURA%' AND autoinfracao IN (SELECT autoinfra FROM bdfrota.tbmovidatramite WHERE matricula = '$matricula' AND (reciboass='' OR reciboass='nao' OR recibolocass=0)); ";
$resultado = mysql_query($sql) or die(mysql_error());
$nummultas = mysql_num_rows($resultado);

$sql = "select count(*) as qtd_descontos from bdfrota.tbdescontousoindevido where matricula = '$matricula' and aval_coordenador = 1 and aceite_colaborador is null";
$numdescontos = mysql_query($sql) or die(mysql_error());
$numdescontos = mysql_fetch_assoc($numdescontos);
$numdescontos = $numdescontos['qtd_descontos'];

$sql = "select count(*) as qtd_bloqueio from bdfrota.tbdescontousoindevido where matricula = '$matricula' and bloqueio = 1 AND data_ocorrencia = curdate() - 5";
$bloqueio = mysql_query($sql) or die(mysql_error());
$bloqueio = mysql_fetch_assoc($bloqueio);
$bloqueio = $bloqueio['qtd_bloqueio'];
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
    <title>Solicitação de Combustível </title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <style>
        .rectangle {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 10px;
        }

        .number {
            border-right: 1px solid #ccc;
            padding-right: 10px;
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <!--nav-->
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>

            <div class="container-fluid px-4">


                <h1 class="h1 pt-1"> Solicitação de combustível </h1>

                <?php
                // Busca o supervisor do técnico
                // tbusuario.idtbsupervisor -> tbsupervisor.idtbsupervisor -> tbsupervisor.matricula -> tbusuario.nome (do supervisor)
                $sql_sup = "SELECT u2.nome as nome_supervisor
                            FROM bdfrota.tbusuario u1 
                            LEFT JOIN bdfrota.tbsupervisor s ON u1.idtbsupervisor = s.idtbsupervisor 
                            LEFT JOIN bdfrota.tbusuario u2 ON s.matricula = u2.matricula
                            WHERE u1.matricula = '$matricula'";
                $resultado_sup = mysql_query($sql_sup);
                
                $tem_supervisor = false;
                if ($resultado_sup && mysql_num_rows($resultado_sup) > 0) {
                    $row_sup = mysql_fetch_array($resultado_sup);
                    $nome_supervisor = $row_sup['nome_supervisor'];
                    
                    if ($nome_supervisor && trim($nome_supervisor) != '') {
                        $tem_supervisor = true;
                        print "<p class='mb-1'><small>Supervisor: $nome_supervisor</small></p>";
                    } else {
                        print "<p class='mb-1'><small style='color: #6c757d; font-style: italic;'>Supervisor não atribuído</small></p>";
                    }
                } else {
                    print "<p class='mb-1'><small style='color: #6c757d; font-style: italic;'>Supervisor não atribuído</small></p>";
                }
                ?>

                <p class="fs-5 fw-bold">
                    <i class="fas fa-user"></i> Técnico: <?php print "$nome"; ?>
                    <?php
                    $sql = "SELECT * FROM bdfrota.tbcotaextraindevida where matricula = '$matricula' and actec = '0'";
                    $resultado = mysql_query($sql) or die(mysql_error());
                    $row = mysql_fetch_array($resultado);
                    if (mysql_num_rows($resultado) >= 1) {
                        print "<a href='./cotasdesconto.php' style='text-decoration: none;'>
                <img src='./src/images/email.svg' alt='Ícone de e-mail' class='mb-2 ms-2' style='height: 2em; vertical-align: middle;'>
            </a>";
                    }
                    ?>

                </p>

                <?php

                if ($nummultas > 0) {
                    // include "popupMultas.php";

                    print "
                    <div class='alert alert-danger border-danger shadow-sm mb-4' role='alert' style='border-left: 5px solid #dc3545;'>
                        <div class='d-flex align-items-center'>
                            <div class='flex-shrink-0'>
                                <i class='fas fa-exclamation-triangle fa-3x text-danger me-3'></i>
                            </div>
                            <div class='flex-grow-1'>
                                <h5 class='alert-heading mb-2'>
                                    <i class='fas fa-file-signature me-2'></i>Multas Pendentes de Assinatura
                                </h5>
                                <p class='mb-2'>
                                    Você possui <strong>$nummultas</strong> multa(s) pendente(s) que requer(em) sua assinatura.
                                </p>
                                <p class='mb-0 text-muted small'>
                                    <i class='fas fa-info-circle me-1'></i>Por favor, compareça na frota para regularizar sua situação.
                                </p>
                            </div>
                        </div>
                    </div>";
                }

                ?>

                <p>
                    <?php
                    $sql1 = "SELECT hodometro, placa FROM bdfrota.tbveiculo WHERE matcond='$matricula';";
                    $resultado1 = mysql_query($sql1) or die(mysql_error());
                    $row1 = mysql_fetch_array($resultado1);
                    $hodometro = $row1['hodometro'];
                    $placa = $row1['placa'];

                    $sql2 = "SELECT max(idtbmanprev) as idtbmanprev FROM bdfrota.tbmanprev WHERE placa='$placa' AND status='CONCLUIDO' ";
                    $resultado2 = mysql_query($sql2) or die(mysql_error());
                    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                    $idtbmanprev = $row2['idtbmanprev'];

                    $sql3 = "SELECT * FROM bdfrota.tbmanprev WHERE idtbmanprev='$idtbmanprev' ";
                    $resultado3 = mysql_query($sql3) or die(mysql_error());
                    $row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
                    $dataocorrencia = $row3['dataocorrencia'];
                    $hodometroman = $row3['hodometro'];

                    $dif = $hodometro - $hodometroman;


                    /*if($dif>=11000){
                     print ""
                    }else*/
                    if ($dif < 11000 && $dif >= 9000) {
                        print "<span style='font-style: italic;'>Procure a Frota e envie seu veículo para manutenção.</span>";
                    }
                    /*elseif($dif<9000){
                      $status="2-EM INTERVALO";
                      $style = "style='background-color: green;' ";
                    }*/

                    //print $dif;
                    
                    ?>

                </p>
                <div class="row">
                    <?php
                    // ============================================
                    // Define o período da semana atual (segunda a domingo)
                    // ============================================
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
                    
                    // ============================================
                    // Query CORRIGIDA - Busca valor recebido da semana atual
                    // Usa subquery para evitar duplicação de valores
                    // ============================================
                    $sql = "SELECT
                                ROUND(COALESCE(ts.orcsemanal, 0) + COALESCE(cota_semana.total_cotas, 0), 2) AS valoraplicado,
                                ROUND(ts.kmproj, 2) AS kmproj,
                                ROUND(ts.kmosatual, 2) AS kmosatual,
                                ROUND(ts.kmproj * 0.75, 2) AS limite
                            FROM (
                                SELECT *
                                FROM bdfrota.tbsaldo
                                WHERE matricula = '$matricula'
                                ORDER BY data DESC
                                LIMIT 1
                            ) ts
                            LEFT JOIN (
                                SELECT 
                                    matricula,
                                    SUM(valorinserido) AS total_cotas
                                FROM bdfrota.tbcotaextraac
                                WHERE matricula = '$matricula'
                                  AND data >= '$segunda 00:00:00'
                                  AND data <= '$domingo 23:59:59'
                                GROUP BY matricula
                            ) cota_semana ON ts.matricula = cota_semana.matricula";
                    $resultado = mysql_query($sql) or die(mysql_error());
                    $row = mysql_fetch_array($resultado);
                    $valoraplicado = $row['valoraplicado'];
                    if ($valoraplicado == '') {
                        $valoraplicado = 0;
                    }
                    $kmproj = $row['kmproj'];
                    if ($kmproj == '') {
                        $kmproj = 0;
                    }
                    $kmosatual = $row['kmosatual'];
                    if ($kmosatual == '') {
                        $kmosatual = 0;
                    }
                    $limite = $row['limite'];
                    if ($limite == '') {
                        $limite = 0;
                    }

                    ?>
                    <div class="col-6">
                        <div class="rectangle">
                            <h5 style="
    font-size: 12px;
">Valor Recebido</h5>
                            <div class="number">R$:
                                <?php print "$valoraplicado"; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="rectangle">
                            <h5 style="
    font-size: 12px;
">KM Projetado</h5>
                            <div class="number">
                                <?php print "$kmproj"; ?> km
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <a href="./rotasdettec.php" style="text-decoration: none; color: inherit;">
                            <div class="rectangle" style="
    justify-content: center;
    display: flex;
    flex-direction: row;
">
                                <div style="
    flex-direction: column;
    display: flex;
">
                                    <h5 style="
    font-size: 12px;
">KM Atual Rodado OS</h5>
                                    <div class="number" style="
    border: none;
">
                                        <?php print "$kmosatual"; ?> km
                                    </div>
                                </div>
                                <svg style="
    display: flex;
" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 80 120">
                                    <defs>
                                        <style>
                                            .cls-1 {
                                                fill: #555353;
                                            }
                                        </style>
                                    </defs>
                                    <title>hand-mouse-cursor</title>
                                    <path class="cls-1"
                                        d="M31.66,83.11a2.37,2.37,0,0,1-4.74,0V78.74a2.82,2.82,0,0,1-.36-.24C25.06,77.3,23.4,76,22,74.77c-2.07-1.69-4.45-3.64-6.1-5a8.76,8.76,0,0,0-3.65-1.84,3.78,3.78,0,0,0-2.06.09A2.15,2.15,0,0,0,9,69.15a7.27,7.27,0,0,0-.41,3.33,12.62,12.62,0,0,0,1.15,4.1,27.12,27.12,0,0,0,3,5.06l.15.22,18,27.21a2.36,2.36,0,0,1,.38,1,15.66,15.66,0,0,0,1.92,6.57,2.94,2.94,0,0,0,2.63,1.49H64.05a8.39,8.39,0,0,0,4.8-1.55,15.29,15.29,0,0,0,4.4-5.15l.34-.58c3.5-6,6.89-11.87,7.24-18.81l-.18-8c0-.11,0-.23,0-.35l0-1.89c.07-5.32.15-11.9-4.72-12.73H72.78c0,1.5-.11,3-.2,4.5-.09,1.32-.17,2.6-.17,3.81a2.37,2.37,0,0,1-4.73,0c0-1.22.09-2.64.18-4.1.32-5,.68-10.77-3.33-11.49H61.42a2.76,2.76,0,0,1-.51,0c0,1.81-.09,3.69-.2,5.5-.09,1.32-.17,2.6-.17,3.81a2.37,2.37,0,1,1-4.73,0c0-1.22.09-2.63.18-4.1.32-5,.68-10.77-3.33-11.49H49.55a2.11,2.11,0,0,1-.62-.08v9.17a2.37,2.37,0,0,1-4.74,0V39.93c0-4.11-1.68-6.71-3.82-7.8a5.48,5.48,0,0,0-2.47-.6,5.38,5.38,0,0,0-2.46.6c-2.13,1.08-3.78,3.69-3.78,7.9V83.11ZM11.92,37.63a3.12,3.12,0,1,1,0,6.23H3.11a3.12,3.12,0,0,1,0-6.23ZM20.82,19a3.12,3.12,0,0,1-4.43,4.39l-7-7A3.11,3.11,0,1,1,13.82,12l7,7Zm41,24.84a3.12,3.12,0,0,1,0-6.23h8.81a3.12,3.12,0,1,1,0,6.23ZM57.3,22.73a3.12,3.12,0,0,1-4.42-4.4l7-7a3.12,3.12,0,0,1,4.42,4.4l-7,7Zm-17-10.81a3.12,3.12,0,1,1-6.23,0V3.11a3.12,3.12,0,0,1,6.23,0v8.81Zm26.8,78.56a1.94,1.94,0,1,1,3.87,0v8.84a1.94,1.94,0,0,1-3.87,0V90.48ZM55.64,86.57a1.94,1.94,0,0,1,3.87,0V99.32a1.94,1.94,0,0,1-3.87,0V86.57ZM26.92,72.72V40c0-6.26,2.8-10.3,6.37-12.12a10.21,10.21,0,0,1,9.2,0c3.61,1.82,6.44,5.86,6.44,12V50.78a2.51,2.51,0,0,1,.62-.08h3.26a2.33,2.33,0,0,1,.53.06,7.89,7.89,0,0,1,7.08,6.49,2.36,2.36,0,0,1,1-.22h3.26a2.33,2.33,0,0,1,.53.06c4.68.72,6.57,3.6,7.24,7.24a2.22,2.22,0,0,1,.37,0h3.26a2.33,2.33,0,0,1,.53.06c9,1.38,8.85,10.29,8.76,17.47,0,3.38.1,6.76.18,10.14a2.43,2.43,0,0,1,0,.27c-.4,8.12-4.08,14.46-7.87,21l-.32.57a20.15,20.15,0,0,1-5.82,6.69,13.15,13.15,0,0,1-7.47,2.38H35.86a7.35,7.35,0,0,1-6.6-3.58,19.2,19.2,0,0,1-2.62-8.08L9,84.47a32,32,0,0,1-3.52-6,17.06,17.06,0,0,1-1.53-5.71,11.59,11.59,0,0,1,.82-5.56,6.7,6.7,0,0,1,3.64-3.58,8.25,8.25,0,0,1,4.74-.35,13.42,13.42,0,0,1,5.76,2.84c1.43,1.18,3.79,3.12,6.09,5l2,1.61Z" />
                                </svg>
                            </div>
                        </a>
                    </div>
                    <div class="col-6">
                        <div class="rectangle">
                            <h5 style="
    font-size: 12px;
">Limite Solic. Devida</h5>
                            <div class="number">
                                <?php print "$limite"; ?> km
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if ($bloqueio > 0) {
                    print "<div class='m-auto text-center '>
                            <p style='margin: auto;font-size: 1.5rem;width: 60%;color: #d2132d;'>Foram identificados usos indevidos associados à sua matrícula, aguardando análise do coordenador responsável. O acesso ao sistema será restabelecido somente após a conclusão de todo o processo, incluindo as etapas posteriores à avaliação do coordenador.</p>
                        </div>";
                } else {
                    if ($numdescontos > 0) {
                        print "<div class='m-auto text-center '>
                                <p style='font-size: 1.5rem; color: #d2132d;'>Existem descontos por uso indevido do veículo aguardando sua assinatura.</p>
                                <p style='font-size: 1.5rem; color: #d2132d;'>Você poderá voltar a utilizar o sistema após a assinatura.</p>
                                <a href='./relatoriodescontousoindevidotec.php'><button class='btn btn-primary px-4 py-2'>Assinar</button></a>
                            </div>";
                    } else {

                        ?>

                        <form action="./control/tecnico.php" method="post" enctype='multipart/form-data'>
                            <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
                            <!--div solicitação-->
                            <div>
                                <label class="form-label mb-0 fs-5" for="ivalor">Solicitação:</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">R$: </span>
                                    <input class="form-control" type="text" name="valor" id="ivalor" required="true"
                                        placeholder="Informe o valor" onkeyup="formatarMoeda()" maxlength="6">
                                </div>
                            </div>

                            <div>
                                <label class="form-label mb-0 fs-5" for="kmhodometro">Hodometro:</label>
                                <div class="input-group mb-3">
                                    <!-- Regra: o hodometro deve ser maior que zero e corresponder ao valor da foto enviada. -->
                                    <span class="input-group-text">Hodometro</span>
                                    <input class="form-control" type="number" name="kmhodometro" id="kmhodometro"
                                        required="true" min="1" step="1" placeholder="Informe o valor do hodometro enviado na foto" maxlength="10" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10); if (this.value !== '' && Number(this.value) <= 0) this.value = '';">
                                </div>
                            </div>



                            <!--div justificativa-->
                            <div class="mb-3">
                                <label class="form-label mb-0 fs-5" for="ijust">Justificativa:</label>
                                <textarea rows="10" class="form-control" name="justificativa" id="ijust"
                                    placeholder="Escreva a justificativa" required="true"></textarea>
                            </div>
                            <?php
                            print "<input type='hidden' name='matricula' value='$matricula'>";
                            print "<input type='hidden' name='placa' value='$placa'>";
                            ?>
                            <label for="inputGroupFile04" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="16" height="16" fill="currentColor" class="bi bi-camera" style="margin-top: -3px;"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z" />
                                    <path
                                        d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z" />
                                </svg> Foto Hodômetro
                            </label>
                            <input type='file' class='form-control' id='inputGroupFile04'
                                aria-describedby='inputGroupFileAddon04' aria-label='Upload' capture='camera' name='arquivo'
                                style='display: none;' required>

                            <!--div botão confirmar-->
                            <div class="mt-4 d-none" id='inputGroupFileAddon04'>
                                <button class="btn btn-success" type="submit" id="btnConfirmar" <?php echo ($numdescontos == 0 ? "" : "disabled"); ?>>
                                    Confirmar
                                </button>
                            </div>

                        </form>
                        <?php
                    }
                }
                ?>
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


    <script>
        //função para formatar
        function formatarMoeda() {
            var elemento = document.getElementById('ivalor');
            var valor = elemento.value;


            valor = valor + '';
            valor = parseInt(valor.replace(/[\D]+/g, ''));
            valor = valor + '';
            valor = valor.replace(/([0-9]{2})$/g, ",$1");

            if (valor.length > 6) {
                valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
            }

            elemento.value = valor;
            if (valor == 'NaN') elemento.value = '';

        }
    </script>

    <script>
        // Função para verificar se o termo foi assinado
        function verificarTermoAssinado() {
            // Faz uma requisição AJAX para verificar o status do termo
            fetch('./control/verificarTermo.php?matricula=<?php echo $matricula; ?>')
                .then(response => response.json())
                .then(data => {
                    if (!data.assinado) { // Se o termo não estiver assinado
                        alert("Você deve assinar o termo de política de uso de veiculos da frota antes de acessar esta página.");
                        window.location.href = './memorando.php'; // Redireciona para o PDF
                    }
                })
                .catch(error => console.error('Erro ao verificar termo:', error));
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Verifica o termo ao carregar a página
            verificarTermoAssinado();
            
            // Evento do input de arquivo
            var fileInput = document.getElementById("inputGroupFile04");
            var btn = document.getElementById("inputGroupFileAddon04");

            if (fileInput) {
                fileInput.addEventListener("change", function () {
                    if (fileInput.value === "") {
                        btn.classList.add("d-none");
                    } else {
                        btn.classList.remove("d-none");
                    }
                });
            }
            
            // Evento de submit do formulário
            document.querySelector("form").addEventListener("submit", function (e) {
                var temSupervisor = <?php echo $tem_supervisor ? 'true' : 'false'; ?>;
                if (!temSupervisor) {
                    e.preventDefault();
                    alert('Pedido não concluído, colaborador sem supervisor.');
                    return;
                }
                const btnConfirmar = document.getElementById("btnConfirmar");
                btnConfirmar.disabled = true;
                btnConfirmar.innerText = "Enviando...";
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="src/js/datatables-simple-demo.js"></script>
</body>

</html>