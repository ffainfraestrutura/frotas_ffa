<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');
$hoje1 = str_replace(" ", "", $hoje);
$hoje1 = str_replace("-", "", $hoje1);
$hoje1 = str_replace(":","", $hoje1);

include_once"../../func/log3.php";
include("../../conecta03.php");
include '../vendor/autoload.php';

$matr_autor = $_POST['matr_autor'];

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "manutencao";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/manutencao/';

$file_array = explode(".", $_FILES["arquivo"]["name"]);
$file_extension = end($file_array);
$arquivo = "$nome-$hoje1.$file_extension";

//print $tipoposse."<br>".$locador."<br>".$arquivo."<br>".$dirfinal;

if($_FILES["arquivo"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	$file_array = explode(".", $_FILES["arquivo"]["name"]);
	$file_extension = end($file_array);

	if(in_array($file_extension, $allowed_extension)){
		$reader = IOFactory::createReader('Xlsx','Xls');
		$spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);
		$data = $spreadsheet->getActiveSheet()->toArray();

		foreach($data as $row){
			
			if($row[0]<>'Tipo Solicitação'){
				$tipo = $row[0];
				$placa = $row[1];
				
				$dataabertura = $row[2];
				$atualizadoem = $row[3];
				$solicitante = $row[4];
				$status = $row[5];
				
				$dataocorrencia = $row[6];
				$modelo = $row[7];
				$km = $row[8];
				$fornecedorman = $row[9];
				$descricao = $row[10];
				$ccusto = $row[11];
				
				$oficina = $row[12];
				$dataagendamento = $row[13];
				$prevsaida = $row[14];
				$dataentrada = $row[15];
				$dataretirada = $row[16];
				$tipopagamento = $row[17];
				$reembolsoaprov = $row[18];
				$valoreemb = $row[19];
				$valoroficina = $row[20];
				$valordesconto = $row[21];
				$valormobra = $row[22];
				$valormaterial = $row[23];
				$valortransp = $row[24];
				$valoroutros = $row[25];
				$valordescontar = $row[26];
				$datavencimento = $row[27];
				$datapagamento = $row[28];
				$formapagamento = $row[29];
				$condpagamento = $row[30];
				$numparcelas = $row[31];
				$valorparc = $row[32];
				$dataprimparc = $row[33];
				$protocolo = $row[34];
				$dataconclusao = $row[35];
				$placaanterior = $row[36];			

				//tratando datas
				if (!empty($dataabertura)){
					
					$dataabertura2 = explode("/", $dataabertura);
					$anoA =$dataabertura2[2];
					$mesA = $dataabertura2[1];
					$diaA = $dataabertura2[0];
					
					$dataaberturaf = $anoA."-".$mesA."-".$diaA;
				} else {
					$dataaberturaf = $dataabertura;
				}

				if (!empty($atualizadoem)){
					
					$atualizadoem2 = explode("/", $atualizadoem);
					$anoL =$atualizadoem2[2];
					$mesL = $atualizadoem2[1];
					$diaL=$atualizadoem2[0];

					$atualizadoemf = $anoL."-".$mesL."-".$diaL." ".$atualizadoem1[1];
				} else {
					$atualizadoemf=$atualizadoem;
				}

				if(!empty($dataocorrencia)){
					
					$dataocorrencia2 = explode("/", $dataocorrencia);
					$anoE =$dataocorrencia2[2];
					$mesE = $dataocorrencia2[1];
					$diaE=$dataocorrencia2[0];
					
					$dataocorrenciaf = $anoE."-".$mesE."-".$diaE;
				} else {
					$dataocorrenciaf=$dataocorrencia;
				}

				if(!empty($dataagendamento)){
					
					$dataagendamento2 = explode("/", $dataagendamento);
					$anoR =$dataagendamento2[2];
					$mesR = $dataagendamento2[1];
					$diaR=$dataagendamento2[0];

					$dataagendamentof = $anoR."-".$mesR."-".$diaR;
				} else {
					$dataagendamentof = $dataagendamento;
				}

				if(!empty($prevsaida)){
					
					$prevsaida2 = explode("/",$prevsaida);
					$anoV = $prevsaida2[2];
					$mesV =$prevsaida2[1];
					$diaV = $prevsaida2[0];
					
					$prevsaidaf = $anoV."-".$mesV."-".$diaV;
				} else {
					$prevsaidaf =$prevsaida;
				}

				if(!empty($dataentrada)){
						
					$dataentrada2 = explode("/", $dataentrada);
					$anoD =$dataentrada2[2];
					$mesD = $dataentrada2[1];
					$diaD=$dataentrada2[0];
					
					$dataentradaf = $anoD."-".$mesD."-".$diaD;
				} else {
					$dataentradaf = $dataentrada;
				}

				if(!empty($dataretirada)){
					
					$dataretirada2 = explode("/", $dataretirada);
					$anoRA =$dataretirada2[2];
					$mesRA = $dataretirada2[1];
					$diaRA=$dataretirada2[0];
						
					$dataretiradaf = $anoRA."-".$mesRA."-".$diaRA;
				} else {
					$dataretiradaf = $dataretirada;
				}

				if(!empty($datavencimento)){
					
					$datavencimento2 = explode("/", $datavencimento);
					$anoRE =$datavencimento2[2];
					$mesRE = $datavencimento2[1];
					$diaRE=$datavencimento2[0];

					$datavencimentof = $anoRE."-".$mesRE."-".$diaRE;
				} else {
					$datavencimentof = $datavencimento;
				}

				if(!empty($datapagamento)){
					
					$datapagamento2 = explode("/", $datapagamento);
					$anoC =$datapagamento[2];
					$mesC = $datapagamento[1];
					$diaC=$datapagamento[0];

					$datapagamentof = $anoC."-".$mesC."-".$diaC;
				}else{
					$datapagamentof = $datapagamento;
				}

				if(!empty($dataprimparc)){
					
					$dataprimparc2 = explode("/", $dataprimparc1[0]);
					$anoC =$dataprimparc2[2];
					$mesP = $dataprimparc2[1];
					$anoP= $dataprimparc2[0];

					$dataprimparcf = $anoB."-".$mesB."-".$diaB;
				} else{
					$dataprimparcf = $dataprimparc;
				}

				if(!empty($dataconclusao)){
					
					$dataconclusao2 = explode("/", $dataconclusao);
					$anoG =$dataconclusao2[2];
					$mesG = $dataconclusao2[1];
					$diaG= $dataconclusao2[0];

					$dataconclusaof = $anoG."-".$mesG."-".$diaG;
				} else{
					$dataconclusaof = $dataconclusao;
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

				if(strpos($km, "'") !== false){
					$km=str_replace("'", "", $km);
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
				if($status == 'CONCLUÍDO'){
					$status = 'CONCLUIDO';
				}

				//tratando protocolo
				$protocolo = substr($protocolo,1);

				$sql3 = "SELECT MAX(idtbmanprev) AS idtbmanprev FROM bdfrota.tbmanprev WHERE placa='$placa'; ";
				$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
				$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
					$maxid =$row3['idtbmanprev'];

				$sql4 = "SELECT tipo, data, placa FROM bdfrota.tbmanprev WHERE idtbmanprev='$maxid';";
				$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
				$row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
					$tipobd =$row4['tipo'];
					$databd =$row4['data'];
					$placabd = $row4['placa'];

					$databd1 = explode(" ", $databd);

				if($placabd==$placa && $tipo == $tipobd && $databd1[0] == $dataaberturaf){
					//atualiza
					$sql = "UPDATE bdfrota.tbmanprev SET placa='$placa', data='$dataaberturaf', tipo='$tipo', hodometro='$km', atualizadoem='$atualizadoemf', solicitante='$solicitante', status='$status', dataocorrencia='$dataocorrenciaf', modelo='$modelo', Km='$km', fornman='$fornecedorman', descricao='$descricao', ccusto='$ccusto', oficina='$oficina', dataagendamento='$dataagendamentof', prevsaida='$prevsaidaf', dataentrada='$dataentradaf', dataretirada='$dataretiradaf', tipopagamento='$tipopagamento', reembolsoaprov='$reembolsoaprov', valorreembolso='$valoreemb', valoroficina='$valoroficina', valordesconto='$valordesconto', valormaoobra='$valormobra', valormaterial='$valormaterial', valortransp='$valortransp', outrosvalor='$valoroutros', descontarcond='$valordescontar', datavencimento='$datavencimentof', datapagamento='$datapagamentof', formapagam='$formapagamento', condicaopag='$condpagamento', numparc='$numparcelas', valorparcela='$valorparc', dataprimparc='$dataprimparcf', protocolo='$protocolo', dataconclusao='$dataconclusaof', placaanterior='$placaanterior'		
						WHERE idtbmanprev='$maxid';";

					$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
				}else{
					$sql = "INSERT INTO bdfrota.tbmanprev(placa, data, tipo, hodometro, atualizadoem, solicitante, status, dataocorrencia, modelo, Km, fornman, descricao, ccusto, oficina, dataagendamento, prevsaida, dataentrada, dataretirada, tipopagamento, reembolsoaprov, valorreembolso, valoroficina, valordesconto, valormaoobra, valormaterial, valortransp, outrosvalor, descontarcond, datavencimento, datapagamento, formapagam, condicaopag, numparc, valorparcela, dataprimparc, protocolo, dataconclusao, placaanterior)				
					VALUES ('$placa', '$dataaberturaf', '$tipo', '$km', '$atualizadoemf', '$solicitante', '$status', '$dataocorrenciaf', '$modelo', '$km', '$fornecedorman', '$descricao', '$ccusto', '$oficina','$dataagendamentof', '$prevsaidaf', '$dataentradaf', '$dataretiradaf', '$tipopagamento', '$reembolsoaprov', '$valoreemb', '$valoroficina', '$valordesconto', '$valormobra', '$valormaterial', '$valortransp', '$valoroutros', '$valordescontar', '$datavencimentof', '$datapagamentof', '$formapagamento', '$condpagamento', '$numparcelas', '$valorparc', '$dataprimparcf', '$protocolo', '$dataconclusaof', '$placaanterior')";

					$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));	
				}							

				//echo "- $sql <br>- $tipo == $tipobd && $databd1[0]== $dataaberturaf.<hr> $km";

				
				
			}
			 
		}
	}

	//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
	$log = new Log($con);
	$acao='Importou man em lote';
	$tipo = 'cadastro'; // tipo antigo atualizou
	$logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');

	// Depois verifica se é possível mover o arquivo para a pasta escolhida
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
		// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
		
		print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso!!!');window.close();</script>";
		
		//window.location=\"../listagemveiculos.php\"

	} else {
		// Não foi possível fazer o upload, provavelmente a pasta está incorreta
		print "<script language='javascript' type='text/javascript'>alert('Não foi possível enviar o arquivo, tente novamente.');window.close();</script>";
		
		//echo "Não foi possível enviar o arquivo, tente novamente<br><a href='$caminho'>caminho</a>";
	}			
}	





?>	