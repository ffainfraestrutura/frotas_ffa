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
			
			if($row[0]<>'Placa'){ //lista de veiculos
				$placa = $row[0];
				$nome = utf8_decode($row[1]);
				$matricula = $row[2];
				$dataentrega = utf8_decode($row[3]);
				$datadev = utf8_decode($row[4]);

				$matricula =substr($matricula, 1);				

				//tratando data
				$dataentrega1 = explode(" ", $dataentrega);
				$dataentrega2 = explode("/", $dataentrega1[0]);
				$dataentrega3 = $dataentrega2[2]."-".$dataentrega2[1]."-".$dataentrega2[0]." ".$dataentrega1[1];

				if($datadev == '31/12/2999 23:59:59' || $datadev==''){
					$datadev3="0000-00-00 00:00:00";
				} else {
					$datadev1 = explode(" ", $datadev);
					$datadev2 = explode("/", $datadev1[0]);
					$datadev3 = $datadev2[2]."-".$datadev2[1]."-".$datadev2[0]." ".$datadev1[1];
				}
				
				if($matricula != ''){
					//selecionando  nome com matricula
					$sql2 = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='".$matricula."'; ";
					$resultado2= mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
					$row2=mysqli_fetch_array($resultado2, MYSQLI_BOTH);
						$nomeaniel=$row2['nome'];
								

					/*$sql3 = "SELECT matricula FROM bdfrota.tbcondutor WHERE matricula='".$matricula."'; ";
					$resultado3= mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));

					if(mysqli_num_rows($resultado3)<=0){*/
						$sql="INSERT INTO bdfrota.tbcondutor(nome, matricula, placaassoc, dataassoc, datadissoc)  VALUES ('$nomeaniel', '$matricula','$placa', '$dataentrega3', '$datadev3');";
						$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
						print "$sql <hr>";
					/*} else {
						$sql="UPDATE bdfrota.tbcondutor SET nome='$nomeaniel', placaassoc= '$placa', dataassoc='$dataentrega3', datadissoc='$datadev3'  WHERE placaassoc = '$placa' AND matricula = '$matricula';";
						$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
					
						echo $sql."<hr>";
					}*/				

					//print"placa $placa | nome $nome | matricula $matricula | datentrega $dataentrega3 | data devolução $datadev3  <hr>";				
					//break;
				}
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