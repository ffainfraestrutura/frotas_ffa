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
//print $matr_autor;
include '../vendor/autoload.php';

include_once"../../func/log3.php";

include("../../conecta03.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "veiculos";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/veiculos/';

//modelo correto ImportModeloVeiculos.xlsx

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
			
			if($row[0]<>'Placa' AND $row[0]<>''){

				$placa = $row[0];//Placa
				
								
				$status = utf8_decode($row[1]);//Status
				$marcapl = utf8_decode($row[2]);//Marca
				$modelo = utf8_decode($row[3]);//Modelo
				$hodometro = $row[4];//Hodometro		
				$tipoposse = utf8_decode($row[5]);//propriedade

				$tipofrota = utf8_decode($row[6]);//Tipo Frota
				$unidade = utf8_decode($row[7]);//Unidade
				$basegestao = utf8_decode($row[8]);
				$anofabr = $row[9];//Ano Fabricação				
				$anomodelo = $row[10];//Ano Modelo

				$gps = $row[11];//GPS				
				$tagpedagio = $row[12];//TAG Pedágio
				$idlocador = $row[13];//Locadora
				$dtdisplocadora = $row[14];//Data Abertura de Contrato		
				$dtdevlocadora = $row[15];//Data Fechamento de Contrato

				$valaquisicao = $row[16];//Valor Aquisição
				$dtdesm = $row[17];//Data de Desmobilização
				$cor = utf8_decode($row[18]);//Cor				
				$combustivel = utf8_decode($row[19]);//Combustível
				$chassi = $row[20];//Chassi

				$renavam = $row[21];//Renavam				
				$ncontrato = $row[22];//Nº Contrato Locação (Pedido)			
				$qtdeixos = $row[23];//Qtde Eixos
				$contrato = $row[24];//Capacidade Tanque

				//status
				if($status=='ATIVO' || $status=='ativo' || $status == 'Ativo'){
					$status='1';
				}elseif($status=='INATIVO' || $status=='inativo' || $status == 'Inativo'){
					$status='0';
				}

				//chassi
				if(strpos($chassi,"'") !== false){
					$chassi = str_replace("'","",$chassi);
				}

				//quantidade de eixos
				if(strpos($qtdeixos,"'") !== false){
					$qtdeixos = substr($qtdeixos,1);
				}

				//tratando renavam
				if(strpos($renavam,"'") !== false){
					$renavam = substr($renavam,1);
				}

				//tratando tagpedagio
				if(strpos($tagpedagio,"'") !== false){
					$tagpedagio = substr($tagpedagio,1);
				}

				//tratando contrato
				if(strpos($contrato,"'") !== false){
					//$contrato = substr($contrato,1);
					$contrato = str_replace("'", "", $contrato);
				}

				if(strpos($ncontrato,"'") !== false){
					//$contrato = substr($contrato,1);
					$ncontrato = str_replace("'", "", $ncontrato);
				}

				
				if(strpos($valaquisicao,"'") !== false){
					//$contrato = substr($contrato,1);
					$valaquisicao = str_replace("'", "", $valaquisicao);
				}

				

				//tratando valor
				if(strpos($valaquisicao,",") !== false){
					$valaquisicao = str_replace(",", ".", $valaquisicao);
				}

				//tratando hodometro
				if(strpos($hodometro,"'") !== false){
					$hodometro = str_replace("'", "", $hodometro);
				}

				if(strpos($hodometro,".") !== false){
					$hodometro = str_replace(".", "", $hodometro);
				}


				//tratando datas
				$dtdisplocadora1 = explode(" ", $dtdisplocadora);
				$dtdisplocadora2 = explode("/", $dtdisplocadora1[0]);
				$dtdisplocadora3 = $dtdisplocadora2[2]."-".$dtdisplocadora2[0]."-".$dtdisplocadora2[1]." ".$dtdisplocadora1[1];

				$dtdevlocadora1 = explode(" ", $dtdevlocadora);
				$dtdevlocadora2 = explode("/", $dtdevlocadora1[0]);
				$dtdevlocadora3 = $dtdevlocadora2[2]."-".$dtdevlocadora2[0]."-".$dtdevlocadora2[1]." ".$dtdevlocadora1[1];

				if($dtdevlocadora == ''){
					$dtdevlocadora3 = " ";
				}

				if(!empty($dtdesm)){
					if(strpos($dtdesm,"'") !== false){
						$dtdesm = str_replace("'", "",$dtdesm);
					}
					
					/*$dtdesm1 = explode("/", $dtdesm);
					$dtdesm2 = $dtdesm1[2]."-".$dtdesm1[1]."-".$dtdesm1[0];*/

					$dtdesm1 = explode(" ", $dtdesm);
					$dtdesm2 = explode("/", $dtdesm1[0]);
					$dtdesm3 = $dtdesm2[2]."-".$dtdesm2[0]."-".$dtdesm2[1]." ".$dtdesm1[1];
				}
				
				//buscando id do modelo na tbmodeloveic
				$sql2="SELECT idtbmodeloveic,marca FROM bdfrota.tbmodeloveic WHERE modelo='$modelo'; ";
				$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));	
				$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
					$modelo = $row2['idtbmodeloveic'];
					$marca = $row2['marca'];				

				//buscando id da locadora
				$locadora2 =  utf8_encode($idlocador);
				
				$sql10="SELECT idtbfornecedor FROM bdfrota.tbfornecedor WHERE fantasia = '$locadora2'";
				$resultado10 = mysqli_query($con, $sql10) or die(mysqli_error($con));
				$row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
				$idlocador = $row10['idtbfornecedor'];

				if($tipoposse=='PROPRIO'){
					
					$tipoposse='PRÓPRIO';

					$tipoposse = utf8_decode($tipoposse);
				}

				if($tipoposse=='PRÓPRIO' || $tipoposse=='PROPRIO'){
					$idlocador = '1';
				}

				$sql11="SELECT idtbatipovel FROM bdfrota.tbatipovel WHERE tipo like '$tipofrota'";
				$resultado11 = mysqli_query($con, $sql11) or die(mysqli_error($con));
				$row11 = mysqli_fetch_array($resultado11, MYSQLI_BOTH);
				$tipofrota = $row11['idtbatipovel'];

				if($gps=='Sim' || $gps == 'sim' || $gps == 'SIM'){
					$gps = 1;
				} elseif($gps=='Não' || $gps == 'não' || $gps == 'NÃO'){
					$gps = 0;
				} elseif($gps=='Nao' || $gps == 'nao' || $gps =='NAO'){
					$gps = 0;
				}

				if($idtbastatusvel==2){
					$matcond ='';
				}
				
				$sql9 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));

				if(mysqli_num_rows($resultado9)<=0){
                 
                    $sql = "INSERT INTO bdfrota.tbveiculo(placa, marca, modelo,   anofabr, anomodelo,  renavam, chassi, combustivel,   cor, qtdeixos, gps, tagpedagio, hodometro, tipoposse, idlocador, statusvel, matcond, ncontloc, dtdisponivelloc, dtdevolucaoloc, valaquisicao, unidade, ccusto, dtdesmobilizacao, datamovimentacao,  contrato, tipovel, status, qtdeixos, basegestao) VALUES ('$placa', '$marca', '$modelo',   '$anofabr', '$anomodelo', '$renavam', '$chassi','$combustivel', '$cor','$qtdeixos', '$gps', '$tagpedagio', '$hodometro', '$tipoposse', '$idlocador', '$idtbastatusvel','$matcond', '$ncontrato', '$dtdisplocadora3','$dtdevlocadora3', '$valaquisicao', '$unidade', '$ccusto', '$dtdesm2', '$hoje', '$contrato', '$tipofrota', '$status', '$qtdeixos','$basegestao' );";  //break;
                
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                
                    //echo "$hoje, $acao, '', $matr_autor, $tipo, ''"; print $sql;
                }else{
                	if(!empty($marca)){
                		$sql = "UPDATE bdfrota.tbveiculo SET marca = '$marca', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($modelo)){
                		$sql = "UPDATE bdfrota.tbveiculo SET modelo = '$modelo', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($anofabr)){
                		$sql = "UPDATE bdfrota.tbveiculo SET anofabr = '$anofabr', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($anomodelo)){
                		$sql = "UPDATE bdfrota.tbveiculo SET anomodelo = '$anomodelo', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($renavam)){
                		$sql = "UPDATE bdfrota.tbveiculo SET renavam='$renavam', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($chassi)){
                		$sql = "UPDATE bdfrota.tbveiculo SET chassi='$chassi', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($combustivel)){
                		$sql = "UPDATE bdfrota.tbveiculo SET combustivel = '$combustivel', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($cor)){
                		$sql = "UPDATE bdfrota.tbveiculo SET cor = '$cor', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($qtdeixos)){
                		$sql = "UPDATE bdfrota.tbveiculo SET qtdeixos = '$qtdeixos', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($gps)){
                		$sql = "UPDATE bdfrota.tbveiculo SET gps = '$gps', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($tagpedagio)){
                		$sql = "UPDATE bdfrota.tbveiculo SET tagpedagio = '$tagpedagio', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($hodometro)){
                		$sql = "UPDATE bdfrota.tbveiculo SET hodometro = '$hodometro', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($tipoposse)){
                		$sql = "UPDATE bdfrota.tbveiculo SET tipoposse = '$tipoposse', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($idlocador)){
                		$sql = "UPDATE bdfrota.tbveiculo SET idlocador = '$idlocador', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($idtbastatusvel)){
                		$sql = "UPDATE bdfrota.tbveiculo SET statusvel = '$idtbastatusvel', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}                	

                	if(!empty($tipofrota)){
                		$sql = "UPDATE bdfrota.tbveiculo SET tipovel = '$tipofrota', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($ncontrato)){
                		$sql = "UPDATE bdfrota.tbveiculo SET ncontloc = '$ncontrato', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($dtdisplocadora3)){
                		$sql = "UPDATE bdfrota.tbveiculo SET dtdisponivelloc = '$dtdisplocadora3', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($dtdevlocadora3)){
                		$sql = "UPDATE bdfrota.tbveiculo SET dtdevolucaoloc = '$dtdevlocadora3', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($valaquisicao)){
                		$sql = "UPDATE bdfrota.tbveiculo SET valaquisicao = '$valaquisicao', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($valaquisicao)){
                		$sql = "UPDATE bdfrota.tbveiculo SET valaquisicao = '$valaquisicao', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}
                	
                	if(!empty($unidade)){
                		$sql = "UPDATE bdfrota.tbveiculo SET unidade = '$unidade', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($dtdesm2)){
                		$sql = "UPDATE bdfrota.tbveiculo SET dtdesmobilizacao = '$dtdesm2', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($contrato)){
                		$sql = "UPDATE bdfrota.tbveiculo SET contrato='$contrato', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($status)){
                		$sql = "UPDATE bdfrota.tbveiculo SET status='$status', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}

                	if(!empty($basegestao)){
                		$sql = "UPDATE bdfrota.tbveiculo SET basegestao='$basegestao', datamovimentacao='$hoje' WHERE placa='$placa';";
                		$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                	}
                                        
                }

                //print $sql;

                /*if($idtbastatusvel==1){
                    $sql2 = "SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND matricula='$matcond' AND ativo=1;";
                    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                    
                    if(mysqli_num_rows($resultado2)<=0){
                    	$sql2 = "SELECT MAX(idtbcondutor) AS idtbcondutor FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo=1;";
                    	$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                    	$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                        	$idtbcondutor = $row2['idtbcondutor'];

                    
                    	$sql3 = "UPDATE bdfrota.tbcondutor SET ativo=0, datadissoc='$hoje' WHERE idtbcondutor='$idtbcondutor';";
                    	$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                        $sql4 = "INSERT INTO bdfrota.tbcondutor (placaassoc,matricula,ativo, dataassoc) VALUES ('$placa','$matcond',1,'$hoje');";
                        $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
                        //print "ok";
                    }
                }elseif($idtbastatusvel==2){
                    $sql2 = "SELECT MAX(idtbcondutor) AS idtbcondutor FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo=1;";
                    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
                        $idtbcondutor = $row2['idtbcondutor'];

                    
                    $sql3 = "UPDATE bdfrota.tbcondutor SET ativo=0, datadissoc='$hoje' WHERE idtbcondutor='$idtbcondutor';";
                    $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                    //PRINT $sql3;
                }*/

				

				
			}
		}					
	}

	$log = new Log($con);
    //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
    $acao='Importou excel de veiculos';
    $tipolog = 'cadastro';
    $logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipolog, '');

	//Depois verifica se é possível mover o arquivo para a pasta escolhida
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
		// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo

		print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!'); window.close();</script>";

		//window.location=\"../listagemveiculos.php\"

	} else {
		// Não foi possível fazer o upload, provavelmente a pasta está incorreta

		print "<script language='javascript' type='text/javascript'>alert('Não foi possível enviar o arquivo, tente novamente.'); window.close();</script>";

		//echo "Não foi possível enviar o arquivo, tente novamente.";
	}
}
?>	