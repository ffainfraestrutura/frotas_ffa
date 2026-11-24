<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log3.php";
include("../../conecta03.php");

$hoje= date('Y-m-d H:i:s');

$marca = strtoupper($_POST['marca']);
$modelo = strtoupper($_POST['modelo']);
$matr_autor = $_POST['matr_autor'];

echo $marca, $modelo."<br>";

$sql2 = "SELECT * FROM bdfrota.tbmodeloveic WHERE modelo = '$modelo';";
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
$row2 = mysqli_num_rows($resultado2);

//print $row2;
if($row2== 0){
	$sql1 = "INSERT INTO bdfrota.tbmodeloveic (modelo, marca) VALUES ('$modelo', '$marca');";
	$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
//print $sql1;
	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	$log = new Log($con);
	$acao='Cadastrou modelo de veiculo';
	$tipo = 'cadastro';
	$logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
	//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert ('Modelo cadastrado com sucesso.');window.close(); 	</script>";
} else {

/*print $idtbmarcaveic;
print "ok";*/
	echo "<script language='javascript' type='text/javascript'> alert('Modelo já cadastrado.'); window.close();	</script>";	
}

//window.location=\"../cadastroveiculo.php\"
/*mysqli_close($con);*/

?>