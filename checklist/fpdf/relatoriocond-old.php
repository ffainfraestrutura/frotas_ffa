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

//itens de teste 
/*$placacorreta = 'RNS3C52';  
$matricula1 = '003000'; 
$datavistoriai = '2023-05-09 11:05:14';*/

//print $placacorreta;
//print $matricula1;

$sql1 = "SELECT nome, matricula, cpf, cnh, categoriacnh, validadecnh, statusveic, matrvistoriador   FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria'";

$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
  $nome = $row1['nome'];
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

  //tratando status do veiculo
  $sql5="SELECT status FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$statusveic';";
  $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
  $row5=mysqli_fetch_array($resultado5, MYSQLI_BOTH);
    $statusveic = $row5[0];
}

//echo $nome, $matricula, $cpf, $cnh, $categoriacnh, $validadecnhreal;

$sql2 = "SELECT placa, modelo, anofabricacao, unidade, centrocusto FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";
        
$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)){
  $placa = $row['placa'];
  $modelo = $row['modelo'];
  $anofabricacao = $row['anofabricacao'];
  $unidade = utf8_encode($row['unidade']);
  $centrocusto = utf8_encode($row['centrocusto']);
}

 $sql3 = "SELECT tipo, vistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";
        
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

  $datavistoria1 = explode(" ",$datavistoria);
  $datavistoria2 = explode("-",$datavistoria1[0]);
  $datavistoriareal = $datavistoria2[2] . "/" .$datavistoria2[1] . "/" .$datavistoria2[0];

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
}

$sql4 = "SELECT documentacao, drenagem, consplaca, alarme, conscabine, luzespainel, combustivel, arrefecimento, mtcxdf, eletrica, macaco, alternador, carroceria, pneus, freios, niveloleo, pressaopneu, direcao FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";
        
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

$sql7 = "SELECT dir FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";
$resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado7, MYSQLI_ASSOC)){
  $imgassinatura = $row['dir'];
}

//print $idtbvistoria;
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
$pdf->MultiCell(160,5,utf8_decode("INFORMAÇÕES DA VISTORIA\n\nTipo: $tipo\nRealizada por: $vistoriador    Matrícula: $matrvistoriador\nData: $datavistoriareal\nEstado geral: $estado\nPossui avaria? $avaria\nHodômetro $hodometro km rodados\nNível do tanque: $niveltanque\nObservação: $observacao\n"),1,1);
$pdf->Ln(4);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("ITENS PERSONALIZADOS DA VISTORIA\n\nDocumentação do veículo: $documentacao\nDrenagem do filtro separador de água: $drenagem\nMacaco, chave de roda, triângulo e extintor: $macaco\nEstado de conservação da placa: $consplaca\nLuz indicadora do alternador: $alternador\nAlarme sonoro: $alarme\nEstado de conservação da carroceria: $carroceria\nEstado de conservação da cabine: $conscabine\nEstado geral dos pneus: $pneus\nEstado de funcionamento das luzes do painel: $luzespainel\nEstado de funcionamento do motor, caixa de mudanças e freio: $mtcxdf\nNível de combustível: $combustivel\nNível do óleo motor: $niveloleo\nSistema de arrefecimento: $arrefecimento\nPressão dos pneus: $pressaopneu\nSistema de direção: $direcao\nSistema elétrico: $eletrica\n"),1,1);
$pdf->Ln(15);
$pdf->Cell(20);
$pdf->MultiCell(160,5,utf8_decode("CIENTE QUE:\n    No exercício da função ao qual fui contratado, estou ciente de que necessitarei de deslocamento em via pública e que, para tanto, a empresa disponibilizará veículo próprio para esse fim. Declaro-me possuidor de Carteira Nacional de Habilitação (CNH), a qual se encontra em pleno vigor, e conhecedor do atual Código Nacional de Trânsito, estando ciente que as penalidades advindas de eventuais transgressões (Multas e/ou Avarias) são de minha responsabilidade, podendo a empresa efetuar descontos.\n    Comprometo-me neste ato a zelar pelo mesmo, e responsabilizando-me por qualquer avaria ou dano causado, inclusive a terceiro, desde que devidamente comprovada a minha responsabilidade. Obrigo-me também, na hipótese de ocorrer qualquer acidente, a providenciar o Registro de Ocorrência (R.O.) junto a delegacia e prestar o auxílio necessário e pronto atendimento no caso de possível (eis) vítima (s), eximindo a empresa de problemas jurídicos que possam ocorrer, tanto criminalmente, como civilmente. Declaro estar recebendo/devolvendo (vide tipo checklist) o veículo anteriormente identificado com documento e chave nas condições descritas."),1,J);




	$pdf->Ln(87);
	$pdf->Cell(20);
	$pdf->MultiCell(160,5,utf8_decode("\n\n\n________________________________________________________________________________________\n                                              Assinatura do Condutor - $nome \n\n\n\n\n\n\n\n\n"),1,1);

	if(file_exists("../../../assinaturas/docs/colass/$matricula.png") && file_exists("..$imgassinatura")){
      if($imgassinatura != ''){
		    $pdf->Image("../../../assinaturas/docs/colass/$matricula.png",75, 187,50);


    		/*list($larguraassinatura, $alturaassinatura) = getimagesize("..$imgassinatura");
    		if($larguraassinatura < $alturaassinatura){
    			$pdf->RotatedImage("..$imgassinatura",31,75,32,54,90); 
    		} else {*/
    			$pdf->Image("..$imgassinatura",61, 150,50);
    		//}
      }
  }

	/*img teste*/ //$pdf->Image("../../../assinaturas/docs/colass/003535.png",61, 190,50);*/


$pdf->Output();
?>

