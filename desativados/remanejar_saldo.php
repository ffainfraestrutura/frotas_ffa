<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
if (!isset($_SESSION['matricula'])) {
    header("Location: index.php");
    exit();
}
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include "conecta05.php";
include_once "./func/consultacartao.php";

$tecnicos = [];
$sql = "SELECT 
            sup.idtbsupervisor,
            u.nome,
            u.matricula,
            u.perfil,
            sal.totalextra,
            sal.idtbsaldo,
            sal.sldprjtd,
            sal.orcsemanal,
            vei.placa
        FROM
            bdfrota.tbsupervisor sup
                JOIN bdfrota.tbusuario u ON sup.idtbsupervisor = u.idtbsupervisor
                JOIN bdaniel.tbfuncionario tec ON u.matricula = tec.matricula
                JOIN bdfrota.tbsaldo sal ON sal.matricula = u.matricula
                JOIN bdfrota.tbveiculo vei ON vei.matcond = u.matricula
        WHERE
            sup.matricula = ?
            AND tec.status != 'demitido'
            AND u.perfil = 0
        GROUP BY nome
        ORDER BY sal.data DESC";

$stmt = mysqli_prepare($conexao05, $sql);
mysqli_stmt_bind_param($stmt, 's', $_SESSION['matricula']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$tecnicos = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <!-- Todo o <head> permanece igual ao exemplo anterior -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Remanejamento de Saldo - Portal FFA</title>
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="http://plentz.github.io/jquery-maskmoney/javascripts/jquery.maskMoney.min.js"></script>
    <style>
        .saldo-info {
            font-size: 1.1em;
            font-weight: bold;
            color: #007bff;
            margin-top: 5px;
            min-height: 25px;
        }

        .form-section {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="sb-nav-fixed">
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Remanejamento de Saldo</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Transfira saldo entre colaboradores</li>
                </ol>

                <div class="card mb-4">
                    <div class="card-header"><i class="fas fa-exchange-alt"></i> Realizar Transferência</div>
                    <div class="card-body">
                        <!-- O form e todo o HTML permanecem exatamente iguais ao exemplo anterior -->
                        <form action="./control/remanejar_saldo.php" method="post" id="transferForm">
                            <!-- ... (copie e cole a seção do <form> do exemplo anterior aqui) ... -->
                            <input type="hidden" name="matricula_autor" id="matricula_autor" value="<?= $matricula ?>">
                            <div class="row">
                                <!-- Coluna de Origem -->
                                <div class="col-md-5">
                                    <div class="form-section">
                                        <h4><i class="fas fa-arrow-up"></i> Origem (Retirar de)</h4>
                                        <label for="matricula_origem" class="form-label">Colaborador:</label>
                                        <select name="matricula_origem" id="matricula_origem" class="form-select"
                                            required>
                                            <option value="">-- Selecione um colaborador --</option>
                                            <?php foreach ($tecnicos as $tecnico): ?>
                                                <option value="<?php echo htmlspecialchars($tecnico['matricula']); ?>">
                                                    <?php echo htmlspecialchars($tecnico['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="saldo-info" id="saldo_origem">Saldo: R$ 0,00</div>
                                        <input type="hidden" name="saldo_numerico_origem" id="saldo_numerico_origem"
                                            value="0">
                                        <input type="hidden" name="placa_origem" id="placa_origem" value="">
                                        <input type="hidden" name="codempresa_origem" id="codempresa_origem" value="">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex flex-column align-items-center justify-content-center">
                                    <label for="valor" id="valor-label" class="form-label fw-bold">Valor a Transferir
                                        (R$)</label>
                                    <input type="text" name="valor" id="valor"
                                        class="form-control form-control-lg text-center numerico" required>
                                    <i id="arrow" class="fas fa-arrow-right fa-2x mt-3 text-success"></i>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-section">
                                        <h4><i class="fas fa-arrow-down"></i> Destino (Adicionar para)</h4>
                                        <label for="matricula_destino" class="form-label">Colaborador:</label>
                                        <select name="matricula_destino" id="matricula_destino" class="form-select"
                                            required>
                                            <option value="">-- Selecione um colaborador --</option>
                                            <?php foreach ($tecnicos as $tecnico): ?>
                                                <option value="<?php echo htmlspecialchars($tecnico['matricula']); ?>">
                                                    <?php echo htmlspecialchars($tecnico['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="saldo-info" id="saldo_destino">Saldo: R$ 0,00</div>
                                        <input type="hidden" name="saldo_numerico_destino" id="saldo_numerico_destino"
                                            value="0">
                                        <input type="hidden" name="placa_destino" id="placa_destino" value="">
                                        <input type="hidden" name="codempresa_destino" id="codempresa_destino" value="">
                                    </div>
                                </div>
                            </div>
                            <p class="text-danger h5 text-center" id="error_text"></p>
                            <div class="text-center mt-4">
                                <button type="submit" id="transfer-button" class="btn btn-primary btn-lg"><i
                                        class="fas fa-check-circle"></i>
                                    Confirmar Transferência</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <?php // include "footer.php"; ?>
    </div>

    <!-- Os scripts do final da página também permanecem os mesmos -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js"></script>
    <script>
        $(document).ready(function () {
            let valorMaximo = null;
            const $valor = $("#valor");
            const $errorText = $("#error_text");
            const $valorLabel = $("#valor-label");
            const $transferButton = $("#transfer-button");
            const $arrow = $("#arrow");
            const $saldoOrigem = $("#saldo_numerico_origem");
            const $saldoDestino = $("#saldo_numerico_destino");
            const $matriculaOrigem = $("#matricula_origem");
            const $matriculaDestino = $("#matricula_destino");

            // botão inicia desabilitado
            $transferButton.prop("disabled", true);

            function validarFormulario() {
                const saldoOrigemVal = $saldoOrigem.val();
                const saldoDestinoVal = $saldoDestino.val();
                const valorDigitado = parseFloat(($valor.val() || "0").replace(",", "."));

                const origemValida = saldoOrigemVal !== "" && saldoOrigemVal !== null && !isNaN(saldoOrigemVal);
                const destinoValida = saldoDestinoVal !== "" && saldoDestinoVal !== null && !isNaN(saldoDestinoVal);
                const valorPositivo = valorDigitado > 0;
                const saldoSuficiente = valorMaximo !== null && valorDigitado <= valorMaximo;
                const origemDiferenteDestino = ($matriculaOrigem.val() && $matriculaDestino.val()) && $matriculaOrigem.val() !== $matriculaDestino.val();

                let erro = "";
                if (!origemDiferenteDestino && ($matriculaOrigem.val() && $matriculaDestino.val())) {
                    erro = "Origem e destino não podem ser iguais";
                } else if (!saldoSuficiente) {
                    erro = valorMaximo == null
                        ? "Erro: saldo de Origem não carregado"
                        : "Erro: valor a transferir maior que o saldo de Origem";
                }

                const invalido = !(origemValida && destinoValida && valorPositivo && saldoSuficiente && origemDiferenteDestino);

                $errorText.text(erro);
                $valor.css("border-color", invalido ? "#ff0000" : "");
                $valorLabel.css("color", invalido ? "#ff0000" : "");
                $("#arrow").removeClass('text-danger text-success');
                $("#arrow").addClass(invalido ? 'text-danger' : 'text-success');

                $transferButton.prop("disabled", invalido);
            }

            function atualizarSaldo(matricula, saldoId, saldoHiddenId, placaId, codEmpresaID) {
                if (matricula) {
                    if (saldoId === "saldo_origem") valorMaximo = null;
                    $.ajax({
                        url: 'ajax_get_saldo.php',
                        type: 'POST',
                        data: { matricula: matricula },
                        dataType: 'json',
                        cache: false,
                        success: function (response) {
                            if (response.success) {
                                $('#' + saldoId).text('Saldo: ' + response.saldo_formatado);
                                $('#' + saldoHiddenId).val(response.saldo_numerico);
                                $('#' + placaId).val(response.placa);
                                $('#' + codEmpresaID).val(response.codempresa);
                                if (saldoId === "saldo_origem") {
                                    valorMaximo = response.saldo_numerico;
                                }
                            } else {
                                $('#' + saldoId).text('Erro ao buscar saldo');
                                $('#' + saldoHiddenId).val("");
                                $('#' + placaId).val("");
                                $('#' + codEmpresaID).val("");
                                if (saldoId === "saldo_origem") {
                                    valorMaximo = null;
                                }
                            }
                            validarFormulario();
                        },
                        error: function () {
                            $('#' + saldoId).text('Erro na requisição');
                            $('#' + saldoHiddenId).val("");
                            if (saldoId === "saldo_origem") {
                                valorMaximo = null;
                            }
                            validarFormulario();
                        }
                    });
                } else {
                    $('#' + saldoId).text('Saldo: R$ 0,00');
                    $('#' + saldoHiddenId).val("");
                    if (saldoId === "saldo_origem") {
                        valorMaximo = null;
                    }
                    validarFormulario();
                }
            }

            // eventos
            $matriculaOrigem.on('change', function () {
                atualizarSaldo($(this).val(), 'saldo_origem', 'saldo_numerico_origem', 'placa_origem', 'codempresa_origem');
            });

            $matriculaDestino.on('change', function () {
                atualizarSaldo($(this).val(), 'saldo_destino', 'saldo_numerico_destino', 'placa_destino', 'codempresa_destino');
            });

            $valor.on("input", validarFormulario);
        });
    </script>
</body>

</html>