<?php
include("../../conecta2.php");

include '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "condutores";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/';

//print $tipoposse."<br>".$locador."<br>".$arquivo."<br>".$dirfinal;

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

        foreach($data as $row){
            
            if($row[0]<>'Matrícula'){ //print "oi";
                $matriculaexcel = utf8_decode($row[0]);
                
                $matriculaexcel=substr($matriculaexcel, 1);

                $sql="SELECT * FROM bdaniel.tbfuncionario where matricula='$matriculaexcel' ";
                $resultado= mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

                if(mysqli_num_rows($resultado)<=0){
                    print "opção 1 - não registrado no aniel | $matriculaexcel <hr>";
                } else{
                     print "opção 2 - registrado no aniel | $matriculaexcel <hr>";
                }

            }
        }
    }
}


 
?>