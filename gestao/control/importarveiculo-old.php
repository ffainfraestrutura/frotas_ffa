<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');  

//$tipoposse = utf8_decode($_POST['tipoposse']);
//$locador = utf8_decode($_POST['locador']);
$matr_autor = $_POST['matr_autor'];
$arquivo = $_POST['arquivo'];

include '../vendor/autoload.php';

include_once"../../func/log.php";
include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "veiculos";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/';

//print $tipoposse."<br>".$locador."<br>".$arquivo."<br>".$dirfinal;

if($_FILES["arquivo"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	$file_array = explode(".", $_FILES["arquivo"]["name"]);
	$file_extension = end($file_array); 

	if(in_array($file_extension, $allowed_extension)){
		$reader = IOFactory::createReader('Xlsx', 'xls');
		$arquivo = "$nome-$hoje.$file_extension";
		$spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);
		$data = $spreadsheet->getActiveSheet()->toArray();

		foreach($data as $row){
			
			if($row[0]<>'Placa'){
				$placa = $row[0];				
				$status = utf8_decode($row[1]);
				$modelo = utf8_decode($row[2]);
				$versao = utf8_decode($row[3]);
				$cor = utf8_decode($row[4]);
				$marca = $row[5];
				$hodometro = $row[6];
				$tipoposse = utf8_decode($row[7]);
				$statusvel = $row[8];
				$datamovimentacao = utf8_decode($row[9]);
				
				$oficina = utf8_decode($row[10]);
				$matcond = $row[11];
				$dtentrcond = $row[12];
				$dtdevcond = utf8_decode($row[13]);
				$tipo = utf8_decode($row[14]);
				$tipovel = utf8_decode($row[15]);
				$aplicacao = utf8_decode($row[16]);				
				$categoria = $row[17];
				$situacao = $row[18];
				$unidade = utf8_decode($row[19]);
				
				$ccusto = utf8_decode($row[20]);
				$anofabr = $row[21];
				$anomodelo = $row[22];
				$combustivel = $row[23];
				$motorizacao = $row[24];
				$chassi = $row[25];
				$nportas = $row[26];
				$renavam = $row[27];
				$uf = $row[28];
				$crvl = utf8_decode($row[29]);
				
				$airbag = $row[30];
				$zerokm = $row[31];
				$gps = $row[32];
				$gpsemp = $row[33];
				$gnv = $row[34];
				$rack = $row[35];
				$tagpedagio = $row[36];
				$idlocador = $row[37];
				$ncontrato = $row[38];
				$dtdisplocadora = $row[39];
				
				$dtdevolucao = $row[40];
				$dtdevlocadora = $row[41];
				$valaquisicao = $row[42];				
				$nummotor = $row[43];
				$qtdeixos = $row[44];
				$qtdpneus = utf8_decode($row[45]);
				$qtdestepes = $row[46];
				$tanque = $row[47];
				$blindagem = $row[48];
				
				if($status == ''){
					$status = '1';
				}
				//tratando matricula
				if(strpos($matcond,"'") !== false){
					$matcond = substr($matcond,1);
				}

				//tratando renavam
				if(strpos($renavam,"'") !== false){
					$renavam = substr($renavam,1);
				}

				//tratando tagpedagio
				if(strpos($tagpedagio,"'") !== false){
					$tagpedagio = substr($tagpedagio,1);
				}

				//tratando tag
				if(strpos($ccusto,"'") !== false){
					$ccusto = substr($ccusto,1);
				}

				//tratando valor
				if(strpos($valaquisicao,",") !== false){
					$valaquisicao = str_replace(",", ".", $valaquisicao);
				}

				//tratando hodometro
				if(strpos($hodometro,".") !== false){
					$hodometro = str_replace(".", "", $hodometro);
				}

				//tratando datas
				if($datamovimentacao != ''){


				$datamovimentacao1 = explode(" ", $datamovimentacao);
				$datamovimentacao2 = explode("/", $datamovimentacao1[0]);
				$datamovimentacao3 = $datamovimentacao2[2]."-".$datamovimentacao2[1]."-".$datamovimentacao2[0]." ".$datamovimentacao1[1];
			} else{
				$datamovimentacao3 = '0000-00-00 00:00:00';
			}

			if($dtentrcond != ''){


				$dtentrcond1 = explode(" ", $dtentrcond);
				$dtentrcond2 = explode("/", $dtentrcond1[0]);
				$dtentrcond3 = $dtentrcond2[2]."-".$dtentrcond2[1]."-".$dtentrcond2[0]." ".$dtentrcond1[1];
			}else{
				$dtentrcond3 ='0000-00-00 00:00:00';
			}

			if($dtdevcond != ''){
				$dtdevcond1 = explode(" ", $dtdevcond);
				$dtdevcond2 = explode("/", $dtdevcond1[0]);
				$dtdevcond3 = $dtdevcond2[2]."-".$dtdevcond2[1]."-".$dtdevcond2[0]." ".$dtdevcond1[1];
			}else{
				$dtdevcond3 ='0000-00-00 00:00:00';
			}

			
			if($dtdevolucao != ''){
				$dtdevolucao1 = explode(" ", $dtdevolucao);
                $dtdevolucao2 = explode("/", $dtdevolucao1[0]);
                $dtdevolucao3 = $dtdevolucao2[2]."-".$dtdevolucao2[1]."-".$dtdevolucao2[0]." ".$dtdevolucao1[1];
			}else{
				$dtdevolucao3 ='0000-00-00 00:00:00';
			}	

				
			if($dtdisplocadora != ''){
				//$dtdisplocadora1 = explode(" ", $dtdisplocadora);
				$dtdisplocadora2 = explode("/", $dtdisplocadora);
				$dtdisplocadora3 = $dtdisplocadora2[2]."-0".$dtdisplocadora2[0]."-".$dtdisplocadora2[1];
			}else{
				$dtdisplocadora3 = '0000-00-00 00:00:00';
			}
				
			if($dtdevlocadora!=''){
				//$dtdevlocadora1 = explode(" ", $dtdevlocadora);
				$dtdevlocadora2 = explode("/", $dtdevlocadora);
				$dtdevlocadora3 = $dtdevlocadora2[2]."-0".$dtdevlocadora2[0]."-".$dtdevlocadora2[1];
			}else{
				$dtdevlocadora3 = " ";
			}

				//buscando id da marca na tbmarcaveic
				$sql1="SELECT idtbmarcaveic FROM bdfrota.tbmarcaveic WHERE marca='".$marca."'; ";
				$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
				if(mysqli_num_rows($resultado1) <= 0){
					//update no tabela de marcas
					$sql3="INSERT INTO bdfrota.tbmarcaveic (marca) VALUES ('".$marca."'); ";
					$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));

					$sql4="SELECT idtbmarcaveic FROM bdfrota.tbmarcaveic WHERE marca='".$marca."'; ";
					$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
					$row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
					$marca = $row4['idtbmarcaveic'];
				} else {
					$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
					$marca = $row1['idtbmarcaveic'];
				}
				
				//buscando id do modelo na tbmodeloveic
				$sql2="SELECT idtbmodeloveic FROM bdfrota.tbmodeloveic WHERE modelo='$modelo'; ";
				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				if(mysqli_num_rows($resultado2) <= 0){
					//update no tabela de modelos
					$sql5="INSERT INTO bdfrota.tbmodeloveic (modelo, marca) VALUES ('$modelo', '$marca' ); ";
					$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));

					$sql6="SELECT idtbmodeloveic FROM bdfrota.tbmodeloveic WHERE modelo='$modelo'; ";
					$resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
					$row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
					$modelo = $row6['idtbmodeloveic'];
				} else {
					$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
					$modelo = $row2['idtbmodeloveic'];
				}

				//buscando id do status na tbstatusvel
				$sql7="SELECT idtbastatusvel FROM bdfrota.tbastatusvel WHERE status='$statusvel'; ";
				$resultado7 = mysqli_query($conexao, $sql7) or die(mysqli_error($conexao));
				$row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
				$statusvel = $row7['idtbastatusvel'];

				//buscando id da aplicação , 
				$sql8="SELECT idtbaplicacaoveic FROM bdfrota.tbaplicacaoveic WHERE aplicacao='$aplicacao'; ";
				$resultado8 = mysqli_query($conexao, $sql8) or die(mysqli_error($conexao));
				$row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
				$aplicacao = $row8['idtbaplicacaoveic'];

				//buscando id da locadora
				$locadora2 =  utf8_encode($idlocador);
				/*if($idlocador == 'MOVIDA PARTICIPAÇÕES S.A. (FLEET)'){
					$idlocador = 'MOVIDA PARTICIPACOES';
				} elseif($idlocador == 'LOCALIZA RENT'){
					$idlocador = 'LOCALIZA RAC';
				} elseif($idlocador == 'MOVIDA LOCAÇÃO DE VEICULOS S/A'){
					$idlocador = 'MOVIDA';
				} elseif($idlocador == 'LOCALIZA FLEET S/A'){
					$idlocador = 'LOCALIZA FLEET';
				} */


				$sql10="SELECT idtbfornecedor FROM bdfrota.tbfornecedor WHERE fantasia = '$locadora2'";
				$resultado10 = mysqli_query($conexao, $sql10) or die(mysqli_error($conexao));
				$row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
				$idlocador = $row10['idtbfornecedor'];

				$sql11="SELECT idtbatipovel FROM bdfrota.tbatipovel WHERE tipo like '$tipovel'";
				$resultado11 = mysqli_query($conexao, $sql11) or die(mysqli_error($conexao));
				$row11 = mysqli_fetch_array($resultado11, MYSQLI_BOTH);
				$tipovel = $row11['idtbatipovel'];

				//tratando entradas binárias
				if($airbag == 'sim' || $airbag == 'SIM'){
					$airbag = 1;
				} elseif($airbag == 'não' || $airbag == 'NÃO'){
					$airbag = 0;
				} elseif($airbag == 'nao' || $airbag =='NAO'){
					$airbag = 0;
				}

				if($zerokm == 'sim' || $zerokm == 'SIM'){
					$zerokm = 1;
				} elseif($zerokm == 'não' || $zerokm == 'NÃO'){
					$zerokm = 0;
				} elseif($zerokm == 'nao' || $zerokm =='NAO'){
					$zerokm = 0;
				}

				if($gnv == 'sim' || $gnv == 'SIM'){
					$gnv = 1;
				} elseif($gnv == 'não' || $gnv == 'NÃO'){
					$gnv = 0;
				} elseif($gnv == 'nao' || $gnv =='NAO'){
					$gnv = 0;
				}

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

				if($rack == 'sim' || $rack == 'SIM'){
					$rack = 1;
				} elseif($rack == 'não' || $rack == 'NÃO'){
					$rack = 0;
				} elseif($rack == 'nao' || $rack =='NAO'){
					$rack = 0;
				}

				/*if($idlocador !=''){
					$tipoposse = 'LOCADO';
				} elseif ($agregado != ''){
					$tipoposse ='AGREGADO';
				}

				if($tipoposse == 'LOCADO'){
					$dtdevlocadora = $dtdevlocadora;
				} elseif($tipoposse ='AGREGADO'){
					$dtdevlocadora = $dtdevolucao;
				}*/
				
				//print "$categoria $classvel $statusvel $tipovel<br>";

				$sql9 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado9 = mysqli_query($conexao, $sql9) or die(mysqli_error($conexao));

				if(mysqli_num_rows($resultado9)<=0){
				//print "$ccusto ";
				/*print "placa $placa, marca $marca, modelo $modelo, versao $versao, categoria $categoria, anofabr $anofabr, anomodelo $anomodelo, tipo $tipovel, aplicacao $aplicacao, zerokm $zerokm, renavam $renavam , chassi $chassi, nummotor $nummotor, uf $uf, combustivel $combustivel, tanque $tanque, motorizacao $motorizacao, cor $cor, nportas $nportas, qtdeixos $qtdeixos, qtdpneus $qtdpneus, qtdestepe $qtdestepes, aro $aro, calibragem $calibragem, velocmax $velocmax, gnv $gnv, gps $gps, tagpedagio $tagpedagio, hodometro $hodometro, status!, tipoposse $tipoposse, idlocador $idlocador, hodometroinicial $hodometroinicial, obsveiculo $observacao, statusvel $statusvel, datamovimentacao $datamovimentacao3, matcond $matcond, oficina $oficina, dtentrega $dtentrcond3, dtdevolucao $dtdevcond3, tipovel $classvel, situacao $situacao, doccrlv $doccrlv, airbag $airbag, gpsemp $gpsemp, rack $rack, ncontloc $ncontrato, dtdisponivelloc $dtdisplocadora3, dtdevolucaoloc $dtdevlocadora3, valaquisicao $valaquisicao, totkmmensal!, blindagem $blindagem, bo!, unidade $unidade, ccusto $ccusto <hr>"; break;*/
				//print $sql10; 
				
				$sql = "INSERT INTO bdfrota.tbveiculo(placa, marca, modelo, versao, categoria, anofabr, anomodelo, tipo, aplicacao, zerokm, renavam, chassi, nummotor, uf, combustivel, tanque, motorizacao, cor, nportas, npassageiros, qtdeixos, qtdpneus, qtdestepe, aro, calibragem, velocmax, gnv, gps, tagpedagio, hodometro, tipoposse, idlocador, hodometroinicial, obsveiculo, statusvel, datamovimentacao, matcond, oficina, dtentrega, dtdevolucao, tipovel, situacao, doccrlv, airbag, gpsemp, rack, ncontloc, dtdisponivelloc, dtdevolucaoloc, valaquisicao, blindagem, unidade, ccusto, status) VALUES ('$placa', '$marca', '$modelo', '$versao', '$categoria', '$anofabr', '$anomodelo', '$classvel', '$aplicacao', '$zerokm', '$renavam', '$chassi', '$nummotor', '$uf','$combustivel','$tanque','$motorizacao', '$cor','$nportas','$numpass','$qtdeixos','$qtdpneus','$qtdestepes','$aro','$calibragem', '$velocmax', '$gnv', '$gps', '$tagpedagio', '$hodometro', '$tipoposse', '$idlocador', '$hodometroinicial', '$observacao', '$statusvel', '$hoje','$matcond','$oficina', '$dtentrcond3', '$dtdevcond3', '$tipovel','$situacao', '$doccrlv', '$airbag', '$gpsemp', '$rack', '$ncontrato', '$dtdisplocadora3','$dtdevlocadora3', '$valaquisicao', '$blindagem', '$unidade', '$ccusto', '$status');";  //break;
				
				$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
				

				
				}/*else{
					$sql = "UPDATE bdfrota.tbveiculo SET placa='$placa', marca='$marca', modelo='$modelo', versao='$versao', categoria='$categoria', anofabr='$anofabr', anomodelo='$anomodelo', tipo='$classvel', aplicacao='$aplicacao', zerokm='$zerokm', renavam='$renavam', chassi='$chassi', nummotor='$nummotor', uf='$uf', combustivel='$combustivel', tanque='$tanque', motorizacao='$motorizacao', cor='$cor', nportas='$nportas', npassageiros='$numpass', qtdeixos='$qtdeixos', qtdpneus='$qtdpneus', qtdestepe='$qtdestepes', aro='$aro', calibragem='$calibragem', velocmax='$velocmax', gnv='$gnv', gps='$gps', tagpedagio='$tagpedagio', hodometro='$hodometro', tipoposse='$tipoposse', idlocador='$idlocador', hodometroinicial='$hodometroinicial', obsveiculo'$observacao', statusvel='$statusvel', datamovimentacao='$hoje', matcond='$matcond', oficina='$oficina', dtentrega='$dtentrcond3', dtdevolucao='$dtdevcond3', tipovel='$tipovel', situacao='$situacao', doccrlv='$doccrlv', airbag='$airbag', gpsemp='$gpsemp', rack='$rack', ncontloc='$ncontrato',, dtdisponivelloc='$dtdisplocadora3', dtdevolucaoloc='$dtdevlocadora3', valaquisicao='$valaquisicao', blindagem='$blindagem', unidade='$unidade', ccusto='$ccusto', status='$status' WHERE placa='$placa';";  
				
				//$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

				}*/

				print $sql."<hr>";

				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
				/*$acao='Importou excel de veiculos';
				$tipo = 'cadastro';
				$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');*/
				//echo "$hoje, $acao, '', $matr_autor, $tipo, ''"; print $sql; 
			}
		}					
	}	
}

// Depois verifica se é possível mover o arquivo para a pasta escolhida
/*if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo

print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../listagemveiculos.php\"</script>";

} else {
// Não foi possível fazer o upload, provavelmente a pasta está incorreta
echo "Não foi possível enviar o arquivo, tente novamente";
}*/


?>	