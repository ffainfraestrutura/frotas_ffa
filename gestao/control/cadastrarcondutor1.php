<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje=date('Y-m-d H:i:s');
header('Content-type: text/html; charset=ISO-8859-1');

include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');

$matricula = $_POST['matricula'];
$nome = utf8_decode($_POST['nome']);
$placaveic = $_POST['placa'];

if(strpos($placaveic, "-") !== false){
  $placacorreta = str_replace("-", "", $placaveic);
} else {
  $placacorreta = str_replace(" ", "", $placaveic);
}
$cartao = $_POST['cartao'];
$data = $_POST['data'];
$matr_autor = $_POST['matr_autor'];


//print $matricula."<br>".$nome."<br>".$placacorreta."<br>".$hoje."<br>".$cartao."<br>".$data;

//conferir se a placa existe no bd
$sql3 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa ='$placacorreta'; ";
$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
$row3 = mysqli_num_rows($resultado3);

if($row3 <= 0){
	print "<script language='javascript' type='text/javascript'> alert ('Placa não existente no sistema.');window.location=\"../cadastrocondutor.php\"	</script>";
} else {

	//selecionar se a matricula ja existe na tbcnh
	$sql5="SELECT matricula from bdfrota.tbcnh WHERE matricula='$matricula';";
	$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
	$row5 = mysqli_num_rows($resultado5);

	if($row5<=0){
		print "<script language='javascript' type='text/javascript'> alert ('Condutor com CNH ainda não cadastrada. Por favor, cadastre a CNH.');window.location=\"../listagemcondutores.php\"	</script>";
	} else {
		//verifico se a ultima entrada está ativa
		//$sql4="SELECT * FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND datadissoc='0000-00-00 00:00:00'; ";
		$sql4 = "SELECT * FROM bdfrota.tbcondutor WHERE matricula = '$matricula' AND ativo=1;";

		$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
		$row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
			$id = $row4['idtbcondutor'];
			$datadissoc = $row4['datadissoc'];
      $ativo = $row4['ativo'];
		$linha4 = mysqli_num_rows($resultado4);


		//se id esta vazio, a ultima entrada não está ativa $id == null || $id == ''
		if($linha4<=0){			
	  	//print "inserir nova linha pro Condutor";
	  	$sql1 = "INSERT INTO bdfrota.tbcondutor (matricula, nome, ativo, placaassoc, cartaoassoc, dataassoc) VALUES ('$matricula', '$nome', 1, '$placacorreta', '$cartao', '$hoje')";
			$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

			$sql4 = "UPDATE bdfrota.tbveiculo SET matcond= '$matricula' WHERE placa ='$placacorreta' ";
			$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));

			//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
		  $acao='Cadastrou condutor';
		  $tipo = 'cadastro';
		  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);

		}else{
			print "<script language='javascript' type='text/javascript'> alert ('Condutor já vinculado a uma placa.');window.location=\"../listagemcondutores.php\"	</script>";
		}

	}	
}
//mysqli_close($conexao);

?>