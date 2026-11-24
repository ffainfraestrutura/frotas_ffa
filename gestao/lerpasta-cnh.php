<?php //lista todos arquivos do diretório origem e move para a pasta destino (um arquivo de cada vez, em lopping)
date_default_timezone_set('America/Sao_Paulo');
session_start();
//header("refresh: 60");

// data e hora atual
$data = date('Y-m-d H:i');
$data1 = str_replace("-","", $data);
$data2 = str_replace(" ","", $data1);
$datafim = str_replace(":", "", $data2);


// Nome do diretório inicial
$dirin = 'origem/';

// Nome do diretório final
$dirfim = 'destino/';

$diretorio = dir($dirin);
//$qtd = count($diretorio);

//echo "Lista de Arquivos do diretório '<strong>".$dirin."</strong>':<br />";
 
while($arquivo = $diretorio -> read()){
  if ($arquivo != '.' && $arquivo != '..'){
    //echo $dirin.$arquivo.'<br>';
    
    rename($dirin.$arquivo, $dirfim.$datafim.$arquivo);
    
    /*if(rename($dirin.$arquivo, $dirfim.$datafim.$arquivo) ==true){
      //echo $dirfim.$datafim.$arquivo;
      //echo "Arquivo Movido";
    }*/

    //echo "Arquivo Movido";
  }
  //sleep(120);
}

$diretorio -> close();

?>

