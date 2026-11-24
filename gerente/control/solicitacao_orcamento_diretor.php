<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../../conecta.php");
include_once"../../func/log.php";

$matriculaf = $_POST['matricula'];
$justificativa = $_POST['justificativa'];

$valor = $_POST['valor'];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);

    // Escapa valores para prevenir SQL injection
    $matriculaf_safe = addslashes($matriculaf);
    $valor_safe = addslashes($valor);
    $justificativa_safe = addslashes($justificativa);
    
    $sql = "INSERT INTO bdfrota.tbpedidosdiretor (matricula, valor, justificativa, data) values ('$matriculaf_safe', '$valor_safe', '$justificativa_safe', '$hoje')";
    
    // Tenta mysqli primeiro, depois mysql (compatibilidade)
    if (function_exists('mysqli_query') && isset($conexao) && $conexao instanceof mysqli) {
        $resultado = mysqli_query($conexao, $sql);
    } else {
        $resultado = mysql_query($sql);
    }
    
    $acao = 'Pediu orçamento extra';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', $valor);
    
    echo"<script language='javascript' type='text/javascript'>
    alert('Pedido de orçamento enviado com sucesso!');
    window.location=\"../solicitacao_orcamento_diretor.php\";
    </script>";
        
?>
