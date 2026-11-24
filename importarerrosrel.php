<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/* selecionar hoje*/
$hoje = date('Y-m-d H:i:s');

include './vendor/autoload.php';

//include "./conecta.php";

include "./conecta2.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

//print "<a href='./vendor/autoload.php'>caminho</a>";
$nome = $_FILES["arquivo"]["name"];

if($_FILES["arquivo"]["name"] != '')
{ 

  $allowed_extension = array('xls', 'xlsx');
  $file_array = explode(".", $_FILES["arquivo"]["name"]);
  $file_extension = end($file_array);
  
  if(in_array($file_extension, $allowed_extension)){
        
    $reader = IOFactory::createReader('Xlsx', 'xls');
    $arquivo = "$nome-$hoje.$file_extension";

    $spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);
    $data = $spreadsheet->getActiveSheet()->toArray();

$N = 1;
    foreach($data as $row){
      
      
      if($row[0]<>'Matricula'){
        $matricula = str_replace("M ", "", $row[0]);
        $op=$row[2];
        $datapedido = $row[1];


        $datapedido2= (explode(" ", $datapedido));
        $datapedido3= (explode("/", $datapedido2[0]));
        $hora= $datapedido2[1];
        $mes= $datapedido3[0];
        $dia= $datapedido3[1];
        $ano= $datapedido3[2];
        $datareal= $ano."-".$mes."-".$dia." $hora:00";
        
       echo "$N - $matricula - $op - $datapedido <br>";
        $sql = "INSERT INTO bdfrota.tbclarotim (matricula, data, operadora) VALUES ('$matricula', '$datapedido', '$op');";
        //echo "$sql <br>";
        $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

         $N = $N + 1;
      }
    }
      
  }
}





?>