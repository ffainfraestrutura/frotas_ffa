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
				$nomecond = utf8_decode($row[1]);
				$matcond = $row[2];//Matricula condutor
				$ccusto = utf8_decode($row[3]);//Centro Custo
				$statusvel = utf8_decode($row[4]);//Status Veículo
				$status = utf8_decode($row[5]);//Status

				$marcapl = utf8_decode($row[6]);//Marca
				$modelo = utf8_decode($row[7]);//Modelo
				$hodometro = $row[8];//Hodometro				
				$tipoposse = utf8_decode($row[9]);//propriedade
				$tipofrota = utf8_decode($row[10]);//Tipo Frota

				$unidade = utf8_decode($row[11]);//Unidade
				$basegestao = utf8_decode($row[12]);
				$anofabr = $row[13];//Ano Fabricação
				$anomodelo = $row[14];//Ano Modelo
				$gps = $row[15];//GPS				
				$tagpedagio = $row[16];//TAG Pedágio

				$idlocador = $row[17];//Locadora
				$dtdisplocadora = $row[18];//Data Abertura de Contrato
				$dtdevlocadora = $row[19];//Data Fechamento de Contrato
				$valaquisicao = $row[20];//Valor Aquisição
				$dtdesm = $row[21];//Data de Desmobilização

				$cor = utf8_decode($row[22]);//Cor				
				$combustivel = utf8_decode($row[23]);//Combustível				
				$chassi = $row[24];//Chassi
				$renavam = $row[25];//Renavam				
				$ncontrato = $row[26];//Nº Contrato Locação (Pedido)			
				$qtdeixos = $row[27];//Qtde Eixos
				$contrato = $row[28];//Capacidade Tanque



				//status
				if($status=='ATIVO' || $status=='ativo' || $status == 'Ativo'){
					$status='1';
				}elseif($status=='INATIVO' || $status=='inativo' || $status == 'Inativo'){
					$status='0';
				}


				//tratando matricula
				if(strpos($matcond,"'") !== false){
					$matcond = substr($matcond,1);
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

				//tratando tag
				if(strpos($ccusto,"'") !== false){
					$ccusto = substr($ccusto,1);
				}

				//tratando contrato
				if(strpos($contrato,"'") !== false){
					$contrato = substr($contrato,1);
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

				//$dtabertcontrato $dtfechcontrato
				if(!empty($dtabertcontrato)){
					if(strpos($dtabertcontrato,"'") !== false){
						$dtabertcontrato = str_replace("'", "",$dtabertcontrato);
					}
					
					$dtabertcontrato1 = explode("/", $dtabertcontrato);
					$dtabertcontrato2 = $dtabertcontrato1[2]."-".$dtabertcontrato1[1]."-".$dtabertcontrato1[0];
				}

				if(!empty($dtfechcontrato)){
					if(strpos($dtfechcontrato,"'") !== false){
						$dtfechcontrato = str_replace("'", "",$dtfechcontrato);
					}
					
					$dtfechcontrato1 = explode("/", $dtfechcontrato);
					$dtfechcontrato2 = $dtfechcontrato1[2]."-".$dtfechcontrato1[1]."-".$dtfechcontrato1[0];
				}
				
				//buscando id da marca na tbmarcaveic			
				
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
				 $idtbastatusvel = $row7['idtbastatusvel'];

				//buscando id da locadora
				$locadora2 =  utf8_encode($idlocador);
				
				$sql10="SELECT idtbfornecedor FROM bdfrota.tbfornecedor WHERE fantasia = '$locadora2'";
				$resultado10 = mysqli_query($con, $sql10) or die(mysqli_error($con));
				$row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
				$idlocador = $row10['idtbfornecedor'];

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
				
				$sql9 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));

				if(mysqli_num_rows($resultado9)<=0){
                 
                    $sql = "INSERT INTO bdfrota.tbveiculo(placa, marca, modelo,   anofabr, anomodelo,  renavam, chassi, combustivel,   cor, qtdeixos, gps, tagpedagio, hodometro, tipoposse, idlocador, statusvel, matcond, ncontloc, dtdisponivelloc, dtdevolucaoloc, valaquisicao, unidade, ccusto, dtdesmobilizacao, datamovimentacao,  contrato, tipovel, status, qtdeixos, basegestao) VALUES ('$placa', '$marca', '$modelo',   '$anofabr', '$anomodelo', '$renavam', '$chassi','$combustivel', '$cor','$qtdeixos', '$gps', '$tagpedagio', '$hodometro', '$tipoposse', '$idlocador', '$idtbastatusvel','$matcond', '$ncontrato', '$dtdisplocadora3','$dtdevlocadora3', '$valaquisicao', '$unidade', '$ccusto', '$dtdesm2', '$hoje', '$contrato', '$tipofrota', '$status', '$qtdeixos','$basegestao' );";  //break;
                
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));                   

                
                    //echo "$hoje, $acao, '', $matr_autor, $tipo, ''"; print $sql;

                    
                }else{
                    $sql = "UPDATE bdfrota.tbveiculo SET marca = '$marca', modelo = '$modelo', anofabr = '$anofabr', anomodelo = '$anomodelo', renavam='$renavam', chassi='$chassi',  combustivel = '$combustivel', cor = '$cor', qtdeixos = '$qtdeixos',  gps = '$gps', tagpedagio = '$tagpedagio', hodometro = '$hodometro', tipoposse = '$tipoposse', idlocador = '$idlocador', statusvel = '$idtbastatusvel', matcond = '$matcond', tipovel = '$tipofrota', ncontloc = '$ncontrato', dtdisponivelloc = '$dtdisplocadora3', dtdevolucaoloc = '$dtdevlocadora3', valaquisicao = '$valaquisicao', unidade = '$unidade', ccusto = '$ccusto', dtdesmobilizacao = '$dtdesm2', datamovimentacao='$hoje',  contrato='$contrato', status='$status', basegestao='$basegestao' WHERE placa='$placa';";  //break;
                    //print $sql;
                
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));                    
                }

                //print $sql;

                if($idtbastatusvel==1){
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
                }

				

				
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