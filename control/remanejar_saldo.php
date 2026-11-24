<?php
include '../func/funcoes.php';
include "../conecta2.php";
date_default_timezone_set('America/Sao_Paulo');

$matricula_autor = $_POST['matricula_autor'];
$matricula_origem = $_POST['matricula_origem'];
$saldo_origem = (float) $_POST['saldo_numerico_origem'];
$placa_origem = $_POST['placa_origem'];
$codempresa_origem = $_POST['codempresa_origem'];
$matricula_destino = $_POST['matricula_destino'];
$saldo_destino = (float) $_POST['saldo_numerico_destino'];
$placa_destino = $_POST['placa_destino'];
$codempresa_destino = $_POST['codempresa_destino'];
$valor = (float) $_POST['valor'];
$data_hora = date("Y-m-d H:i:s");

if ($codempresa_origem == 1) {
    removerSaldoTicket($placa_origem, $valor);
} else {
    removerSaldoValeCard($placa_origem, $valor);
}

if ($codempresa_destino == 1) {
    adicionarSaldoTicket($placa_destino, $valor);
} else {
    adicionarSaldoValeCard($placa_destino, $valor);
}

$saldo_final_origem = $saldo_origem - $valor;
$saldo_final_destino = $saldo_destino + $valor;

$sql = "INSERT INTO bdfrota.tbremanejamentosaldo
(
matricula_origem,
saldo_inicial_origem,
saldo_final_origem,
matricula_destino,
saldo_inicial_destino,
saldo_final_destino,
valor_transferido,
data_hora_transferencia,
status,
autor
)
VALUES
(?,?,?,?,?,?,?,?,?,?)
";
$status = 1;
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "sddsdddsis",
    $matricula_origem,
    $saldo_origem,
    $saldo_final_origem,
    $matricula_destino,
    $saldo_destino,
    $saldo_final_destino,
    $valor,
    $data_hora,
    $status,
    $matricula_autor
);
mysqli_stmt_execute($stmt);
echo "
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Sucesso!',
        text: 'Transferência realizada com sucesso!',
        icon: 'success',
        confirmButtonText: 'Ok'
    }).then(() => {
        window.location.href = '../remanejar_saldo.php';
    });
});
</script>
";