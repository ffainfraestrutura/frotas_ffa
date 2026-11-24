<?php

@include_once 'conecta.php';

class Log {
    private $con; // Atributo para armazenar a conexão com o banco de dados

    // Construtor da classe
    public function __construct($con) {
        include("conecta.php");
        $this->conexao = $con;

    }

	/*function enviarlog($datahora, $acao, $flag, $matricula, $mat_autor, $valor_ant, $valor_novo) {

		$sqlog = "INSERT INTO bdfrota.tblog (data_e_hora, acao, flag, matricula, mat_autor, valor_ant, valor_novo) values ('$datahora', '$acao', '$flag', '$matricula', '$mat_autor', '$valor_ant', '$valor_novo')";  
	    
	    $resultadolog = mysqli_query($con, $sqlog) or die(mysqli_error($con));
		
		//$resultadolog = mysql_query($sqlog) or die (mysql_error());
		
		return $resultadolog;

	}*/

	function enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa) {

		$sqlog = "INSERT INTO bdfrota.tblog (data_e_hora, acao, matricula, mat_autor, tipo, placa) values ('$datahora', '$acao', '$matricula', '$mat_autor', '$tipo', '$placa')";

		//`bdfrota-teste`
	    
	    //$resultadolog = mysqli_query($con, $sqlog) or die(mysqli_error($con));

		//$resultadolog = mysql_query($sqlog) or die (mysql_error());

		$resultado = mysqli_query($this->conexao, $sqlog) or die(mysqli_error($this->conexao));
		
		return $resultadolog;
		

	}
}



?>