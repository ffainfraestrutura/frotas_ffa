<?php

//teste

include 'vendor/autoload.php';

$connect = new PDO("mysql:host=10.27.0.100;dbname=bdindicador", "blvp", "qrdrps");


include "conecta.php";



use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if($_FILES["import_excel"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	$file_array = explode(".", $_FILES["import_excel"]["name"]);
	$file_extension = end($file_array);
	if(in_array($file_extension, $allowed_extension))
	{
		$reader = IOFactory::createReader('Xlsx');
		$spreadsheet = $reader->load($_FILES['import_excel']['tmp_name']);

		$data = $spreadsheet->getActiveSheet()->toArray();

		$sql = "truncate table bdfrota.tbhccampotim";
		$resultado = mysql_query($sql) or die(mysql_error());
		
		date_default_timezone_set('America/Sao_Paulo');
		$hoje = date('Y-m-d H:i:s');

		foreach($data as $row)
		{
					$matricula = $row[0];
					$nome = $row[1];
					$estado = $row[2];
					$sup = $row[3];
					$cord =	$row[4];
					$ger = $row[5];
					$dir = $row[6];
					$spoa = $row[8];

					$empityrows = 1;
					if ($empityrows <= 8) {
					
					if ($row[0] == null or $row[0] == "Matricula") {
						$empityrows = $empityrows + 1;
					} else {

						$sql = "insert into bdfrota.tbhccampotim(matricula, nome, estado, supervisor, coordenador, gerente, diretor)  
								values('$matricula', '$nome', '$estado', '$sup', '$cord', '$ger', '$dir')";

						//echo "TESTE - $sql";
						$resultado = mysql_query($sql) or die(mysql_error());




			}		}
		}	
		$message = '<div class="alert alert-success">Importado com sucesso</div>';

	}
	else
	{
		$message = '<div class="alert alert-danger">Only .xls or .xlsx file allowed</div>';
	}
}
else
{
	$message = '<div class="alert alert-danger">Please Select File</div>';
}

echo $message;

?>