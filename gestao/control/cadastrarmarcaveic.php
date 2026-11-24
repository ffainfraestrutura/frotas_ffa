<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log3.php";
include("../../conecta03.php");


$hoje= date('Y-m-d H:i:s');

$nvmarca = strtoupper($_POST['nvmarca']);
$matr_autor = $_POST['matr_autor'];


//echo $nvmarca;

$sql2 = "SELECT * FROM bdfrota.tbmarcaveic WHERE marca = '".$nvmarca."';";
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
$row2 = mysqli_num_rows($resultado2);

if($row2 == 0){
	$sql1 = "INSERT INTO bdfrota.tbmarcaveic (marca) VALUES ('".$nvmarca."')";
	$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

	$log = new Log($con);
	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	$acao='Cadastrou nova marca de veiculo';
	$tipo = 'cadastro';
	$logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
	//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert ('Marca cadastrada com sucesso.');window.close();	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert('Marca já cadastrada.');window.close();</script>";	
}

//window.location=\"../cadastroveiculo.php\"
/*mysqli_close($con);*/

?>