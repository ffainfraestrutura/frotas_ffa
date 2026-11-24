<?php
//importar os itens de excel para o BD
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

/*selecionar hoje*/
$hoje = date('Y-m-d H:i:s');

$tipoposse = utf8_encode($_POST['tipoposse']);
$locador = utf8_encode($_POST['locador']);
//$arquivo = $_POST['arquivo'];

include '../vendor/autoload.php';

include("../../conecta2.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$nome = "vistoriasnextfleet";
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
			
			if($row[0]<>'id'){ //print "oi";
				$id = $row[0];
				$tipo = utf8_decode($row[1]);
				$datavistoria = $row[2];
				$vistoriador = $row[3];
				$placa = $row[4];
				$modelo = utf8_decode($row[5]);
				$matriculacond = $row[6];
				$nome = utf8_decode($row[7]);
				$estado = utf8_decode($row[8]);
				$hodometro = $row[9];
				$niveltanque = utf8_decode($row[10]);
				$avaria = utf8_decode($row[11]);
				$observacao = utf8_decode($row[12]);
				$statusveic = utf8_decode($row[13]);
				$hodometroatual = $row[14];
				$unidade = utf8_decode($row[15]);

				//tratando datas
				if (!empty($datavistoria)){
					$datavistoria1 = explode(" ", $datavistoria);

					$datavistoria2 = explode("/", $datavistoria1[0]);
					$anoI =$datavistoria2[0];
					$mesI = $datavistoria2[1];
					$diaI=$datavistoria2[2];

					
					$datavistoriaf = $diaI."-".$mesI."-".$anoI." ".$datavistoria1[1];
				} else {
					$datavistoriaf = $datavistoria;
				}

				$matriculacond=substr($matriculacond, 1);				

				//tratando binarios
				if(utf8_encode($avaria) == 'SIM' || utf8_encode($avaria) == 'sim'){
					$avaria = '1'; 
				} elseif(utf8_encode($avaria) == 'NÃO' || utf8_encode($avaria) == 'não') {
					$avaria='0';
				} elseif(utf8_encode($avaria) == 'NAO' || utf8_encode($avaria) == 'nao') {
					$avaria ='0'; 
				}				

				//tratando dados vistoriador
				$vistoriador1 = explode(" | ", $vistoriador);
				$nomevist =  $vistoriador1[1];
				$matrvist = $vistoriador1[0];

				//selecionando codfilial,codempresa, nome, ccusto, cpf com matricula
				$sql2 = "SELECT codfilial, codempresa, nome, ccusto, cpf FROM bdaniel.tbfuncionario WHERE matricula='$matriculacond'; ";
				$resultado2= mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
				$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
				
				$codfilial = $row2['codfilial'];
				$codempresa = $row2['codempresa'];				
				$nomecond = $row2['nome'];
				$ccusto = $row2['ccusto'];
				$cpf = $row2['cpf'];

				/*if($nomecond == ''){
					$nomecond = 'vazionome';
				}*/

				//selecionando filial correta
				$sql5= "SELECT nome FROM bdaniel.tbfilial WHERE codempresa='$codempresa' AND codfilial='$codfilial'; ";
				$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
				$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
					$filial=$row5['nome'];

				//selecionando cnh,categoriacnh,validadecnh com matricula
				$sql3 = "SELECT numcnh,categoria,validade FROM bdfrota.tbcnh WHERE matricula='$matriculacond'; ";
				$resultado3= mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
				$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
					$numcnh = $row3['numcnh'];
					$categoria = utf8_encode($row3['categoria']);
					$validade = $row3['validade'];

				/*$sql3 = "SELECT matricula FROM bdaniel.tbfuncionario WHERE nome='".$nome."'; ";
				$resultado3= mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
				$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
					$matriculaaniel = utf8_encode($row3['matricula']);
					if($matriculaaniel == ''){
						$matriculaaniel = 'vazio';
					}

				$i=1;
				if($nomecond != $nome){

				print"$i - nome excel $nome | nome aniel $nomecond | matricula excel $matriculacond | matricula aniel $matriculaaniel  <hr>"; $i++;}*/

				//tratando status veiculo
				$sql4= "SELECT idtbastatusvel, status FROM bdfrota.tbastatusvel  WHERE status='$statusveic'";
				$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
				$row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
					$idtbastatusvel=$row4['idtbastatusvel'];
					$status = utf8_encode($row4['status']);

				$sql5 = "SELECT * FROM bdfrota.tbveiculo WHERE placa='$placa' AND datavistoria='$datavistoriaf' AND tipo='$tipo' ";
				$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));

				if(mysqli_num_rows($resultado)<=0){
					$sql="INSERT INTO bdfrota.tbvistoria (nome, matricula, cpf, cnh, categoriacnh, validadecnh, placa, modelo,  unidade, centrocusto, tipo, vistoriador, matrvistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao, statusveic, idnextfleet) VALUES ('$nome', '$matriculacond', '$cpf', '$numcnh', '$categoria', '$validade', '$placa', '$modelo',  '$filial', '$ccusto', '$tipo', '$nomevist', '$matrvist', '$datavistoriaf', '$estado', '$avaria', '$hodometro', '$niveltanque', '$observacao', '$idtbastatusvel', '$id')";
          			$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
				}else{
					$sql="UPDATE bdfrota.tbvistoria SET nome='$nome', matricula='$matriculacond', cpf='$cpf', cnh='$numcnh', categoriacnh='$categoria', validadecnh='$validade', placa='$placa', modelo='$modelo',  unidade='$filial', centrocusto='$ccusto', tipo='$tipo', vistoriador='$nomevist', matrvistoriador='$matrvist', datavistoria='$datavistoriaf', estado='$estado', avaria='$avaria', hodometro='$hodometro', niveltanque='$niveltanque', observacao='$observacao', statusveic='$idtbastatusvel',  idnextfleet='$id' WHERE placa='$placa' AND datavistoria='$datavistoriaf' AND tipo='$tipo';";
          			$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
				}
				print $sql;
			 
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