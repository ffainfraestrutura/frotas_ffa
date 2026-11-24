<?php
//require('fpdf/fpdf.php');
require('./fpdf.php');
require('../../conecta03.php');

header("Content-type: text/html; charset=utf-8");

//$placacorreta = "RNN5G07"; // $_POST['placa'];
//$datavistoria = "2024-05-02 08:46:29"; // $_POST['datavistoria'];
$idtbvistoria = 6400; // $_POST['idtbvistoria'];


$sql = "SELECT * FROM bdfrota.tbvistoria WHERE idtbvistoria= '$idtbvistoria';";

$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
  $nome = $row['nome'];
  $matricula = $row['matricula'];
  $cpf = $row['cpf'];
  $cnh = $row['cnh'];
  $categoriacnh = $row['categoriacnh'];
  $validadecnh = $row['validadecnh'];
  $assinado = $row['assinadocond'];
  $statusveic = $row['statusveic'];
  $matrvistoriador = $row['matrvistoriador'];

  $placa = $row['placa'];
  $modelo = $row['modelo'];
  $anofabricacao = $row['anofabricacao'];
  $unidade = $row['unidade'];
  $centrocusto = $row['centrocusto'];

  $tipo = $row['tipo'];
  $vistoriador = $row['vistoriador'];
  $datavistoria = $row['datavistoria'];
  $estado = strtoupper($row['estado']);
  $avaria = $row['avaria'];
  $hodometro = $row['hodometro'];
  $niveltanque = $row['niveltanque'];
  $observacao = $row['observacao'];

  if(strpos($observacao,"\n") !==false){    
    $observacao = str_replace("\n", " ", $observacao);
  }

  $datavistoria1 = explode(" ",$datavistoria);
  $datavistoria2 = explode("-", $datavistoria1[0]);
  $datavistoriareal = $datavistoria2[2] . "/" .$datavistoria2[1] . "/" .$datavistoria2[0];

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
if($avaria == 0){
  $avaria = 'NÃO';
} elseif($avaria == 1){
  $avaria = 'SIM';
}

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

//TRATANDO TIPO
$sql6="SELECT tipo FROM bdfrota.tbatipovist WHERE idtbatipovist='$tipo' ";
$resultado6= mysqli_query($con, $sql6) or die(mysqli_error($con));
$row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
  $tipo= $row6[0];

$sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria= '$idtbvistoria' AND idtbvistfotos not in (40,41,42,43,44,45,47,1265, 1268, 2639,2640,2641,2642,2643,2644,2645,2646,2647, 3198,3199,3201) ;";

//$sql5 = "SELECT placa, frontal, traseira, direita, esquerda, bateria, selfie, gps, extra1, extra2, extra3, extra4, extra5 FROM bdfrota.tbvistoriafotos WHERE placa = 'KPB1973';";

$resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));

while($row = mysqli_fetch_array($resultado5, MYSQLI_ASSOC)){
  $placa = $row['placa'];
  $frontal = $row['frontal'];
  $fototraseira = $row['traseira'];
  $direita = $row['direita'];
  $esquerda = $row['esquerda'];
  $bateriaf = $row['bateria'];
  $selfie = $row['selfie'];
  $fotocnh = $row['cnh'];
  $extra1 = $row['extra1'];
  $extra2 = $row['extra2'];
  $extra3 = $row['extra3'];
}

if($frontal != ''){

if(strpos($frontal, "_") === false){
  $frontal1=explode("/", $frontal);
  $frontal = "/docs_old/".$frontal1[2];
}


}

if($fototraseira != ''){

if(strpos($fototraseira, "_") === false){
  $fototraseira1=explode("/", $fototraseira);
  $fototraseira = "/docs_old/".$fototraseira1[2];
}

}


if($direita != ''){
if(strpos($direita, "_") === false){
  $direita1=explode("/", $direita);
  $direita = "/docs_old/".$direita1[2];
}

}

if($esquerda != ''){
if(strpos($esquerda, "_") === false){
  $esquerda1=explode("/", $esquerda);
  $esquerda = "/docs_old/".$esquerda1[2];
}

}

/*if($bateriaf !== '' || $bateriaf !== ' '){
    if(strpos($bateriaf, "_") === false){
      $bateriaf1=explode("/", $bateriaf);
      $bateriaf = "/docs_old/".$bateriaf1[2];
    }
}else{print "oi";}*/

if(!empty($selfie)){
  
  if(strpos($selfie, "_") === false){
    $selfie1=explode("/", $selfie);
    $selfie = "/docs_old/".$selfie1[2];
  }

  //print "$selfie";
  
}

if(!empty($fotocnh)){

  if($fotocnh != ' '){
    if(strpos($fotocnh, "_") === false){
      $fotocnh1=explode("/", $fotocnh);
      $fotocnh = "/docs_old/".$fotocnh1[2];
    }
  }

}

//if(file_exists($extra1)){
if($extra1 != ''){
  if($extra1 != ' '){
    if(strpos($extra1, "_") === false){
    $extra1a=explode("/", $extra1);
    $extra1 = "/docs_old/".$extra1a[2];
  }
  }
  
}
//}

//if(file_exists($extra2)){
if($extra2 != ''){
  if($extra2 != ' '){
    if(strpos($extra2, "_") === false){
    $extra2a=explode("/", $extra2);
    $extra2 = "/docs_old/".$extra2a[2];
  }
  }
  
}
//}

//if(file_exists($extra3)){
if($extra3 != ''){
  if($extra3 != ' '){
    if(strpos($extra3, "_") === false){
  $extra3a=explode("/", $extra3);
  $extra3 = "/docs_old/".$extra3a[2];
}
  }

}

if(!empty($imgassinaturavist)){
  if($imgassinaturavist != ' '){
    if(strpos($imgassinaturavist, "_") === false){
      $imgassinaturavista = explode("/", $imgassinaturavist);
      $imgassinaturavist = $imgassinaturavista[0]."/docs_old/".$imgassinaturavista[2]."/".$imgassinaturavista[3];
    }
  }

}

if(!empty($imgassinaturacond)){
  if($imgassinaturacond != ' '){
    if(strpos($imgassinaturacond, "_") === false){
      $imgassinaturaconda = explode("/", $imgassinaturacond);
      $imgassinaturacond = $imgassinaturaconda[0]."/docs_old/".$imgassinaturaconda[2]."/".$imgassinaturaconda[3];
    }
  }

}


class PDF extends FPDF {
  /*function Header() {
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(0, 10, utf8_decode('RELATÓRIO DE VISTORIA DE VEÍCULO'), 0, 1, 'C');
      $this->Ln(10);
  }

    function ChapterTitle($num, $label) {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Pergunta ' . $num . ': ' . $label, 0, 1);
        $this->Ln(4);
    }

    function ChapterBody($body) {
        $this->SetFont('Arial', '', 12);
        $this->Write(10, $this->UTF8ToISO8859($body)); // Corrige a codificação
        $this->Ln();
    }

    function UTF8ToISO8859($text) {
        return utf8_decode($text);
    }*/
}

$pdf = new PDF();
$pdf->AddPage();

$pdf->Image('../../src/images/logoredonda.png',10,8,10);
// Arial bold 15
$pdf->SetFont('Arial','B',8);
// Cor de fundo
$pdf->SetFillColor(255);
// Move to the right
$pdf->Cell(12);

// Title
$pdf->Cell(170,6,utf8_decode('RELATÓRIO DE VISTORIA DE VEÍCULO'),1,1,'C','true');
// Line break
$pdf->Ln(1);

$pdf->SetFont('Arial','',8);


$pdf->Cell(12);
$pdf->MultiCell(170,4,utf8_decode("INFORMAÇÕES DO CONDUTOR"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(20,4,utf8_decode("Nome"),1,1);

$pdf->Ln(-4);
$pdf->Cell(32);
$pdf->MultiCell(68,4,utf8_decode("$nome"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(100);
$pdf->MultiCell(20,4,utf8_decode("CNH"),1,1);

$pdf->Ln(-4);
$pdf->Cell(120);
$pdf->MultiCell(62,4,utf8_decode("$cnh"),1,1);

//esquerda
$pdf->Cell(12);
$pdf->MultiCell(20,4,utf8_decode("Matrícula"),1,1);

$pdf->Ln(-4);
$pdf->Cell(32);
$pdf->MultiCell(68,4,utf8_decode("$matricula"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(100);
$pdf->MultiCell(40,4,utf8_decode("CNH categoria"),1,1);

$pdf->Ln(-4);
$pdf->Cell(140);
$pdf->MultiCell(42,4,utf8_decode("$categoriacnh"),1,1);

//esquerda
$pdf->Cell(12);
$pdf->MultiCell(20,4,utf8_decode("CPF"),1,1);

$pdf->Ln(-4);
$pdf->Cell(32);
$pdf->MultiCell(68,4,utf8_decode("$cpf"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(100);
$pdf->MultiCell(40,4,utf8_decode("CNH vencimento"),1,1);

$pdf->Ln(-4);
$pdf->Cell(140);
$pdf->MultiCell(42,4,utf8_decode("$validadecnhreal"),1,1);

$pdf->Ln(1);
$pdf->Cell(12);
$pdf->MultiCell(170,4,utf8_decode("INFORMAÇÕES DO VEÍCULO"),1,1);
//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(15,4,utf8_decode("Placa"),1,1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(60,4,utf8_decode("$placa"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(15,4,utf8_decode("Unidade"),1,1);

$pdf->Ln(-4);
$pdf->Cell(102);
$pdf->MultiCell(80,4,utf8_decode("$unidade"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(15,4,utf8_decode("Modelo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(45,4,utf8_decode("$modelo"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(72);
$pdf->MultiCell(35,4,utf8_decode("Centro de Custo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(107);
$pdf->MultiCell(75,4,utf8_decode("$centrocusto"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(15,4,utf8_decode("Ano"),1,1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(45,4,utf8_decode("$anofabricacao"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(72);
$pdf->MultiCell(30,4,utf8_decode("Status do Veículo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(102);
$pdf->MultiCell(80,4,utf8_decode("$statusveic"),1,1);


$pdf->Ln(1);
$pdf->Cell(12);
$pdf->MultiCell(170,4,utf8_decode("INFORMAÇÕES DA VISTORIA"),1,1);

//linha inteira
$pdf->Cell(12);
$pdf->MultiCell(15,4,utf8_decode("Tipo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(27);
$pdf->MultiCell(155,4,utf8_decode("$tipo"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(25,4,utf8_decode("Realizada por"),1,1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(78,4,utf8_decode("$vistoriador"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(115);
$pdf->MultiCell(15,4,utf8_decode("Data"),1,1);

$pdf->Ln(-4);
$pdf->Cell(130);
$pdf->MultiCell(52,4,utf8_decode("$datavistoriareal"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(25,4,utf8_decode("Estado geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(50,4,utf8_decode("$estado"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(30,4,utf8_decode("Possui avaria?"),1,1);

$pdf->Ln(-4);
$pdf->Cell(117);
$pdf->MultiCell(65,4,utf8_decode("$avaria"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(25,4,utf8_decode("Hodômetro"),1,1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(50,4,utf8_decode("$hodometro"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(30,4,utf8_decode("Nível do tanque"),1,1);

$pdf->Ln(-4);
$pdf->Cell(117);
$pdf->MultiCell(65,4,utf8_decode("$niveltanque"),1,1);

//linha inteira
$pdf->Cell(12);
$pdf->MultiCell(25,4,utf8_decode("Observação"),1,1);

$pdf->Ln(-4);
$pdf->Cell(37);
$pdf->MultiCell(145,4,utf8_decode("$observacao"),1,1);

$pdf->Ln(1);
$pdf->Cell(12);
$pdf->MultiCell(170,4,utf8_decode("ITENS PERSONALIZADOS DA VISTORIA"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Teto - Est Geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$teto"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Teto Geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$tetoesp"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Coluna do teto - esquerda"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$tetoesq"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Coluna do teto - lado direito"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$tetodir"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Frente Estado Geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$frente"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Capô"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$capo"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Parabrisa"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$parabrisa"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Farol esquerdo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$farolesq"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Farol direito"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$faroldir"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Para-choque dianteiro"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$parachoque"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Grade frontal"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$grade"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Lateral esquerda - Estado Geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$latesq"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Para-lama esquerdo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$paralamaesq"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Retrovisor Lado Esquerdo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$retrovesq"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Caixa de Ar Lado Esquerdo"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$cxaresq"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Porta Dianteira Esquerda"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$ptdiantesq"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Porta Traseira Esquerda"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$pttrasesq"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Lateral direita - Estado Geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$latdir"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Para-lama direito"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$paralamadir"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Retrovisor Lado Direito"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$retrovdir"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Caixa de Ar Lado Direito"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$cxardir"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Porta Dianteira Direita"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$ptdiantdir"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Porta Traseira Direita"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$pttrasdir"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Traseira - Estado Geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$traseira"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Lanterna Esquerda"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$lantesq"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Lanterna Direita"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$lantdir"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Tampa da Mala"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$tmpmala"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Para-choque Traseiro"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$parachoquet"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Itens Internos Estado Geral"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$itinterno"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Painel"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$painel"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Som"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$som"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Bancos"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$bancos"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Iluminação Interna"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$ilumint"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Tampa do Bagagito"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$tmpbag"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Retrovisor interno"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$retrovint"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Tapetes"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$tapetes"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Pneus"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$pneus"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Step"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$step"),1,1);


//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Kit Step"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$kitstep"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Calotas"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$calotas"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Marca bateria"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$bateria"),1,1);

//esquerda
//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("SafeCar"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$safecar"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Limpeza interna"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(27,4,utf8_decode("$limpint"),1,1);

//direita
$pdf->Ln(-4);
$pdf->Cell(87);
$pdf->MultiCell(55,4,utf8_decode("Limpeza externa"),1,1);

$pdf->Ln(-4);
$pdf->Cell(142);
$pdf->MultiCell(40,4,utf8_decode("$limpext"),1,1);

//esquerda
//$pdf->Ln(2);
$pdf->Cell(12);
$pdf->MultiCell(48,4,utf8_decode("Marca dos Pneus"),1,1);

$pdf->Ln(-4);
$pdf->Cell(60);
$pdf->MultiCell(122,4,utf8_decode("$marcapneus"),1,1);

$pdf->Ln(1);
$pdf->Cell(2);
//$pdf->MultiCell(181, 3, "oi", 0, 'J', false);
$pdf->MultiCell(181, 3,utf8_decode("CIENTE QUE:\n    No exercício da função ao qual fui contratado, estou ciente de que necessitarei de deslocamento em via pública e que, para tanto, a empresa disponibilizará veículo próprio para esse fim. Declaro-me possuidor de Carteira Nacional de Habilitação (CNH), a qual se encontra em pleno vigor, e conhecedor do atual Código Nacional de Trânsito, estando ciente que as penalidades advindas de eventuais transgressões (Multas e/ou Avarias) são de minha responsabilidade, podendo a empresa efetuar descontos.\n    Comprometo-me neste ato a zelar pelo mesmo, e responsabilizando-me por qualquer avaria ou dano causado, inclusive a terceiro, desde que devidamente comprovada a minha responsabilidade. Obrigo-me também, na hipótese de ocorrer qualquer acidente, a providenciar o Registro de Ocorrência (R.O.) junto a delegacia e prestar o auxílio necessário e pronto atendimento no caso de possível (eis) vítima (s), eximindo a empresa de problemas jurídicos que possam ocorrer, tanto criminalmente, como civilmente. Declaro estar recebendo/devolvendo (vide tipo checklist) o veículo anteriormente identificado com documento e chave nas condições descritas.\n"),0, 'J', false);

if(!empty($frontal)){

  //$pdf->Ln(2);
  //$pdf->Cell(12);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO FRONTAL"),1,1);
  $pdf->Image("..$frontal",12,203,32,32);
}

if(!empty($fototraseira)){
  //$pdf->Ln(-5);
  //$pdf->Cell(75);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO TRASEIRA"),1,1);
  $pdf->Image("..$fototraseira",45,203,32,32);
}

if(!empty($direita)){
  //$pdf->Ln(-5);
  //$pdf->Cell(130);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO LATERAL DIREITA"),1,1);
  $pdf->Image("..$direita",78,203,32,32);
}
//

if(!empty($esquerda)){
  //$pdf->Ln(52);
  //$pdf->Cell(20);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO LATERAL ESQUERDA"),1,1);
  $pdf->Image("..$esquerda",111,203,32,32);
}

if(!empty($selfie)){
  //$pdf->Ln(-5);
  //$pdf->Cell(75);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO SELFIE VISTORIADOR"),1,1);
  $pdf->Image("..$selfie",144,203,32,32);
}


if(!empty($fotocnh)){
  //$pdf->Ln(-5);
  //$pdf->Cell(130);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO CNH"),1,1);
  $pdf->Image("..$fotocnh",176,203,32,32);
}

//

if($extra1 != null){
  //$pdf->Ln(55);
  //$pdf->Cell(20);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 01"),1,1);
  $pdf->Image("..$extra1",12,236,32,32);
}

if($extra2 != null){
  //$pdf->Ln(-5);
  //$pdf->Cell(75);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 02"),1,1);
  $pdf->Image("..$extra2",45,236,32,32);       
}

if($extra3 != null){
  //$pdf->Ln(-5);
  //$pdf->Cell(130);
  //$pdf->MultiCell(50,5,utf8_decode("FOTO EXTRA 03"),1,1);
  $pdf->Image("..$extra3",78,236,32,32);
}


if($extra1 == ''){
  $ln = 40;
  $yassvist = 242;
  $yimgvist = 265;

  $yasscond = 242;
  $yimgcond = 265;

}else{
  $ln = 90;
  $yassvist = 10;
  $yimgvist = 32;

  $yasscond = 10;
  $yimgcond = 32;
}

$pdf->Ln($ln);
$pdf->Cell(12);
$pdf->MultiCell(80,5,utf8_decode("\n_________________________________________________\nAssinatura do Vistoriador \n $vistoriador"),0,'L',false);

if($matrvistoriador){// != $matricula vistoriador diferente do condutor
  if($assinadovist==1){
    $filename= "http://ffasip.ddns.net:4242/assinaturas/docs/colass/$matrvistoriador.png";
    $file_headers = @get_headers($filename);

    if($file_headers[0] == 'HTTP/1.0 200 OK' || $file_headers[0] == 'HTTP/1.1 200 OK'){
      $pdf->Image("http://ffasip.ddns.net:4242/assinaturas/docs/colass/$matrvistoriador.png",30, $yassvist,50);

      if(file_exists("..$imgassinaturavist")){
        $pdf->Image("..$imgassinaturavist",25, $yimgvist,25,25);
      }else{
        
        $filename2= "http://ffasip.ddns.net:4242/frotas/checklist$imgassinaturavist";
        $file_headers2 = @get_headers($filename2);

        if($file_headers2[0] == 'HTTP/1.0 200 OK' || $file_headers2[0] == 'HTTP/1.1 200 OK'){
          
          $pdf->Image("http://ffasip.ddns.net:4242/frotas/checklist$imgassinaturavist",25, $yimgvist,25,25);
        }
      }
    }
  }
} /*else {//condutor fez a vistoria
  if(file_exists("../../../assinaturas/docs/colass/$matricula.png") && file_exists("..$imgassinaturacond")){
      
    if(!empty($imgassinaturacond)){

    $pdf->Image("http://ffasip.ddns.net:4242/assinaturas/docs/colass/$matricula.png",30, $yimgvist,50);
    $pdf->Image("..$imgassinaturacond",25, $yimgvist,25,25);

    }  
          
  }
}*/

$pdf->Ln(-20);
$pdf->Cell(95);
$pdf->MultiCell(95,5,utf8_decode("\n_________________________________________________\nAssinatura do Condutor \n $nome"),0,'R',false);


if($assinado==1){
  /*if(file_exists("../../../assinaturas/docs/colass/$matricula.png") && file_exists("..$imgassinaturacond")){
    
    $pdf->Image("../../../assinaturas/docs/colass/$matricula.png",120, $yasscond,50);

    $pdf->Image("..$imgassinaturacond",175, $yimgcond,25,25); 
  
  }*/

  $filename3 = "http://ffasip.ddns.net:4242/assinaturas/docs/colass/$matricula.png";
  $file_headers3 = @get_headers($filename3);

  if($file_headers3[0] == 'HTTP/1.0 200 OK' || $file_headers3[0] == 'HTTP/1.1 200 OK'){
    

    $pdf->Image("http://ffasip.ddns.net:4242/assinaturas/docs/colass/$matricula.png",120, $yasscond,50);

    if(!empty($imgassinaturacond) && file_exists("..$imgassinaturacond")){
      
      $pdf->Image("..$imgassinaturacond",175, $yimgcond,25,25);

    }else{
      
      $filename4= "http://ffasip.ddns.net:4242/frotas/checklist$imgassinaturacond";
      $file_headers4 = @get_headers($filename4);
      $pdf->MultiCell(80,5,utf8_decode("\n$file_headers4[0] $imgassinaturacond"),0,'L',false);
      
      if($file_headers4[0] == 'HTTP/1.0 200 OK' || $file_headers4[0] == 'HTTP/1.1 200 OK'){
      //$pdf->MultiCell(80,5,utf8_decode("\n$file_headers4[0] $imgassinaturacond"),0,'L',false);
        
        $pdf->Image("http://ffasip.ddns.net:4242/frotas/checklist$imgassinaturacond",175, $yimgcond,25,25);
      }
    }
  }
}

  //img teste   $pdf->Image("../../../assinaturas/docs/colass/003535.png",85, 36,50);
//}


$pdf->Output();

?>

