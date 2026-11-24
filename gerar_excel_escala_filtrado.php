<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['matricula']) || empty($_SESSION['matricula'])) {
    die("Acesso negado. Faça login.");
}

// Verifica se o perfil do usuário tem permissão (todos exceto 0 - técnicos)
if (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 0) {
    die("Você não tem permissão para acessar esta página.");
}

// Recebe os dados filtrados via POST
$dadosFiltrados = isset($_POST['dados']) ? json_decode($_POST['dados'], true) : [];

if (empty($dadosFiltrados)) {
    die("Nenhum dado filtrado foi enviado.");
}

// Nome do arquivo
$filename = "escala_tecnicos_filtrado_" . date('Y-m-d_His') . ".xls";

// Headers para download do arquivo Excel
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Início do conteúdo HTML/Excel com encoding UTF-8
echo "\xEF\xBB\xBF"; // BOM para UTF-8
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table {
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            width: 100%;
        }
        th {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            font-size: 12px;
        }
        td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
        }
        .centro {
            text-align: center;
        }
        .direita {
            text-align: right;
        }
        .header-sabado {
            background-color: #2196F3 !important;
        }
        .header-domingo {
            background-color: #FF9800 !important;
        }
        .destaque {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            font-size: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>MATRÍCULA</th>
                <th>NOME DO TÉCNICO</th>
                <th>COORDENADOR</th>
                <th>SUPERVISOR</th>
                <th>PLACA</th>
                <th class="header-sabado">SÁBADO</th>
                <th class="header-domingo">DOMINGO</th>
                <th>VALOR/DIA</th>
                <th>VALOR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dadosFiltrados as $row): ?>
            <tr>
                <td class="centro"><?php echo htmlspecialchars($row['matricula']); ?></td>
                <td><?php echo htmlspecialchars($row['nome_tecnico']); ?></td>
                <td class="centro"><?php echo htmlspecialchars($row['coordenador']); ?></td>
                <td class="centro"><?php echo htmlspecialchars($row['supervisor']); ?></td>
                <td class="centro"><?php echo htmlspecialchars($row['placa']); ?></td>
                <td class="centro"><?php echo nl2br(htmlspecialchars($row['sabado'])); ?></td>
                <td class="centro"><?php echo nl2br(htmlspecialchars($row['domingo'])); ?></td>
                <td class="direita"><?php echo htmlspecialchars($row['valor_por_dia']); ?></td>
                <td class="direita destaque">
                    <?php 
                    // Valor já foi aplicado com regra de 45/75 (tipovel 1 ou 4) ou valor mínimo no cálculo
                    echo htmlspecialchars($row['valor']); 
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer-info">
        <strong>Gerado em:</strong> <?php echo date('d/m/Y H:i:s'); ?> | 
        <strong>Total de registros:</strong> <?php echo count($dadosFiltrados); ?>
    </div>
</body>
</html>
