<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');

//$arquivo = $_POST['arquivo'];

include '../vendor/autoload.php';

include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "multas";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/';

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

			if($row[0]<>'Data Infração'){				
				$datainfracao = $row[0];
				$horainfracao = $row[1];
				$placa = $row[2];				
				$tipoinfracao = utf8_decode($row[3]);
				$autoinfracao = utf8_decode($row[4]);
				$originario = $row[5];
				$codigom = $row[6];
				$endereco = utf8_decode($row[7]);
				$vencimento = $row[8];
				$municipio = utf8_decode($row[9]);
				$uf = utf8_decode($row[10]);
				$orgao = utf8_decode($row[11]);

				//tratando datas
				if (!empty($datainfracao)){
					$datainfracao1 = explode(" ", $datainfracao);

					$datainfracao2 = explode("/", $datainfracao1[0]);
					$anoI =$datainfracao2[0];
					$mesI = $datainfracao2[1];
					$diaI=$datainfracao2[2];

					
					$datainfracaof = $diaI."-".$mesI."-".$anoI." ".$datainfracao1[1];
				} else {
					$datainfracaof = $datainfracao;
				}				
				

				if(!empty($vencimento)){
					$vencimento = explode("/", $vencimento);
					$anoV =$vencimento[0];
					$mesV = $vencimento[1];
					$diaV=$vencimento[2];
					
					$vencimentof = $diaV."-".$mesV."-".$anoV;
				} else {
					$vencimentof = $vencimento;
				}
				
				

				//formatando valores
				$valor=str_replace(",", ".", $valor);

				$taxaadm=str_replace(",", ".", $taxaadm);

				$valtotal=str_replace(",", ".", $valtotal);

				$valparcelas=str_replace(",", ".", $valparcelas);

				$valcdesc = str_replace(",", ".", $valcdesc);

				$valdesconto = $valor - $valcdesc;
				$valdesconto = str_replace(",", ".", $valdesconto);

				$descontocond = str_replace(",", ".", $descontocond);

				$matriculac = substr($matriculac,1);

				//tratando autoinfracao
				$autoinfracao = substr($autoinfracao,1);

				//tratando numero de protocolo
				$numprotocolo = substr($numprotocolo,1);				

				//tratando locador/fornecedor
				if($fornecedor == 'PROPRIO'){
					$idtbfornecedor = 1;
				}elseif(utf8_encode($fornecedor) == 'MOVIDA PARTICIPAÇÕES S.A. (FLEET)') {
					$idtbfornecedor= 9;
				}elseif(utf8_encode($fornecedor) == 'MOVIDA' || utf8_encode($fornecedor) == 'MOVIDA LOCAÇÃO DE VEICULOS S/A'){
					$idtbfornecedor = 2;
				} elseif($fornecedor == 'LOCALIZA RENT') {
					$idtbfornecedor= 4;
				} elseif(strpos($fornecedor, 'LOCALIZA RAC') !== false){
					$idtbfornecedor= 4;
				} elseif(strpos($fornecedor, 'LOCALIZA FLEET') !== false){
					$idtbfornecedor= 3;
				} else {
					$sql1 = "SELECT idtbfornecedor, fantasia FROM bdfrota.tbfornecedor WHERE fantasia LIKE '%".$fornecedor."'";
					$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
					$linhas1 = mysqli_num_rows($resultado1);
					if($linhas != 0){
						$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
						$idtbfornecedor = $row1['idtbfornecedor'];
					} else {
						$idtbfornecedor = 0;
					}
				}
				
				//tratando nome
				$sql2 = "SELECT nome, codfilial, codempresa, cpf FROM bdaniel.tbfuncionario WHERE matricula='".$matriculac."'; ";
				$resultado2= mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
				$nomec = utf8_decode($row2['nome']);
				$codfilial = $row2['codfilial'];
				$codempresa = $row2['codempresa'];
				$cpf = $row2['cpf'];


				$sql4="SELECT nome FROM bdaniel.tbfilial WHERE codempresa='$codempresa' AND codfilial='$codfilial'; ";
				$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
				$row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
					$filial=$row4['nome'];

				//print "placca $placa,filial $filial,  ccusto $ccusto, idtbfornecedor $idtbfornecedor, tipoinfracao $tipoinfracao, autoinfracao $autoinfracao, datainfracaof $datainfracaof, datalimitelocf $datalimitelocf, expedicaof $expedicaof, recebimentof $recebimentof, vencimentof $vencimentof, codigom $codigom, pontos $pontos, gravidade $gravidade, valor $valor, valdesconto $valdesconto, taxaadm $taxaadm, juros $juros, valtotal $valtotal, orgao $orgao, endereco $endereco, municipio $municipio, matriculac $matriculac,nomec $nomec, numparcelas $numparcelas, valparcelas $valparcelas, descontoapartirf $descontoapartirf, descontadocondutor $descontadocondutor, tipodesconto $tipodesconto, datadescontof $datadescontof, realinfrator $realinfrator, pessoaindicada $pessoaindicada, assinaturacond $assinaturacond, reciboassinadof $reciboassinadof, recusaassinarf $recusaassinarf, datalimitecondf $datalimitecondf, status $status, etapa $etapa, descricaoinfra $descricaoinfra, valcdesc $valcdesc,recursoorgao $recursoorgao, protocoloorgao $protocoloorgao,comprpagam $comprpagam, pagoempresa $pagoempresa,datapagamf $datapagamf,motivonpg $motivonpg, numprotocolo $numprotocolo, expprotocolof $expprotocolof, recprotocolof $recprotocolof, uf $uf, limitereg $limitereg, medreal $medreal"; break;

			$sql3 = "INSERT INTO bdfrota.tbmulta(placa, filial, ccusto, fornecedor, tipoinfracao, autoinfracao, datainfracao, datalimiteloc, expedicao, recebimento, vencimento, codigom, pontos, gravidade, valor, valdesconto, taxaadm, juros, valtotal, orgao, endereco, municipio, matriculac, nomec, numparcelas, valparcelas, descontoapartir, descontadocondutor, tipodesconto, datadesconto, realinfrator, pessoaindicada, condassinatura, reciboassinado, recusaassinar, datalimitecond, status, etapa, descricaoinfra, valcomdesc, datarecorg, protrecorg, comprpag, pgempresa, datapagempr, motivonpg, numprotocolo, expprotocolo, recprotocolo, ufinfra, limiteregul, medicaoreal) 

				VALUES ('".$placa."', '".$filial."', '".$ccusto."', '".$idtbfornecedor."', '".$tipoinfracao."', '".$autoinfracao."', '".$datainfracaof."', '".$datalimitelocf."', '".$expedicaof."', '".$recebimentof."', '".$vencimentof."', '".$codigom."', '".$pontos."', '".$gravidade."', '".$valor."', '".$valdesconto."', '".$taxaadm."', '".$juros."', '".$valtotal."', '".$orgao."', '".$endereco."', '".$municipio."', '".$matriculac."','".$nomec."', '".$numparcelas."', '".$valparcelas."', '".$descontoapartirf."', '".$descontadocondutor."', '".$tipodesconto."', '".$datadescontof."', '".$realinfrator."', '".$pessoaindicada."', '".$assinaturacond."', '".$reciboassinadof."', '".$recusaassinarf."', '".$datalimitecondf."', '".$status."', '".$etapa."', '".$descricaoinfra."', '".$valcdesc."','".$recursoorgao."', '".$protocoloorgao."','".$comprpagam."', '".$pagoempresa."','".$datapagamf."','".$motivonpg."','".$numprotocolo."', '".$expprotocolof."', '".$recprotocolof."', '".$uf."', '".$limitereg."', '".$medreal."' );";
				
				$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));

				$sql5 = "INSERT INTO bdfrota.tbmovidatramite(placa, autoinfra, pontuacao, gravidade, apCondDV, dtinfra, dtvenc, valor, status, nome, matricula, cpf, tramite, dtcons) VALUES ('$placa', '$autoinfracao', '$pontos', '$gravidade', '$datalimitecondf', '$datainfracao', '$vencimentof', '$valor', 1, '$nomec','$matriculac', '$cpf', 'Inserir infrator', '$hoje');";
					
				$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));

				//echo $sql5."<hr>";
			}
		}
	}
}

// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
	// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
	print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../../../multas/testemultas2.php\"</script>";

} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta
	echo "Não foi possível enviar o arquivo, tente novamente";
}
?>