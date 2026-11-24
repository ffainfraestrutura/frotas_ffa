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

include '../vendor/autoload.php';

include_once"../../func/log.php";
include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


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
			
			if($row[0]<>'Matrícula'){ 
				$matricula = $row[0];
				$statuscond = $row[1];
				$placa = $row[2];
				$numcnh = $row[3];
				$categoria = $row[4];
				$validadecnh = $row[5];
				$ufcnh = utf8_decode($row[6]);
				
				$pontos = $row[7];
				$suspensa = $row[8];
				
				$cartao = $row[9];
				$dataassoc = $row[10];
				$datadissoc = $row[11];
				

				//tratando a matricula strpos("I love php, I love php too!","php");
				if(strpos($matricula, "'") !== false){
					$matricula =substr($matricula, 1);
				}
				

				//tratando numcnh
				if(strpos($numcnh, "'") !== false){
					$numcnh =substr($numcnh, 1);
				}

				//tratando data
				$validadecnh1 = explode(" ", $validadecnh);
				$validadecnh2 = explode("/", $validadecnh1[0]);
				$validadecnh3 = $validadecnh2[2]."-".$validadecnh2[1]."-".$validadecnh2[0]." ".$validadecnh1[1];

				$dataassoc1 = explode(" ", $dataassoc);
				$dataassoc2 = explode("/", $dataassoc1[0]);
				$dataassoc3 = $dataassoc2[2]."-".$dataassoc2[1]."-".$dataassoc2[0]." ".$dataassoc1[1];
				if($dataassoc3 =='-- '){
					$dataassoc3 ='0000-00-00';
				}

				$datadissoc1 = explode(" ", $datadissoc);
				$datadissoc2 = explode("/", $datadissoc1[0]);
				$datadissoc3 = $datadissoc2[2]."-".$datadissoc2[1]."-".$datadissoc2[0]." ".$datadissoc1[1];
				if($datadissoc3 =='-- '){
					$datadissoc3 ='0000-00-00';
				}
				
				if($statuscond=='ATIVO'){
					$statuscond='1';
				}else{
					$statuscond='0';
				}

				if($suspensa=='NÃO' || $suspensa=='NAO' || $suspensa=='não' || $suspensa=='nao'){
					$suspensa='0';
				}elseif($suspensa=='SIM' || $suspensa=='sim'){
					$suspensa='0';
				}

				if($matricula != ''){
		          //selecionando  nome com matricula
		          $sql2 = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula='$matricula'; ";
		          $resultado2= mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
		          $row2=mysqli_fetch_array($resultado2, MYSQLI_BOTH);
		            $nomeaniel=$row2['nome'];
		        }       

		        if($placa!=''){
		        	//atualizando tbcondutor
			        $sql3 = "SELECT matricula FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND placaassoc='$placa' ; ";
			        $resultado3= mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));

			        if(mysqli_num_rows($resultado3)<=0){
			          $sql="INSERT INTO bdfrota.tbcondutor(nome, matricula, placaassoc, dataassoc, datadissoc, ativo)  VALUES ('$nomeaniel', '$matricula','$placa', '$dataassoc3', '$datadissoc3', '$statuscond');";
			          $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
			          //print "$sql <hr>";
			        } else {
			        	if($datadissoc3 != '0000-00-00'){
			        		$sql="UPDATE bdfrota.tbcondutor SET datadissoc='$datadissoc3', ativo='$statuscond'  WHERE placaassoc = '$placa' AND matricula = '$matricula';";
			          		//$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

			          		//echo $sql."<hr> $datadissoc $dataassoc $validade";
			        	}
			          
			        }

			        $sql5="UPDATE bdfrota.tbveiculo SET matcond = '$matricula' WHERE placa = '$placa' ";
			        $resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));

		        }
		        

		        if($numcnh!=''){
		        	//atualizando tbcnh
			        $sql4 = "SELECT matricula FROM bdfrota.tbcnh WHERE matricula='$matricula'; ";
			        $resultado4= mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));

			        if(mysqli_num_rows($resultado4)<=0){
			          $sql="INSERT INTO bdfrota.tbcnh(numcnh, validade, uf, categoria, matricula, pontos, suspensa)  VALUES ('$numcnh', '$validadecnh3','$ufcnh', '$categoria', '$matricula', '$pontos','$suspensa');";
			          $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
			          //print "$sql <hr>";
			        } else {
			          $sql="UPDATE bdfrota.tbcnh SET numcnh='$numcnh', validade='$validadecnh3', uf='$ufcnh', categoria='$categoria', pontos='$pontos', suspensa='$suspensa'  WHERE matricula = '$matricula';";
			          $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
			          
			          //echo $sql."<hr>";
			        }
		        }
		        
				
				
			}
		}
	}					
}

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Importou condutores em lote';
$tipo = 'cadastro';
$logresult = enviarlognovo($hoje, $acao, '', $matr_autor, $tipo, '');
//print "$hoje, $acao, $matricula, $matr_autor, $tipo, $placa";

print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../listagemcondutores.php\"</script>";


// Depois verifica se é possível mover o arquivo para a pasta escolhida
/*if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $arquivo)) {
	// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
	print "<script language='javascript' type='text/javascript'>alert('Importado com sucesso sucesso!!!');window.location=\"../listagemmultas.php\"</script>";

} else {
	// Não foi possível fazer o upload, provavelmente a pasta está incorreta
	echo "Não foi possível enviar o arquivo, tente novamente";
}*/


?>	