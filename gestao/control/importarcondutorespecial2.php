<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');

include '../vendor/autoload.php';

include("../../conecta2.php");

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
			
			if($row[0]<>'Nome'){ //lista de condutor
				$nome = utf8_decode($row[0]);
				$matriculacond = $row[1];
				$status = utf8_decode($row[2]);
				$statuscond = utf8_decode($row[3]);
				$placa = utf8_decode($row[4]);


				$matriculacond=substr($matriculacond, 1);				

				//tratando binarios
				if(utf8_encode($status) == 'ATIVO' || utf8_encode($status) == 'ativo'){
					$status = '1'; 
				} elseif(utf8_encode($status) == 'INATIVO' || utf8_encode($status) == 'inativo') {
					$status='0';
				} 

				//tratando demissão
				if(utf8_encode($statuscond) == 'DEMISSÃO' || utf8_encode($statuscond) == 'demissão'){
					$statuscond = 'DEMITIDO';
				}

				$sql="SELECT matricula FROM bdfrota.tbcondutor";
                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                while($row=mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                	$mat = $row['matricula'];

                	if($mat == $matriculacond){
                		$sql1="UPDATE bdfrota.tbcondutor SET statuscond = '$statuscond', ativo='$status' WHERE matricula = '$matriculacond' AND placaassoc='$placa' ";
                		$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
                		echo "$sql1 <hr>";
                	}
                }


				/*$sql="UPDATE bdfrota.tbcondutor SET ativo = '$status' WHERE matricula = '$matriculacond' ";
                $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));*/
                
			}
		}					
	}	
}

// Depois verifica se é possível mover o arquivo para a pasta escolhida
/*if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
	// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
	print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../listagemmultas.php\"</script>";

} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta
	echo "Não foi possível enviar o arquivo, tente novamente";
}*/


?>	