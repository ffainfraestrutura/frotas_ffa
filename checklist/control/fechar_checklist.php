<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Recebe os parâmetros
$idtbvistoria = $_GET['idtbvistoria'] ?? '';
$placa = $_GET['placa'] ?? '';
$matricula = $_GET['matricula'] ?? '';
$cpf1 = $_GET['cpf1'] ?? '';
$cpf2 = $_GET['cpf2'] ?? '';

// Validações básicas
if (empty($idtbvistoria) || empty($placa)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('Erro: Dados insuficientes para fechar o checklist.');
        window.location='../checklistinicio.php';
    </script>";
    exit();
}

// Conecta ao banco
include "../conecta.php";

// Atualiza o status do checklist para fechado (statusreg = 1)
$dataFechamento = date('Y-m-d H:i:s');
$sqlUpdate = "UPDATE bdfrota.tbvistoria 
              SET statusreg = 1, 
                  obs = CONCAT(COALESCE(obs, ''), ' [Fechado automaticamente em $dataFechamento para abertura de novo checklist]')
              WHERE idtbvistoria = ?";

$stmt = mysqli_prepare($con, $sqlUpdate);
mysqli_stmt_bind_param($stmt, 'i', $idtbvistoria);
$resultado = mysqli_stmt_execute($stmt);

if ($resultado) {
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    
    // Sucesso ao fechar - Agora vamos criar um formulário POST para enviar os dados e continuar o fluxo
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>Processando...</title>
    </head>
    <body>
        <p style='text-align: center; margin-top: 50px; font-family: Arial;'>Checklist anterior fechado. Iniciando novo checklist...</p>
        <form id='continuarForm' method='POST' action='../checklistp1.php'>
            <input type='hidden' name='placa' value='<?php echo $placa; ?>'>
            <input type='hidden' name='cpf1' value='<?php echo $cpf1; ?>'>
            <input type='hidden' name='cpf' value='<?php echo $cpf2; ?>'>
        </form>
        <script>
            // Submete o formulário automaticamente após breve pausa
            setTimeout(function() {
                document.getElementById('continuarForm').submit();
            }, 500);
        </script>
    </body>
    </html>
    <?php
    exit();
} else {
    // Erro ao fechar
    $erro = mysqli_error($con);
    echo "<script language='javascript' type='text/javascript'>
        alert('Erro ao fechar o checklist anterior: $erro\\n\\nPor favor, tente novamente ou contate o suporte.');
        window.location='../checklistinicio.php';
    </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>
