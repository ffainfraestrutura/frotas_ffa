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
				$matcond = $row[1];//Matricula condutor
				$ccusto = utf8_decode($row[2]);//Centro Custo
				$statusvel = utf8_decode($row[3]);//Status Veículo
				$status = utf8_decode($row[4]);//Status

				$marcapl = utf8_decode($row[5]);//Marca
				$modelo = utf8_decode($row[6]);//Modelo
				$hodometro = $row[7];//Hodometro				
				$tipoposse = utf8_decode($row[8]);//propriedade
				$tipofrota = utf8_decode($row[9]);//Tipo Frota

				$unidade = utf8_decode($row[10]);//Unidade
				$anofabr = $row[11];//Ano Fabricação
				$anomodelo = $row[12];//Ano Modelo
				$gps = $row[13];//GPS				
				$tagpedagio = $row[14];//TAG Pedágio

				$idlocador = $row[15];//Locadora
				$dtabertcontrato = $row[16];//Data Abertura de Contrato
				$dtfechcontrato = $row[17];//Data Fechamento de Contrato
				$valaquisicao = $row[18];//Valor Aquisição
				$dtdesm = $row[19];//Data de Desmobilização

				$cor = utf8_decode($row[20]);//Cor				
				$combustivel = utf8_decode($row[21]);//Combustível
				$motorizacao = $row[22];//Motorização
				$chassi = $row[23];//Chassi
				$renavam = $row[24];//Renavam

				$uf = $row[25];//UF Emplacamento
				$doccrlv = utf8_decode($row[26]);//Documento Ano (CRLV)
				$ncontrato = $row[27];//Nº Contrato Locação (Pedido)			
				$qtdeixos = $row[28];//Qtde Eixos
				$tanque = $row[29];//Capacidade Tanque

				$contrato = $row[30];//Contrato

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
				$dtdisplocadora1 = explode(" ", $dtdisplocadora);
				$dtdisplocadora2 = explode("/", $dtdisplocadora1[0]);
				$dtdisplocadora3 = $dtdisplocadora2[2]."-".$dtdisplocadora2[1]."-".$dtdisplocadora2[0]." ".$dtdisplocadora1[1];

				$dtdevlocadora1 = explode(" ", $dtdevlocadora);
				$dtdevlocadora2 = explode("/", $dtdevlocadora1[0]);
				$dtdevlocadora3 = $dtdevlocadora2[2]."-".$dtdevlocadora2[1]."-".$dtdevlocadora2[0]." ".$dtdevlocadora1[1];

				if($dtdevlocadora == ''){
					$dtdevlocadora3 = " ";
				}

				if(!empty($dtdesm)){
					if(strpos($dtdesm,"'") !== false){
						$dtdesm = str_replace("'", "",$dtdesm);
					}
					
					$dtdesm1 = explode("/", $dtdesm);
					$dtdesm2 = $dtdesm1[2]."-".$dtdesm1[1]."-".$dtdesm1[0];
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

				if($gps == 'sim' || $gps == 'SIM'){
					$gps = 1;
				} elseif($gps == 'não' || $gps == 'NÃO'){
					$gps = 0;
				} elseif($gps == 'nao' || $gps =='NAO'){
					$gps = 0;
				}
				
				$sql9 = "SELECT placa FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado9 = mysqli_query($con, $sql9) or die(mysqli_error($con));

				if(mysqli_num_rows($resultado9)<=0){
                 
                    $sql = "INSERT INTO bdfrota.tbveiculo(placa, marca, modelo,   anofabr, anomodelo, tipo,  renavam, chassi,  uf, combustivel, tanque, motorizacao, cor, qtdeixos,  gps, tagpedagio, hodometro, tipoposse, idlocador, statusvel, matcond, situacao, doccrlv, ncontloc, dtdisponivelloc, dtdevolucaoloc, valaquisicao, unidade, ccusto, dtdesmobilizacao, datamovimentacao, dtabcontrato, dtfechcontrato, contrato) VALUES ('$placa', '$marca', '$modelo',   '$anofabr', '$anomodelo', '$renavam', '$chassi', '$uf','$combustivel','$tanque','$motorizacao', '$cor','$qtdeixos', '$gps', '$tagpedagio', '$hodometro', '$tipoposse', '$idlocador', '$idtbastatusvel','$matcond', '$tipofrota', '$doccrlv', '$ncontrato', '$dtdisplocadora3','$dtdevlocadora3', '$valaquisicao', '$unidade', '$ccusto', '$dtdesm2', '$hoje','$dtabertcontrato2', '$dtfechcontrato2', '$contrato' );";  //break;
                
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                    

                
                    //echo "$hoje, $acao, '', $matr_autor, $tipo, ''"; print $sql;

                    
                }else{
                    $sql = "UPDATE bdfrota.tbveiculo SET marca = '$marca', modelo = '$modelo', anofabr = '$anofabr', anomodelo = '$anomodelo', renavam='$renavam', chassi='$chassi', uf = '$uf', combustivel = '$combustivel', tanque = '$tanque', motorizacao = '$motorizacao', cor = '$cor', qtdeixos = '$qtdeixos',  gps = '$gps', tagpedagio = '$tagpedagio', hodometro = '$hodometro', tipoposse = '$tipoposse', idlocador = '$idlocador', statusvel = '$idtbastatusvel',  matcond = '$matcond', tipovel = '$tipofrota', doccrlv = '$doccrlv', ncontloc = '$ncontrato', dtdisponivelloc = '$dtdisplocadora3', dtdevolucaoloc = '$dtdevlocadora3', valaquisicao = '$valaquisicao', unidade = '$unidade', ccusto = '$ccusto', dtdesmobilizacao = '$dtdesm2', datamovimentacao='$hoje', dtabcontrato='$dtabertcontrato2', dtfechcontrato='$dtfechcontrato2', contrato='$contrato' WHERE placa='$placa';";  //break;
                
                    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                    /*$log = new Log($con);
                    $acao='Importou excel de veiculos';
                    $tipolog = 'cadastro';
                    $logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipolog, '');*/
                }

                print $sql;

                if($idtbastatusvel==1){
                    $sql2 = "SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND matricula='$matcond' AND ativo=1;";
                    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                    if(mysqli_num_rows($resultado2)<=0){
                        $sql3 = "INSERT INTO bdfrota.tbcondutor (placaassoc,matricula,ativo, dataassoc) VALUES ('$placa','$matcond',1,'$hoje');";
                        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
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