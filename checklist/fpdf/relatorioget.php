<?php
require'./fpdf.php';
//require'./rotation.php';
//require('../../conecta.php');
require('../conecta.php');
header("Content-type: text/html; charset=utf-8");

$idtbvistoria = $_POST['idtbvistoria'];
$placacorreta = $_POST['placa'];
$datavistoria = $_POST['datavistoria'];

//print $idtbvistoria;
//print $datavistoria;

$sql = "SELECT * FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";

$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
  //idtbvistoria, nome, matricula, cpf, cnh, categoriacnh, validadecnh, placa, modelo, anofabricacao, unidade, centrocusto, tipo, vistoriador, matrvistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao, documentacao, teto, tetoesp, tetoesq, tetodir, frente, capo, parabrisa, farolesq, faroldir, parachoque, grade, latesq, paralamaesq, retrovesq, cxaresq, ptdiantesq, latdir, dir, assinadocond, statusveic, paralamadir, retrovdir, cxardir, ptdiantdir, pttrasdir, traseira, lantesq, lantdir, tmpmala, parachoquet, itinterno, painel, som, bancos, ilumint, tmpbag, retrovint, tapetes, pneus, step, marcapneus, kitstep, calotas, bateria, safecar, limpint, limpext, assinadovist, dirassvist, pttrasesq, idnextfleet

  $nome = $row['nome'];
  $matricula = $row['matricula'];
  $cpf = $row['cpf'];
  $cnh = $row['cnh'];
  $categoriacnh = $row['categoriacnh'];
  $validadecnh = $row['validadecnh'];
  $assinadocond = $row['assinadocond'];
  $statusveic = $row['statusveic'];
  $matrvistoriador = $row['matrvistoriador'];

  $validadecnh1 = explode(" ",$validadecnh);
  $validadecnh2 = explode("-",$validadecnh1[0]);
  $validadecnhreal = $validadecnh2[2] . "/" .$validadecnh2[1] . "/" .$validadecnh2[0];

  $tipo = utf8_encode($row['tipo']);
  $vistoriador = utf8_encode($row['vistoriador']);
  $datavistoria = $row['datavistoria'];
  $estado = utf8_encode($row['estado']);
  $avaria = $row['avaria'];
  $hodometro = $row['hodometro'];
  $niveltanque = $row['niveltanque'];
  $observacao = utf8_encode($row['observacao']);

  $placa=$row['placa'];
  $modelo = utf8_encode($row['modelo']);
  $unidade = utf8_encode($row['unidade']);
  $centrocusto = utf8_encode($row['centrocusto']);

  $datavistoria1 = explode(" ",$datavistoria);
  $datavistoria1 = explode("-", $datavistoria1[0]);
  $datavistoriareal = $datavistoria1[2] . "/" .$datavistoria1[1] . "/" .$datavistoria1[0];

  $teto= strtoupper($row['teto']);
  $tetoesp= strtoupper($row['tetoesp']);
  $tetoesq= strtoupper($row['tetoesq']);
  $tetodir= strtoupper($row['tetodir']);
  $frente= strtoupper($row['frente']);
  $capo= strtoupper($row['capo']);
  $parabrisa= strtoupper($row['parabrisa']);
  $farolesq= strtoupper($row['farolesq']);
  $faroldir= strtoupper($row['faroldir']);
  $parachoque= strtoupper($row['parachoque']);
  $grade= strtoupper($row['grade']);
  $latesq= strtoupper($row['latesq']);
  $paralamaesq= strtoupper($row['paralamaesq']);
  $retrovesq= strtoupper($row['retrovesq']);
  $cxaresq= strtoupper($row['cxaresq']);
  $ptdiantesq= strtoupper($row['ptdiantesq']);
  $pttrasesq= strtoupper($row['pttrasesq']);
  $latdir= strtoupper($row['latdir']);
  $paralamadir= strtoupper($row['paralamadir']);
  $retrovdir = strtoupper($row['retrovdir']);
  $cxardir = strtoupper($row['cxardir']);
  $ptdiantdir = strtoupper($row['ptdiantdir']);
  $pttrasdir = strtoupper($row['pttrasdir']);
  $traseira =  strtoupper($row['traseira']);
  $lantesq = strtoupper($row['lantesq']);
  $lantdir = strtoupper($row['lantdir']);
  $tmpmala = strtoupper($row['tmpmala']);
  $parachoquet = strtoupper($row['parachoquet']);
  $itinterno = strtoupper($row['itinterno']);
  $painel = strtoupper($row['painel']);
  $som = strtoupper($row['som']);
  $bancos = strtoupper($row['bancos']);
  $ilumint = strtoupper($row['ilumint']);
  $tmpbag = strtoupper($row['tmpbag']);
  $retrovint = strtoupper($row['retrovint']);
  $tapetes = strtoupper($row['tapetes']);
  $pneus = strtoupper($row['pneus']);
  $step = strtoupper($row['step']);
  $marcapneus = strtoupper($row['marcapneus']);
  $kitstep = strtoupper($row['kitstep']);
  $calotas = strtoupper($row['calotas']);
  $bateria = strtoupper($row['bateria']);
  $safecar = strtoupper($row['safecar']);
  $limpint = strtoupper($row['limpint']);
  $limpext = strtoupper($row['limpext']);

  $imgassinaturavist = $row['dirassvist'];
  $imgassinaturacond = $row['dir'];

  $assinadovist = $row['assinadovist'];

  $anofabricacao = $row['anofabricacao'];

  //tratando status do veiculo
  $sql5="SELECT status FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$statusveic';";
  $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
  $row5=mysqli_fetch_array($resultado5, MYSQLI_BOTH);
    $statusveic = utf8_encode($row5[0]);

}

//echo $nome, $matricula, $cpf, $cnh, $categoriacnh, $validadecnhreal;
if($avaria == 0){
	$avaria = 'NÃO';
} elseif($avaria == 1){
	$avaria = 'SIM';
}

//TRATANDO TIPO
$sql6="SELECT tipo FROM bdfrota.tbatipovist WHERE idtbatipovist='$tipo' ";
$resultado6= mysqli_query($con, $sql6) or die(mysqli_error($con));
$row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
  $tipo= utf8_encode($row6[0]);

//tratando NAOOK
if($frente == 'NAOOK'){
  $frente = 'NÃO OK';
} 

if($teto == 'NAOOK'){
  $teto = 'NÃO OK'; 
} 

if($latesq == 'NAOOK'){
  $latesq = 'NÃO OK';
} 

if($latdir == 'NAOOK'){
  $latdir = 'NÃO OK';
} 

if($traseira == 'NAOOK'){
  $traseira = 'NÃO OK';
} 

if($itinterno == 'NAOOK'){
  $itinterno = 'NÃO OK';
} 

if($painel == 'NAOOK'){
  $painel = 'NÃO OK';
} 

if($som == 'NAOOK'){
  $som = 'NÃO OK';
} 

if($bancos == 'NAOOK'){
  $bancos = 'NÃO OK';
} 

if($ilumint == 'NAOOK'){
  $ilumint = 'NÃO OK';
} 

if($pneus == 'NAOOK'){
  $pneus = 'NÃO OK';
} 

if($step == 'NAOOK'){
  $step = 'NÃO OK';
} 

if($kitstep == 'NAOOK'){
  $kitstep = 'NÃO OK';
} 

if($calotas == 'NAOOK'){
  $calotas = 'NÃO OK';
}

class PDF extends FPDF
{
	// Page header
	function Header()
	{
		// Logo
		$this->Image('../../src/images/logoredonda.png',10,10,15);
		// Arial bold 15
		$this->SetFont('Arial','B',12);
		// Cor de fundo
		$this->SetFillColor(255);
		// Line break
		$this->Ln(3);
		// Move to the right
		$this->Cell(20);
		// Title
		$this->Cell(160,12,utf8_decode('RELATÓRIO DE VISTORIA DO VEÍCULO'),1,1,'C','true');
		// Line break
		$this->Ln(2);
	}

	function RotatedImage($file,$x,$y,$w,$h,$angle)	{
    //Image rotated around its upper-left corner
    $this->Rotate($angle,$x,$y);
    $this->Image($file,$x,$y,$w,$h);
    $this->Rotate(0);
	}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','',10);

$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DO CONDUTOR\n\nNome: $nome\nMatrícula: $matricula\nCPF: $cpf\nCNH: $cnh\nCategoria CNH: $categoriacnh\nCNH vencimento: $validadecnhreal\n"),1,1);

$pdf->Ln(4);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DO VEÍCULO\n\nPlaca: $placa\nModelo: $modelo\nStatus do Veículo: $statusveic\nAno: $anofabricacao\nUnidade: $unidade\nCentro Custo: $centrocusto\n"),1,1);
$pdf->Ln(2);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DA VISTORIA\n\nTipo: $tipo\nRealizada por: $vistoriador	Matrícula: $matrvistoriador\nData: $datavistoriareal\nEstado geral: $estado\nPossui avaria? $avaria\nHodômetro: $hodometro km rodados\nNível do tanque: $niveltanque\nObservação: $observacao\n"),1,1);
$pdf->Ln(4);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("ITENS PERSONALIZADOS DA VISTORIA\n\nTeto do veículo - Estado Geral: $teto\nTeto geral: $tetoesp\nColuna do teto - lado esquerdo: $tetoesq\nColuna do teto - lado direito: $tetodir\nFrente do veículo - Estado Geral: $frente\nCapô: $capo\nPara-brisa: $parabrisa\nFarol esquerdo: $farolesq\nFarol direito: $faroldir\nPara-choque dianteiro: $parachoque\nGrade frontal: $grade\nLateral Esquerda: $latesq\nPara-lama esquerdo: $paralamaesq\nRetrovisor Lado Esquerdo: $retrovesq\nCaixa de Ar Lado Esquerdo: $cxaresq\nPorta Dianteira Esquerda: $ptdiantesq\nPorta Traseira Esquerda: $pttrasesq\nLateral Direita: $latdir\nPara-lama direito: $paralamadir\nRetrovisor Lado Direita: $retrovdir\nCaixa de Ar Lado Direito: $cxardir\nPorta Dianteira Direita: $ptdiantdir\nPorta Traseira Direita: $pttrasdir\nTraseira do Veículo - Estado Geral: $traseira\nLanterna Esquerda: $lantesq\nLanterna Direita: $lantdir\nTampa da Mala: $tmpmala\nPara-choque Traseiro: $parachoquet\nItens Internos - Estado Geral: $itinterno\nPainel: $painel\nSom: $som\nBancos: $bancos\nIluminação Interna: $ilumint\nTampa do Bagagito: $tmpbag\nRetrovisor interno: $retrovint\nTapetes: $tapetes\nCalotas: $calotas\nMarca da bateria: $bateria\nLimpeza interna: $limpint\nLimpeza externa: $limpext\nPneus: $pneus\nStep: $step\nMarca dos Pneus: $marcapneus\nKit Step: $kitstep\nSafeCar: $safecar\n"),1,1);
$pdf->Ln(15);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("CIENTE QUE:\n    No exercício da função ao qual fui contratado, estou ciente de que necessitarei de deslocamento em via pública e que, para tanto, a empresa disponibilizará veículo próprio para esse fim. Declaro-me possuidor de Carteira Nacional de Habilitação (CNH), a qual se encontra em pleno vigor, e conhecedor do atual Código Nacional de Trânsito, estando ciente que as penalidades advindas de eventuais transgressões (Multas e/ou Avarias) são de minha responsabilidade, podendo a empresa efetuar descontos.\n    Comprometo-me neste ato a zelar pelo mesmo, e responsabilizando-me por qualquer avaria ou dano causado, inclusive a terceiro, desde que devidamente comprovada a minha responsabilidade. Obrigo-me também, na hipótese de ocorrer qualquer acidente, a providenciar o Registro de Ocorrência (R.O.) junto a delegacia e prestar o auxílio necessário e pronto atendimento no caso de possível (eis) vítima (s), eximindo a empresa de problemas jurídicos que possam ocorrer, tanto criminalmente, como civilmente. Declaro estar recebendo/devolvendo (vide tipo checklist) o veículo anteriormente identificado com documento e chave nas condições descritas."),1,J);


  $pdf->Ln(100);
  $pdf->Cell(20);
  $pdf->MultiCell(160,5,utf8_decode("\n\n\n ________________________________________________________________________________\n                                          Assinatura do Vistoriador - $vistoriador \n\n\n\n\n\n\n\n\n"),1,1,);

  if($assinadovist==1){
    if(file_exists("../../../assinaturas/docs/colass/$matrvistoriador.png") && file_exists("..$imgassinaturavist")){
      
      $pdf->Image("../../../assinaturas/docs/colass/$matrvistoriador.png",61, 38,50);
      $pdf->Image("..$imgassinaturavist",31, 60,50);      

    }
  }

	$pdf->Ln(20);
	$pdf->Cell(20);
	$pdf->MultiCell(160,5,utf8_decode("\n\n\n ________________________________________________________________________________\n                                          Assinatura do Condutor - $nome  \n\n\n\n\n\n\n\n\n"),1,1,);

	if($assinadocond==1){
		if(file_exists("../../../assinaturas/docs/colass/$matricula.png") && file_exists("..$imgassinaturacond")){
			$pdf->Image("../../../assinaturas/docs/colass/$matricula.png",61, 122,50);

				$pdf->Image("..$imgassinaturacond",40, 138,50);
			
		}
	}
  //$pdf->Image("../../../assinaturas/docs/colass/003535.png",61, 122,50);


	//img teste 
	//$pdf->Image("../../../assinaturas/docs/colass/003535.png",61, 190,50);

	
$pdf->Output();

?>

