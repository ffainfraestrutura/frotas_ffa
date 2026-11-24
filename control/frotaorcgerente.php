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
include("../conecta.php");
include_once"../func/log.php";

$matriculager = $_POST['matriculager'];
$id = $_POST['id'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);

// Busca o saldo do diretor de frotas logado
$sql_saldo = "SELECT valor FROM bdfrota.tbfrota WHERE matricula = '$matricula'";
$resultado_saldo = mysql_query($sql_saldo) or die (mysql_error());
$row_saldo = mysql_fetch_array($resultado_saldo);
$saldo_frota = $row_saldo[0];

if ($decisao == 2) {
    
    // Verifica se o diretor tem saldo suficiente
    if ($saldo_frota < $valor) {
        echo"<script language='javascript' type='text/javascript'>alert('Saldo insuficiente! Seu saldo atual é R$ " . number_format($saldo_frota, 2, ',', '.') . " e o valor solicitado é R$ " . number_format($valor, 2, ',', '.') . "');window.location=\"../../frotaorcgerente.php\"</script>";
        die();
    }

    // Atualiza o pedido para aprovado (flag = 2)
    $sql = "UPDATE bdfrota.tbpedidosgerente SET flag = $decisao WHERE idtbpedidosgerente = $id";
    $resultado = mysql_query($sql) or die (mysql_error());

    // Adiciona o valor ao saldo do gerente
    $sql = "UPDATE bdfrota.tbgerente SET valor = valor + $valor WHERE matricula = '$matriculager'";
    $resultado = mysql_query($sql) or die (mysql_error());
    
    // Desconta o valor do saldo do diretor de frotas
    $sql = "UPDATE bdfrota.tbfrota SET valor = valor - $valor WHERE matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());

    // Registra a distribuição de orçamento
    $sql = "INSERT INTO bdfrota.tborcdistfrota (matr_frota, matr_gerente, valor_ant, valor_ins, data) 
            VALUES ('$matricula', '$matriculager', '$saldo_frota', '$valor', '$hoje')";
    $resultado = mysql_query($sql) or die (mysql_error());

    // Log da ação
    $acao = 'Orçamento extra para gerente aprovado';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculager, $matricula, $saldo_frota, $valor);
    
    echo"<script language='javascript' type='text/javascript'>alert('Pedido Aprovado! Valor R$ " . number_format($valor, 2, ',', '.') . " adicionado ao saldo do gerente e descontado do seu saldo.');window.location=\"../../frotaorcgerente.php\"</script>";
            
} else {

    // Nega o pedido (flag = 1)
    $sql = "UPDATE bdfrota.tbpedidosgerente SET flag = $decisao WHERE idtbpedidosgerente = $id";
    $resultado = mysql_query($sql) or die (mysql_error());

    // Log da ação
    $acao = 'Pedido de orçamento extra para gerente negado';
    $flag = '1';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculager, $matricula, $valor_ant, $valor);

    echo"<script language='javascript' type='text/javascript'>alert('Pedido Negado!');window.location=\"../../frotaorcgerente.php\"</script>";
}

?>
