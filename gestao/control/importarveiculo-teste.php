<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');
$hoje1 = str_replace(" ","",$hoje);
$hoje1= str_replace("-","",$hoje1);
$hoje1 = str_replace(":","", $hoje1);

//$tipoposse = utf8_decode($_POST['tipoposse']);
//$locador = utf8_decode($_POST['locador']);

$matr_autor = $_POST['matr_autor'];
$arquivo = $_POST['arquivo'];

include '../vendor/autoload.php';

include_once"../../func/log.php";

include("../../conecta03.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "veiculos";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/veiculos/';

//modelo correto ImportModeloVeiculos.xls

if($_FILES["arquivo"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	$file_array = explode(".", $_FILES["arquivo"]["name"]);
	$file_extension = end($file_array); 

	if(in_array($file_extension, $allowed_extension)){
		$reader = IOFactory::createReader('Xlsx', 'Xls');
		
		$arquivo = "$nome-$hoje1.$file_extension";
		
		$spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);
		$data = $spreadsheet->getActiveSheet()->toArray();

		foreach($data as $row){
			
			if($row[0]<>'Placa'){
				$placa = $row[0];
				$matcond = $row[1];	
				$ccusto = utf8_decode($row[2]);
				$statusvel = $row[3];
				$status = utf8_decode($row[4]);
				$marca = $row[5];
				$modelo = utf8_decode($row[6]);
				$hodometro = $row[7];
				$tipoposse = utf8_decode($row[8]);
				$tipovel = utf8_decode($row[9]);
				$unidade = utf8_decode($row[10]);
				$anofabr = $row[11];
				$anomodelo = $row[12];
				$gps = $row[13];
				$tagpedagio = $row[14];
				$locadora = utf8_decode($row[15]);
				$dtdisplocadora = $row[16];
				$dtdevlocadora = $row[17];
				$valaquisicao = $row[18];
				$dtdesmobilizacao = $row[19];
				$cor = utf8_decode($row[20]);
				$combustivel = $row[21];
				$chassi = $row[22];
				$renavam = $row[23];
				$ncontrato = $row[24];
				$qtdeixos = $row[25];
				$contrato = $row[26];
				$basegestao = utf8_decode($row[27]);
				

				//tratando matricula
				if(strpos($matcond,"'") !== false){
					$matcond = str_replace("'", "", $matcond);
				}

				//tratando hodometro
				if(strpos($hodometro,".") !== false){
					$hodometro = str_replace(".", "", $hodometro);
				}

				if(strpos($hodometro,"'") !== false){
					$hodometro = str_replace("'", "", $hodometro);
				}

				//tratando valor de aquisição
				if(strpos($valaquisicao,"'") !== false){
					$valaquisicao = str_replace("'", "", $valaquisicao);
				}

				if(strpos($valaquisicao,",") !== false){
					$valaquisicao = str_replace(",", ".", $valaquisicao);
				}

				//tratando chassi
				if(strpos($chassi,"'") !== false){
					$chassi = str_replace("'", "", $chassi);
				}

				//tratando renavam
				if(strpos($renavam,"'") !== false){
					$renavam = str_replace("'", "", $renavam);
				}

				//tratando tagpedagio
				if(strpos($tagpedagio,"'") !== false){
					$tagpedagio = str_replace("'", "", $tagpedagio);
				}

				//tratando centro de custo
				if(strpos($ccusto,"'") !== false){
					$ccusto = str_replace("'", "", $ccusto);
				} 

				//tratando numero do contrato
				if(strpos($ncontrato,"'") !== false){
					$ncontrato = str_replace("'", "", $ncontrato);
				}

				//tratando qt de eixos
				if(strpos($qtdeixos,"'") !== false){
					$qtdeixos = str_replace("'", "", $qtdeixos);
				}

				//tratando contrato
				if(strpos($contrato,"'") !== false){
					$contrato = str_replace("'", "", $contrato);
				}

				//tratando datas
				/*$dtdesmobilizacao1 = explode(" ", $dtdesmobilizacao);
                $dtdesmobilizacao2 = explode("/", $dtdesmobilizacao1[0]);
                $dtdesmobilizacao3 = $dtdesmobilizacao2[2]."-".$dtdesmobilizacao2[1]."-".$dtdesmobilizacao2[0]." ".$dtdesmobilizacao1[1];*/
                if($dtdesmobilizacao<>'0000-00-00 00:00:00'){
					$dtdesmobilizacao1 = date_create($dtdesmobilizacao);
    				$dtdesmobilizacao2 = date_format($dtdesmobilizacao1,"Y-m-d H:i:s");
				}else{
					$dtdesmobilizacao2 = $dtdesmobilizacao;
				}

				/*$dtdisplocadora1 = explode(" ", $dtdisplocadora);
				$dtdisplocadora2 = explode("/", $dtdisplocadora1[0]);
				$dtdisplocadora3 = $dtdisplocadora2[2]."-".$dtdisplocadora2[1]."-".$dtdisplocadora2[0]." ".$dtdisplocadora1[1];*/
				if($dtdisplocadora<>'0000-00-00 00:00:00'){
					$dtdisplocadora1 = date_create($dtdisplocadora);
    				$dtdisplocadora2 = date_format($dtdisplocadora1,"Y-m-d H:i:s");
				}else{
					$dtdisplocadora2 = $dtdisplocadora;
				}

				/*$dtdevlocadora1 = explode(" ", $dtdevlocadora);
				$dtdevlocadora2 = explode("/", $dtdevlocadora1[0]);
				$dtdevlocadora3 = $dtdevlocadora2[2]."-".$dtdevlocadora2[1]."-".$dtdevlocadora2[0]." ".$dtdevlocadora1[1];*/
				if($dtdevlocadora<>'0000-00-00 00:00:00'){
					$dtdevlocadora1 = date_create($dtdevlocadora);
    				$dtdevlocadora2 = date_format($dtdevlocadora1,"Y-m-d H:i:s");
    			}else{
    				$dtdevlocadora2 = $dtdevlocadora;
    			}    			

				if($dtdevlocadora == ''){
					$dtdevlocadora3 = " ";
				}

				//tratndo status
				if($status=='ativo' || $status=='ATIVO'){
					$status = 1;
				}elseif($status=='inativo' || $status=='INATIVO'){
					$status = 0;
				}
				
				//buscando id da marca na tbmarcaveic
				/*$sql1="SELECT idtbmarcaveic FROM bdfrota.tbmarcaveic WHERE marca='$marca'; "; 
				$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
				$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
					$marca = $row1['idtbmarcaveic'];*/				
				
				//buscando id do modelo na tbmodeloveic
				$sql2="SELECT idtbmodeloveic,marca FROM bdfrota.tbmodeloveic WHERE modelo='$modelo'; ";
				$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
				$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
					$modelo = $row2['idtbmodeloveic'];
					$marca = $row2['marca'];

				//buscando id do status na tbstatusvel
				$sql7="SELECT idtbastatusvel FROM bdfrota.tbastatusvel WHERE status='$statusvel'; ";
				$resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
				$row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
					$statusvel = $row7['idtbastatusvel'];

				//buscando id da locadora
				$sql10="SELECT idtbfornecedor FROM bdfrota.tbfornecedor WHERE fantasia = '$locadora'";
				$resultado10 = mysqli_query($con, $sql10) or die(mysqli_error($con));
				$row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
					$idlocador = $row10['idtbfornecedor'];

				//buscando tipo veiculo
				$sql10="SELECT idtbatipovel FROM bdfrota.tbatipovel WHERE tipo = '$tipovel'";
				$resultado10 = mysqli_query($con, $sql10) or die(mysqli_error($con));
				$row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
					$tipovel = $row10['idtbatipovel'];

				if($gps == 'sim' || $gps == 'SIM'){
					$gps = 1;
				} elseif($gps == 'não' || $gps == 'NÃO'){
					$gps = 0;
				} elseif($gps == 'nao' || $gps =='NAO'){
					$gps = 0;
				}

				if($tagpedagio == 'sim' || $tagpedagio == 'SIM'){
					$tagpedagio = 1;
				} elseif($tagpedagio == 'não' || $tagpedagio == 'NÃO'){
					$tagpedagio = 0;
				} elseif($tagpedagio == 'nao' || $tagpedagio =='NAO'){
					$tagpedagio = 0;
				} elseif($tagpedagio == "'"){
					$tagpedagio = " ";
				}

				$sql9 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));

				if(mysqli_num_rows($resultado9)<=0){
				 
					$sql = "INSERT INTO bdfrota.tbveiculo(placa, matcond, ccusto, statusvel, status, marca, modelo, hodometro, tipoposse, tipovel, unidade, anofabr, anomodelo, gps, tagpedagio, idlocador, dtdisponivelloc,  dtdevolucaoloc, valaquisicao, dtdesmobilizacao, cor, combustivel, chassi, renavam, ncontloc, qtdeixos, contrato, basegestao, datamovimentacao) VALUES ('$placa', '$matcond', '$ccusto', '$statusvel', '$status', '$marca', '$modelo', '$hodometro', '$tipoposse', '$tipovel', '$unidade', '$anofabr', '$anomodelo', '$gps', '$tagpedagio', '$idlocador', '$dtdisplocadora2', '$dtdevlocadora2', '$valaquisicao', '$dtdesmobilizacao2', '$cor', '$combustivel', '$chassi', '$renavam', '$ncontrato', '$qtdeixos', '$contrato', '$basegestao', '$hoje')";  //break;
					$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
					
				}else{ 
					/*if(!empty($matcond)){

                		$sql = "UPDATE bdfrota.tbveiculo SET matcond = '$matcond', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                	}*/

                	if(!empty($ccusto)){
                		$sql = "UPDATE bdfrota.tbveiculo SET ccusto='$ccusto', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($statusvel)){
                		$sql = "UPDATE bdfrota.tbveiculo SET statusvel='$statusvel',matcond = '$matcond', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($status)){
                		$sql = "UPDATE bdfrota.tbveiculo SET status='$status', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($marca)){
                		$sql = "UPDATE bdfrota.tbveiculo SET marca='$marca', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($modelo)){
                		$sql = "UPDATE bdfrota.tbveiculo SET modelo='$modelo', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($hodometro)){
                		$sql = "UPDATE bdfrota.tbveiculo SET hodometro='$hodometro', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($tipoposse)){
                		$sql = "UPDATE bdfrota.tbveiculo SET tipoposse='$tipoposse', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($tipovel)){
                		$sql = "UPDATE bdfrota.tbveiculo SET tipovel='$tipovel', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($unidade)){
                		$sql = "UPDATE bdfrota.tbveiculo SET unidade='$unidade', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($anofabr)){
                		$sql = "UPDATE bdfrota.tbveiculo SET anofabr='$anofabr', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($anomodelo)){
                		$sql = "UPDATE bdfrota.tbveiculo SET anomodelo='$anomodelo', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($gps)){
                		$sql = "UPDATE bdfrota.tbveiculo SET gps='$gps', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($tagpedagio)){
                		$sql = "UPDATE bdfrota.tbveiculo SET tagpedagio='$tagpedagio', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($idlocador)){
                		$sql = "UPDATE bdfrota.tbveiculo SET idlocador='$idlocador', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($dtdisplocadora)){
                		$sql = "UPDATE bdfrota.tbveiculo SET dtdisponivelloc='$dtdisplocadora2', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($dtdevlocadora)){
                		$sql = "UPDATE bdfrota.tbveiculo SET dtdevolucaoloc='$dtdevlocadora2', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($valaquisicao)){
                		$sql = "UPDATE bdfrota.tbveiculo SET valaquisicao='$valaquisicao', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($dtdesmobilizacao)){
                		$sql = "UPDATE bdfrota.tbveiculo SET dtdesmobilizacao='$dtdesmobilizacao2', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($cor)){
                		$sql = "UPDATE bdfrota.tbveiculo SET cor='$cor', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($combustivel)){
                		$sql = "UPDATE bdfrota.tbveiculo SET combustivel='$combustivel', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($chassi)){
                		$sql = "UPDATE bdfrota.tbveiculo SET chassi='$chassi', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($renavam)){
                		$sql = "UPDATE bdfrota.tbveiculo SET renavam='$renavam', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($ncontrato)){
                		$sql = "UPDATE bdfrota.tbveiculo SET ncontloc='$ncontrato', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($qtdeixos)){
                		$sql = "UPDATE bdfrota.tbveiculo SET qtdeixos='$qtdeixos', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($contrato)){
                		$sql = "UPDATE bdfrota.tbveiculo SET contrato='$contrato', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($basegestao)){
                		$sql = "UPDATE bdfrota.tbveiculo SET basegestao='$basegestao', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}
					
				}
				
			}
		}					
	}	
}

//Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
	// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	/*$acao='Importou excel de veiculos';
	$tipolog = 'cadastro';
	//$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipolog, '');*/

	print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!'); window.close();</script>";

	//window.location=\"../listagemveiculos.php\"

} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta

	print "<script language='javascript' type='text/javascript'>alert('Não foi possível enviar o arquivo, tente novamente.'); window.close();</script>";

	//echo "Não foi possível enviar o arquivo, tente novamente.";
}


?>	