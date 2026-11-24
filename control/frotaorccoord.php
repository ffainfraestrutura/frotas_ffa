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

$matriculaf = $_POST['matriculasup'];
$id = $_POST['id'];
$decisao = $_POST['decisao'];
$valor = $_POST["valor"];
$valor = str_replace(",", ".", $valor);
$valor_ant = $_POST["valor_ant"];
$valor_ant = str_replace(",", ".", $valor_ant);

$sql1 = "SELECT idtbsaldofrota, saldoatual FROM bdfrota.tbsaldofrota order by data_e_hora desc limit 1";
$resultado1 = mysql_query($sql1) or die(mysql_error());
$row1 = mysql_fetch_array($resultado1);
$idsaldofrota = $row1[0];
$saldofrota = $row1[1];
if ($decisao == 2) {

    if($saldofrota < $valor){
        echo"<script language='javascript' type='text/javascript'>alert('Fundo insuficiente para aprovação do pedido!');window.location=\"../../frotaorccoord.php\"</script>";
        exit();
    }

    $sql = "UPDATE bdfrota.tbpedidosgerente SET flag = $decisao where idtbpedidosgerente = $id";
    $resultado = mysql_query($sql) or die (mysql_error());

    // Atualiza tbgerente OU tbdiretor, dependendo de onde a matrícula existe
    $sql_update_gerente = "UPDATE bdfrota.tbgerente SET valor = valor + $valor, orcrecebido = orcrecebido + $valor WHERE matricula = '$matriculaf'";
    $result_gerente = mysql_query($sql_update_gerente);
    
    if (mysql_affected_rows() == 0) {
        // Se não atualizou em tbgerente, tenta em tbdiretor
        $sql_update_diretor = "UPDATE bdfrota.tbdiretor SET valor = valor + $valor, orcrecebido = orcrecebido + $valor WHERE matricula = '$matriculaf'";
        mysql_query($sql_update_diretor) or die (mysql_error());
    }
    
    $sql = "UPDATE bdfrota.tbsaldofrota SET saldoatual = saldoatual - $valor where idtbsaldofrota = '$idsaldofrota'";
    $resultado = mysql_query($sql) or die (mysql_error());

    $sql = "INSERT INTO bdfrota.tborcdistfrota (matr_frota, matr_coord, valor_ant, valor_ins, data) VALUES ('$matricula', '$matriculaf', '$valor_ant', '$valor', '$hoje')";
    $resultado = mysql_query($sql) or die (mysql_error());

    $acao = 'Orçamento extra frota';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);
    echo"<script language='javascript' type='text/javascript'>alert('Pedido Aceito!');window.location=\"../../frotaorccoord.php\"</script>";
        
} else {

    $sql = "UPDATE bdfrota.tbpedidosgerente SET flag = $decisao where idtbpedidosgerente  = $id";
    $resultado = mysql_query($sql) or die (mysql_error());

    $acao = 'Não aceitou orçamento extra frota';
    $flag = '2';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

    echo"<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../../frotaorccoord.php\"</script>";
}





        
?>
