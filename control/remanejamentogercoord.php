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

$matricula = $_POST['matriculager'];
$matriculaf = $_POST['matriculacoord'];
$valor = $_POST["valor"];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
$tipocota = $_POST["tipocota"];

    
if ($tipocota == 'retirada') {

    $sql = "SELECT valor FROM bdfrota.tbcoord where matricula = '$matriculaf'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldocoord = $row[0];


            if ($valor > $saldocoord) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../remanejamentogerente.php\"</script>";

            } else {


            $sql = "UPDATE bdfrota.tbcoord SET valor = valor - $valor, orcrecebido = orcrecebido - $valor where matricula = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());

            $sql = "UPDATE bdfrota.tbgerente SET saldoorc = saldoorc + $valor where matricula = '$matricula'";
            $resultado = mysql_query($sql) or die (mysql_error());

            $acao = 'Retirou saldo';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldosup, $valor);

                echo"<script language='javascript' type='text/javascript'>alert('Retirada feito com sucesso!');window.location=\"../remanejamentogerente.php\"</script>";
        } 
}

if ($tipocota == 'entrada') {

            $sql = "SELECT saldoorc FROM bdfrota.tbgerente where matricula = '$matricula'";
            $resultado = mysql_query($sql) or die (mysql_error());
            $row = mysql_fetch_array($resultado);
            $saldogerente = $row[0];

            if ($valor > $saldogerente) {
                
                echo"<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../remanejamentogerente.php\"</script>";

            } else {

            $sql = "UPDATE bdfrota.tbcoord SET  valor = valor + $valor, orcrecebido = orcrecebido + $valor where matricula = '$matriculaf'";
            $resultado = mysql_query($sql) or die (mysql_error());
            
            $sql = "UPDATE bdfrota.tbgerente SET saldoorc = saldoorc - $valor where matricula = '$matricula'";
            $resultado = mysql_query($sql) or die (mysql_error());
            
            $acao = 'Aceitou orçamento extra';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);
               echo"<script language='javascript' type='text/javascript'>alert('Orçamento Enviado');window.location=\"../remanejamentogerente.php\"</script>";
            }

}

?>
