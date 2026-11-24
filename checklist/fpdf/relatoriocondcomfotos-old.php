<?php
require'./fpdf.php';
//require('../../conecta.php');
require('../conecta.php');
header("Content-type: text/html; charset=utf-8");
$link = "$_SERVER[REQUEST_URI]";
$idaux = explode("=", $link);

$placacorreta = $_POST['placa'];
$matricula1 = $_POST['matricula'];
$datavistoriai = $_POST['datavistoria'];

$idtbvistoria = $_POST['idtbvistoria'];

//print "$idtbvistoria $placacorreta $matricula1";

$sql1 = "SELECT * FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";

$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
  $nome = utf8_encode($row1['nome']);
  $matricula = $row1['matricula'];
  $cpf = $row1['cpf'];
  $cnh = $row1['cnh'];
  $categoriacnh = $row1['categoriacnh'];
  $validadecnh = $row1['validadecnh'];
  $statusveic = $row1['statusveic'];
  $matrvistoriador = $row1['matrvistoriador'];

  $validadecnh1 = explode(" ",$validadecnh);
  $validadecnh2 = explode("-",$validadecnh1[0]);
  $validadecnhreal = $validadecnh2[2] . "/" .$validadecnh2[1] . "/" .$validadecnh2[0];

}

//tratando status do veiculo - , 
$sql2="SELECT status FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$statusveic'";
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
$row = mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
  $statusveic = utf8_encode($row['status']);


$sql3 = "SELECT placa, modelo, anofabricacao, unidade, centrocusto FROM bdfrota.tbvistoria WHERE placa= '$placacorreta'";
        
$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
  $placa = $row['placa'];
  $modelo = $row['modelo'];
  $anofabricacao = $row['anofabricacao'];
  $unidade = utf8_encode($row['unidade']);
  $centrocusto = utf8_encode($row['centrocusto']);
}

 $sql4 = "SELECT tipo, vistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";
        
$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado4, MYSQLI_ASSOC)){
  $tipo = utf8_encode($row['tipo']);
  $vistoriador = utf8_encode($row['vistoriador']);
  $datavistoria = $row['datavistoria'];
  $estado = utf8_encode($row['estado']);
  $avaria = $row['avaria'];
  $hodometro = $row['hodometro'];
  $niveltanque = $row['niveltanque'];
  $observacao = utf8_encode($row['observacao']);

  $datavistoria1 = explode(" ",$datavistoria);
  $datavistoria1 = explode("-", $datavistoria1[0]);
  $datavistoriareal = $datavistoria1[2] . "/" .$datavistoria1[1] . "/" .$datavistoria1[0];

  if($avaria == 0){
  	$avaria = 'NÃO';
  } elseif($avaria == 1){
  	$avaria = 'SIM';
  }  
}

//idtbatipovist, tipo
$sql8="SELECT * FROM bdfrota.tbatipovist WHERE idtbatipovist='$tipo';";
$resultado8=mysqli_query($con, $sql8) or die(mysqli_error($con));
$row8 = mysqli_fetch_array($resultado8, MYSQLI_BOTH);
	$tipo = utf8_encode($row8['tipo']);


$sql5 = "SELECT documentacao, drenagem, consplaca, alarme, conscabine, luzespainel, combustivel, arrefecimento, mtcxdf, eletrica, macaco, alternador, carroceria, pneus, freios, niveloleo, pressaopneu, direcao FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";
        
$resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado5, MYSQLI_ASSOC)){
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

$sql6 = "SELECT placa, frontal, traseira, direita, esquerda, bateria, selfie, gps, extra1, extra2, extra3, extra4, extra5 FROM bdfrota.tbvistoriafotos WHERE idtbvistoria= '$idtbvistoria';";

$resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado6, MYSQLI_ASSOC)){
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

// Captura o tamanho da imagem e guarda nas variáveis
list($largurafrontal, $alturafrontal) = getimagesize("..$frontal");
list($larguratraseira, $alturatraseira) = getimagesize("..$traseira");
list($larguradireita, $alturadireita) = getimagesize("..$direita");
list($larguraesquerda, $alturaesquerda) = getimagesize("..$esquerda");
list($largurabateria, $alturabateria) = getimagesize("..$bateria");
list($larguraselfie, $alturaselfie) = getimagesize("..$selfie");
list($larguragps, $alturagps) = getimagesize("..$gps");
list($larguraextra1, $alturaextra1) = getimagesize("..$extra1");
list($larguraextra2, $alturaextra2) = getimagesize("..$extra2");
list($larguraextra3, $alturaextra3) = getimagesize("..$extra3");
list($larguraextra4, $alturaextra4) = getimagesize("..$extra4");
list($larguraextra5, $alturaextra5) = getimagesize("..$extra5");

$sql7 = "SELECT dir FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";
$resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado7, MYSQLI_ASSOC)){
  $imgassinatura = $row['dir'];
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
		$this->Cell(160,12,utf8_decode('RELATÓRIO DE VISTORIA DE VEÍCULO'),1,1,'C','true');
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

$pdf->SetFont('Times','',10);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DO CONDUTOR\n\nNome: $nome\nMatrícula: $matricula\nCPF: $cpf\nCNH: $cnh\nCategoria CNH: $categoriacnh\nCNH vencimento: $validadecnhreal\n"),1,1);
$pdf->Ln(4);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DO VEÍCULO\n\nPlaca: $placa\nModelo: $modelo\nStatus do Veículo: $statusveic\nAno: $anofabricacao\nUnidade: $unidade\nCentro Custo: $centrocusto\n"),1,1);
$pdf->Ln(4);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DA VISTORIA\n\nTipo: $tipo\nRealizada por: $vistoriador    	Matrícula: $matrvistoriador\nData: $datavistoriareal\nEstado geral: $estado\nPossui avaria? $avaria\nHodômetro $hodometro km rodados\nNível do tanque: $niveltanque\nObservação: $observacao\n"),1,1);
$pdf->Ln(4);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("ITENS PERSONALIZADOS DA VISTORIA\n\nDocumentação do veículo: $documentacao\nDrenagem do filtro separador de água: $drenagem\nMacaco, chave de roda, triângulo e extintor: $macaco\nEstado de conservação da placa: $consplaca\nLuz indicadora do alternador: $alternador\nAlarme sonoro: $alarme\nEstado de conservação da carroceria: $carroceria\nEstado de conservação da cabine: $conscabine\nEstado geral dos pneus: $pneus\nEstado de funcionamento das luzes do painel: $luzespainel\nEstado de funcionamento do motor, caixa de mudanças e freio: $mtcxdf\nNível de combustível: $combustivel\nNível do óleo motor: $niveloleo\nSistema de arrefecimento: $arrefecimento\nPressão dos pneus: $pressaopneu\nSistema de direção: $direcao\nSistema elétrico: $eletrica\n"),1,1);
$pdf->Ln(15);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("CIENTE QUE:\n    No exercício da função ao qual fui contratado, estou ciente de que necessitarei de deslocamento em via pública e que, para tanto, a empresa disponibilizará veículo próprio para esse fim. Declaro-me possuidor de Carteira Nacional de Habilitação (CNH), a qual se encontra em pleno vigor, e conhecedor do atual Código Nacional de Trânsito, estando ciente que as penalidades advindas de eventuais transgressões (Multas e/ou Avarias) são de minha responsabilidade, podendo a empresa efetuar descontos.\n    Comprometo-me neste ato a zelar pelo mesmo, e responsabilizando-me por qualquer avaria ou dano causado, inclusive a terceiro, desde que devidamente comprovada a minha responsabilidade. Obrigo-me também, na hipótese de ocorrer qualquer acidente, a providenciar o Registro de Ocorrência (R.O.) junto a delegacia e prestar o auxílio necessário e pronto atendimento no caso de possível (eis) vítima (s), eximindo a empresa de problemas jurídicos que possam ocorrer, tanto criminalmente, como civilmente. Declaro estar recebendo/devolvendo (vide tipo checklist) o veículo anteriormente identificado com documento e chave nas condições descritas."),1,J);

$pdf->Ln(2);
$pdf->Cell(20);
$pdf->MultiCell(50,5,utf8_decode("FOTO FRONTAL"),1,1);
//$pdf->Image("..$frontal",30,100,50);
if($largurafrontal<$alturafrontal){
	$pdf->RotatedImage("..$frontal",30,128,28,50,90); /* funciona, mas proporção boa para todos, fica o questionamento*/
} else {
	$pdf->Image("..$frontal",30,100,50);
}

$pdf->Ln(-5);
$pdf->Cell(75);
$pdf->MultiCell(50,5,utf8_decode("FOTO TRASEIRA"),1,1);
/*$pdf->Image("..$traseira",85,100,50);*/
if($larguratraseira<$alturatraseira){
	$pdf->RotatedImage("..$traseira",85,128,28,50,90); /* funciona, mas proporção boa para todos, fica o questionamento*/
} else {
	$pdf->Image("..$traseira",85,100,50);
} 

$pdf->Ln(-5);
$pdf->Cell(130);
$pdf->MultiCell(50,5,utf8_decode("FOTO LATERAL DIREITA"),1,1);
/*$pdf->Image("..$direita",140,100,50);*/
if($larguradireita<$alturadireita){
	$pdf->RotatedImage("..$direita",140,128,28,50,90);
} else {
	$pdf->Image("..$direita",140,100,50);
}

$pdf->Ln(32);
$pdf->Cell(20);
$pdf->MultiCell(50,5,utf8_decode("FOTO LATERAL ESQUERDA"),1,1);
/*$pdf->Image("..$esquerda",30,138,50);*/
/*$pdf->Image("..$esquerda",30,142,50);*/
if($larguraesquerda<$alturaesquerda){
	$pdf->RotatedImage("..$esquerda",30,166,28,50,90);
} else {
	$pdf->Image("..$esquerda",30,138,50);
}

$pdf->Ln(-5);
$pdf->Cell(75);
$pdf->MultiCell(50,5,utf8_decode("FOTO BATERIA"),1,1);
/*$pdf->Image("..$bateria",85,138,50);*/
/*$pdf->Image("..$bateria",85,142,50);*/
if($largurabateria<$alturabateria){
	$pdf->RotatedImage("..$bateria",85,166,28,50,90);
} else {
	$pdf->Image("..$bateria",85,138,50);
}

$pdf->Ln(-5);
$pdf->Cell(130);
$pdf->MultiCell(50,5,utf8_decode("FOTO SELFIE VISTORIADOR"),1,1);
/* $pdf->Image("..$selfie",140,138,50); */
/* $pdf->Image("..$selfie",140,142,50); */
if($larguraselfie<$alturaselfie){
	$pdf->RotatedImage("..$selfie",140,166,28,50,90); 
} else {
	$pdf->Image("..$selfie",140,138,50);
} 

$pdf->Ln(32);
$pdf->Cell(20);
$pdf->MultiCell(50,5,utf8_decode("FOTO GPS"),1,1);
/* $pdf->Image("..$gps",30,175,50); */
/* $pdf->Image("..$gps",30,184,50); */
if($larguragps<$alturagps){
	$pdf->RotatedImage("..$gps",30,203,28,50,90); 
} else {
	$pdf->Image("..$gps",30,175,50);
}

if($extra1 != null){
	$pdf->Ln(-5);
	$pdf->Cell(75);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 01"),1,1);
	/*$pdf->Image("..$extra1",85,175,50);*/
	/*$pdf->Image("..$extra1",85,184,50);*/
	if($larguraextra1<$alturaextra1){
		$pdf->RotatedImage("..$extra1",85,203,28,50,90); 
	} else {
		$pdf->Image("..$extra1",85,175,50);
	}
}

if($extra2 != null){
	$pdf->Ln(-5);
	$pdf->Cell(130);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 02"),1,1);
	/*$pdf->Image("..$extra2",140,175,50);*/
	/*$pdf->Image("..$extra2",140,184,50);*/
	if($larguraextra2<$alturaextra2){
		$pdf->RotatedImage("..$extra2",140,203,28,50,90); 
	} else {
		$pdf->Image("..$extra2",140,175,50);
	}
}

if($extra3 != null){
	$pdf->Ln(37);
	$pdf->Cell(20);
	$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 03"),1,1);
	/*$pdf->Image("..$extra3",30,216,50);*/
	/*$pdf->Image("..$extra3",30,226,50);*/
	if($larguraextra3<$alturaextra3){
		$pdf->RotatedImage("..$extra3",30,244,28,50,90); 
	} else {
		$pdf->Image("..$extra3",30,216,50);
	}

	if($extra4 != null){
		$pdf->Ln(-5);
		$pdf->Cell(75);
		$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 04"),1,1);
		/*$pdf->Image("..$extra4",85,216,50);*/
		/*$pdf->Image("..$extra4",85,226,50);*/
		if($larguraextra4<$alturaextra4){
			$pdf->RotatedImage("..$extra4",85,244,28,50,90); 
		} else {
			$pdf->Image("..$extra4",85,216,50);
		}
	}

		if($extra5 != null){
		$pdf->Ln(-5);
		$pdf->Cell(130);
		$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 05"),1,1);
		/*$pdf->Image("..$extra5",140,216,50);*/
		/*$pdf->Image("..$extra5",140,226,50);*/
		if($larguraextra5<$alturaextra5){
			$pdf->RotatedImage("..$extra5",140,244,28,50,90); 
		} else {
			$pdf->Image("..$extra4",140,216,50);
		}
	}

	$pdf->Ln(70);
	$pdf->Cell(20);
	$pdf->MultiCell(160,5,utf8_decode("\n ________________________________________________________________________________________\n                                              Assinatura do Condutor - $nome \n\n\n\n\n\n\n\n\n"),1,1);

	if(file_exists("../../../assinaturas/docs/colass/$matricula.png") && file_exists("..$imgassinatura")){
		if($imgassinatura != ''){
			$pdf->Image("../../../assinaturas/docs/colass/$matricula.png",75, 27,50);
			//$pdf->Image("..$imgassinatura",31, 42,50);

			list($larguraassinatura, $alturaassinatura) = getimagesize("..$imgassinatura");
			if($larguraassinatura < $alturaassinatura){
				$pdf->RotatedImage("..$imgassinatura",31,75,32,54,90); 
			} else {
				$pdf->Image("..$imgassinatura",31, 42,50);
			}	
		}
	}

	//img teste $pdf->Image("../../../assinaturas/docs/colass/003535.png",75, 261,50);
} else {
	$pdf->Ln(50);
	$pdf->Cell(20);
	$pdf->MultiCell(160,5,utf8_decode("\n ________________________________________________________________________________________\n                                              Assinatura do Condutor - $nome \n\n\n\n\n\n"),1,1);
	
	if(file_exists("../../../assinaturas/docs/colass/$matricula.png") && file_exists("..$imgassinatura")){
		$pdf->Image("../../../assinaturas/docs/colass/$matricula.png",85, 224,50);
		//$pdf->Image("..$imgassinatura",31, 239,50);	

		list($larguraassinatura, $alturaassinatura) = getimagesize("..$imgassinatura");
		if($larguraassinatura < $alturaassinatura){
			$pdf->RotatedImage("..$imgassinatura",31,272,32,54,90); 
		} else {
			$pdf->Image("..$imgassinatura",31, 248,50);
		}
	}

	//img teste $pdf->Image("../../../assinaturas/docs/colass/003535.png",85, 224,50);
}

$pdf->Output();
?>