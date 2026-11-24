<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');

$tipoposse = utf8_decode($_POST['tipoposse']);
$locador = utf8_decode($_POST['locador']);
//$arquivo = $_POST['arquivo'];
$mat_autor = $_POST['mat_autor'];

include_once"../../func/log.php";

include '../vendor/autoload.php';

include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


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
			
			if($row[0]<>'Tipo Solicitação'){
				$tipo = utf8_decode($row[0]);
				$placa = utf8_decode($row[1]);
				$statusveic = $row[2];
				$dataabertura = $row[3];
				$atualizadoem = $row[4];
				$solicitante = utf8_decode($row[5]);
				$status = utf8_decode($row[6]);
				$etapa = utf8_decode($row[7]);
				$dataocorrencia = $row[8];
				$modelo = utf8_decode($row[9]);
				$km = $row[10];
				$fornecedorman = utf8_decode($row[11]);
				$descricao = utf8_decode($row[12]);
				$ccusto = utf8_decode($row[13]);
				$condutor = utf8_decode($row[14]);
				$oficina = utf8_decode($row[15]);
				$dataagendamento = $row[16];
				$prevsaida = $row[17];
				$dataentrada = $row[18];
				$dataretirada = $row[19];
				$tipopagamento = utf8_decode($row[20]);
				$reembolsoaprov = utf8_decode($row[21]);
				$valoreemb = $row[22];
				$valoroficina = $row[23];
				$valordesconto = $row[24];
				$valormobra = $row[25];
				$valormaterial = $row[26];
				$valortransp = $row[27];
				$valoroutros = $row[28];
				$valordescontar = $row[29];
				$datavencimento = $row[30];
				$datapagamento = $row[31];
				$formapagamento = utf8_decode($row[32]);
				$condpagamento = utf8_decode($row[33]);
				$numparcelas = $row[34];
				$valorparc = $row[35];
				$dataprimparc = $row[36];
				$protocolo = $row[37];
				$dataconclusao = $row[38];
				$placaanterior = $row[39];

				if(strpos($condutor,"'") !== false){
				  $condutor=substr($condutor, 1);
				}

				if(strpos($km,"'") !== false){
				  $km=substr($km, 1);
				}

				if(strpos($km,".") !== false){
					$km =str_replace(".","", $km);
				}
				

				//tratando datas
				if (!empty($dataabertura)){

					if(strpos($dataabertura, " ") !== false){
						$dataabertura1 = explode(" ", $dataabertura);
						$dataabertura2 = explode("/", $dataabertura1[0]);
						$anoA =$dataabertura2[2];
						$mesA = $dataabertura2[0];
						$diaA = $dataabertura2[1];

						if(strlen($mesA)==1){
							$mesA = "0".$mesA;
						}

						if(strlen($diaA)==1){
							$diaA = "0".$diaA;
						}
						
						$dataaberturaf = $anoA."-".$mesA."-".$diaA;
					}else{
						$dataabertura2 = explode("/", $dataabertura);
						$anoA =$dataabertura2[2];
						$mesA = $dataabertura2[0];
						$diaA = $dataabertura2[1];

						if(strlen($mesA)==1){
							$mesA = "0".$mesA;
						}

						if(strlen($diaA)==1){
							$diaA = "0".$diaA;
						}
						
						$dataaberturaf = $anoA."-".$mesA."-".$diaA;
					}
					
				} else {
					$dataaberturaf = $dataabertura;
				}


				if (!empty($atualizadoem)){
					
					if(strpos($atualizadoem, " ") !== false){
						$atualizadoem1 = explode(" ", $atualizadoem);
						$atualizadoem2 = explode("/", $atualizadoem1[0]);
						$anoL =$atualizadoem2[2];
						$mesL = $atualizadoem2[1];
						$diaL=$atualizadoem2[0];

						if(strlen($mesL)==1){
							$mesL = "0".$mesL;
						}

						if(strlen($diaL)==1){
							$diaL = "0".$diaL;
						}

						$atualizadoemf = $anoL."-".$mesL."-".$diaL;
					}else{
						$atualizadoem2 = explode("/", $atualizadoem);
						$anoL =$atualizadoem2[2];
						$mesL = $atualizadoem2[1];
						$diaL=$atualizadoem2[0];						

						if(strlen($mesL)==1){
							$mesL = "0".$mesL;
						}

						if(strlen($diaL)==1){
							$diaL = "0".$diaL;
						}

						$atualizadoemf = $anoL."-".$mesL."-".$diaL." ".$atualizadoem1[1];
						
					}

				} else {
					$atualizadoemf=$atualizadoem;
				}

				

				if(!empty($dataocorrencia)){

					if(strpos($dataocorrencia, " ") !== false){
						$dataocorrencia1 = explode(" ", $dataocorrencia);
						$dataocorrencia2 = explode("/", $dataocorrencia1[0]);
						$anoE =$dataocorrencia2[2];
						$mesE = $dataocorrencia2[0];
						$diaE=$dataocorrencia2[1];

						if(strlen($mesE)==1){
							$mesE = "0".$mesE;
						}

						if(strlen($diaE)==1){
							$diaE = "0".$diaE;
						}
						
						$dataocorrenciaf = $anoE."-".$mesE."-".$diaE;

					}else{
						$dataocorrencia2 = explode("/", $dataocorrencia);
						$anoE =$dataocorrencia2[2];
						$mesE = $dataocorrencia2[0];
						$diaE=$dataocorrencia2[1];

						if(strlen($mesE)==1){
							$mesE = "0".$mesE;
						}

						if(strlen($diaE)==1){
							$diaE = "0".$diaE;
						}
						
						$dataocorrenciaf = $anoE."-".$mesE."-".$diaE;

					}
				} else {
					$dataocorrenciaf=$dataocorrencia;
				}

				if(!empty($dataagendamento)){
					if(strpos($dataagendamento, " ") !== false){
						$dataagendamento1 = explode(" ", $dataagendamento);
						$dataagendamento2 = explode("/", $dataagendamento1[0]);
						$anoR =$dataagendamento2[2];
						$mesR = $dataagendamento2[0];
						$diaR=$dataagendamento2[1];

						if(strlen($mesR)==1){
							$mesR = "0".$mesR;
						}

						if(strlen($diaR)==1){
							$diaR = "0".$diaR;
						}


						$dataagendamentof = $anoR."-".$mesR."-".$diaR;
					}else{
						$dataagendamento2 = explode("/", $dataagendamento);
						$anoR =$dataagendamento2[2];
						$mesR = $dataagendamento2[0];
						$diaR=$dataagendamento2[1];

						if(strlen($mesR)==1){
							$mesR = "0".$mesR;
						}

						if(strlen($diaR)==1){
							$diaR = "0".$diaR;
						}

						$dataagendamentof = $anoR."-".$mesR."-".$diaR;
					}
				} else {
					$dataagendamentof = $dataagendamento;
				}



				if(!empty($prevsaida)){
					if(strpos($prevsaida, " ") !== false){
						$prevsaida1 = explode(" ", $prevsaida);
						$prevsaida2 = explode("/",$prevsaida1[0]);
						$anoV = $prevsaida2[2];
						$mesV =$prevsaida2[0];
						$diaV = $prevsaida2[1];

						if(strlen($mesV)==1){
							$mesV = "0".$mesV;
						}

						if(strlen($diaV)==1){
							$diaV = "0".$diaV;
						}
						
						$prevsaidaf = $anoV."-".$mesV."-".$diaV;
					}else{

						$prevsaida2 = explode("/",$prevsaida);
						$anoV = $prevsaida2[2];
						$mesV =$prevsaida2[0];
						$diaV = $prevsaida2[1];

						if(strlen($mesV)==1){
							$mesV = "0".$mesV;
						}

						if(strlen($diaV)==1){
							$diaV = "0".$diaV;
						}
						
						$prevsaidaf = $anoV."-".$mesV."-".$diaV;

					}
				} else {
					$prevsaidaf =$prevsaida;
				}


				if(!empty($dataentrada)){
					if(strpos($dataentrada, " ") !== false){
						$dataentrada1 = explode(" ", $dataentrada);	
						$dataentrada2 = explode("/", $dataentrada1[0]);
						$anoD =$dataentrada2[2];
						$mesD = $dataentrada2[0];
						$diaD=$dataentrada2[1];

						if(strlen($mesD)==1){
							$mesD = "0".$mesD;
						}

						if(strlen($diaD)==1){
							$diaD = "0".$diaD;
						}
						
						$dataentradaf = $anoD."-".$mesD."-".$diaD;
					}else{
						$dataentrada2 = explode("/", $dataentrada);
						$anoD =$dataentrada2[2];
						$mesD = $dataentrada2[0];
						$diaD=$dataentrada2[1];

						if(strlen($mesD)==1){
							$mesD = "0".$mesD;
						}

						if(strlen($diaD)==1){
							$diaD = "0".$diaD;
						}

						$dataentradaf = $anoD."-".$mesD."-".$diaD;

					}

				} else {
					$dataentradaf = $dataentrada;
				}

				//print"$dataentradaf<hr>";


				if(!empty($dataretirada)){

					if(strpos($dataentrada, " ") !== false){

						$dataretirada1= explode(" ", $dataretirada);
						$dataretirada2 = explode("/", $dataretirada1[0]);
						$anoRA =$dataretirada2[2];
						$mesRA = $dataretirada2[0];
						$diaRA=$dataretirada2[1];

						if(strlen($mesRA)==1){
							$mesRA = "0".$mesRA;
						}

						if(strlen($diaRA)==1){
							$diaRA = "0".$diaRA;
						}
							
						$dataretiradaf = $anoRA."-".$mesRA."-".$diaRA;
					}else{

						$dataretirada2 = explode("/", $dataretirada);
						$anoRA =$dataretirada2[2];
						$mesRA = $dataretirada2[0];
						$diaRA=$dataretirada2[1];

						if(strlen($mesRA)==1){
							$mesRA = "0".$mesRA;
						}

						if(strlen($diaRA)==1){
							$diaRA = "0".$diaRA;
						}
							
						$dataretiradaf = $anoRA."-".$mesRA."-".$diaRA;

					}
				} else {
					$dataretiradaf = $dataretirada;
				}



				if(!empty($datavencimento)){
					if(strpos($datavencimento, " ") !== false){
						$datavencimento1 = explode(" ", $datavencimento);
						$datavencimento2 = explode("/", $datavencimento1[0]);
						$anoRE =$datavencimento2[2];
						$mesRE = $datavencimento2[0];
						$diaRE=$datavencimento2[1];

						if(strlen($mesRE)==1){
							$mesRE = "0".$mesRE;
						}

						if(strlen($diaRE)==1){
							$diaRE = "0".$diaRE;
						}

						$datavencimentof = $anoRE."-".$mesRE."-".$diaRE;
					}else{
						$datavencimento2 = explode("/", $datavencimento);
						$anoRE =$datavencimento2[2];
						$mesRE = $datavencimento2[0];
						$diaRE=$datavencimento2[1];

						if(strlen($mesRE)==1){
							$mesRE = "0".$mesRE;
						}

						if(strlen($diaRE)==1){
							$diaRE = "0".$diaRE;
						}

						$datavencimentof = $anoRE."-".$mesRE."-".$diaRE;
					}
				} else {
					$datavencimentof = $datavencimento;
				}



				if(!empty($datapagamento)){
					if(strpos($datapagamento, " ") !== false){

						$datapagamento1 = explode(" ", $datapagamento);
						$datapagamento2 = explode("/", $datapagamento1[0]);
						$anoC =$datapagamento2[2];
						$mesC = $datapagamento2[0];
						$diaC=$datapagamento2[1];

						if(strlen($mesC)==1){
							$mesC = "0".$mesC;
						}

						if(strlen($diaC)==1){
							$diaC = "0".$diaC;
						}

						$datapagamentof = $anoC."-".$mesC."-".$diaC;
					}else{
						$datapagamento2 = explode("/", $datapagamento);
						$anoC =$datapagamento2[2];
						$mesC = $datapagamento2[0];
						$diaC=$datapagamento2[1];

						if(strlen($mesC)==1){
							$mesC = "0".$mesC;
						}

						if(strlen($diaC)==1){
							$diaC = "0".$diaC;
						}

						$datapagamentof = $anoC."-".$mesC."-".$diaC;

					}
				}else{
					$datapagamentof = $datapagamento;
				}



				if(!empty($dataprimparc)){
					$dataprimparc1 = explode(" ", $dataprimparc);
					$dataprimparc2 = explode("/", $dataprimparc1[0]);
					$anoC =$dataprimparc2[2];
					$mesP = $dataprimparc2[1];
					$anoP= $dataprimparc2[0];

					$dataprimparcf = $anoB."-".$mesB."-".$diaB." ".$dataprimparc1[1];
				} else{
					$dataprimparcf = $dataprimparc;
				}

				if(!empty($dataconclusao)){
					$dataconclusao1 = explode(" ", $dataconclusao);
					$dataconclusao2 = explode("/", $dataconclusao1[0]);
					$anoG =$dataconclusao2[2];
					$mesG = $dataconclusao2[1];
					$diaG= $dataconclusao2[0];

					$dataconclusaof = $anoG."-".$mesG."-".$diaG." ".$dataconclusao1[1];
				} else{
					$dataconclusaof = $dataconclusao;
				}

				if(strpos($oficina, "'") !== false){
					$oficina=str_replace("'", " ", $oficina);
				}

				//formatando valores
				if(strpos($valoreemb, ",") !== false){
					$valoreemb=str_replace(",", ".", $valoreemb);
				}

				if(strpos($valoroficina, ",") !== false){
					$valoroficina=str_replace(",", ".", $valoroficina);
				}

				if(strpos($valordesconto, ",") !== false){
					$valordesconto=str_replace(",", ".", $valordesconto);
				}

				if(strpos($valormobra, ",") !== false){
					$valormobra=str_replace(",", ".", $valormobra);
				}

				if(strpos($valormaterial, ",") !== false){
					$valormaterial=str_replace(",", ".", $valormaterial);
				}

				if(strpos($valortransp, ",") !== false){
					$valortransp=str_replace(",", ".", $valortransp);
				}

				if(strpos($valordescontar, ",") !== false){
					$valordescontar=str_replace(",", ".", $valordescontar);
				}

				if(strpos($valoroutros, ",") !== false){
					$valoroutros=str_replace(",", ".", $valoroutros);
				}

				if(strpos($valorparc, ",") !== false){
					$valorparc=str_replace(",", ".", $valorparc);
				}

				//tratando binarios
				if($reembolsoaprov == 'SIM' || $reembolsoaprov == 'sim'){
					$reembolsoaprov = '1';
				}	elseif (utf8_encode($reembolsoaprov) == 'NÃO' || utf8_encode($reembolsoaprov) == 'não') {
					$reembolsoaprov='0';
				} elseif($reembolsoaprov == 'NAO' || $reembolsoaprov == 'nao') {
					$reembolsoaprov='0';
				}

				//tratando status
				if($status == utf8_decode('CONCLUÍDO')){
					$status = 'CONCLUIDO';
				}

				//tratando protocolo
				$protocolo = substr($protocolo,1);



				//tratando statusveic idtbastatusvel, status
				$sql2="SELECT * FROM bdfrota.tbastatusvel WHERE status like '%$status%'";
				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
					$statusveic=$row2['idtbastatusvel'];


				//verificando se a manutenção já está cadastrada
				$sql3="SELECT * FROM bdfrota.tbmanprev WHERE placa='$placa' AND data='$dataaberturaf' AND tipo='$tipo'";
				$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
				$linhas3=mysqli_num_rows($resultado3);

				/*if($statusveic == '3'){
					$status = '0';
				}else{*/
					$status = 'ABERTO';
				//}

				if($linhas3<=0){//retorna 296ln
					//print $reembolsoaprov; break;

					$sql = "INSERT INTO bdfrota.tbmanprev(placa, data, tipo, hodometro, statusveic, atualizadoem, solicitante, status, etapa, dataocorrencia, modelo, Km, fornman, descricao, ccusto, condutor, oficina, dataagendamento, prevsaida, dataentrada, dataretirada, tipopagamento, reembolsoaprov, valorreembolso, valoroficina, valordesconto, valormaoobra, valormaterial, valortransp, outrosvalor, descontarcond, datavencimento, datapagamento, formapagam, condicaopag, numparc, valorparcela, dataprimparc, protocolo, dataconclusao, placaanterior)
					
					VALUES ('$placa', '$dataaberturaf', '$tipo', '$km', '$statusveic', '$atualizadoemf', '$solicitante', '$status', '$etapa', '$dataocorrenciaf', '$modelo', '$km', '$fornecedorman', '$descricao', '$ccusto', '$condutor', '$oficina','$dataagendamentof', '$prevsaidaf', '$dataentradaf', '$dataretiradaf', '$tipopagamento', '$reembolsoaprov', '$valoreemb', '$valoroficina', '$valordesconto', '$valormobra', '$valormaterial', '$valortransp', '$valoroutros', '$valordescontar', '$datavencimentof', '$datapagamentof', '$formapagamento', '$condpagamento', '$numparcelas', '$valorparc', '$dataprimparcf', '$protocolo', '$dataconclusaof', '$placaanterior')";

					$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

					//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
					$acao='Enviou planilha manutencao';
					$tipo = 'cadastro';
					$logresult = enviarlognovo($hoje, $acao, '', $vistoriador, $tipo, '');

					//echo "- $sql <hr>";
				} /*else{//retorna 4836
					$sql = "UPDATE bdfrota.tbmanprev SET data='$dataaberturaf', tipo='$tipo', hodometro='$km', statusveic='$statusveic', atualizadoem='$atualizadoemf', solicitante='$solicitante', status='$status', etapa='$etapa', dataocorrencia='$dataocorrenciaf', modelo='$modelo', Km='$km', fornman='$fornecedorman', descricao='$descricao', ccusto='$ccusto', condutor='$condutor', oficina='$oficina', dataagendamento='$dataagendamentof', prevsaida='$prevsaidaf', dataentrada='$dataentradaf', dataretirada='$dataretiradaf', tipopagamento='$tipopagamento', reembolsoaprov='$reembolsoaprov', valorreembolso='$valoreemb', valoroficina='$valoroficina', valordesconto='$valordesconto', valormaoobra='$valormobra', valormaterial='$valormaterial', valortransp='$valortransp', outrosvalor='$valoroutros', descontarcond='$valordescontar', datavencimento='$datavencimentof', datapagamento='$datapagamentof', formapagam='$formapagamento', condicaopag='$condpagamento', numparc='$numparcelas', valorparcela='$valorparc', dataprimparc='$dataprimparcf', protocolo='$protocolo', dataconclusao='$dataconclusaof', placaanterior='$placaanterior' WHERE placa='$placa';";

					//$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

					echo "? $sql<hr>";
					
				}*/

				//print "$dataocorrenciaf<hr>";

				//print "- TIPO MANUTENÇÃO: $tipo <br> PLACA: $placa <br> STATUS DO VEIC: $statusveic<br> DATA DE ABERT: $dataaberturaf <br> ATUALIZADO EM: $atualizadoemf <br> SOLICITANTE: $solicitante <br> STATUS: $status <br> ETAPA: $etapa <br> DATA OCORRENCIA: $dataocorrenciaf <br> MODELO: $modelo <br> KM: $km <br> FORNECEDOR MANUTENÇÃO: $fornecedorman <br> DESCRIÇÃO: $descricao <br> CCUSTO: $ccusto <br> CONDUTOR: $condutor <br> OFICINA: $oficina <br> DATA AGEND: $dataagendamentof <br> PREV SAIDA: $prevsaidaf <br> DATA ENTR: $dataentradaf <br> DATA RETIRADA: $dataretiradaf <br> TIPO PAG: $tipopagamento <br> REEMBOLSO: $reembolsoaprov <br> VALORREEMB: $valoreemb <br> VALOR OFICINA: $valoroficina <br> VALDESC: $valordesconto <br> VALMOBRA: $valormobra <br> VAL MATERIAL: $valormaterial <br> VAL TRANSP: $valortransp <br> VAL DESCONTAR: $valordescontar <br> DATA VENC: $datavencimentof <br> DATA PAGAM: $datapagamentof <br> FORMA PG: $formapagamento <br> CONDIÇÃO PAG: $condpagamento <br> NUM PARCELAS: $numparcelas <br> DATA PRIM PARC: $dataprimparcf <br> protocolo: $protocolo <br> DATA CONCL: $dataconclusaof <br> PLACA ANTERIOR: $placaanterior <br> VALOR OUTROS: $valoroutros <br> VALOR PARCELA: $valorparc <hr>";
				
				
				}
			 
			}
		}					
	}	

$nome = "planilhamanutencao";
// Nome do diretório para onde mover
$dir1 = '../docs/';

$_UP['pasta'] = '../docs/';
 
// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
 
// Array com as extensões permitidas
$_UP['extensoes'] = array('xls', 'xlsx');
 
// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
$_UP['renomeia'] = false;
 
// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
 
// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
if ($_FILES['arquivo']['error'] != 0) {
die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
exit; // Para a execução do script
}
 
// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
 
// Faz a verificação da extensão do arquivo
$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
if (array_search($extensao, $_UP['extensoes']) === false) {
echo "Por favor, envie arquivos com as seguintes extensões: xls ou xlsx.";
}
 
// Faz a verificação do tamanho do arquivo
else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
}
 
// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
else {
// Primeiro verifica se deve trocar o nome do arquivo
if ($_UP['renomeia'] == true) {
// Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
$nome_final = time()."-".$nome.'.xls';
} else {
// Mantém o nome original do arquivo
$nome_final = $nome."-".$hoje.".".$extensao;
}
 
// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {


// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
print "<script language='javascript' type='text/javascript'> window.alert('Upload feito com sucesso!'); </script>
       <script language='javascript' type='text/javascript'> window.location=\"../listagemmanutencaoaberta.php\";</script>";
} else {
// Não foi possível fazer o upload, provavelmente a pasta está incorreta
print "<script language='javascript' type='text/javascript'> window.alert('Não foi possível fazer o upload.'); </script>
<script language='javascript' type='text/javascript'> window.location=\"../listagemmanutencaoaberta.php\";</script>";

}
 
}



?>	