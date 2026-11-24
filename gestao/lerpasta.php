<?php //lista todos arquivos do diretório origem e move para a pasta destino (um arquivo de cada vez, em lopping)
date_default_timezone_set('America/Sao_Paulo');
session_start();

include "../conecta03.php";

/*if($con){
  print "tudo ok";
}*/

echo "inicio lerpasta";

// data e hora atual
$data = date('Y-m-d H:i');
$data1 = str_replace("-","", $data);
$data2 = str_replace(" ","", $data1);
$datafim = str_replace(":", "", $data2);


// Nome do diretório inicial
$dirin = 'docs/cnh/';

// Nome do diretório final
//$dirfim = 'destino/';

$diretorio = dir($dirin);


//echo "Lista de Arquivos do diretório '<strong>".$dirin."</strong>':<br />";
 
while($arquivo = $diretorio -> read()){
  
  if ($arquivo != '.' && $arquivo != '..'){
    

    $caminho = "/".$dirin.$arquivo;

    $extensao = ".". end(explode('.', $arquivo));
    $matricula1 = str_replace("cnh-","","$arquivo");
    $matricula = str_replace("$extensao","","$matricula1");

    //print "- $matricula <hr>";

    $sql="SELECT doc1 FROM bdfrota.tbcnh WHERE matricula='$matricula';";
    $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
    $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);
      $doc1 = $row['doc1'];

    $linhas = mysqli_num_rows($resultado);

    //print "- $sql<hr>";

    //print "- $matricula $doc1<hr>";
    if($linhas>0){
      if(empty($doc1)){
        //print "- $matricula $doc1<hr>";

        $sql2 = "UPDATE bdfrota.tbcnh SET doc1='$caminho' WHERE matricula='$matricula';";
        $resultado = mysqli_query($con, $sql2) or die(mysqli_error($con));


        print "- $sql2<hr>";
      }

    }
    

    
    
    
  }
  
}

$diretorio -> close();

echo "fim de lerpasta";



?>

