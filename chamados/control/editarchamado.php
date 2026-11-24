<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje= date('Y-m-d H:i:s');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");
include_once"../../func/log.php";
include("../../conecta.php");

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo=$_SESSION['tipo'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

$idtbchamados=$_POST['idtbchamados'];
$resposta= $_POST['resposta'];
$status = $_POST['status'];
$placa = $_POST['placa'];

//print $idtbchamados."<br>".$resposta."<br>".$status."<br>".$hoje;

if($status == 2){
	$sql = "UPDATE bdfrota.tbchamados SET status = '".$status."', data_fech='".$hoje."', resposta='".$resposta."' WHERE idtbchamados='".$idtbchamados."';";
	$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
} else{
	$sql = "UPDATE bdfrota.tbchamados SET status = '".$status."', resposta='".$resposta."' WHERE idtbchamados='".$idtbchamados."';";
	$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
}

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Editou chamado';
$tipo = 'chamado';
$logresult = enviarlognovo($hoje, $acao,$matricula, '', $tipo, $placa);

echo "<script language='javascript' type='text/javascript'> alert ('Chamado editado com sucesso.');window.location=\"../listagemchamados.php\"	</script>";

mysqli_close($con);

?>