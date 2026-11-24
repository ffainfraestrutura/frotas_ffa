<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje=date('Y-m-d H:i:s');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");


$hoje= date('Y-m-d H:i:s');

$idtbcondutor = $_POST['idtbcondutor'];
$matricula = $_POST['matricula'];
$nome = utf8_decode($_POST['nome']);
$matr_autor = $_POST['matr_autor'];

$placaveic = $_POST['placa'];
if(strpos($placaveic, "-") !== false){
  $placacorreta = str_replace("-", "", $placaveic);
} else {
  $placacorreta = str_replace(" ", "", $placaveic);
}

$cartao = $_POST['cartao'];
$nvdataassoc = $_POST['nvdataassoc'];
$datadess = $_POST['datadess'];
$ativo = $_POST['ativo'];

$sql5="SELECT placaassoc FROM bdfrota.tbcondutor WHERE idtbcondutor='$idtbcondutor'; ";
$resultado5= mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
$placaassoc = $row5['placaassoc'];

//print $matricula."<br>".$nome."<br>".$placacorreta."<br>".$hoje."<br>".$idtbcondutor."<br>".$nvdataassoc."<br>".$datadess."<br>".$ativo."<br> aqui".$placaassoc;

$sql3 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa ='$placacorreta'; ";
$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
$row3 = mysqli_num_rows($resultado3);

if($row3 == 0){
	print "<script language='javascript' type='text/javascript'> alert ('Placa não existente no sistema.');window.location=\"../listagemcondutores.php\"	</script>";
} else {
	$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
	$placabd = $row3[0];

	if(empty($nvdataassoc)){

		if(empty($datadess)){//quando a data de associação não foi atualizada e a data de dissociação não foi adicionada;
			//print "opção 1"; 

			if($placaassoc == $placacorreta){
				$sql1 = "UPDATE bdfrota.tbcondutor SET matricula = '$matricula', nome = '$nome', placaassoc = '$placacorreta', cartaoassoc = '$cartao', ativo = '1', dataassoc='$hoje' WHERE idtbcondutor = '$idtbcondutor' ; ";
				$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

				$sql2 = "UPDATE bdfrota.tbveiculo SET matcond = '$matricula', dtentrega='$hoje', statusvel='1', status='1' WHERE placa = '$placacorreta' ; ";
				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
			  //print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";
			} else {
				$sql1 = "INSERT INTO bdfrota.tbcondutor (matricula, nome, placaassoc, cartaoassoc, ativo, dataassoc) VALUES ('$matricula', '$nome', '$placacorreta', '$cartao', '1', '$hoje'); ";
				$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
			}

			echo "<script language='javascript' type='text/javascript'> alert ('Condutor editado com sucesso.');window.location=\"../listagemcondutores.php\"	</script>";
		} else {//quando a data de associação não foi atualizada e a data de dissociação foi adicionada;
			//print "opção 2";

			if($placaassoc == $placacorreta){
				$sql2 = "UPDATE bdfrota.tbcondutor SET matricula = '$matricula', nome = '$nome', placaassoc = '$placacorreta', datadissoc = '$datadess', cartaoassoc = '$cartao', ativo = '0' WHERE idtbcondutor = '$idtbcondutor' ; ";
				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
			} else {
				$sql2 = "INSERT INTO bdfrota.tbcondutor (matricula, nome, placaassoc, datadissoc, cartaoassoc, ativo) VALUES ('$matricula', '$nome', '$placacorreta', '$datadess', '$cartao', '$ativo'); ";
				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
			}

			echo "<script language='javascript' type='text/javascript'> alert ('Condutor editado com sucesso.');window.location=\"../listagemcondutores.php\"	</script>";
		}
		
	} else {
		if(empty($datadess)){//quando a data de associação foi atualizada e a data de dissociação não foi adicionada;
			//print "opção 3";

			if($placaassoc == $placacorreta){
				$sql3 = "UPDATE bdfrota.tbcondutor SET matricula = '$matricula', nome = '$nome', placaassoc = '$placacorreta', cartaoassoc = '$cartao', dataassoc = '$nvdataassoc', ativo = '1' WHERE idtbcondutor = '".$idtbcondutor."' ; ";
				$resultado1 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
			} else {
				$sql3 = "INSERT INTO bdfrota.tbcondutor (matricula, nome, placaassoc, cartaoassoc, dataassoc, ativo) VALUES ('$matricula', '$nome', '$placacorreta', '$cartao', '$nvdataassoc', '1'); ";
				$resultado1 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
			}
			echo "<script language='javascript' type='text/javascript'> alert ('Condutor editado com sucesso.');window.location=\"../listagemcondutores.php\"	</script>";
		} else {//quando a data de associação foi atualizada e a data de dissociação foi adicionada;
			//print "opção 4";
			
			if($placaassoc == $placacorreta){
				$sql4 = "UPDATE bdfrota.tbcondutor SET matricula = '$matricula', nome = '$nome', placaassoc = '$placacorreta', datadissoc = '$datadess', cartaoassoc = '$cartao', dataassoc = '$nvdataassoc', ativo = 0 WHERE idtbcondutor = '$idtbcondutor' ; ";
				$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
				//print $sql4;
			} else{
				$sql4 = "INSERT INTO bdfrota.tbcondutor (matricula, nome, placaassoc, datadissoc, cartaoassoc, dataassoc, ativo) VALUES ('$matricula', '$nome', '$placacorreta', '$datadess', '$cartao', '$nvdataassoc', '0'); ";
				$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
			  $acao='Editou condutor';
			  $tipo = 'cadastro';
			  $logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);
				//print $sql4;
			}

			echo "<script language='javascript' type='text/javascript'> alert ('Condutor editado com sucesso.');window.location=\"../listagemcondutores.php\"	</script>";
		}	
	}


}



?>