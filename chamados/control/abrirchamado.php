<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje= date('Y-m-d H:i:s');

//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");

include_once"../../func/log3.php";
include("../../conecta03.php");

//print "<a href='../conecta.php'> caminho</a>;";

$nome= $_POST['nome'];
$matricula= $_POST['matricula'];
$telefone = $_POST['telefone'];
$placa = $_POST['placa'];
$hodometro = $_POST['hodometro'];
$ccusto = $_POST['ccusto'];
$tipochamado = $_POST['tipochamado'];
$tiposinistro = $_POST['tiposinistro'];
$corientesini = $_POST['corientesini'];
$manuprev = $_POST['manuprev'];
$prevcor = $_POST['prevcor'];
$manucor = $_POST['manucor'];

$enderecog = $_POST['enderecog'];


//print $nome."<br>".$matricula."<br>".$telefone."<br>".$placa."<br>".$hodometro."<br>".$ccusto ."<br>".$tipochamado."<br>".$tiposinistro."<br>".$corientesini."<br>".$manuprev."<br>".$prevcor ."<br>".$manucor;

//idtbchamados, matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente


$sql1 = "INSERT INTO bdfrota.tbchamados (matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente, data_abert, enderecog) VALUES 
	('$matricula', '$nome', '$telefone', '$ccusto', '$placa', '$hodometro', '$tipochamado', '$tiposinistro', '$manuprev', '$prevcor', '$manucor', 1, '$corientesini', '$hoje', '$enderecog')";
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$log = new Log($con);
$acao='Abriu chamado';
$tipo = 'chamado';
$logresult = $log->enviarlognovo($hoje, $acao, '', $matricula, $tipo, $placa);

echo "<script language='javascript' type='text/javascript'> alert ('Chamado cadastrado com sucesso.'); window.close();</script>";
//window.location=\"../listagemchamadoscliente.php\"


mysqli_close($con);

?>