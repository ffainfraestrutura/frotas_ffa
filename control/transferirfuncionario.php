<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['perfil'] = $perfil;
include("../conecta.php");
include_once"../func/log.php";

// Verifica se é transferência de técnico ou supervisor
if(isset($_POST['matriculatec'])) {
    // TRANSFERÊNCIA DE TÉCNICO PARA SUPERVISOR
    $matriculaf = $_POST['matriculatec'];

    $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
    
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbsupervisor = $row[0];

    $sql = "UPDATE bdfrota.tbusuario SET idtbsupervisor = $idtbsupervisor where matricula = '$matriculaf'";
    $resultado = mysql_query($sql) or die (mysql_error());

    $acao = 'Transferiu tecnico';
    $flag = '7';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

    echo"<script language='javascript' type='text/javascript'>alert('Transferencia de técnico concluida!');window.location=\"../transferirfuncionario.php\"</script>";

} elseif(isset($_POST['matricula_sup'])) {
    // TRANSFERÊNCIA DE SUPERVISOR PARA COORDENADOR
    $matriculaf = $_POST['matricula_sup'];

    // Busca o ID do coordenador logado na tabela correta (tbcoord)
    $sql = "SELECT idtbcoordenador FROM bdfrota.tbcoord where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbcoordenador = $row[0];

    // Verificar se o ID do coordenador foi encontrado
    if($idtbcoordenador) {
        // Atualiza o campo idtbcoordenador do supervisor selecionado ($matriculaf)
        // para apontar para o coordenador logado ($idtbcoordenador)
        $sql = "UPDATE bdfrota.tbsupervisor SET idtbcoordenador = '$idtbcoordenador' WHERE matricula = '$matriculaf'";
        $resultado = mysql_query($sql) or die (mysql_error());
    } else {
        echo"<script language='javascript' type='text/javascript'>alert('Erro: Coordenador não encontrado!');window.location=\"../transferirfuncionario.php\"</script>";
        exit;
    }

    $acao = 'Transferiu supervisor';
    $flag = '8';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

    echo"<script language='javascript' type='text/javascript'>alert('Transferencia de supervisor concluida!');window.location=\"../transferirfuncionario.php\"</script>";

} elseif(isset($_POST['matricula_coord'])) {
    // TRANSFERÊNCIA DE COORDENADOR PARA GERENTE
    $matriculaf = $_POST['matricula_coord'];

    // Busca o ID do gerente logado na tabela correta (tbgerente)
    $sql = "SELECT idtbgerente FROM bdfrota.tbgerente where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($resultado);
    $idtbgerente = $row[0];

    if($idtbgerente) {
        // Atualiza o campo idtbgerente do coordenador selecionado
        $sql = "UPDATE bdfrota.tbcoord SET idtbgerente = '$idtbgerente' WHERE matricula = '$matriculaf'";
        $resultado = mysql_query($sql) or die (mysql_error());
    } else {
        echo"<script language='javascript' type='text/javascript'>alert('Erro: Gerente não encontrado!');window.location=\"../transferirfuncionario.php\"</script>";
        exit;
    }

    $acao = 'Transferiu coordenador';
    $flag = '9';
    $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, '', '');

    echo"<script language='javascript' type='text/javascript'>alert('Transferencia de coordenador concluida!');window.location=\"../transferirfuncionario.php\"</script>";
}    
?>
