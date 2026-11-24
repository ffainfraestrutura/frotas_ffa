<?php
require'./fpdf.php';


require('../../conecta03.php');
header("Content-type: text/html; charset=utf-8");

$placacorreta = $_POST['placa'];
$datavistoria = $_POST['datavistoria'];
$idtbvistoria = /*$_POST['idtbvistoria']*/3872;


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
  $unidade = utf8_encode($row['unidade']);
  $centrocusto = utf8_encode($row['centrocusto']);

  $tipo = utf8_encode($row['tipo']);
  $vistoriador = utf8_encode($row['vistoriador']);
  $datavistoria = $row['datavistoria'];
  $estado = strtoupper(utf8_encode($row['estado']));
  $avaria = $row['avaria'];
  $hodometro = $row['hodometro'];
  $niveltanque = $row['niveltanque'];
  $observacao = utf8_encode($row['observacao']);

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
  $tipo= utf8_encode($row6[0]);

$sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria= '$idtbvistoria' AND idtbvistfotos not in (40,41,42,43,44,45,47, 2639,2640,2641,2642,2643,2644,2645,2646,2647, 3198,3199,3201) ;";

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

/*class PDF extends FPDF
{
  // Page header
  /*function Header()
  {
    // Logo
    //$this->Image('../../src/images/logoredonda.png',10,10,15);
    $this->Image('../../src/images/logoredonda.png',10,8,10);
    // Arial bold 15
    $this->SetFont('Arial','B',11);
    // Cor de fundo
    $this->SetFillColor(255);
    // Line break
    //$this->Ln(3);
    // Move to the right
    $this->Cell(12);
    // Title
    $this->Cell(170,6,utf8_decode('RELATÓRIO DE VISTORIA DE VEÍCULO'),1,1,'C','true');
    // Line break
    $this->Ln(2);
  }*
}*/

/*class PDF extends PDF_Rotate{
  //Função para rotacionar a imagem
  function RotatedImage($file,$x,$y,$w,$h,$angle) {
    //Image rotated around its upper-left corner
    $this->Rotate($angle,$x,$y);
    $this->Image($file,$x,$y,$w,$h);
    $this->Rotate(0);
  }
}*/

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->Output();

//print "ok $frontal $fototraseira $direita $esquerda $bateria $selfie $fotocnh $extra1 $extra2 $extra3";

//print $largurafrontal." x".$alturafrontal; //1366 x768


?>

