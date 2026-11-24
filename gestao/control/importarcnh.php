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

$nome = "cnh";
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
				$matricula = $row[0];
				$numCNH = $row[1];
				$catCNH = $row[2];
				$validadeCNH = utf8_decode($row[3]);
				$ufCNH = utf8_decode($row[4]);
				$pontosCNH = utf8_decode($row[5]);
				$consultaCNH = utf8_decode($row[6]);
				$suspensa = utf8_decode($row[7]);

				$matricula =substr($matricula, 1);
				$numCNH =substr($numCNH, 1);				

				//tratando data
				if($validadeCNH !=''){
					$validadeCNH1 = explode(" ", $validadeCNH);
					$validadeCNH2 = explode("/", $validadeCNH1[0]);
					$validadeCNH3 = $validadeCNH2[2]."-".$validadeCNH2[1]."-".$validadeCNH2[0]." ".$validadeCNH1[1];
				}

				//tratando suspensa
				if(utf8_encode($suspensa) == 'NÃO' || utf8_encode($suspensa)=='não'){
					$suspensa='0';
				}elseif($suspensa == 'NAO' || $suspensa=='nao'){
					$suspensa='0';
				}elseif ($suspensa=='SIM'||$suspensa == 'sim') {
					$suspensa = '1';
				}

				if($numCNH != '' && $matricula!=''){
					$sql="SELECT matricula FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'; ";              
              		$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
     				
     				if(mysqli_num_rows($resultado)<=0){
     					print " $matricula <hr>";

     				} /*else{
     					$row=mysqli_fetch_array($resultado, MYSQLI_BOTH);
     						$mataniel=$row['matricula'];

     					if($matricula == $mataniel){
     						print "PESSOA $matricula; $numCNH ;  $catCNH ; $validadeCNH3 ; $ufCNH; $pontosCNH ; $consultaCNH; $suspensa  <hr>";

     						/*$sql2="INSERT INTO bdfrota.tbcnh(numcnh, validade, uf, categoria, matricula, pontos, consulta, suspensa) VALUES ('$numCNH', '$validadeCNH3', '$ufCNH', '$catCNH', '$mataniel', '$pontosCNH', '$consultaCNH', '$suspensa') ";              
              				$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
     					}

					/*$sql="INSERT INTO bdfrota.tbcnh(numcnh, validade, uf, categoria, matricula, pontos, consulta, suspensa) VALUES ($numCNH, $validadeCNH3, $) ";              
              		$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));*/
					//print "PESSOA $matricula; $numCNH ;  $catCNH ; $validadeCNH3 ; $ufCNH; $pontosCNH ; $consultaCNH;  <hr>";
					//}
				}
				//break;
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