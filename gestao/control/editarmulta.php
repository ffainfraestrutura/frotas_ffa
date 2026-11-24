<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log.php";

include("../../conecta2.php");

$hoje = date('Y-m-d H:i:s');

$matr_autor = $_POST['matr_autor'];

$idtbmulta = $_POST['idtbmulta'];
$placa = $_POST['placa'];
$filial = utf8_decode($_POST['filial']);
$ccusto = utf8_decode($_POST['ccusto']);
$fornecedor = utf8_decode($_POST['fornecedor']);
$anotacao = utf8_decode($_POST['anotacao']);
$tipoinfracao = utf8_decode($_POST['tipoinfracao']);
$autoinfracao= $_POST['autoinfracao'];
$datainfracao = $_POST['datainfracao'];
$horainfracao = $_POST['horainfracao'];
$datalimitecond = $_POST['datalimitecond'];
$datalimiteloc = $_POST['datalimiteloc'];
$expedicao = $_POST['expedicao'];
$recebimento = $_POST['recebimento'];
$vencimento = $_POST['vencimento'];
$codigom = $_POST['codigom'];
$pontos = $_POST['pontos'];
$gravidade = $_POST['gravidade'];
$valor= $_POST['valor'];
$valdesconto = $_POST['valdesconto'];
$taxaadm = $_POST['taxaadm'];
$juros = $_POST['juros'];
$valtotal = $_POST['valtotal'];
$orgao = utf8_decode($_POST['orgao']);
$endereco = utf8_decode($_POST['endereco']);
$municipio = utf8_decode($_POST['municipio']);
$matriculac = $_POST['matriculac'];
$nomec = utf8_decode($_POST['nomec']);
$numparcelas = $_POST['numparcelas'];
$valparcelas = $_POST['valparcelas'];
$descontoapartir = $_POST['descontoapartir'];
$descontadocondutor = $_POST['descontadocondutor'];
$tipodesconto = utf8_decode($_POST['tipodesconto']);
$datadesconto = $_POST['datadesconto'];
$realinfrator = $_POST['realinfrator'];
$pessoaindicada = utf8_decode($_POST['pessoaindicada']);
$condassinatura = $_POST['condassinatura'];
$reciboassinado = $_POST['reciboassinado'];
$recusaassinar = $_POST['recusaassinar'];

$status=$_POST['status'];
$etapa = $_POST['etapa'];
$descricaoinfra = utf8_decode($_POST['descricaoinfra']);
$valcomdesc = $_POST['valcomdesc'];
$datarecorg = $_POST['datarecorg'];
$protrecorg = $_POST['protrecorg'];
$comprpag = $_POST['comprpag'];
$pgempresa = $_POST['pgempresa'];
$datapagempr = $_POST['datapagempr'];
$motivonpg = utf8_decode($_POST['motivonpg']);
$numprotocolo = $_POST['numprotocolo'];
$expprotocolo = $_POST['expprotocolo'];
$recprotocolo = $_POST['recprotocolo'];

//tratando placa
if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} else {
  $placa = str_replace(" ", "", $placa);
}

//tratando hora da infração
$horainfracao =$horainfracao.":00";

$diainfracao= $datainfracao." ".$horainfracao;

//tratando valores
$valor = explode(" ", $valor);
$valor = str_replace(".", " ", $valor[1]);
$valor = str_replace(",", ".", $valor);

$valdesconto = explode(" ", $valdesconto);
$valdesconto = str_replace(".", " ", $valdesconto[1]);
$valdesconto = str_replace(",", ".", $valdesconto);


$taxaadm = explode(" ", $taxaadm);
$taxaadm = str_replace(".", " ", $taxaadm[1]);
$taxaadm = str_replace(",", ".", $taxaadm);

$valtotal = explode(" ", $valtotal);
$valtotal = str_replace(".", " ", $valtotal[1]);
$valtotal = str_replace(",", ".", $valtotal);

$valparcelas = explode(" ", $valparcelas);
$valparcelas = str_replace(".", " ", $valparcelas[1]);
$valparcelas = str_replace(",", ".", $valparcelas);

$valcomdesc = explode(" ", $valcomdesc);
$valcomdesc = str_replace(".", " ", $valcomdesc[1]);
$valcomdesc = str_replace(",", ".", $valcomdesc);

/*$sql = "SELECT idtbmulta FROM bdfrota.tbmulta WHERE idtbmulta= '$idtbmulta' ";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row=mysqli_fetch_array($resultado, MYSQLI_ASSOC);
$idtbmulta2 = $row['idtbmulta'];
print"teste $idtbmulta";*/

$sql = "SELECT cpf FROM bdaniel.tbfuncionario WHERE matricula= '$matriculac' ";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
  $cpf = $row['cpf'];

//echo $valtotal;

//echo "placa='$placa', filial='$filial', ccusto='$ccusto', fornecedor='$fornecedor', anotacao='$anotacao', tipoinfracao='$tipoinfracao', autoinfracao='$autoinfracao', datainfracao='$diainfracao', datalimitecond='$datalimitecond', datalimiteloc='$datalimiteloc', expedicao='$expedicao',recebimento='$recebimento', vencimento='$vencimento', codigom='$codigom', pontos='$pontos', gravidade='$gravidade', valor='$valor', valdesconto='$valdesconto', taxaadm='$taxaadm', juros='$juros', valtotal='$valtotal', orgao='$orgao', endereco='$endereco', municipio='$municipio', matriculac='$matriculac', nomec='$nomec', numparcelas='$numparcelas', valparcelas='$valparcelas', descontoapartir='$descontoapartir', descontadocondutor='$descontadocondutor', tipodesconto='$tipodesconto', datadesconto='$datadesconto', realinfrator='$realinfrator', pessoaindicada='$pessoaindicada', condassinatura='$condassinatura', reciboassinado='$reciboassinado', recusaassinar= '$recusaassinar', status = '$status', etapa = '$etapa', descricaoinfra = '$descricaoinfra', valcomdesc = '$valcomdesc', datarecorg = '$datarecorg', protrecorg = '$protrecorg', comprpag = '$comprpag', pgempresa = '$pgempresa', datapagempr = '$datapagempr', motivonpg = '$motivonpg', numprotocolo = '$numprotocolo', expprotocolo = '$expprotocolo', recprotocolo = '$recprotocolo' WHERE idtbmulta= '$idtbmulta <br>  placa='$placa', autoinfra='$autoinfracao', pontuacao='$pontos', gravidade='$gravidade', apCondDV='$datalimitecond', dtinfra='$diainfracao', dtvenc='$vencimento', valor='$valtotal',  nome='$nomec', matricula='$matriculac', cpf='$cpf', locadora='$fornecedor' WHERE placa='$placa' AND dtinfra='$diainfracao' AND autoinfra='$autoinfracao'";

$sql1 = "UPDATE bdfrota.tbmulta SET placa='$placa', filial='$filial', ccusto='$ccusto', fornecedor='$fornecedor', anotacao='$anotacao', tipoinfracao='$tipoinfracao', autoinfracao='$autoinfracao', datainfracao='$diainfracao', datalimitecond='$datalimitecond', datalimiteloc='$datalimiteloc', expedicao='$expedicao',recebimento='$recebimento', vencimento='$vencimento', codigom='$codigom', pontos='$pontos', gravidade='$gravidade', valor='$valor', valdesconto='$valdesconto', taxaadm='$taxaadm', juros='$juros', valtotal='$valtotal', orgao='$orgao', endereco='$endereco', municipio='$municipio', matriculac='$matriculac', nomec='$nomec', numparcelas='$numparcelas', valparcelas='$valparcelas', descontoapartir='$descontoapartir', descontadocondutor='$descontadocondutor', tipodesconto='$tipodesconto', datadesconto='$datadesconto', realinfrator='$realinfrator', pessoaindicada='$pessoaindicada', condassinatura='$condassinatura', reciboassinado='$reciboassinado', recusaassinar= '$recusaassinar', status = '$status', etapa = '$etapa', descricaoinfra = '$descricaoinfra', valcomdesc = '$valcomdesc', datarecorg = '$datarecorg', protrecorg = '$protrecorg', comprpag = '$comprpag', pgempresa = '$pgempresa', datapagempr = '$datapagempr', motivonpg = '$motivonpg', numprotocolo = '$numprotocolo', expprotocolo = '$expprotocolo', recprotocolo = '$recprotocolo' WHERE idtbmulta= '$idtbmulta'";
$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

$sql2 = "UPDATE bdfrota.tbmovidatramite SET placa='$placa', autoinfra='$autoinfracao', pontuacao='$pontos', gravidade='$gravidade', apCondDV='$datalimitecond', dtinfra='$diainfracao', dtvenc='$vencimento', valor='$valtotal',  nome='$nomec', matricula='$matriculac', cpf='$cpf', locadora='$fornecedor' WHERE idmulta='$idtbmulta' ";
$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Editou multa';
$tipo = 'cadastro';
$logresult = enviarlognovo($hoje, $acao, $matriculac, $matr_autor, $tipo, $placa);
//echo "$hoje, $acao, $matriculac, $matr_autor, $tipo, $placa";

echo "<script language='javascript' type='text/javascript'> alert ('Multa editada com sucesso.');window.location=\"../listagemmultas.php\"	</script>";
?>