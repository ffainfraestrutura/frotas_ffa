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
include_once "../../func/log.php";

$matriculager = $_POST['matriculager'];  // Matrícula do gerente
$id = $_POST['id'];
$decisao = $_POST['decisao'];
$valor = isset($_POST["valor"]) ? $_POST["valor"] : '';
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
$valor = $valor !== '' ? number_format(floatval($valor), 2, '.', '') : 0;
$valor_ant = isset($_POST["valor_ant"]) ? $_POST["valor_ant"] : '';
$valor_ant = str_replace(".", "", $valor_ant);
$valor_ant = str_replace(",", ".", $valor_ant);
$valor_ant = $valor_ant !== '' ? number_format(floatval($valor_ant), 2, '.', '') : 0;

// Busca o saldo do diretor
$sql = "SELECT valor FROM bdfrota.tbdiretor WHERE matricula = '$matricula'";
$resultado = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($resultado);

// Valida se o diretor existe no banco
if (!$row) {
    echo "<script language='javascript' type='text/javascript'>alert('Erro: Diretor não encontrado no sistema!');window.location=\"../aprovarorcamentocomplementar.php\"</script>";
    exit();
}

$saldodiretor = floatval($row[0]);

if ($decisao == 2) {  // Aprovar

    // Valida se o valor solicitado é válido (somente para aprovação)
    if ($valor <= 0) {
        echo "<script language='javascript' type='text/javascript'>alert('Erro: Valor inválido!');window.location=\"../aprovarorcamentocomplementar.php\"</script>";
        exit();
    }

    // Verifica saldo disponível
    if ($valor > $saldodiretor) {
        $saldo_formatado = number_format($saldodiretor, 2, ',', '.');
        $valor_formatado = number_format($valor, 2, ',', '.');
        echo "<script language='javascript' type='text/javascript'>
                alert('Saldo Insuficiente!\\n\\nSaldo disponível: R$ $saldo_formatado\\nValor solicitado: R$ $valor_formatado');
                window.location=\"../aprovarorcamentocomplementar.php\"
              </script>";
        exit();
    } else {
        // Obtém o saldo diretamente do banco para registrar a movimentação sem
        // depender do valor enviado pelo formulário.
        $sql = "SELECT valor FROM bdfrota.tbgerente WHERE matricula = '$matriculager'";
        $resultado = mysql_query($sql) or die(mysql_error());
        $row_gerente = mysql_fetch_array($resultado);

        if (!$row_gerente) {
            echo "<script language='javascript' type='text/javascript'>alert('Erro: Gerente não encontrado no sistema!');window.location=\"../aprovarorcamentocomplementar.php\"</script>";
            exit();
        }

        $saldo_anterior_gerente = number_format(floatval($row_gerente[0]), 2, '.', '');

        // Atualiza flag do pedido para aprovado
        $sql = "UPDATE bdfrota.tbpedidosdiretor SET flag = $decisao WHERE idtbpedidosdiretor = $id";
        $resultado = mysql_query($sql) or die(mysql_error());

        // Adiciona valor ao saldo do gerente
        $sql = "UPDATE bdfrota.tbgerente SET valor = valor + $valor, orcrecebido = orcrecebido + $valor WHERE matricula = '$matriculager'";
        $resultado = mysql_query($sql) or die(mysql_error());

        // Subtrai valor do saldo do diretor
        $sql = "UPDATE bdfrota.tbdiretor SET valor = valor - $valor WHERE matricula = '$matricula'";
        $resultado = mysql_query($sql) or die(mysql_error());

        // Lê novamente o saldo já atualizado para que o log reflita o valor
        // efetivamente persistido após a aprovação.
        $sql = "SELECT valor FROM bdfrota.tbgerente WHERE matricula = '$matriculager'";
        $resultado = mysql_query($sql) or die(mysql_error());
        $row_gerente = mysql_fetch_array($resultado);
        $saldo_atual_gerente = number_format(floatval($row_gerente[0]), 2, '.', '');

        // Registra também o saldo efetivamente persistido do diretor após o
        // débito, permitindo auditar os dois lados da transferência.
        $sql = "SELECT valor FROM bdfrota.tbdiretor WHERE matricula = '$matricula'";
        $resultado = mysql_query($sql) or die(mysql_error());
        $row_diretor = mysql_fetch_array($resultado);
        $saldo_anterior_diretor = number_format($saldodiretor, 2, '.', '');
        $saldo_atual_diretor = number_format(floatval($row_diretor[0]), 2, '.', '');

        $flag = '2';
        $acao_gerente = 'Aprovou orçamento extra de gerente';
        $logresult = enviarlog($hoje, $acao_gerente, $flag, $matriculager, $matricula, $saldo_anterior_gerente, $saldo_atual_gerente);

        $acao_diretor = 'Aprovou orçamento extra de gerente - débito no saldo do diretor';
        $logresult = enviarlog($hoje, $acao_diretor, $flag, $matricula, $matricula, $saldo_anterior_diretor, $saldo_atual_diretor);
        
        $valor_formatado = number_format($valor, 2, ',', '.');
        echo "<script language='javascript' type='text/javascript'>
                alert('Pedido Aprovado!\\n\\nOrçamento de R$ $valor_formatado transferido para o gerente.');
                window.location=\"../aprovarorcamentocomplementar.php\"
              </script>";
    }
} else {  // Negar

    // Atualiza flag do pedido para negado
    $sql = "UPDATE bdfrota.tbpedidosdiretor SET flag = $decisao WHERE idtbpedidosdiretor = $id";
    $resultado = mysql_query($sql) or die(mysql_error());

    $acao = 'Negou orçamento extra de gerente';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculager, $matricula, $valor_ant, $valor);

    echo "<script language='javascript' type='text/javascript'>alert('Pedido Negado!');window.location=\"../aprovarorcamentocomplementar.php\"</script>";
}


?>