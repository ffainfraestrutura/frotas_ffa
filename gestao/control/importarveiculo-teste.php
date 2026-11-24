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

include './vendor/autoload.php';

//include_once"../../func/log.php";
include("./conecta.php");

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
			
			if($row[0]<>'id'){
				$id = $row[0];
				$placa = $row[1];
				$numfrota = $row[2];
				$status = utf8_decode($row[3]);
				$modelo = utf8_decode($row[4]);
				$versao = utf8_decode($row[5]);
				$modcomp = $row[6];

				$cor = utf8_decode($row[7]);
				$marca = $row[8];
				$hodometro = $row[9];
				$ultcom = $row[10];
				$tipoposse = utf8_decode($row[11]);
				$statusvel = $row[12];
				$datamovimentacao = utf8_decode($row[13]);
				$diascorridos = $row[14];
				
				$oficina = utf8_decode($row[15]);
				$condutor = $row[16];
				$matcond = $row[17];
				$cpfcond = $row[18];
				$cargocond = $row[19];
				$carroben = $row[20];

				$dtentrcond = $row[21];
				$dtdevcond = utf8_decode($row[22]);
				$tipovel = utf8_decode($row[23]);
				$tipo = utf8_decode($row[24]);
				$aplicacao = utf8_decode($row[25]);				
				$categoria = $row[26];
				$pais = $row[27];
				$regiao = $row[28];
				$unidade = utf8_decode($row[29]);
				$basegestao = $row[30];
				$baseoperac = $row[31];
				$divisao = $row[32];
				$vicepres = $row[33];
				$diretoria = $row[34];
				$gerencia = $row[35];
				$coord = $row[36];
				$superv = $row[37];

				$ccusto = utf8_decode($row[38]);
				$ccustodesc = $row[39];
				$ordemcusteio = $row[40];
				$anofabr = $row[41];
				$anomodelo = $row[42];
				$idade = $row[43];
				$codpatr = $row[44];
				$itemcont = $row[45];
				$classevalor = $row[46];
				$codfipe = $row[47];
				$combustivel = $row[48];
								
				$motorizacao = $row[49];
				$chassi = $row[50];
				$nportas = $row[51];
				$renavam = $row[52];
				$uf = $row[53];
				$licenciamentoano = $row[54];
				$licenciamentoisento = $row[55];
				$licenciamentostatus = $row[56];
				$licencimentovencto = $row[57];
				$licencimentodtpagto = $row[58];
				$licenciamentovalor = $row[59];
				$possuiDUT = $row[60];
				$crvl = utf8_decode($row[61]);
				$crvlArquivo = $row[62];
				$seguroAno = $row[63];
				$seguroCorporativo=$row[64];
				$segInicioVig = $row[65];
				$seguroFimVig = $row[66];
				$seguroParc = $row[67];
				$seguroParcVenc = $row[68];
				$isentoRod = $row[69];
				$isentoRodVenc = $row[70];

				$airbag = $row[71];
				$zerokm = $row[72];
				$hodometroCompra = $row[73];
				$elegivelGPS = $row[74];
				$gps = $row[75];
				$gpsemp = $row[76];
				$gpsValMensal = $row[77];
				$gpsDtInstal = $row[78];
				$GPSIdentificador = $row[79];
				$GPSAcelerometro = $row[80];

				$gnv = $row[81];
				$GNVValidade = $row[82];

				$rack = $row[83];
				$tagpedagio = $row[84];
				$idlocador = $row[85];
				$ncontratopedido = $row[86];
				$ncontrato = $row[87];
				$DataIncop =$row[88];
				$cnpjUnidade = $row[89];

				$dtdisplocadora = $row[90];
				$dtdevolucao = $row[91];
				$mesAnoDev = $row[92];
				$dtDevSMulta = $row[93];
				$dtDevCarencia = $row[94];

				$dtdevlocadora = $row[95];
				$valaquisicao = $row[96];
				$franquiaKm = $row[97];
				$valorKmInferior = $row[98];
				$valorKmExcedente=$row[99];
				$valorFranquiaSeguro=$row[100];
				$valorBenFunc = $row[101];
				$taxadeprecAnual = $row[102];
				$ValorCalculadoDeprec=$row[103];
				$PoliticaFrota=$row[104];
				$PlanoAgregamento=$row[105];
				$CodFornecedorAgregamento=$row[106];
				$codBonAgregamento=$row[107];
				$CarroAgregado=$row[108];
				$CarroAgregadoNome=$row[109];
				$TotalKmRodadoAtual=$row[110];
				$LocalRodagem=$row[111];
				$PlanoChecklist=$row[112];
				$ChecklistSinalizadorTempo=$row[113];
				$Checklist=$row[114];
				$checklistTipo=$row[115];
				$ChecklistData=$row[116];
				$ChecklistEstado=$row[117];
				$ChecklistTemAvarias=$row[118];
				$ChecklistObs=$row[119];
				$PlanoManPrev=$row[120];
				$ManPrevData=$row[121];
				$ManPrevHod=$row[122];
				$PlanoRodPneus=$row[123];
				$RodizioPneusData= $row[124];
				$RodizioPneusHod=$row[125];
				$GPSDataultimaPos=$row[126];
				$GPSultimohodometro=$row[127];
				$GPSultimoStatus=$row[128];	
				$ultimoabastecimento=$row[129];
				$municpioultAbastecimento=$row[130];
				$matrultabastecimento=$row[131];
				$CondultimoAbastecimento=$row[132];
				$HodometroultAbastec = $row[133];
				$PlacaAnterior=$row[134];
				$Registro=$row[135];
				$atualizado =$row[136];	
				$Por = $row[137];
				$EmpresaPropr = $row[138];
				$EmpresaAlocada = $row[139];
				$Consulta =$row[140];
				$Catalogo =$row[141];

				$nummotor = $row[142];
				$qtdeixos = $row[143];
				$qtdpneus = utf8_decode($row[144]);
				$qtdestepes = $row[145];

				$Tara = $row[146];
				$Lota=$row[147];
				$PesoBruto=$row[148];
				$CMT= $row[149];
				$QtdEquipamentos = $row[150];
				$CabAuxiliar = $row[151];
				$Carroceria = $row[152];	
				$Aterramento = $row[153];	
				$DuplaIsol=$row[154];
				$CapacidadePalet=$row[155];
				$tanque = $row[156];

				$UsaArla  = $row[157];	
				$CapacidadeTanqueArla = $row[158];	
				$GarantiaVeíc = $row[158];
				$Tacografo	 = $row[160];
				$TacografoTipo = $row[161];	
				$TacografoNum  = $row[162];
				$Certificado = $row[163];	
				$TacografoDataValidade = $row[164];

				$blindagem = $row[165];

				$BlindagemDataInstal = $row[166];	
				$BlindagemGarantia = $row[167]; 
				$BlindagemDataGarantia = $row[168];	
				$ValorBlindagem = $row[169];	
				$ValorBlindagemAcess = $row[170];	
				$ValorDeprecBlindagem = $row[171];	
				$Plotagem = $row[172];
				$NFe = $row[173];	
				$CTe = $row[174];	
				$ResponsImediato = $row[175];	
				$Respons2 = $row[176];
				$Respons3 = $row[177];
				$Respons4 = $row[178];
				$Respons5 = $row[179];
				$Respons6 = $row[180];	
				$MedirEfic = $row[181];
				$TipoPlaca = $row[182];	
				$IdTicket = $row[183];				

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
				$datamovimentacao1 = explode(" ", $datamovimentacao);
				$datamovimentacao2 = explode("/", $datamovimentacao1[0]);
				$datamovimentacao3 = $datamovimentacao2[2]."-".$datamovimentacao2[1]."-".$datamovimentacao2[0]." ".$datamovimentacao1[1];

				$dtentrcond1 = explode(" ", $dtentrcond);
				$dtentrcond2 = explode("/", $dtentrcond1[0]);
				$dtentrcond3 = $dtentrcond2[2]."-".$dtentrcond2[1]."-".$dtentrcond2[0]." ".$dtentrcond1[1];

				$dtdevcond1 = explode(" ", $dtdevcond);
				$dtdevcond2 = explode("/", $dtdevcond1[0]);
				$dtdevcond3 = $dtdevcond2[2]."-".$dtdevcond2[1]."-".$dtdevcond2[0]." ".$dtdevcond1[1];

				$dtdevolucao1 = explode(" ", $dtdevolucao);
                $dtdevolucao2 = explode("/", $dtdevolucao1[0]);
                $dtdevolucao3 = $dtdevolucao2[2]."-".$dtdevolucao2[1]."-".$dtdevolucao2[0]." ".$dtdevolucao1[1];

				$dtdisplocadora1 = explode(" ", $dtdisplocadora);
				$dtdisplocadora2 = explode("/", $dtdisplocadora1[0]);
				$dtdisplocadora3 = $dtdisplocadora2[2]."-".$dtdisplocadora2[1]."-".$dtdisplocadora2[0]." ".$dtdisplocadora1[1];

				$dtdevlocadora1 = explode(" ", $dtdevlocadora);
				$dtdevlocadora2 = explode("/", $dtdevlocadora1[0]);
				$dtdevlocadora3 = $dtdevlocadora2[2]."-".$dtdevlocadora2[1]."-".$dtdevlocadora2[0]." ".$dtdevlocadora1[1];

				if($dtdevlocadora == ''){
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
				$sql = "INSERT INTO bdfrota.tbveiculo(placa, marca, modelo, versao, categoria, anofabr, anomodelo, tipo, aplicacao, zerokm, renavam, chassi, nummotor, uf, combustivel, tanque, motorizacao, cor, nportas, npassageiros, qtdeixos, qtdpneus, qtdestepe, aro, calibragem, velocmax, gnv, gps, tagpedagio, hodometro, tipoposse, idlocador, hodometroinicial, obsveiculo, statusvel, datamovimentacao, matcond, oficina, dtentrega, dtdevolucao, tipovel, situacao, doccrlv, airbag, gpsemp, rack, ncontloc, dtdisponivelloc, dtdevolucaoloc, valaquisicao, blindagem, unidade, ccusto) VALUES ('$placa', '$marca', '$modelo', '$versao', '$categoria', '$anofabr', '$anomodelo', '$classvel', '$aplicacao', '$zerokm', '$renavam', '$chassi', '$nummotor', '$uf','$combustivel','$tanque','$motorizacao', '$cor','$nportas','$numpass','$qtdeixos','$qtdpneus','$qtdestepes','$aro','$calibragem', '$velocmax', '$gnv', '$gps', '$tagpedagio', '$hodometro', '$tipoposse', '$idlocador', '$hodometroinicial', '$observacao', '$statusvel', '$datamovimentacao3','$matcond','$oficina', '$dtentrcond3', '$dtdevcond3', '$tipovel','$situacao', '$doccrlv', '$airbag', '$gpsemp', '$rack', '$ncontrato', '$dtdisplocadora3','$dtdevlocadora3', '$valaquisicao', '$blindagem', '$unidade', '$ccusto');";  //break;
				
				//$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
				//print $sql."<hr>";

				//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
				/*$acao='Importou excel de veiculos';
				$tipo = 'cadastro';
				$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
				//echo "$hoje, $acao, '', $matr_autor, $tipo, ''"; print $sql;*/
				} 
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