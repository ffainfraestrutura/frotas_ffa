<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../../conecta.php");
include_once"../../func/log.php";
include("../../conecta2.php");


$hoje = date('Y-m-d H:i:s');

$idtbacodmulta = $_POST['idtbacodmulta'];
$matr_autor = $_POST['matr_autor'];

$codigo = $_POST['codigo'];
$descricao = utf8_decode($_POST['descricao']);
$pontos = $_POST['pontos'];
$valor = $_POST['valor'];
$infrator = utf8_decode($_POST['infrator']);
$gravidade = utf8_decode($_POST['gravidade']);
$valorcdesc = $_POST['valorcdesc'];
$valorsdesc = $_POST['valorsdesc'];
$valorjuros = $_POST['valorjuros'];

//tratando valores
$valor = explode(" ", $valor);
$valor = str_replace(".", " ", $valor[1]);
$valor = str_replace(",", ".", $valor);

$valorcdesc = explode(" ", $valorcdesc);
$valorcdesc = str_replace(".", " ", $valorcdesc[1]);
$valorcdesc = str_replace(",", ".", $valorcdesc);

$valorsdesc = explode(" ", $valorsdesc);
$valorsdesc = str_replace(".", " ", $valorsdesc[1]);
$valorsdesc = str_replace(",", ".", $valorsdesc);

$valorjuros = explode(" ", $valorjuros);
$valorjuros = str_replace(".", " ", $valorjuros[1]);
$valorjuros = str_replace(",", ".", $valorjuros);

$sql1 = "UPDATE bdfrota.tbacodmulta SET codigomulta = '$codigo', descricao = '$descricao', pontos = '$pontos', valor='$valor', infrator='$infrator', gravidade = '$gravidade', valorcdesc = '$valorcdesc', valorsdesc = '$valorsdesc', valorjuros = '$valorjuros' WHERE idtbacodmulta = '$idtbacodmulta';";

$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

//print $sql1;

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
/*$acao='Editou codigo multa';
$tipo = 'cadastro';
$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');*/
//print "$hoje, $acao, '', $matr_autor, $tipo, $placa";

echo "<script language='javascript' type='text/javascript'> alert ('Cadastro de código de multa editado com sucesso.');window.location=\"../listagemcodmulta.php\"	</script>";

?>