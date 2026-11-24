<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');
$hoje1 = str_replace(" ", "", $hoje);
$hoje1 = str_replace("-","",$hoje1);
$hoje1 = str_replace(":","",$hoje1);

include '../vendor2/autoload.php';

//include("../../conecta2.php");

include("../../conecta03.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "cnh";
// Nome do diretório para onde mover
$_UP['pasta'] = '../docs/condutor/';

$matr_autor = $_POST['matr_autor'];

$file_array = explode(".", $_FILES["arquivo"]["name"]);
$file_extension = end($file_array);
$arquivo = "$nome-$hoje1.$file_extension";

$arqtemp = $_FILES['arquivo']['tmp_name'];

//print $tipoposse."<br>".$locador."<br>".$arquivo."<br>".$dirfinal;

if($_FILES["arquivo"]["name"] != '')
{
	$allowed_extension = array('xls', 'xlsx');
	/*$file_array = explode(".", $_FILES["arquivo"]["name"]);
	$file_extension = end($file_array);*/

	if(in_array($file_extension, $allowed_extension)){

		$reader = IOFactory::createReader('Xlsx', 'xls');		
		$spreadsheet = $reader->load($_FILES['arquivo']['tmp_name']);
		$data = $spreadsheet->getActiveSheet()->toArray();


		foreach($data as $row){
			
			if($row[0]<>'Matrícula'){ //print "oi"; '', '', '$ufcnh', '', '$matriculacond', '', '$suspensa'
				$matriculacond = $row[0];
				$numcnh = $row[1];				
				$validadecnh = $row[2];
				$ufcnh = $row[3];
				$categoria = $row[4];
				$pontos = $row[5];
				$consulta = $row[6];
				$suspensa = $row[7];

				if(strpos($matriculacond,"'") !== false){
					$matriculacond =substr($matriculacond, 1);
				}

				if(strpos($numCNH,"'") !== false){
					$numcnh =substr($numcnh, 1);
				}								

				//tratando data
				if($validadecnh !=''){
					/*$validadeCNH1 = explode(" ", $validadeCNH);
					$validadeCNH2 = explode("/", $validadeCNH1[0]);
					$validadeCNH3 = $validadeCNH2[2]."-".$validadeCNH2[1]."-".$validadeCNH2[0]." ".$validadeCNH1[1];*/

					if(strpos($validadecnh,"'") !== false){
						$validadecnh =substr($validadecnh, 1);
					}

					$validadecnh2 = explode("/", $validadecnh);
					$validadecnh3 = $validadecnh2[2]."-".$validadecnh2[1]."-".$validadecnh2[0];
				}

				//print "$validadecnh<br>$validadecnh3<hr>";

				//tratando suspensa
				if(utf8_encode($suspensa) == 'NÃO' || utf8_encode($suspensa)=='não'){
					$suspensa='0';
				}elseif($suspensa == 'NAO' || $suspensa=='nao'){
					$suspensa='0';
				}elseif ($suspensa=='SIM'||$suspensa == 'sim') {
					$suspensa = '1';
				}

				$sql="SELECT matricula FROM bdfrota.tbcnh WHERE matricula='$matriculacond';";
                $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                $linhas1 = mysqli_num_rows($resultado);

                if($linhas1 <=0){
                	$sql2 = "INSERT INTO bdfrota.tbcnh (numcnh, validade, uf, categoria, matricula, pontos, suspensa, consulta) 
                	VALUES  ('$numcnh', '$validadecnh3', '$ufcnh', '$categoria', '$matriculacond', '$pontos', '$suspensa', '$consulta')";
                	$resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($conexao));
					//print "- $sql2<br>";
                }else{
                	$sql2a = "UPDATE bdfrota.tbcnh SET numcnh='$numcnh', validade='$validadecnh3', uf='$ufcnh', categoria='$categoria', pontos = '$pontos', suspensa = '$suspensa', consulta='$consulta' WHERE matricula='$matriculacond';";
                	$resultado2a = mysqli_query($con, $sql2a) or die(mysqli_error($conexao));
                	
                }
			}
		}					
	}

		$log = new Log($con);	  
	  $acao='Importou cnh em lote';
	  $tipo = 'cadastro';
	  $logresult = $log->enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');

	// Depois verifica se é possível mover o arquivo para a pasta escolhida
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
		// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
		print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.close();</script>";

		//window.location=\"../listagemcondutores.php\"

	} else {
		// Não foi possível fazer o upload, provavelmente a pasta está incorreta
		//echo "Não foi possível enviar o arquivo, tente novamente";

		print "<script language='javascript' type='text/javascript'>alert('Não foi possível enviar o arquivo, tente novamente.');window.close();\"</script>";

	}
}




?>	