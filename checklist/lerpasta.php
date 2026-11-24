<?php
 //lista todos arquivos do diretório origem e move para a pasta destino (um arquivo de cada vez, em lopping)
date_default_timezone_set('America/Sao_Paulo');
session_start();

// data e hora atual
$data = date('Y-m-d H:i');
$data1 = str_replace("-","", $data);
$data2 = str_replace(" ","", $data1);
$datafim = str_replace(":", "", $data2);


// Nome do diretório inicial
$dirin = 'nextfleet/';

// Nome do diretório final
$dirfim = './nextfleetf/';

$diretorio = dir($dirin);

//$qtd = count($diretorio);

while($arquivo = $diretorio -> read()){
  if ($arquivo != '.' && $arquivo != '..'){

    print $arquivo"<br>";
}
}
    

$diretorio -> close();

?>

