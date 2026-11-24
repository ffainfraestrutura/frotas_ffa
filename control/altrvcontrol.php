<?php
session_start();
include("../conecta.php");

$idtbrv = $_POST['idtbrv'];
$matricula = $_POST['matricula'];
$nome = $_POST['nome'];
$dtrab = $_POST['dtrab'];
$absenteismo = $_POST['absenteismo'];
$produtividade = $_POST['produtividade'];
$elegibilidade = $_POST['elegibilidade'];
$elegibilidade = $_POST['elegibilidade'];
$efetividade = $_POST['efetividade'];
$taxa = $_POST['taxa'];
$FTTC = $_POST['FTTC'];
$FTTH = $_POST['FTTH'];
$RNC = $_POST['RNC'];
$TREA = $_POST['TREA'];
$ttbase = $_POST['ttbase'];
$tx_repetida = $_POST['tx_repetida'];
$gerente = $_POST['gerente'];
$coordenador = $_POST['coordenador'];
$supervisor = $_POST['supervisor'];
$valor = $_POST['valor'];
$tipobd = $_POST['tipobd'];

if ($idtbrv == '') {
echo "<script>alert('Erro ao alterar!');</script>";
echo "<script>window.location=\"../alterarrv.php\"</script>";
} else {

  
  $sql = "UPDATE bdindicador.tbrv set matricula = '$matricula', nome = '$nome', dtrab = '$dtrab', absenteismo = '$absenteismo', produtividade = '$produtividade', elegibilidade = '$elegibilidade', elegibilidade = '$elegibilidade', efetividade = '$efetividade', taxa = '$taxa', FTTC = '$FTTC', FTTH = '$FTTH', RNC = '$RNC', TREA = '$TREA', ttbase = '$ttbase', tx_repetida = '$tx_repetida', gerente = '$gerente', coordenador = '$coordenador', supervisor = '$supervisor', valor_total = '$valor', tipo = '$tipobd' where idtbrv = $idtbrv";
  $resultado = mysql_query($sql) or die (mysql_error());

  echo "<script>alert('Alterado com sucesso');</script>";
  echo "<script>window.location=\"../alterarrv.php\"</script>";
}



      
?>