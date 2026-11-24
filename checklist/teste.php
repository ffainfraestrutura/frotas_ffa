<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

//include"../conecta.php";
include"../conecta03.php";

//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");

$sql1 = "SELECT DISTINCT * FROM bdfrota.tbvistoria WHERE idtbvistoria='4728';";

/*datavistoria>='2023-08-31 00:00:00' AND datavistoria<='2023-12-01 23:59:59' AND nome<>'' AND (idtbvistoria<2995 or  idtbvistoria>3015) AND idtbvistoria NOT IN (3028, 3031, 3032, 4719, 4718) AND placa<>'ABC1234';*/

$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

while($row1 = mysqli_fetch_array($resultado1, MYSQLI_ASSOC)){
  $idtbvistoria = $row1['idtbvistoria'];
  $placacorreta = $row1['placa'];
  $datavistoria = $row1['datavistoria'];
  $assinado = $row1['assinadocond'];
  $idnextfleet = $row1['idnextfleet'];
  $tipo = $row1['tipo'];
  $nome = utf8_encode($row1['nome']);


  $datavistoria1 = explode(" ",$datavistoria);
  $datavistoria2 = explode("-", $datavistoria1[0]);
  $datavistoriareal = $datavistoria2[2] . "/" .$datavistoria2[1] . "/" .$datavistoria2[0];

  //$sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE placa = '$placacorreta' AND datavistoriaf='$datavistoria1[0]';";
  $sql5 = "SELECT * FROM bdfrota.tbvistoriafotos WHERE idtbvistoria ='$idtbvistoria' and idtbvistfotos not in (40,41,42,43,44,45,47) ;";
  $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
  $linhas = mysqli_num_rows($resultado5);

  if($linhas <= 0){
    //$link = '././fpdf/relatorio.php';
    $link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatorio.php';                
  } else {
    $row5 = mysqli_fetch_array($resultado5, MYSQLI_ASSOC);
      $extra1 = $row5['extra1'];
      $frontal = $row5['frontal'];

    //print "$extra1, $frontal";
    if(strpos($extra1, "pdf") !== false){
      $link = 'https://ffa.painel-telecom.com:4545/frotas/checklist/fpdf/relatorio.php';
    }else{
      $achou = 'f';     
      
      // Nome do diretório inicial
      $dirin = 'docs_old/';
      $buscar = substr($frontal, 10);

      $diretorio = dir($dirin);     
      
      while(($arquivo = $diretorio -> read()) && ($achou=='f')){
      //while($arquivo = $diretorio -> read()){
        
        if ($arquivo != '.' && $arquivo != '..' && $arquivo != 'ass'){
          //print "- $arquivo $buscar<hr>";
          
          if(strpos($arquivo, $buscar) !== false){
            $link = 'https://ffa.painel-telecom.com:4545/frotas/checklist/fpdf/relatoriocomfotos.php';
            print "- achou $arquivo, $buscar";
            //break;
            $achou = 'true';
          
          }else{
            $link = 'http://ffasip.ddns.net:4242/frotas/checklist/fpdf/relatoriocomfotos.php';
             print "- não achou $arquivo, $buscar";
            
            //break;
          }
          
          
        }
         
      }
      
      $diretorio -> close();
      print $link;
    }
  }               


}

              
?>            
