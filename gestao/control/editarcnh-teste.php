<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");

include_once"../../func/log.php";
include("../../conecta03.php");

/*$log = "../../func/log.php";
print '<br /><a href="' . $log . '">Clique aqui para acessar o arquivo</a>';
//$logresult = */

$hoje=date('Y-m-d H:i:s');

$nome= utf8_decode($_POST['nome']);
$matricula=$_POST['matricula'];
$cnh = $_POST['cnh'];
$validade = $_POST['validade'];
$uf = $_POST['uf'];
$categoria = $_POST['categoria'];
$pontos = $_POST['pontos'];
$consulta = $_POST['consulta'];
$suspensa = $_POST['suspensa'];
$matr_autor = $_POST['matr_autor'];

//echo $nome, $matricula, $cnh, $validade, $uf, $categoria, $pontos, $consulta, $suspensa;

$sql="SELECT doc1 FROM bdfrota.tbcnh WHERE matricula='$matricula';";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
$row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
  $doc1 = $row['doc1'];

//print $doc1;

$arquivo = $_FILES['arquivo']['name']; 

//print $arquivo;

if(!empty($arquivo)){
  
  $_UP['pasta'] = '../docs/cnh/';
  //$_UP['pasta'] = '../docs/';
  
  $_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
 
  $_UP['extensoes'] = array('jpg', 'png', 'gif', 'pdf');

  $_UP['renomeia'] = false;
 
  $_UP['erros'][0] = 'Não houve erro';
  $_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
  $_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
  $_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
  $_UP['erros'][4] = 'Não foi feito o upload do arquivo';
 
  // Verifica se houve algum erro com o upload. 
  if ($_FILES['arquivo']['error'] != 0) {
    die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
    exit; // Para a execução do script
  }
 
  // Faz a verificação da extensão do arquivo
  $extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
  if (array_search($extensao, $_UP['extensoes']) === false) {
    echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, gif ou pdf";
  }else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
    echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
  }else {
    
    if ($_UP['renomeia'] == true) {
    
      //$nome_final = time().'.jpg';

      $pasta = '/docs/cnh/';
      $nome_final = "cnh-".$matricula."(2).".$extensao;
      $nome_final1 = $pasta.$nome_final;

    } else {    
      //$nome_final = $_FILES['arquivo']['name'];

      if(!empty($doc1)){
        $nome_final = "cnh-".$matricula."(2).".$extensao;
      }else{
        $nome_final = "cnh-".$matricula.".".$extensao;
      }

      $pasta = '/docs/cnh/';
      
      $nome_final1 = $pasta.$nome_final;

      //print $nome_final1;
    }
    
    $aqr = $_FILES['arquivo']['tmp_name'];

    //print $aqr;
 
    // Depois verifica se é possível mover o arquivo para a pasta escolhida
    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {    
      echo "Upload efetuado com sucesso!";
      echo '<br /><a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a>';

      /*if(!empty($doc1)){
        $sql1 = "UPDATE bdfrota.tbcnh SET numcnh ='$cnh' , validade = '$validade', uf = '$uf', categoria = '$categoria', matricula = '$matricula' , pontos = '$pontos', consulta = '$consulta', suspensa = '$suspensa', doc2='$nome_final1' WHERE matricula = '$matricula'";
      }else{
        $sql1 = "UPDATE bdfrota.tbcnh SET numcnh ='$cnh' , validade = '$validade', uf = '$uf', categoria = '$categoria', matricula = '$matricula' , pontos = '$pontos', consulta = '$consulta', suspensa = '$suspensa', doc1='$nome_final1' WHERE matricula = '$matricula'";
      }
      $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
      
      $log = new Log($con);
      $acao='Editou cnh';
      $tipo = 'cadastro';
      $logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, '');

      $sqlog = "INSERT INTO bdfrota.tblog (data_e_hora, acao, matricula, mat_autor, tipo, placa) values ('$hoje', '$acao', '$matricula', '$matr_autor', '$tipo', '')";    
      $resultadolog = mysqli_query($con, $sqlog) or die(mysqli_error($con));
        

      echo "<script language='javascript' type='text/javascript'> alert('Cadastro de CNH editado com sucesso.');window.close(); </script>";*/
        
    } else { 
        /*print "aqui";*/      
      echo "Não foi possível enviar o arquivo, tente novamente";
      echo '<br /><a href="' . $_UP['pasta'] . '">Clique aqui para acessar o arquivo</a>';

      /*if(!empty($doc1)){
        $sql1 = "UPDATE bdfrota.tbcnh SET numcnh ='$cnh' , validade = '$validade', uf = '$uf', categoria = '$categoria', matricula = '$matricula' , pontos = '$pontos', consulta = '$consulta', suspensa = '$suspensa', doc2='$nome_final1' WHERE matricula = '$matricula'";
      }else{
        $sql1 = "UPDATE bdfrota.tbcnh SET numcnh ='$cnh' , validade = '$validade', uf = '$uf', categoria = '$categoria', matricula = '$matricula' , pontos = '$pontos', consulta = '$consulta', suspensa = '$suspensa', doc1='$nome_final1' WHERE matricula = '$matricula'";
      }
      $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));/

      //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
      $log = new Log($con);
      $acao='Editou cnh';
      $tipo = 'cadastro';
      $logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, '');
        

      echo "<script language='javascript' type='text/javascript'> alert('Não foi possível enviar o arquivo, tente novamente.');window.close(); </script>";*/

      //echo $aqr;
      
    }
 
  }
}

/*if(!empty($doc1)){
  $sql1 = "UPDATE bdfrota.tbcnh SET numcnh ='$cnh' , validade = '$validade', uf = '$uf', categoria = '$categoria', matricula = '$matricula' , pontos = '$pontos', consulta = '$consulta', suspensa = '$suspensa', doc2='$nome_final1' WHERE matricula = '$matricula'";
}else{
  $sql1 = "UPDATE bdfrota.tbcnh SET numcnh ='$cnh' , validade = '$validade', uf = '$uf', categoria = '$categoria', matricula = '$matricula' , pontos = '$pontos', consulta = '$consulta', suspensa = '$suspensa', doc1='$nome_final1' WHERE matricula = '$matricula'";
}
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
  $log = new Log($con);
  $acao='Editou cnh';
  $tipo = 'cadastro';
  $logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placa);
  

echo "<script language='javascript' type='text/javascript'> alert('Cadastro de CNH editado com sucesso.');window.close(); </script>";

//window.location=\"../listagemgeralcondutores.php\"

//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";
//print $sql1;*/
?>