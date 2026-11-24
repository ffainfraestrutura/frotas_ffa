<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');

$hoje= date('Y-m-d H:i:s');

$hoje1 = str_replace(" ","", $hoje);
$hoje1 = str_replace("-","", $hoje1);
$hoje1 = str_replace(":","", $hoje1);

include("../../conecta03.php");
include_once"../../func/log3.php";


$autoinfra = $_POST['autoinfracao'];
$placa = $_POST['placa'];
$matricula = $_POST['matricula'];


$arquivo = $_FILES['frontal']['name'];
//print "$autoinfra - $placa - $matricula - $arquivo";

$sql1="SELECT recibolocass FROM bdfrota.tbmovidatramite WHERE autoinfra='$autoinfra' AND placa='$placa'; ";
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($conexao));
$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
  $recibolocass = $row1['recibolocass'];

if(!empty($arquivo)){
  
  $_UP['pasta'] = '../docs/assinaturas/';
  
  //$_UP['tamanho'] = 1024 * 1024 * 8; // 2Mb
 
  $_UP['extensoes'] = array('jpg', 'gif', 'pdf');

  $_UP['renomeia'] = false;
 
  $_UP['erros'][0] = 'Não houve erro';
  $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
  $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
  $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
  $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
 
  // Verifica se houve algum erro com o upload. 
  if ($_FILES['frontal']['error'] != 0) {
    die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['frontal']['error']]);
    exit; // Para a execução do script
  }
 
  // Faz a verificação da extensão do arquivo
  $extensao = strtolower(end(explode('.', $_FILES['frontal']['name'])));
  if (array_search($extensao, $_UP['extensoes']) === false) {
    
    echo "Por favor, envie arquivos com as seguintes extensões: jpg, gif ou pdf";
  
  }/*else if ($_UP['tamanho'] < $_FILES['frontal']['size']) {
    
    echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
  
  }*/else {
    
    
    if ($_UP['renomeia'] == true) {
    
      //$nome_final = time().'.jpg';

      $pasta = '/docs/assinatura/';
      $nome_final = "ass-$hoje1-$matricula.$extensao";
      $nome_final1 = $pasta.$nome_final;

    } else {
      $pasta = '/docs/cnh/';
      $nome_final = "ass-$hoje1-$matricula.$extensao";
      $nome_final1 = $pasta.$nome_final;
    }

    

    $qualidade = 30;
        
    $path = $_FILES['frontal']['tmp_name'];
    $pathfinal = $_UP['pasta'] . $nome_final;

    if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
      $imagemTmp = imagecreatefromjpeg($path);
        
    }
    else if (stripos($extensao,'png')!== false){
      $imagemTmp=imagecreatefrompng($nome_final);
    }
    else if (stripos($extensao,'gif')!== false){
      $imagemTmp=imagecreatefromgif($nome_final);
    }
    else{
      //return 0; 
    }
    
    // Depois verifica se é possível mover o arquivo para a pasta escolhida
    //if (move_uploaded_file($_FILES['frontal']['tmp_name'], $_UP['pasta'] . $nome_final)) {
    if (imagejpeg($imagemTmp, $pathfinal, $qualidade) === true) {    
      
      
      $sql1 = "UPDATE bdfrota.tbmovidatramite SET reciboass ='sim' WHERE autoinfra= '$autoinfra' AND placa='$placa'";
      
      $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
      
      //if($recibolocass=='1'){
        //atualiza tbmovida tramite
        $sql3 = "UPDATE bdfrota.tbmovidatramite SET tramite = 'Finalizado Frota', tdp = 'Validar Recibo', tfin = 'Fazer Pagamento', status = 2, recibolocass=1 WHERE autoinfra='$autoinfra' AND placa='$placa';";
        $resultado = mysqli_query($con, $sql3) or die(mysqli_error($con));

        //atualiza a tbmulta
        $sql4 = "UPDATE bdfrota.tbmulta SET etapa = 'MULTA ASSINADA' WHERE placa='$placa' AND autoinfracao='$autoinfra'; ";
        $resultado4= mysqli_query($con, $sql4) or die(mysqli_error($con));

        //print $sql4;
      //}

      $log = new Log($con);
      $acao="Assinou recibo FFA - $autoinfra";
      $tipo = 'multa';
      $logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matricula, $tipo, $placa);
      
      echo "<script language='javascript' type='text/javascript'> alert('Assinado com sucesso.'); window.location=\"http://ffasip.ddns.net:4242/frotas/multas/reciboffaass.php?autoinfra=$autoinfra&placa=$placa\"; </script>";

      //window.close();
        
    } else { 

    	//print $_UP['pasta']; print  $nome_final ;
        
		  echo "<script language='javascript' type='text/javascript'> alert('Erro no envio da imagem.');window.location=\"http://ffasip.ddns.net:4242/frotas/multas/multascondutor.php\"; </script>";
      
    }
 
  }

}


?>