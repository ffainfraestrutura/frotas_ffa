<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');  

//$tipoposse = utf8_decode($_POST['tipoposse']);
//$locador = utf8_decode($_POST['locador']);
$matr_autor = $_POST['matr_autor'];
$arquivo = $_POST['arquivo'];

$_UP['pasta'] = '../docs/';

include '../vendor/autoload.php';

include_once"../../func/log.php";
include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/*$a = $_FILES["arquivo"]["name"];
print $a;*/

if($_FILES["arquivo"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	$file_array = explode(".", $_FILES["arquivo"]["name"]);
	$file_extension = end($file_array); 

	if(in_array($file_extension, $allowed_extension)){
		//$reader = IOFactory::createReader('Xlsx', 'Xls');
		//$reader = IOFactory::createReader('Xlsx');
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['arquivo']['tmp_name']);
		$reader->setReadDataOnly(TRUE);

		$arquivo = "$nome-$hoje.$file_extension";
		$spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);
		$data = $spreadsheet->getActiveSheet()->toArray();

		foreach($data as $row){
			
			if($row[0]<>'Placa'){ 
				$placa = $row[0];
				$hodometro = $row[1];

		        $sql1="SELECT * FROM bdfrota.tbveiculo WHERE placa='$placa' ";
				$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
				$linhas = mysqli_num_rows($resultado1);

				if($linhas<=0){

				}else{
		        	$sql ="UPDATE bdfrota.tbveiculo SET hodometro = '$hodometro', datamovimentacao = '$hoje' WHERE placa = '$placa' ";
		        	$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
		        }
				
			}
		}
	}
}

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Atualizou hodometros';
$tipo = 'atualizou';
$logresult = enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placacorreta);

//print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../listagemveiculos.php\"</script>";

// Depois verifica se é possível mover o arquivo para a pasta escolhida
if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
	// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
	print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../listagemveiculos.php\"</script>";

} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta
	echo "Não foi possível enviar o arquivo, tente novamente";
}


?>	