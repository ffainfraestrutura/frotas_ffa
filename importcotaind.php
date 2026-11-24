<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
$datat = '2022-12-21';
$hoje = date('Y-m-d H:i:s',strtotime("$datat"));
$mes = date('m',strtotime("$datat"));
$ano = date('Y',strtotime("$datat"));
$diasmes = date('t',strtotime("$datat"));
$mesesc = strftime('%B',strtotime("$datat"));

$comAcentos = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú');

$semAcentos = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U');

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

		foreach($data as $row)
		{
			if ($row[0] <> '') {
				$nome = str_replace($comAcentos, $semAcentos, $row[0]);
				$nome = str_replace('0', 'O', $nome);
				/*$sql = "SELECT matricula FROM bdaniel.tbfuncionario where nome like '%$nome%' and status <> 'demitido'";
				$resultado = mysql_query($sql) or die(mysql_error());
				$rowsql = mysql_fetch_array($resultado);
				$matricula = $rowsql[0];*/
				$matricula = $row[0];
				$valor = $row[2];
				$valor = str_replace(",", ".", $valor);

				echo "$matricula - $valor<br>";
				$sql = "insert into bdfrota.tbcotaindividual(matricula, cotadiaria)  
								values('$matricula', '$valor')";
				$resultado = mysql_query($sql) or die(mysql_error());

				
				
			}

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