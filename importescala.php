<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
$mes = date('m');
$ano = date('Y');
$diasmes = date('t');
$mesesc = strftime('%B');

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

		$sql = "truncate table bdfrota.tbescala";
		$resultado = mysql_query($sql) or die(mysql_error());
		


		foreach($data as $row)
		{
			if ($row[0] <> 'DADOS FUNCIONÁRIO' and $row[0] <> 'NOME' and $row[0] <> 'REPARO_RJ' and $row[0] <> '') {
				$nome = str_replace($comAcentos, $semAcentos, $row[0]);
				$nome = str_replace('0', 'O', $nome);
				$sql = "SELECT matricula FROM bdaniel.tbfuncionario where nome = '$nome' and status <> 'demitido'";
				$resultado = mysql_query($sql) or die(mysql_error());
				$rowsql = mysql_fetch_array($resultado);
				$matricula = $rowsql[0];
				$i = 1;
				$coltrab = 3;
				$dia = 21;
				while ($i <= $diasmes) {
					$data = "$ano-$mes-$dia";
					if ($row["$coltrab"] <> 'P') {
						$trabalho = '1';
					} else {
						$trabalho = '0';
					}
					$semesc = strftime('%a', strtotime("$data"));
					$i = $i + 1;
					$coltrab = $coltrab + 1;
					$dia = $dia + 1;
					if ($dia > $diasmes) {
						$dia = 1;
					}
					if ($matricula <> '') {
						echo "$nome - $matricula - $data - $trabalho - $semesc <br>";
						$sql = "insert into bdfrota.tbescala(matricula, mes, ano, dia, status, semana)  
								values('$matricula', '$mes', '$ano', '$data', '$trabalho', '$semesc')";
						$resultado = mysql_query($sql) or die(mysql_error());
					}						
				}
				
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