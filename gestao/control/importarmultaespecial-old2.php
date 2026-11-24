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

			if($row[0]<>'Data Infração'){//Data Infração0	Hora da infração1	Placa3	Tipo Infração3	Nº Auto Infração (AIT)4	Se NIC AIT Originario5	Código Infração6	Endereço da multa7	Data Vencimento Multa	Município	UF	Orgão Autuador
			
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

				//tratando tipoinfracao (0 - Notificação; 1 - Multa; 2 - NIC
				if($tipoinfracao == '0'){
					$tipoinfracaof = 'NOTIFICAÇÃO';
				}

				if($tipoinfracao == '1'){
					$tipoinfracaof = 'MULTA';
				}

				if($tipoinfracao = '2'){
					$tipoinfracaof = 'MULTA POR NÃO IDENTIFICAÇÃO DO CONDUTOR';
				}

				/*if($tipoinfracao == '2'){
					$sql10 ="SELECT autoinfracao FROM bdfrota.tbmulta WHERE autoinfracao= '$originario' ";
					$resultado10 = mysqli_query($conexao, $sql10) or die(mysqli_error($conexao));
					$row10 = mysqli_fetch_array($resultado10, MYSQLI_BOTH);
						$aioriginario = $row10['autoinfracao'];
				}*/

				//tratando datas
				if (!empty($datainfracao)){
					$datainfracao1 = explode(" ", $datainfracao);

					$datainfracao2 = explode("/", $datainfracao1[0]);
					$anoI =$datainfracao2[2];
					$mesI = $datainfracao2[1];
					$diaI=$datainfracao2[0];

					
					$datainfracaof = $anoI."-".$mesI."-".$diaI." ".$datainfracao1[1];
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
				
				//$sql= "SELECT nome, matricula FROM bdfrota.tbcondutor where placa='$placa' and dataassoc<='$datainfracao' and (datadissoc>='$datainfracao' or datadissoc<>'0000-00-00 00:00:00')";
				$sql = "SELECT nome, matricula FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND dataassoc<='$datainfracaof' AND (datadissoc>='$datainfracaof' OR datadissoc='0000-00-00 00:00:00');";
				$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
				$row=mysqli_fetch_array($resultado, MYSQLI_BOTH) ;
					$nomec = utf8_encode($row['nome']);
					$matriculac = $row['matricula'];

				$sql2 = "SELECT nome, codfilial, codempresa, cpf, ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matriculac'; ";
				$resultado2= mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
					$nomec = utf8_decode($row2['nome']);
					$codfilial = $row2['codfilial'];
					$codempresa = $row2['codempresa'];
					$cpf = $row2['cpf'];
					$ccusto = $row2['ccusto'];


				$sql3="SELECT nome FROM bdaniel.tbfilial WHERE codempresa='$codempresa' AND codfilial='$codfilial'; ";
				$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
				$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
					$filial=$row3['nome'];

				/*if($nomec == ''){
					$sql="SELECT matcond FROM bdfrota.tbveiculo WHERE placa='RNN5A36' AND datamovimentacao<='$datainfracaof'; ";
					$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
					$row=mysqli_fetch_array($resultado, MYSQLI_BOTH) ;
						$matriculac = $row['matcond'];
				}*/
				
				//+30dias
				$datalimitecond = date("Y-m-d H:i:s", strtotime($datainfracaof . "+30 days")); 

				//$mais5min = date("Y-m-d H:i:s", strtotime($data . "+5 minutes"));
				//+25dias
				$datalimiteloc = date("Y-m-d H:i:s", strtotime($datainfracaof . "+25 days"));

				$sql4="SELECT * FROM bdfrota.tbacodmulta where codigomulta='$codigom' ";
				$resultado4=mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
				$row4=mysqli_fetch_array($resultado4, MYSQLI_BOTH);
					$descricaoinfra = $row4['descricao'];
					$valorcdesc = $row4['valorcdesc'];
					$valorsdesc = $row4['valorsdesc'];
					$valorjuros = $row4['valorjuros'];
					$valortotal = $row4['valor'];
					$pontos = $row4['pontos'] ;
					$gravidade = $row4['gravidade'];

				/*formatando valores
				$valor=str_replace(",", ".", $valor);

				$taxaadm=str_replace(",", ".", $taxaadm);

				$valtotal=str_replace(",", ".", $valtotal);

				$valparcelas=str_replace(",", ".", $valparcelas);

				$valcdesc = str_replace(",", ".", $valcdesc);

				$valdesconto = $valor - $valcdesc;
				$valdesconto = str_replace(",", ".", $valdesconto);

				$descontocond = str_replace(",", ".", $descontocond);*/

				//tratando autoinfracao
				if(strpos($autoinfracao, "'") !== false){
					$autoinfracao = substr($autoinfracao,1);
				}				

				$sql5 = "SELECT idlocador FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
				$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH) ;
					$idlocador = $row5['idlocador'];
				
				$sql6 = "SELECT taxaadm FROM bdfrota.tbfornecedor WHERE idtbfornecedor='$idlocador' ";
				$resultado6 = mysqli_query($conexao, $sql6) or die(mysqli_error($conexao));
				$row6 = mysqli_fetch_array($resultado4, MYSQLI_BOTH) ;
					$taxaadm = $row6['taxaadm'];
					
				//print "placca $placa,filial $filial,  ccusto $ccusto, idtbfornecedor $idtbfornecedor, tipoinfracao $tipoinfracao, autoinfracao $autoinfracao, datainfracaof $datainfracaof, datalimitelocf $datalimitelocf, expedicaof $expedicaof, recebimentof $recebimentof, vencimentof $vencimentof, codigom $codigom, pontos $pontos, gravidade $gravidade, valor $valor, valdesconto $valdesconto, taxaadm $taxaadm, juros $juros, valtotal $valtotal, orgao $orgao, endereco $endereco, municipio $municipio, matriculac $matriculac,nomec $nomec, numparcelas $numparcelas, valparcelas $valparcelas, descontoapartirf $descontoapartirf, descontadocondutor $descontadocondutor, tipodesconto $tipodesconto, datadescontof $datadescontof, realinfrator $realinfrator, pessoaindicada $pessoaindicada, assinaturacond $assinaturacond, reciboassinadof $reciboassinadof, recusaassinarf $recusaassinarf, datalimitecondf $datalimitecondf, status $status, etapa $etapa, descricaoinfra $descricaoinfra, valcdesc $valcdesc,recursoorgao $recursoorgao, protocoloorgao $protocoloorgao,comprpagam $comprpagam, pagoempresa $pagoempresa,datapagamf $datapagamf,motivonpg $motivonpg, numprotocolo $numprotocolo, expprotocolof $expprotocolof, recprotocolof $recprotocolof, uf $uf, limitereg $limitereg, medreal $medreal"; break;

			$sql11 = "SELECT autoinfracao FROM bdfrota.tbmulta WHERE autoinfracao = '$autoinfracao' ";
			$resultado11 = mysqli_query($conexao, $sql11) or die(mysqli_error($conexao));
			$linhas = mysqli_num_rows($resultado11);

			if($linhas <= 0){

				$sql7 = "INSERT INTO bdfrota.tbmulta(placa, filial, ccusto, fornecedor, tipoinfracao, autoinfracao, datainfracao, datalimiteloc, vencimento, codigom, pontos, gravidade, valor, valdesconto, taxaadm, juros, valtotal, orgao, endereco, municipio, matriculac, nomec,  datalimitecond, descricaoinfra, valcomdesc, ufinfra, originario)
					VALUES ('$placa', '$filial', '$ccusto', '$idlocador', '$tipoinfracaof', '$autoinfracao', '$datainfracaof', '$datalimiteloc', '$vencimentof', '$codigom', '$pontos', '$gravidade', '$valor', '$valdesconto', '$taxaadm', '$juros', '$valtotal', '$orgao', '$endereco', '$municipio', '$matriculac', '$nomec',  '$datalimitecond', '$descricaoinfra', '$valcdesc', '$uf', '$originario');";			
					$resultado7 = mysqli_query($conexao, $sql7) or die(mysqli_error($conexao));

					$sql8="SELECT MAX(idtbmulta) AS idtbmulta FROM bdfrota.tbmulta WHERE placa='$placa' ";
					$resultado8 = mysqli_query($conexao, $sql8) or die(mysqli_error($conexao));
					$row8=mysqli_fetch_array($resultado8, MYSQLI_BOTH);
						$idtbmulta=$row8[0];

					$sql9 = "INSERT INTO bdfrota.tbmovidatramite(placa, autoinfra, pontuacao, gravidade, apCondDV, dtinfra, dtvenc, valor, status, nome, matricula, cpf, tramite, dtcons, idmulta) VALUES ('$placa', '$autoinfracao', '$pontos', '$gravidade', '$datalimitecond', '$datainfracaof', '$vencimentof', '$valor', 1, '$nomec','$matriculac', '$cpf', 'Inserir infrator', '$hoje', '$idtbmulta');";					
					$resultado9 = mysqli_query($conexao, $sql9) or die(mysqli_error($conexao));

					//print "$sql7 <br>$sql8 <hr>";
				}
			}
		}
	}
}

// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
	// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
	//print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../../../multas/testemultas2.php\"</script>";
	print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../importmultas.php\"</script>";

} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta
	//echo "Não foi possível enviar o arquivo, tente novamente";
	print "<script language='javascript' type='text/javascript'>alert('Não foi possível importar o arquivo, por favor tente novamente.');window.location=\"../importmultas.php\"</script>";
}
?>