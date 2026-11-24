<?php //lista todos arquivos do diretório origem e move para a pasta destino (um arquivo de cada vez, em lopping)
date_default_timezone_set('America/Sao_Paulo');
session_start();

include "../conecta03.php";

/*if($con){
  print "tudo ok";
}*/

echo "inicio lerpasta<hr>";

// data e hora atual
$data = date('Y-m-d H:i');
$data1 = str_replace("-","", $data);
$data2 = str_replace(" ","", $data1);
$datafim = str_replace(":", "", $data2);


// Nome do diretório inicial
$dirin = 'docs/termos/';

// Nome do diretório final
//$dirfim = 'destino/';

$diretorio = dir($dirin);


//echo "Lista de Arquivos do diretório '<strong>".$dirin."</strong>':<br />";
 
while($arquivo = $diretorio -> read()){
  
  if ($arquivo != '.' && $arquivo != '..'){

    if(strpos($arquivo,"TRC") !== false && strpos($arquivo,"pdf") !== false){
      $arq = explode("-", $arquivo);
      $matricula = $arq[0];

      $caminho = "/".$dirin.$arquivo;

      $sql = "UPDATE bdfrota.tbcnh SET termorescisao='$caminho' WHERE matricula='$matricula'; ";
      $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
      
      print "- $sql<br>";
      /*if(rename("docs/termos/$arquivo", "docs/termos/CA/$arquivo")){
        //print "- ok<br>";
        print "- $arquivo<br>";
      }*/     
            
    }
  }
  
}

$diretorio -> close();

echo "<br>fim de lerpasta";



?>

