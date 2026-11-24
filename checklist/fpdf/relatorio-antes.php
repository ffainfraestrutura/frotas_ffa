<?php
require'./fpdf.php';
//require('../../conecta.php');
require('../conecta.php');
header("Content-type: text/html; charset=utf-8");
//$link = "$_SERVER[REQUEST_URI]";
//$idaux = explode("=", $link);
$placacorreta = $_POST['placa'];
$datavistoria = $_POST['datavistoria'];

//print $placacorreta;

$sql1 = "SELECT nome, matricula, cpf, cnh, categoriacnh, validadecnh FROM bdfrota.tbvistoria WHERE placa= '$placacorreta';";

$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
  $nome = $row1['nome'];
  $matricula = $row1['matricula'];
  $cpf = $row1['cpf'];
  $cnh = $row1['cnh'];
  $categoriacnh = $row1['categoriacnh'];
  $validadecnh = $row1['validadecnh'];

  $validadecnh1 = explode(" ",$validadecnh);
  $validadecnh2 = explode("-",$validadecnh1[0]);
  $validadecnhreal = $validadecnh2[2] . "/" .$validadecnh2[1] . "/" .$validadecnh2[0];

}

//echo $nome, $matricula, $cpf, $cnh, $categoriacnh, $validadecnhreal;

$sql2 = "SELECT placa, modelo, anofabricacao, unidade, centrocusto FROM bdfrota.tbvistoria WHERE placa= '$placacorreta'";
        
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
  $placa = $row['placa'];
  $modelo = $row['modelo'];
  $anofabricacao = $row['anofabricacao'];
  $unidade = utf8_encode($row['unidade']);
  $centrocusto = $row['centrocusto'];
}

 $sql3 = "SELECT tipo, vistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao FROM bdfrota.tbvistoria WHERE placa= '$placacorreta'";
        
$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
  $tipo = utf8_encode($row['tipo']);
  $vistoriador = utf8_encode($row['vistoriador']);
  $datavistoria = $row['datavistoria'];
  $estado = utf8_encode($row['estado']);
  $avaria = $row['avaria'];
  $hodometro = $row['hodometro'];
  $niveltanque = $row['niveltanque'];
  $observacao = utf8_encode($row['observacao']);

  $datavistoria1 = explode("-",$datavistoria);
  $datavistoriareal = $datavistoria1[2] . "/" .$datavistoria1[1] . "/" .$datavistoria1[0];  
}

$sql4 = "SELECT documentacao, drenagem, consplaca, alarme, conscabine, luzespainel, combustivel, arrefecimento, rodoar, mtcxdf, eletrica, baloesdear, macaco, alternador, carroceria, pneus, freios, niveloleo, pressaooleo, pressaopneu, direcao, tacografo FROM bdfrota.tbvistoria WHERE placa= '$placacorreta'";
        
$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado4, MYSQLI_ASSOC)){
  $documentacao = $row['documentacao'];
  $drenagem = $row['drenagem'];
  $consplaca = $row['consplaca'];
  $alarme = $row['alarme'];
  $conscabine = $row['conscabine'];
  $luzespainel = $row['luzespainel'];
  $combustivel = $row['combustivel'];
  $arrefecimento = $row['arrefecimento'];
  $rodoar = $row['rodoar'];
  $mtcxdf = $row['mtcxdf'];
  $eletrica = $row['eletrica'];
  $baloesdear = $row['baloesdear'];
  $macaco = $row['macaco'];
  $alternador = $row['alternador'];
  $carroceria = $row['carroceria'];
  $pneus = $row['pneus'];
  $freios = $row['freios'];
  $niveloleo = $row['niveloleo'];
  $pressaooleo = $row['pressaooleo'];
  $pressaopneu = $row['pressaopneu'];
  $direcao = $row['direcao'];
  $tacografo = $row['tacografo'];
}

$sql5 = "SELECT placa, frontal, traseira, direita, esquerda, bateria, selfie, gps, extra1, extra2, extra3, extra4, extra5 FROM bdfrota.tbvistoriafotos WHERE placa = '$placacorreta';";

//placateste
//$sql5 = "SELECT placa, frontal, traseira, direita, esquerda, bateria, selfie, gps, extra1, extra2, extra3, extra4, extra5 FROM bdfrota.tbvistoriafotos WHERE placa = 'KPB1973';";

$resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado5, MYSQLI_ASSOC)){
	$placa = $row['placa'];
	$frontal = $row['frontal'];
	$traseira = $row['traseira'];
	$direita = $row['direita'];
	$esquerda = $row['esquerda'];
	$bateria = $row['bateria'];
	$selfie = $row['selfie'];
	$gps = $row['gps'];
	$extra1 = $row['extra1'];
	$extra2 = $row['extra2'];
	$extra3 = $row['extra3'];
	$extra4 = $row['extra4'];
	$extra5 = $row['extra5'];
}
class PDF extends FPDF
{
	// Page header
	function Header()
	{
		// Logo
		$this->Image('../../frotas/src/images/logoredonda.png',10,10,15);
		// Arial bold 15
		$this->SetFont('Arial','B',12);
		// Cor de fundo
		$this->SetFillColor(255);
		// Line break
		$this->Ln(3);
		// Move to the right
		$this->Cell(20);
		// Title
		$this->Cell(160,12,utf8_decode('RELATÓRIO DE VISTORIA DE VEÍCULO'),1,1,'C','true');
		// Line break
		$this->Ln(2);
	}
}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times','',10);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DO CONDUTOR\nNome: $nome\nMatrícula: $matricula\nCPF: $cpf\nCNH: $cnh\nCategoria CNH: $categoriacnh\nCNH vencimento: $validadecnhreal\n"),1,1);
$pdf->Ln(2);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DO VEÍCULO\nPlaca: $placa\nModelo: $modelo\nAno: $anofabricacao\nUnidade: $unidade\nCentro Custo: $centrocusto\n"),1,1);
$pdf->Ln(2);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DA VISTORIA\nTipo: $tipo\nRealizada por: $vistoriador\nData: $datavistoriareal\nEstado geral: $estado\nPossui avaria? $avaria\nHodômetro $hodometro\nNível do tanque: $niveltanque\nObservação: $observacao\n"),1,1);
$pdf->Ln(2);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("ITENS PERSONALIZADOS DA VISTORIA\nDocumentação do veículo: $documentacao\nBalões de ar: $baloesdear\nDrenagem do filtro separador de água: $drenagem\nMacaco, chave de roda, triângulo e extintor: $macaco\nEstado de conservação da placa: $consplaca\nLuz indicadora do alternador: $alternador\nAlarme sonoro: $alarme\nEstado de conservação da carroceria: $carroceria\nEstado de conservação da cabine: $conscabine\nEstado geral dos pneus: $pneus\nEstado de funciionamento das luzes do painel: $luzespainel\nEstado de funciionamento das luzes do painel: $luzespainel\nEstado de funcionamento do motor, caixa de mudanças e freio: $mtcxdf\nNível de combustível: $combustivel\nNível do óleo motor: $niveloleo\nSistema de arrefecimento: $arrefecimento\nPressão do óleo motor: $pressaooleo\nPressão do sistema pneumático, indicador rodoar: $rodoar\nPressão dos pneus: $pressaopneu\nSistema de direção: $direcao\nSistema elétrico: $eletrica\nTacógrafo: $tacografo\n"),1,1);
$pdf->Ln(15);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("CIENTE QUE:\n    No exercício da função ao qual fui contratado, estou ciente de que necessitarei de deslocamento em via pública e que, para tanto, a empresa disponibilizará veículo próprio para esse fim. Declaro-me possuidor de Carteira Nacional de Habilitação (CNH), a qual se encontra em pleno vigor, e conhecedor do atual Código Nacional de Trânsito, estando ciente que as penalidades advindas de eventuais transgressões (Multas e/ou Avarias) são de minha responsabilidade, podendo a empresa efetuar descontos.\n    Comprometo-me neste ato a zelar pelo mesmo, e responsabilizando-me por qualquer avaria ou dano causado, inclusive a terceiro, desde que devidamente comprovada a minha responsabilidade. Obrigo-me também, na hipótese de ocorrer qualquer acidente, a providenciar o Registro de Ocorrência (R.O.) junto a delegacia e prestar o auxílio necessário e pronto atendimento no caso de possível (eis) vítima (s), eximindo a empresa de problemas jurídicos que possam ocorrer, tanto criminalmente, como civilmente. Declaro estar recebendo/devolvendo (vide tipo checklist) o veículo anteriormente identificado com documento e chave nas condições descritas."),1,J);

$pdf->Ln(2);
$pdf->Cell(20);
$pdf->MultiCell(50,5,utf8_decode("FOTO FRONTAL"),1,1);
$pdf->Image("..$frontal",30,100,50);

$pdf->Ln(-5);
$pdf->Cell(75);
$pdf->MultiCell(50,5,utf8_decode("FOTO TRASEIRA"),1,1);
$pdf->Image("..$traseira",85,100,50);

$pdf->Ln(-5);
$pdf->Cell(130);
$pdf->MultiCell(50,5,utf8_decode("FOTO LATERAL DIREITA"),1,1);
$pdf->Image("..$direita",140,100,50);

$pdf->Ln(32);
$pdf->Cell(20);
$pdf->MultiCell(50,5,utf8_decode("FOTO LATERAL ESQUERDA"),1,1);
$pdf->Image("..$esquerda",30,138,50);

$pdf->Ln(-5);
$pdf->Cell(75);
$pdf->MultiCell(50,5,utf8_decode("FOTO BATERIA"),1,1);
$pdf->Image("..$bateria",85,138,50);

$pdf->Ln(-5);
$pdf->Cell(130);
$pdf->MultiCell(50,5,utf8_decode("FOTO SELFIE VISTORIADOR"),1,1);
$pdf->Image("..$selfie",140,138,50);

$pdf->Ln(32);
$pdf->Cell(20);
$pdf->MultiCell(50,5,utf8_decode("FOTO GPS"),1,1);
$pdf->Image("..$gps",30,175,50);

if($extra1 != null){
	$pdf->Ln(-5);
	$pdf->Cell(75);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 01"),1,1);
	$pdf->Image("..$extra1",85,175,50);
}

if($extra2 != null){
	$pdf->Ln(-5);
	$pdf->Cell(130);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 02"),1,1);
	$pdf->Image("..$extra2",140,175,50);
}

if($extra3 != null){
	$pdf->Ln(32);
	$pdf->Cell(20);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 03"),1,1);
	$pdf->Image("..$extra3",30,212,50);

	$pdf->Ln(50);
	$pdf->Cell(20);
	$pdf->MultiCell(160,5,utf8_decode("\n ________________________________________________________________________________________\n                                              Assinatura do Condutor - $nome"),1,1);

	if($assinado==1){
		if(file_exists("../../../assinaturas/docs/colass/$matricula.png")){
			$pdf->Image("../../../assinaturas/docs/colass/$matricula.png",75, 261,50);	
		
			//img teste $pdf->Image("../../../assinaturas/docs/colass/003535.png",75, 261,50);
		} else {
		$pdf->Ln(50);
		$pdf->Cell(20);
		$pdf->MultiCell(160,5,utf8_decode("\n ________________________________________________________________________________________\n                                              Assinatura do Condutor - $nome"),1,1);
		}
		
		if($assinado==1){
			if(file_exists("../../../assinaturas/docs/colass/$matricula.png")){
				$pdf->Image("../../../assinaturas/docs/colass/$matricula.png",85, 224,50);	
			}
		}

		//img teste $pdf->Image("../../../assinaturas/docs/colass/003535.png",85, 224,50);
	}

}
if($extra4 != null){
	$pdf->Ln(-70);
	$pdf->Cell(75);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 04"),1,1);
	$pdf->Image("..$extra4",85,212,50);
}

if($extra5 != null){
	$pdf->Ln(-5);
	$pdf->Cell(130);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 05"),1,1);
	$pdf->Image("..$extra5",140,212,50);
}






	
$pdf->Output();
?>

