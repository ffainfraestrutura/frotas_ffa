<?php

@include_once '../conecta.php';

function enviarlog($datahora, $acao, $flag, $matricula, $mat_autor, $valor_ant, $valor_novo) {

	$sqlog = "INSERT INTO bdfrota.tblog (data_e_hora, acao, flag, matricula, mat_autor, valor_ant, valor_novo) values ('$datahora', '$acao', '$flag', '$matricula', '$mat_autor', '$valor_ant', '$valor_novo')";  
	$resultadolog = mysql_query($sqlog) or die (mysql_error());
	
	return $resultadolog;

}

function enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa) {

	$sqlog = "INSERT INTO bdfrota.tblog (data_e_hora, acao, matricula, mat_autor, tipo, placa) values ('$datahora', '$acao', '$matricula', '$mat_autor', '$tipo', '$placa')";  
	$resultadolog = mysql_query($sqlog) or die (mysql_error());
	
	return $resultadolog;

}

?>