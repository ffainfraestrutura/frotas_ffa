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

$nome= $_POST['nome'];
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


$sql = "SELECT numcnh FROM bdfrota.tbcnh WHERE matricula = '$matricula'";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
$numlin = mysqli_num_rows($resultado);
if ($numlin == 0) {
	$sql = "INSERT INTO bdfrota.tbcnh (numcnh,validade,categoria) VALUES ('$cnh','$validadecnh','$categoriacnh')";
	$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
	$numlin = mysqli_num_rows($resultado);
}
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



$centrocusto = $_POST['centrocusto'];
$tipo = $_POST['tipo'];
$vistoriador = $_POST['vistoriador'];

$datavistoria = $_POST['datavistoria'];	
$horavistoria = $_POST['horavistoria'];
$estado = $_POST['estado'];
$avaria = $_POST['avaria'];
$hodometro = $_POST['hodometro'];
$niveltanque = $_POST['niveltanque'];
$observacao = $_POST['observacao'];
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



$sql = "INSERT INTO bdfrota.tbvistoria (nome, matricula, cpf, cnh, categoriacnh, validadecnh, placa, modelo, anofabricacao, centrocusto, tipo, vistoriador, matrvistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao, documentacao, teto, tetoesp, tetoesq, tetodir, frente, capo, parabrisa, farolesq, faroldir, parachoque, grade, latesq, paralamaesq, retrovesq, cxaresq, ptdiantesq, latdir, paralamadir, assinadocond, statusveic, retrovdir, cxardir, ptdiantdir, pttrasdir, traseira, lantesq, lantdir, tmpmala, parachoquet, itinterno, painel, som, bancos, ilumint, tmpbag, retrovint, tapetes, pneus, step, marcapneus, kitstep, calotas, bateria, safecar, limpint, limpext, assinadovist, pttrasesq) VALUES ('$nome', '$matricula', '$cpf', '$cnh', '$categoriacnh', '$validadecnh', '$placa', '$modelo', '$anofabricacao', '$centrocusto', '$tipo', '$vistoriador', '$matrvistoriador', '$datavistoriaf', '$estado', '$avaria', '$hodometro', '$niveltanque', '$observacao', '$documentacao', '$teto', '$tetoesp', '$tetoesq', '$tetodir', '$frente', '$capo', '$parabrisa', '$farolesq', '$faroldir', '$parachoque', '$grade', '$latesq', '$paralamaesq', '$retrovesq', '$cxaresq', '$ptdiantesq', '$latdir', '$paralamadir', '0', '$statusveic', '$retrovdir', '$cxardir', '$ptdiantdir', '$pttrasdir', '$traseira', '$lantesq', '$lantdir', '$tmpmala', '$parachoquet', '$itinterno', '$painel', '$som', '$bancos', '$ilumint', '$tmpbag', '$retrovint', '$tapetes', '$pneus', '$step', '$marcapneus', '$kitstep', '$calotas', '$bateria', '$safecar', '$limpint', '$limpext', '0', '$pttrasesq');";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
$id_inserido = mysqli_insert_id($con);

$sql = "INSERT INTO bdfrota.tbvistoriafotos (placa, datavistoriaf, idtbvistoria) values ('$placa', '$datavistoriaf', '$id_inserido')";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
/*$sql2 = "UPDATE bdfrota.tbveiculo SET hodometro = '$hodometro', statusvel='$statusveic', matcond='$matricula', ccusto='$centrocusto', datamovimentacao='$datavistoriaf' WHERE placa ='$placa' ";
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));*/
//print "$sql <br> $sql2";

//maximo idtbcondutor do condutor ativo
/*$sqla="SELECT MAX(idtbcondutor) FROM bdfrota.tbcondutor WHERE matricula='$matricula' /*AND placaassoc='$placa' AND ativo=1; ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];*/

	if($tipo == 2){
		//recebimento/associação do veículo
		
		//atualizando tbveiculo
		$sql3 = "UPDATE bdfrota.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datavistoriaf', ccusto='$centrocusto' WHERE placa ='$placa' ";//datamovimentacao='$hoje',
		$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
		
		//pegar maximo registro do condutor como ativo
		$sqla="SELECT idtbcondutor, placaassoc FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND ativo=1 ORDER BY idtbcondutor DESC";
		$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
		$linhasa = mysqli_num_rows($resultadoa);
	
		//pegar maximo registro da placa como ativo
		$sqlb="SELECT idtbcondutor, placaassoc, matricula FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo=1 ORDER BY idtbcondutor DESC";
		$resultadob= mysqli_query($con, $sqlb) or die(mysqli_error($con));
		$linhasb = mysqli_num_rows($resultadob);   
	   
		if($linhasa>0){// "se o registro ativo existe, conferir placa<br>";
			$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
				$idtbcondutor = $rowa['idtbcondutor'];
				$placaassoc1 = $rowa['placaassoc'];
				
				
			//se a placa for diferente, tenho que desvincular e vincular na nova
			if($placaassoc1 != $placa){
				//desvincula a antiga
				$sql4="UPDATE bdfrota.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
				$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

				$sql4="UPDATE bdfrota.tbveiculo SET datamovimentacao='$datavistoriaf', matcond = '', statusvel='2' WHERE placa='$placaassoc1'; ";
				$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
				
				//print "$idtbcondutor;$placaassoc1; $placa<br> ";
				
				//vincula a nova
				$sql4="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO'); ";
				$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));            
			}
	
		}else{// "se não há registro ativo, basta incluir novo registro";
			
			$sql5="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO'); ";
			$resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
	
		}
	
		if($linhasb>0){//print "se a placa já está ativa, conferir se é com o mesmo condutor<br>";
			$rowb = mysqli_fetch_array($resultadob, MYSQLI_BOTH);
				$idtbcondutorpl = $rowb['idtbcondutor'];
				$mat_assoc1 = $rowb['matricula'];
			
			//se a placa for diferente, tenho que desvincular e vincular na nova
			if($mat_assoc1 != $matricula){
				//print "desvincula a antiga";
				$sql4="UPDATE bdfrota.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutorpl'; ";
				$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
				
			}
	
		}else{//"placa nao esta ativa,";
	
		}
	
	
		//print "<BR> $sql3 <br> $sql4";
	} elseif($tipo == 3){
		//devolução/desvinculação do veículo

		//pegar maximo registro do condutor como ativo
		$sqla="SELECT idtbcondutor, placaassoc FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND ativo=1 ORDER BY idtbcondutor DESC";
		$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
		$linhasa = mysqli_num_rows($resultadoa);

		if($linhasa>0){// "se o registro ativo existe";
			$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
				$idtbcondutor = $rowa['idtbcondutor'];
		}
	
		$sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$datavistoriaf', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
		$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

		//ccusto='$centrocusto',
		
		$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$datavistoriaf', status='$status', statusvel='$statusveic', matcond='', hodometro = '$hodometro' WHERE placa ='$placa' ";
		$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
	}

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Realizou vistoria/checklist comum-'.$tipo;
$tipo = 'checklist';
$logresult = enviarlognovo($hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa);

echo "
<script language='javascript' type='text/javascript'> window.location=\"../checklistp2.php?placa=$placa&datavistoria=$datavistoria&matricula=$matricula&idinserido=$id_inserido\"	</script>";

//mysqli_close($con);*/

?>