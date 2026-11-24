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

//checklist_ID_1454143_QOT4475_20230814_102709.pdf  checklist_ID_1454143_QOT4475_20230814_102709.pdf
/*while($arquivo = $diretorio -> read()){
  if ($arquivo != '.' && $arquivo != '..'){*/

$arquivo='checklist_ID_1454143_QOT4475_20230814_102709.pdf';
    $ext = explode(".", $arquivo);

    /*if(strlen($arquivo)>32){*/
      $p = (explode('_',$arquivo));

      $arquivof = $p[0]."_".$p[1]."_".$p[2]."_".$p[3].".".$ext[1];

      $nomeinicial = $dirin.$arquivo;
      $nome = $dirin.$arquivof;


      //$nome = $dirfim.$arquivo;

      //copy($nomeinicial, $nome);

     if(file_exists($nome)){
        echo "já existe";
      } else{
        
        if(copy($nomeinicial, $nome)){ //rename($nomeinicial, $nome)
            echo "Successfully Renamed $arquivo to $arquivof." ;
        }else{
            //echo "não consegue copiar de $nomeinicial para $nome.<br>" ;
            echo "não consegue copiar de $arquivo para $arquivof.<br>";
        } 
      }

       

      //echo "- $dirfim.$arquivof<br>";

      //echo "Arquivo Movido";

    //}*/
  /*}
}*/

$diretorio -> close();

?>

