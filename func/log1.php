<?php

include("conecta.php");

if($con){
  print "tudo ok";
}

function enviarlog($datahora, $acao, $flag, $matricula, $mat_autor, $valor_ant, $valor_novo) {


	$sqlog = "INSERT INTO bdfrota.tblog (data_e_hora, acao, flag, matricula, mat_autor, valor_ant, valor_novo) values ('$datahora', '$acao', '$flag', '$matricula', '$mat_autor', '$valor_ant', '$valor_novo')";  
	$resultadolog = mysqli_query($con, $sqlog) or die(mysqli_error($con));
	
	return $resultadolog;

}

?>