<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');

include("../../conecta2.php");

$sql= "SELECT nome, matricula, placa, datavistoria, tipo FROM bdfrota.tbvistoria ORDER BY datavistoria;";
$resultado= mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
	$nome = utf8_decode($row[0]);
	$matriculacond = $row[1];
	$placa = utf8_decode($row[2]);
	$datavistoria = $row[3];
	$tipo = $row[4];
	

	if($tipo == 2){
		//$tipo == 2 - RECEBIMENTO VEÍCULO PELO CONDUTOR

		$dataassoc = $datavistoria;

		//print" nome $nome | matricula $matriculacond | placa $placa | datavistoria $datavistoria | tipo $tipo  <hr>";
		if($nome <> ''){
			$sql1 = "INSERT INTO bdfrota.tbcondutor (nome, matricula, placaassoc, dataassoc) VALUES ('$nome', '$matriculacond', '$placa', '$dataassoc');";
			$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
			print $sql1."<hr>";
		}
		
	} elseif($tipo == 3){
		if($nome <> ''){
			$sql1 = "SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND matricula='$matriculacond'; ";
			$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

			//print $sql1."<hr>";

			if(mysqli_num_rows($resultado1)<=0){
				$datadissoc = $datavistoria;
				$sql2 = "INSERT INTO bdfrota.tbcondutor (nome, matricula, placaassoc, datadissoc) VALUES ('$nome', '$matriculacond', '$placa', '$datadissoc');";
				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				print $sql2."<hr>";
			} else{
				$datadissoc = $datavistoria;
				$sql2 = "UPDATE bdfrota.tbcondutor SET datadissoc= '$datadissoc' WHERE placaassoc='$placa' AND matricula='$matriculacond';";
				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				print $sql2."<hr>";
			}
		}
	}

	//print" nome $nome | matricula $matriculacond | placa $placa | dataassociacao $dataassoc | tipo $tipo  <hr>";
}

?>	