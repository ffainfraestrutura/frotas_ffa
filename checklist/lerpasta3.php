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
$dirin = './';

// Nome do diretório final
$dirfim = './nextfleet/';

$diretorio = dir($dirin);
//$qtd = count($diretorio);

//checklist_ID_1454143_QOT4475_20230814_102709.pdf 
while($arquivo = $diretorio -> read()){
  if ($arquivo != '.' && $arquivo != '..'){

    $ext = explode(".", $arquivo);

    
      echo "- $arquivo<br>";//$arquivo      

    rename($arquivo, $arquivo."2");

    

    
  }
  
}

$diretorio -> close();

?>

