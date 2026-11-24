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

$sqla = "SELECT codigomulta FROM bdfrota.tbacodmulta WHERE codigomulta='$codigo'; ";
$resultadoa = mysqli_query($conexao, $sqla) or die(mysqli_error($conexao));
$linhas = mysqli_num_rows($resultadoa);

if($linhas<=0){

	$sql1 = "INSERT INTO bdfrota.tbacodmulta (codigomulta, descricao, pontos, valor, infrator, gravidade, valorcdesc, valorsdesc, valorjuros) VALUES ('$codigo', '$descricao', '$pontos','$valor','$infrator', '$gravidade', '$valorcdesc', '$valorsdesc', '$valorjuros');";

	$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

	//print $sql1;

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	/*$acao='Editou codigo multa';
	$tipo = 'cadastro';
	$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');*/
	//print "$hoje, $acao, '', $matr_autor, $tipo, $placa";

	echo "<script language='javascript' type='text/javascript'> alert ('Código de multa cadastrado com sucesso.');window.location=\"../listagemcodmulta.php\"	</script>";
} else {
	echo "<script language='javascript' type='text/javascript'> alert ('Código de multa já cadastrado.');window.location=\"../listagemcodmulta.php\"	</script>";

	//print 'Código de multa já cadastrado.';
}

?>