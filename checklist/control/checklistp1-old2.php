<?php 
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include_once"../../conecta.php";

/*$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";
$db="bdfrota";

$con=mysqlI_connect($servidor,$usuario,$senha,$db) or die (mysqli_error());*/

$hoje = date('Y-m-d H:i:s');
$agora = date("H:i:s");

$nome= utf8_decode($_POST['nome']);
$matricula=$_POST['matricula'];
$cpf = $_POST['cpf'];

if(strpos($cpf, ".") !== false){
  $cpf= str_replace(".", "", $cpf);
  
  if(strpos($cpf, "-") !== false){
  	$cpf = str_replace("-", "", $cpf);
  }
} else {
  $cpf = $cpf;
}
	
$cnh = $_POST['cnh'];
$categoriacnh = $_POST['categoriacnh'];
$validadecnh = $_POST['validadecnh'];

/*$cnh2 = $_POST['cnh2'];
$categoriacnh2 = $_POST['categoriacnh2'];
$validadecnh2 = $_POST['validadecnh2'];

if($cnh == ''){
	$cnhfim = $cnh2;
	$categoriacnhfim = $categoriacnh2;
	$validadecnhfim = $validadecnh2;

	$validadecnhfim =  str_replace('/', '-', $validadecnhfim);
	$validadecnhfim = date("Y-m-d", strtotime($validadecnhfim));
} else {
	$cnhfim = $cnh;
	$categoriacnhfim = $categoriacnh;
	$validadecnhfim = $validadecnh;
}*/

$placa = $_POST['placa'];

if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} elseif(strpos($placa, " ") !== false) {
  $placa = str_replace(" ", "", $placa);
} else{
	$placa=$placa;
}

$modelo = $_POST['modelo'];
$anofabricacao = $_POST['anofabricacao'];

$unidade = utf8_decode($_POST['unidade']);
//$basegestao = $unidade;

$centrocusto = utf8_decode($_POST['centrocusto']);
$tipo = utf8_decode($_POST['tipo']);
$vistoriador = utf8_decode($_POST['vistoriador']);

$datavistoria = $_POST['datavistoria'];
$horavistoria = $_POST['horavistoria'];
$estado = $_POST['estado'];
$avaria = $_POST['avaria'];
$hodometro = $_POST['hodometro'];
$niveltanque = $_POST['niveltanque'];
$observacao = utf8_decode($_POST['observacao']);
$documentacao = $_POST['documentacao'];
$matrvistoriador = $_POST['matrvistoriador'];
$statusveic = $_POST['statusveic'];

$teto= $_POST['teto'];
$tetoesp=$_POST['tetoesp'];
$tetoesq=$_POST['tetoesq'];
$tetodir=$_POST['tetodir'];
$frente=$_POST['frente'];
$capo=$_POST['capo'];
$parabrisa=$_POST['parabrisa'];
$farolesq=$_POST['farolesq'];
$faroldir=$_POST['faroldir'];
$parachoque=$_POST['parachoque'];
$grade=$_POST['grade'];
$latesq=$_POST['latesq'];
$paralamaesq=$_POST['paralamaesq'];
$retrovesq=$_POST['retrovesq'];
$cxaresq=$_POST['cxaresq'];
$ptdiantesq=$_POST['ptdiantesq'];
$pttrasesq=$_POST['pttrasesq'];
$latdir=$_POST['latdir'];
$paralamadir= $_POST['paralamadir'];
$retrovdir = $_POST['retrovdir'];
$cxardir = $_POST['cxardir'];
$ptdiantdir = $_POST['ptdiantdir'];
$pttrasdir = $_POST['pttrasdir'];
$traseira =  $_POST['traseira'];
$lantesq = $_POST['lantesq'];
$lantdir = $_POST['lantdir'];
$tmpmala = $_POST['tmpmala'];
$parachoquet = $_POST['parachoquet'];
$itinterno = $_POST['itinterno'];
$painel = $_POST['painel'];
$som = $_POST['som'];
$bancos = $_POST['bancos'];
$ilumint = $_POST['ilumint'];
$tmpbag = $_POST['tmpbag'];
$retrovint = $_POST['retrovint'];
$tapetes = $_POST['tapetes'];
$pneus = $_POST['pneus'];
$step = $_POST['step'];
$marcapneus = $_POST['marcapneus'];
$kitstep = $_POST['kitstep'];
$calotas = $_POST['calotas'];
$bateria = $_POST['bateria'];
$safecar = $_POST['safecar'];
$limpint = $_POST['limpint'];
$limpext = $_POST['limpext'];

$datavistoriaf = $datavistoria." ".$agora;
//$datavistoriaf = $datavistoria." ".$horavistoria;


if($unidade == 'CAMPOS' || $unidade == 'LAGOS' || $unidade=='MACAE' || $unidade=='NITEROI' || $unidade=='NITERÓI' || $unidade == 'NOVA FRIBURGO' || $unidade=='OLARIA' || $unidade == 'PETROPOLIS' || $unidade=='TERESOPOLIS'|| $unidade=='TRES RIOS' || $unidade=='VASSOURAS'){
	$unidadef='RJ';
}elseif($unidade=='CASCAVEL' || $unidade=='CURITIBA' || $unidade=='PONTA GROSSA'){
	$unidadef='PR';
}elseif($unidade=='SAO MATEUS' || $unidade=='SAO MATHEUS'){
	$unidadef = 'SP';
}

if($statusveic == '3' || $statusveic == '23' || $statusveic == '44'){
	$status = '0';
} else {
	$status = '1';
}

if($tipo == '2'){
	$statusveic = '1';
}

if(strpos($validadecnh, "/") !== false){
	$validadecnh1 = explode("/", $validadecnh);
	$validadecnh = $validadecnh1[2]."-".$validadecnh1[1]."-".$validadecnh1[0];
}



/*$sqla1="SELECT idtbstatusveic FROM bdfrota.tbcondutor WHERE matricula='$matricula' ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];*/

//print" $modelo = 'modelo'; $anofabricacao = 'anofabricacao'; $unidade = unidade'; $centrocusto = 'centrocusto'; $tipo 'tipo'; $vistoriador = vistoriador'; $datavistoria = datavistoria; $estado = estado'; $avaria = avaria; $hodometro 'hodometro; $niveltanque = niveltanque; $observacao 'observacao; $documentacao = documentacao; $matrvistoriador = matrvistoriador; $statusveic = statusveic; $teto = teto; $tetoesp= tetoesp; $tetoesq=tetoesq; $tetodir='tetodir'; $frente='frente'; $capo=capo'; $parabrisa='parabrisa'; $farolesq=farolesq; $faroldir='faroldir; $parachoque=parachoque'; $grade= grade; $latesq=latesq; $paralamaesq= paralamaesq; $retrovesq= retrovesq; $cxaresq= cxaresq; $ptdiantesq= ptdiantesq; $pttrasesq= 'pttrasesq'; $latdir= 'latdir'; $paralamadir=  'paralamadir'; $retrovdir =  'retrovdir'; $cxardir =  cxardir'; $ptdiantdir =  'ptdiantdir'; $pttrasdir =  pttrasdir; $traseira =   'traseira'; $lantesq = 'lantesq'; $lantdir =  'lantdir'; $tmpmala =  'tmpmala'; $parachoquet = 'parachoquet'; $itinterno = itinterno'; $painel =  'painel'; $som =  'som'; $bancos = bancos'; $ilumint =  ilumint; $tmpbag =  'tmpbag'; $retrovint =  retrovint; $tapetes =  tapetes; $pneus = pneus; $step = step; $marcapneus =  marcapneus; $kitstep =  kitstep; $calotas =  calotas; $bateria =  'bateria'; $safecar = 'safecar'; $limpint = limpint; $limpext = limpext;";

//pegar idtbcondutor
$sqla="SELECT MAX(idtbcondutor) FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND placaassoc='$placa' ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];

//print $centrocusto;
//print "$cnhfim, $categoriacnhfim, $validadecnhfim,<br>$cnh2, $categoriacnh2, $validadecnh2,<br>$cnh, $categoriacnh, $validadecnh,<br>";

$sql = "INSERT INTO bdfrota.tbvistoria (nome, matricula, cpf, cnh, categoriacnh, validadecnh, placa, modelo, anofabricacao, unidade, centrocusto, tipo, vistoriador, matrvistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao, documentacao, teto, tetoesp, tetoesq, tetodir, frente, capo, parabrisa, farolesq, faroldir, parachoque, grade, latesq, paralamaesq, retrovesq, cxaresq, ptdiantesq, latdir, paralamadir, assinadocond, statusveic, retrovdir, cxardir, ptdiantdir, pttrasdir, traseira, lantesq, lantdir, tmpmala, parachoquet, itinterno, painel, som, bancos, ilumint, tmpbag, retrovint, tapetes, pneus, step, marcapneus, kitstep, calotas, bateria, safecar, limpint, limpext, assinadovist, pttrasesq) VALUES ('$nome', '$matricula', '$cpf', '$cnh', '$categoriacnh', '$validadecnh', '$placa', '$modelo', '$anofabricacao', '$unidade', '$centrocusto', '$tipo', '$vistoriador', '$matrvistoriador', '$datavistoriaf', '$estado', '$avaria', '$hodometro', '$niveltanque', '$observacao', '$documentacao', '$teto', '$tetoesp', '$tetoesq', '$tetodir', '$frente', '$capo', '$parabrisa', '$farolesq', '$faroldir', '$parachoque', '$grade', '$latesq', '$paralamaesq', '$retrovesq', '$cxaresq', '$ptdiantesq', '$latdir', '$paralamadir', '0', '$statusveic', '$retrovdir', '$cxardir', '$ptdiantdir', '$pttrasdir', '$traseira', '$lantesq', '$lantdir', '$tmpmala', '$parachoquet', '$itinterno', '$painel', '$som', '$bancos', '$ilumint', '$tmpbag', '$retrovint', '$tapetes', '$pneus', '$step', '$marcapneus', '$kitstep', '$calotas', '$bateria', '$safecar', '$limpint', '$limpext', '0', '$pttrasesq');";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

$sql2 = "UPDATE bdfrota.tbveiculo SET hodometro = '$hodometro', statusvel='$statusveic', matcond='$matricula', ccusto='$centrocusto', datamovimentacao='$datavistoriaf' WHERE placa ='$placa' ";
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
//print "$sql <br> $sql2";

if($tipo == 2){
	//recebimento/associação do veículo

	$sql2="SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo='1'; ";
    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
    $linhas2 = mysqli_num_rows($resultado2);
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $id = $row2['idtbcondutor'];
	
    if($linhas2<=0){
        $sql3="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
    }else{
        $sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$id'; ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

        $sql5="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
        $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
    }

    //retirando a placa anterior do condutor 
		$sqla="SELECT placa, matcond FROM bdfrota.tbveiculo WHERE matcond='$matricula';";
		$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
		$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
		  $placaantes = $rowa['placa'];

		if(!empty($placaantes)){
			$sql4 = "UPDATE bdfrota.tbveiculo SET statusvel='2', matcond='', datamovimentacao='$datavistoriaf' WHERE placa ='$placaantes' ";
		  $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
		}
	

	
	$sql4 = "UPDATE bdfrota.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datavistoriaf', ccusto='$centrocusto', unidade='$unidadef' WHERE placa ='$placa' ";//datamovimentacao='$hoje',, basegestao='$unidade'
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
	
	/*$sql3="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
	$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

	
	$sql4 = "UPDATE bdfrota.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datavistoriaf', ccusto='$centrocusto', unidade='$unidadef', basegestao='$unidade' WHERE placa ='$placa' ";//datamovimentacao='$hoje',
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

	//print "<BR> $sql3 <br> $sql4";*/
} elseif($tipo == 3){
	//devolução/desvinculação do veículo
	
	$sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
	$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

	$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$datavistoriaf', status='$status', statusvel='$statusveic', matcond='', ccusto='$centrocusto', unidade='$unidadef', basegestao='$unidade' WHERE placa ='$placa' ";
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

	/*print "<br> $sql3 <br> $sql4";*/
 
}

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Realizou vistoria/checklist';
$tipo = 'checklist';
$logresult = enviarlognovo($hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa);

//print "$hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa";

echo "
<script language='javascript' type='text/javascript'> window.location=\"../checklistp2.php?placa=$placa&datavistoria=$datavistoria&matricula=$matricula\"	</script>";
//mysqli_close($con);

?>