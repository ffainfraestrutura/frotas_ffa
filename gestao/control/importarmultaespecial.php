<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s'); 

$_UP['pasta'] = '../docs/'; 

//$tipoposse = utf8_decode($_POST['tipoposse']);
//$locador = utf8_decode($_POST['locador']);
$matr_autor = $_POST['matr_autor'];
//$arquivo = $_POST['arquivo'];

$arquivo = $_FILES["arquivo"]["name"];

include '../vendor/autoload.php';

include_once"../../func/log.php";
include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if($_FILES["arquivo"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	$file_array = explode(".", $_FILES["arquivo"]["name"]);
	$file_extension = end($file_array);
	if(in_array($file_extension, $allowed_extension))
	{
		$reader = IOFactory::createReader('Xlsx');
		$spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);

		$data = $spreadsheet->getActiveSheet()->toArray();
	    		

		foreach($data as $row){			
			if($row[0]<>'PLACA'){
				$placa = $row[0];				
				$datahoracadastro = $row[1];
				$datahorainfracao = $row[2];
				$autoinfracao = $row[3];				
				$autoinfracao2 = utf8_decode($row[4]);
				$originario = $row[5];
				$orgao = $row[6];												
				$endereco = utf8_decode($row[7]);
				$municipio = utf8_decode($row[8]);
				$uf = utf8_decode($row[9]);
				$codigom = utf8_decode($row[10]);
				$descricaoinfra = $row[11];
				$valor = $row[12];
				$datalimitecond = $row[13];			

				$nomecond = utf8_decode($row[14]);
				
				$pontos =  $row[15];

				$gravidade = utf8_decode($row[16]);
				$gravidadef = $row[16];

				$etapa = $row[17];
				$tramite = $row[18];

				if(!empty($codigom)){
					if(strpos($codigom,"-") !== false){
						$codigom = str_replace("-","",$codigom);
					}
				}

				//tratando datas
				if (!empty($datahoracadastro)){
					if(strpos($datahoracadastro, " ") !== false){
						$datahoracadastro1 = explode(" ", $datahoracadastro);

						$datahoracadastro2 = explode("/", $datahoracadastro1[0]);
						$anoI =$datahoracadastro2[2];
						$mesI = $datahoracadastro2[1];
						$diaI = $datahoracadastro2[0];

						if(strlen($mesI) === 1){
							$mesI = "0".$mesI;
						}

						if(strlen($diaI) === 1){
							$diaI = "0".$diaI;
						}

						if($mesI>12){
							$datahoracadastrof = $anoI."-".$diaI."-".$mesI." ".$horainfra;
						}else{
							$datahoracadastrof = $anoI."-".$mesI."-".$diaI." ".$horainfra;
						}
						
						//$datahoracadastrof = $anoI."-".$mesI."-".$diaI." ".$datahoracadastro1[1];
					}elseif(strpos($datahoracadastro, "/") !== false){
						$datahoracadastro2 = explode("/", $datahoracadastro);
						$anoI =$datahoracadastro2[2];
						$mesI = $datahoracadastro2[1];
						$diaI = $datahoracadastro2[0];

						if(strlen($mesI) === 1){
							$mesI = "0".$mesI;
						}

						if(strlen($diaI) === 1){
							$diaI = "0".$diaI;
						}

						if($mesI>12){
							$datahoracadastrof = $anoI."-".$diaI."-".$mesI." ".$horainfra;
						}else{
							$datahoracadastrof = $anoI."-".$mesI."-".$diaI." ".$horainfra;
						}
						
						//$datahoracadastrof = $anoI."-".$mesI."-".$diaI." 00:00:00";
					}

				} else {
					$datahoracadastrof = $datahoracadastro;
				}

				

				//$datahorainfracao
				if (!empty($datahorainfracao)){
					if(strpos($datahorainfracao, " ") !== false){
						$datahorainfracao1 = explode(" ", $datahorainfracao);

						$datahorainfracao2 = explode("/", $datahorainfracao1[0]);
						$anoI = $datahorainfracao2[2];
						$mesI = $datahorainfracao2[1];
						$diaI = $datahorainfracao2[0];

						if(strlen($mesI) === 1){
							$mesI = "0".$mesI;
						}

						if(strlen($diaI) === 1){
							$diaI = "0".$diaI;
						}

						if(strlen($datahorainfracao1[1])<=8){
							$horainfra = $datahorainfracao1[1].":00";
						}else{
							$horainfra = $datahorainfracao1[1];
						}

						if($mesI>12){
							$datahorainfracaof = $anoI."-".$diaI."-".$mesI." ".$horainfra;
						}else{
							$datahorainfracaof = $anoI."-".$mesI."-".$diaI." ".$horainfra;
						}
						
						//$datahorainfracaof = $anoI."-".$mesI."-".$diaI." ".$horainfra;

					}/*elseif(strpos($datahorainfracao, "/") !== false){						
						$datahorainfracao2 = explode("/", $datahorainfracao);
						$anoI = $datahorainfracao2[2];
						$mesI = $datahorainfracao2[1];
						$diaI = $datahorainfracao2[0];

						if(strlen($mesI) === 1){
							$mesI = "0".$mesI;
						}

						if(strlen($diaI) === 1){
							$diaI = "0".$diaI;
						}

						/*if(strlen($datahorainfracao1[1])<=8){
							$horainfra = $datahorainfracao1[1].":00";
						}else{
							$horainfra = $datahorainfracao1[1];
						}*
						$datahorainfracaof = $anoI."-".$mesI."-".$diaI." ".$horainfra;

					}*/
				} else {
					$datahorainfracaof = $datahorainfracao;
				}

				//$apCondDV
				if($datalimitecond != ''){

					$datalimitecond1 = explode("/", $datalimitecond);
					$anoV = $datalimitecond1[0];
					$mesV = $datalimitecond1[1];
					$diaV = $datalimitecond1[2];
					
					$datalimitecondf = $diaV."-".$mesV."-".$anoV;
				} else {
					//+30dias
					$datalimitecondf = date("Y-m-d H:i:s", strtotime($datahorainfracaof . "+30 days"));
				}

				$vencimentof = date("Y-m-d H:i:s", strtotime($datahorainfracaof . "+20 days"));

				//+25dias
				$datalimiteloc = date("Y-m-d H:i:s", strtotime($datahorainfracaof . "+25 days"));

				//etapas e tramites
				if($etapa == 'ABERTO'){
					
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status = 1;				
					
				}elseif($etapa == 'EM ABERTO - SEM INFORMAÇÃO'){
					
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status = 1;					
				}elseif($etapa == 'BAIXADO'){
					
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status = 1;
				}elseif($etapa == 'EM PROCESSO DE PAGAMENTO'){
					
					$tdp = 'Confirmar Desconto';
					$tfin = 'Fazer Pagamento';
					$tsup = '';
					$status=2;

				}elseif($etapa == 'PAGO'){
					
					$tdp = 'DP Finalizado';
					$tfin = 'Finalizado Financeiro';
					$tsup = '';
					$status = 5;
				}elseif($etapa == ''){
					
					$tdp = '';
					$tfin = '';
					$tsup = '';
					$status = 1;
					$etapa = 'EM ABERTO';
				}

				if($nomecond != ''){//se o condutor for informado
					$sqla = "SELECT max(idtbfuncionario) AS idfunc FROM bdaniel.tbfuncionario WHERE nome='$nomecond';";
					$resultadoa= mysqli_query($conexao, $sqla) or die(mysqli_error($conexao));
					$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
						$idfunc= utf8_decode($rowa['idfunc']);

					$sql2 = "SELECT nome, codfilial, codempresa, cpf, ccusto, matricula FROM bdaniel.tbfuncionario WHERE idtbfuncionario = '$idfunc'; ";
					$resultado2= mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
					$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
						$nomec = utf8_decode($row2['nome']);
						$codfilial = $row2['codfilial'];
						$codempresa = $row2['codempresa'];
						$cpf = $row2['cpf'];
						$ccusto = $row2['ccusto'];
						$matriculac = $row2['matricula'];
				}else{
					$sql = "SELECT nome, matricula FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND dataassoc<='$datahorainfracaof' AND (datadissoc>='$datahorainfracaof' OR datadissoc='0000-00-00 00:00:00');";
					$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
					$row=mysqli_fetch_array($resultado, MYSQLI_BOTH) ;
						$nomec = utf8_encode($row['nome']);
						$matriculac = $row['matricula'];
					
					$sqla = "SELECT max(idtbfuncionario) AS idfunc FROM bdaniel.tbfuncionario WHERE nome='$nomec';";
					$resultadoa= mysqli_query($conexao, $sqla) or die(mysqli_error($conexao));
					$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
						$idfunc= utf8_decode($rowa['idfunc']);

					$sql2 = "SELECT nome, codfilial, codempresa, cpf, ccusto, matricula FROM bdaniel.tbfuncionario WHERE idtbfuncionario='$idfunc';";
					$resultado2= mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
					$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
						$nomec = utf8_decode($row2['nome']);
						$codfilial = $row2['codfilial'];
						$codempresa = $row2['codempresa'];
						$cpf = $row2['cpf'];
						$ccusto = $row2['ccusto'];
						$matriculac = $row2['matricula'];

				}


				$sql3="SELECT nome FROM bdaniel.tbfilial WHERE codempresa='$codempresa' AND codfilial='$codfilial'; ";
				$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
				$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
					$filial=$row3['nome'];				 


				if($descricaoinfra == ''){
				$sql4="SELECT descricao  FROM bdfrota.tbacodmulta where codigomulta='$codigom' ";
				$resultado4=mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
				$row4=mysqli_fetch_array($resultado4, MYSQLI_BOTH);
					$descricaoinfra = $row4['descricao'];					
				}

				if(!empty($pontos)){
					$sql4="SELECT pontos FROM bdfrota.tbacodmulta where codigomulta='$codigom' ";
					$resultado4=mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
					$row4=mysqli_fetch_array($resultado4, MYSQLI_BOTH);
						$pontos = $row4['pontos'];
				}

				if(empty($gravidade)){

					$sql4="SELECT gravidade FROM bdfrota.tbacodmulta where codigomulta='$codigom' ";
					$resultado4=mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
					$row4=mysqli_fetch_array($resultado4, MYSQLI_BOTH);
						$gravidade = $row4['gravidade'];
				}elseif($gravidade == '-'){					
					
					$sql4="SELECT gravidade FROM bdfrota.tbacodmulta where codigomulta='$codigom' ";
					$resultado4=mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
					$row4=mysqli_fetch_array($resultado4, MYSQLI_BOTH);
						$gravidade = $row4['gravidade'];

				}

				/*if($gravidadef =='Infração gravíssima'){
					$gravidade = 'GRAVISSIMA';
				}elseif($gravidadef =='Infração grave'){
					$gravidade = 'GRAVE';
				}elseif($gravidadef =='Infração média'){
					$gravidade = 'MEDIA';
				}elseif($gravidadef =='Infração leve'){
					$gravidade = 'LEVE';
				}*/

				if($valor ==''){
					$sql5="SELECT valor FROM bdfrota.tbacodmulta where codigomulta='$codigom' ";
					$resultado5=mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
					$row5=mysqli_fetch_array($resultado5, MYSQLI_BOTH);
						$valor = $row5['valor'];
				}

				if(strpos($valor, "R$")!==false){
					$valor = substr($valor,2);
				}

				if(strpos($valor, ",")!==false){
					$valor = str_replace(",", ".", $valor);
					
				}

				//tratando autoinfracao
				if(strpos($autoinfracao, "'") !== false){
					$autoinfracao = substr($autoinfracao,1);
				}				

				$sql5 = "SELECT idlocador, ccusto FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
				$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH) ;
					$idlocador = $row5['idlocador'];
					$ccusto = $row5['ccusto'];
				
				$sql6 = "SELECT taxaadm FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
				$resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
				$row6 = mysqli_fetch_array($resultado4, MYSQLI_BOTH) ;
					$taxaadm = $row6['taxaadm'];

				if($taxaadm == '' || $taxaadm == null){
					if($idlocador == '2' || $idlocador == '9' || $idlocador == '16' || $idlocador == '17' || $idlocador == '19'){//movida
						$taxaadm = round((0.15*$valor),2);
					} elseif($idlocador == '3' || $idlocador == '4' || $idlocador == '14' || $idlocador == '15'){//localiza
						$taxaadm = '25.00';
					} elseif($idlocador == '6' || $idlocador == '8'){
						$taxaadm = round((0.2*$valor),2);
						
					}
				}

				/*formatando valores*/
				if(strpos($taxaadm, ",")!==false){
					$taxaadm = str_replace(",", ".", $taxaadm);
				}

				$valtotal =  ($valor+$taxaadm);
				if(strpos($valtotal, ",")!==false){
					$valtotal = str_replace(",", ".", $valtotal);
				}
				$valdesconto='0.00';
				$juros = '0.00';
					
				// AND placa='$placa'
				$sql11 = "SELECT autoinfracao FROM bdfrota.tbmulta WHERE autoinfracao = '$autoinfracao' ";
				$resultado11 = mysqli_query($conexao, $sql11) or die(mysqli_error($conexao));				
				$linhas = mysqli_num_rows($resultado11);

				if($linhas <= 0){

					$sql7 = "INSERT INTO bdfrota.tbmulta(placa, filial, ccusto, fornecedor, autoinfracao, datainfracao, datalimiteloc, vencimento, codigom, pontos, gravidade, valor, valdesconto, taxaadm, juros, valtotal, orgao, endereco, municipio, matriculac, nomec,  datalimitecond, descricaoinfra, valcomdesc, ufinfra, originario, grupo, datahoracadastro, autoinfracao2, aitfisico, situacao, etapa, finalnotificacao, idmultaexcel, faturamento, recurso, limiteregul, medicaoreal)
					VALUES ('$placa', '$filial', '$ccusto', '$idlocador', '$autoinfracao', '$datahorainfracaof', '$datalimiteloc', '$vencimentof', '$codigom', '$pontos', '$gravidade', '$valor', '$valdesconto', '$taxaadm', '$juros', '$valtotal', '$orgao', '$endereco', '$municipio', '$matriculac', '$nomec',  '$datalimitecondf', '$descricaoinfra', '$valcdesc', '$uf', '$originario', '$grupo','$datahoracadastrof', '$autoinfracao2','$aitfisico', '$situacao', '$etapa', '$finalnotificacao','$idmultaexcel','$faturamentof','$recurso', '$velreg','$velmedida');";			
					$resultado7 = mysqli_query($conexao, $sql7) or die(mysqli_error($conexao));

					$sql8="SELECT MAX(idtbmulta) AS idtbmulta FROM bdfrota.tbmulta WHERE autoinfracao='$autoinfracao' ";
					$resultado8 = mysqli_query($conexao, $sql8) or die(mysqli_error($conexao));
					$row8=mysqli_fetch_array($resultado8, MYSQLI_BOTH);
						$idtbmulta=$row8[0];

					//placa='$placa' AND
					$sql10="SELECT * FROM bdfrota.tbmovidatramite WHERE  autoinfra = '$autoinfracao';";
					$resultado10 = mysqli_query($conexao, $sql10) or die(mysqli_error($conexao));
					
					$linhas10 = mysqli_num_rows($resultado10);

					if($linhas10<=0){
						
						$sql9 = "INSERT INTO bdfrota.tbmovidatramite(placa, autoinfra, pontuacao, gravidade, apCondDV, dtinfra, dtvenc, valor, nome, matricula, cpf, dtcons, idmulta, dataenvioemail, tramite, tdp,tfin, tsup, status) VALUES ('$placa', '$autoinfracao', '$pontos', '$gravidade', '$datalimitecondf', '$datahorainfracaof', '$vencimentof', '$valtotal', '$nomec','$matriculac', '$cpf', '$hoje', '$idtbmulta','$dtemailenviadof', '$tramite', '$tdp','$tfin', '$tsup','$status');";					
						$resultado9 = mysqli_query($conexao, $sql9) or die(mysqli_error($conexao));
					}/*else{
						$sql9 = "UPDATE bdfrota.tbmovidatramite SET placa='$placa', autoinfra='$autoinfracao', pontuacao='$pontos', gravidade='$gravidade', apCondDV='$datalimitecondf', dtinfra='$datahorainfracaof', dtvenc='$vencimentof', valor='$valor', nome='$nomec', matricula='$matriculac', cpf='$cpf',  dtcons='$hoje', dataenvioemail='$dtemailenviadof', tramite='$tramite', tdp='$tdp',tfin='$tfin', tsup='$tsup', status='$status' WHERE placa='$placa' AND autoinfra = '$autoinfracao';";					
						//$resultado9 = mysqli_query($conexao, $sql9) or die(mysqli_error($conexao));	
					}*/
					print "- $sql7 <br> - $sql9 <hr>";
							
				}		

			}		
		}
	}					
}


// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
	// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
	//print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../listagemmultas.php\"</script>";
	print "Importado com sucesso sucesso!!!";
} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta
	echo "Não foi possível enviar o arquivo, tente novamente";
}


?>	