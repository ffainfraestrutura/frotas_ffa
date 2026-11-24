<?php
include '../../func/funcoes.php';
include "../../conecta2.php";
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

// Seleciona saldo da origem
$sql = "SELECT valoraplicado, kmproj, valrem, orcsemanal, idtbsaldo 
        FROM bdfrota.tbsaldo 
        WHERE matricula = ? 
        ORDER BY data desc 
        LIMIT 1";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "s", $matricula_origem);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$saldo_origem = mysqli_fetch_assoc($result); // Só retorna uma linha
$valoraplicado_origem = $saldo_origem['valoraplicado'];
$kmproj_origem = round($saldo_origem['kmproj'], 2);
$valrem_origem = $saldo_origem['valrem'];
$orcsemanal_origem = $saldo_origem['orcsemanal'];
$idtbsaldo_origem = $saldo_origem['idtbsaldo'];

// Seleciona saldo do destino
mysqli_stmt_bind_param($stmt, "s", $matricula_destino);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$saldo_destino = mysqli_fetch_assoc($result);

$valoraplicado_destino = $saldo_destino['valoraplicado'];
$kmproj_destino = round($saldo_destino['kmproj'], 2);
$valrem_destino = $saldo_destino['valrem'];
$orcsemanal_destino = $saldo_destino['orcsemanal'];
$idtbsaldo_destino = $saldo_destino['idtbsaldo'];

// Exemplo de valores (ajuste conforme seu código)

$novovalor_origem = $valoraplicado_origem - $valor;
$novovalor_destino = $valoraplicado_destino + $valor;

// Calcula novo kmproj origem
if ($kmproj_origem != 0) {
    if ($valoraplicado_origem == 0) {
        $valoraplicado_origem = $orcsemanal_origem;
        $novovalor_origem = $valoraplicado_origem - $valor;
    }
    $novokmproj_origem = round(($novovalor_origem * $kmproj_origem) / $valoraplicado_origem, 2);

} else {
    $novokmproj_origem = 0;
}
// Calcula novo kmproj destino
if ($kmproj_destino) {
    if ($valoraplicado_destino == 0) {
        $valoraplicado_destino = $orcsemanal_destino;
        $novovalor_destino = $valoraplicado_destino + $valor;
    }
    $novokmproj_destino = round(($novovalor_destino * $kmproj_destino) / $valoraplicado_destino, 2);
}
// Atualiza kmproj da origem
$sql = "UPDATE bdfrota.tbsaldo SET kmproj = ?, valoraplicado = valoraplicado - ?, valrem = valrem - ? WHERE idtbsaldo = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "dddi", $novokmproj_origem, $valor, $valor, $idtbsaldo_origem);
mysqli_stmt_execute($stmt);

// Atualiza kmproj do destino
$sql = "UPDATE bdfrota.tbsaldo SET kmproj = ?, valoraplicado = valoraplicado + ?, valrem = valrem + ? WHERE idtbsaldo = ?";
$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "dddi", $novokmproj_destino, $valor, $valor, $idtbsaldo_destino);
mysqli_stmt_execute($stmt);

echo "
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Sucesso!',
        text: 'Transfer�ncia realizada com sucesso!',
        icon: 'success',
        confirmButtonText: 'Ok'
    }).then(() => {
        window.location.href = '../remanejarsaldo.php';
    });
}); 
</script>
";