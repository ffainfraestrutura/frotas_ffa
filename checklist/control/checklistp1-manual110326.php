<?php 
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');
//include("../conecta.php");
include_once"../../func/log.php";
include_once"../../conecta.php";

/*$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";
$db="bdfrota";

$con=mysqlI_connect($servidor,$usuario,$senha,$db) or die (mysqli_error());*/

$hoje = date('Y-m-d H:i:s');
$agora = date("H:i:s");

$placa = $_POST['placa'];

if(strpos($placa, "-") !== false){
  $placa = str_replace("-", "", $placa);
} elseif(strpos($placa, " ") !== false) {
  $placa = str_replace(" ", "", $placa);
} else{
	$placa=$placa;
}

$modelo = $_POST['modelo'];
$anofabricacao = $_POST['anofabricacao'];

$nome= $_POST['nome'];
$matricula=$_POST['matricula'];

$cpf = $_POST['cpf'];
//$cpf1 = $_POST['cpf1'];

/*if(empty($cpf)){
	$cpf = $cpf1;
}*/


if(strpos($cpf, ".") !== false){
  $cpf= str_replace(".", "", $cpf);
  
  if(strpos($cpf, "-") !== false){
  	$cpf = str_replace("-", "", $cpf);
  }
} else {
  $cpf = $cpf;
}

$cnh = $_POST['cnh'];
$categoriacnh = $_POST['categoriacnh'];
$validadecnh = $_POST['validadecnh'];

$centrocusto = $_POST['centrocusto'];
$tipo = $_POST['tipo'];
$vistoriador = $_POST['vistoriador'];

$datavistoria = $_POST['datavistoria'];
$horavistoria = $_POST['horavistoria'].":00";
$estado = $_POST['estado'];
$avaria = $_POST['avaria'];
$hodometro = $_POST['hodometro'];
$niveltanque = $_POST['niveltanque'];
$matrvistoriador = $_POST['matrvistoriador'];
$statusveic = $_POST['statusveic'];
$observacao = $_POST['observacao'];
$unidade = $_POST['unidade'];

$datafinal = $datavistoria." ".$horavistoria;

if($statusveic=='3' || $statusveic=='23' || $statusveic=='44'){
	$status = '0';
} else {
	$status='1';
}

if($tipo==2){
	$statusveic='1';
}

if($unidade == 'CAMPOS' || $unidade == 'LAGOS' || $unidade=='MACAE' || $unidade=='NITEROI' || $unidade=='NITERÓI' || $unidade == 'NOVA FRIBURGO' || $unidade=='OLARIA' || $unidade == 'PETROPOLIS' || $unidade=='TERESOPOLIS'|| $unidade=='TRES RIOS' || $unidade=='VASSOURAS'){
	$unidadef='RJ';
}elseif($unidade=='CASCAVEL' || $unidade=='CURITIBA' || $unidade=='PONTA GROSSA'){
	$unidadef='PR';
}elseif($unidade=='SAO MATEUS' || $unidade=='SAO MATHEUS'){
	$unidadef = 'SP';
}elseif($unidade == 'SERRA'){
	$unidadef = 'ES';
}elseif($unidade == 'BELO HORIZONTE' || $unidade == 'GOVERNADOR VALADARES' || $unidade == 'JUIZ DE FORA' || $unidade == 'MINAS GERAIS' || $unidade == 'UBERLANDIA' || $unidade == 'VARGINHA'){
	$unidadef = 'MG';
}

//print "$placa <br>$nome<br>$matricula<br>$cpf<br>$cnh <br>$categoriacnh <br>$validadecnh<br>$centrocusto <br>$tipo <br>$vistoriador <br>$datavistoria <br>$horavistoria <br>$estado <br>$avaria <br>$hodometro <br>$niveltanque <br>$matrvistoriador <br>$statusveic";

// Pasta onde o arquivo vai ser salvo
//$_UP['pasta'] = '../docs/';
$_UP['pasta'] = '../docs_old/';

//$dir1 = '/docs/';
$dir1 = '/docs_old/';

// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 32; // 32Mb
 
// Array com as extensões permitidas
$_UP['extensoes'] = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
 
// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
$_UP['renomeia'] = false;
 
// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';

if (is_uploaded_file($_FILES['pdfvist']['tmp_name'])) {
 
    // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
    if ($_FILES['pdfvist']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['pdfvist']['error']]);
        exit; // Para a execução do script
    }

    $extensao = strtolower(end(explode('.', $_FILES['pdfvist']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    } // Faz a verificação do tamanho do arquivo
    else if ($_UP['tamanho'] < $_FILES['pdfvist']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
    else { // Primeiro verifica se deve trocar o nome do arquivo
        if ($_UP['renomeia'] == true) {
            // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
            $nome_final = time().'.'.$extensao;
        } else {  // Mantém o nome original do arquivo            

            $nome_final = $placa."-".$datavistoria."-pdfvist.".$extensao;
            $pdfvist = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['pdfvist']['tmp_name'], $_UP['pasta'] . $nome_final);
    }
}

if(!empty($_FILES['imagem1']['tmp_name'])){
// Refazendo o processo para imagem1
if (is_uploaded_file($_FILES['imagem1']['tmp_name'])) {
 
    if ($_FILES['imagem1']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['imagem1']['error']]);
        exit; 
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['imagem1']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['imagem1']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.'.$extensao;

        } else {  // Mantém o nome original do arquivo           

            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$datavistoria."-imagem1.".$extensao;
            $imagem1 = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['imagem1']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
            
        $path = $_FILES['imagem1']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
        // $imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp = imagecreatefromgif($path);
        }
        else{
        //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade); 
    }
}
}

if(!empty($_FILES['imagem2']['tmp_name'])){
// Refazendo o processo para imagem2
if (is_uploaded_file($_FILES['imagem2']['tmp_name'])) {
 
    if ($_FILES['imagem2']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['imagem2']['error']]);
        exit; 
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['imagem2']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['imagem2']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.'.$extensao;

        } else {  // Mantém o nome original do arquivo           

            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$datavistoria."-imagem2.".$extensao;
            $imagem2 = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['imagem2']['tmp_name'], $_UP['pasta'] . $nome_final);
        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
            
        $path = $_FILES['imagem2']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
        // $imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp = imagecreatefromgif($path);
        }
        else{
        //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}
}

if(!empty($_FILES['imagem3']['tmp_name'])){
// Refazendo o processo para imagem3
if (is_uploaded_file($_FILES['imagem3']['tmp_name'])) {
 
    if ($_FILES['imagem3']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['imagem3']['error']]);
        exit; 
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['imagem3']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['imagem3']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.'.$extensao;

        } else {  // Mantém o nome original do arquivo           

            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$datavistoria."-imagem3.".$extensao;
            $imagem3 = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['imagem3']['tmp_name'], $_UP['pasta'] . $nome_final);

        /*if (move_uploaded_file($_FILES['imagem3']['tmp_name'], $_UP['pasta'] . $nome_final)) {
            // Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
            echo "Upload efetuado com sucesso!";
            echo '<br /><a href="' . $_UP['pasta'] . $nome_final . '">Clique aqui para acessar o arquivo</a>';
            } else {
            // Não foi possível fazer o upload, provavelmente a pasta está incorreta
            echo "Não foi possível enviar o arquivo, tente novamente";
        }*/

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
            
        $path = $_FILES['imagem3']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            // $imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp = imagecreatefromgif($path);
        }
        else{
            //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);

    }
}
}


/*$sqla1="SELECT idtbstatusveic FROM bdfrota.tbcondutor WHERE matricula='$matricula' ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];*/

//pegar idtbcondutor
$sqla="SELECT MAX(idtbcondutor) FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND placaassoc='$placa' ";
$resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
$rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
	$idtbcondutor = $rowa[0];

//print $centrocusto;
//print "$cnhfim, $categoriacnhfim, $validadecnhfim,<br>$cnh2, $categoriacnh2, $validadecnh2,<br>$cnh, $categoriacnh, $validadecnh,<br>";

// 

$sql = "INSERT INTO bdfrota.tbvistoria (nome, matricula, cpf, cnh, categoriacnh, validadecnh, placa, centrocusto, tipo, vistoriador, matrvistoriador, datavistoria, estado, avaria, hodometro, niveltanque, observacao, statusveic, anofabricacao, modelo, statusreg) VALUES ('$nome', '$matricula', '$cpf', '$cnh', '$categoriacnh', '$validadecnh', '$placa', '$centrocusto', '$tipo', '$vistoriador', '$matrvistoriador', '$datafinal', '$estado', '$avaria', '$hodometro', '$niveltanque', '$observacao','$statusveic','$anofabricacao', '$modelo', '1');";

//print $sql;
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

/*if($tipo == 2){
	//recebimento/associação do veículo

    $sql2="SELECT * FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo='1'; ";
    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
    $linhas2 = mysqli_num_rows($resultado2);
    $row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
        $id = $row2['idtbcondutor'];
	//print $id;
    if($linhas2<=0){
        $sql3="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
    
    }else{
        $sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$hoje', ativo = 0, statuscond='' WHERE idtbcondutor='$id'; ";
        $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

        $sql5="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO') ";
        $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
    }

	$sql4 = "UPDATE bdfrota.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datafinal', ccusto='$centrocusto' WHERE placa ='$placa' ";//datamovimentacao='$hoje',, basegestao='$unidade'
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
    
    //print $linhas2;
	
    //print "<BR>tipo2 - $sql3 <br> $sql5";
} elseif($tipo == 3){
	//devolução/desvinculação do veículo
	
	$sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$datafinal', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
	$resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

	$sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$datafinal', status='$status', statusvel='$statusveic', matcond='', ccusto='$centrocusto' WHERE placa ='$placa' ";
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

	//print "<br> $sql3 <br> $sql4";
 
}*/

if($tipo == 2){
    //recebimento/associação do veículo
    
    //atualizando tbveiculo
    $sql3 = "UPDATE bdfrota.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datafinal', ccusto='$centrocusto', unidade='$unidadef', basegestao='$unidade' WHERE placa ='$placa' ";//datamovimentacao='$hoje',
    $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));
    
    //pegar maximo registro do condutor como ativo
    $sqla="SELECT idtbcondutor, placaassoc FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND ativo=1 ORDER BY idtbcondutor DESC";
    $resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
    $linhasa = mysqli_num_rows($resultadoa);

    //pegar maximo registro da placa como ativo
    $sqlb="SELECT idtbcondutor, placaassoc, matricula FROM bdfrota.tbcondutor WHERE placaassoc='$placa' AND ativo=1 ORDER BY idtbcondutor DESC";
    $resultadob= mysqli_query($con, $sqlb) or die(mysqli_error($con));
    $linhasb = mysqli_num_rows($resultadob);   
   
    if($linhasa>0){// "se o registro ativo existe, conferir placa<br>";
        $rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
            $idtbcondutor = $rowa['idtbcondutor'];
            $placaassoc1 = $rowa['placaassoc'];
            
            
        //se a placa for diferente, tenho que desvincular e vincular na nova
        if($placaassoc1 != $placa){
            //desvincula a antiga
            $sql4="UPDATE bdfrota.tbcondutor SET datadissoc ='$datafinal', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
            $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));

            $sql4="UPDATE bdfrota.tbveiculo SET datamovimentacao='$datafinal', matcond = '', statusvel='2' WHERE placa='$placaassoc1'; ";
			$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
            
            //print "$idtbcondutor;$placaassoc1; $placa<br> ";
            
            //vincula a nova
            $sql4="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO'); ";
            $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));            
        }

    }else{// "se não há registro ativo, basta incluir novo registro";
        
        $sql5="INSERT INTO bdfrota.tbcondutor (nome, matricula, ativo, placaassoc, dataassoc, statuscond) VALUES ('$nome', '$matricula', 1, '$placa','$hoje', 'COM VEICULO VINCULADO'); ";
        $resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));

    }

    if($linhasb>0){//print "se a placa já está ativa, conferir se é com o mesmo condutor<br>";
        $rowb = mysqli_fetch_array($resultadob, MYSQLI_BOTH);
            $idtbcondutorpl = $rowb['idtbcondutor'];
            $mat_assoc1 = $rowb['matricula'];
        
        //se a placa for diferente, tenho que desvincular e vincular na nova
        if($mat_assoc1 != $matricula){
            //print "desvincula a antiga";
            $sql4="UPDATE bdfrota.tbcondutor SET datadissoc ='$datafinal', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutorpl'; ";
            $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
            
        }

    }else{//"placa nao esta ativa,";

    }


    //print "<BR> $sql3 <br> $sql4";
} elseif($tipo == 3){
    //devolução/desvinculação do veículo

    //pegar maximo registro do condutor como ativo
    $sqla="SELECT idtbcondutor, placaassoc FROM bdfrota.tbcondutor WHERE matricula='$matricula' AND ativo=1 ORDER BY idtbcondutor DESC";
    $resultadoa= mysqli_query($con, $sqla) or die(mysqli_error($con));
    $linhasa = mysqli_num_rows($resultadoa);

    if($linhasa>0){// "se o registro ativo existe";
        $rowa = mysqli_fetch_array($resultadoa, MYSQLI_BOTH);
            $idtbcondutor = $rowa['idtbcondutor'];
    }

    $sql3="UPDATE bdfrota.tbcondutor SET datadissoc ='$datafinal', ativo = 0, statuscond='' WHERE idtbcondutor='$idtbcondutor'; ";
    $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

    //ccusto='$centrocusto',
    
    $sql4 = "UPDATE bdfrota.tbveiculo SET datamovimentacao='$datafinal', status='$status', statusvel='$statusveic', matcond='', unidade='$unidadef', basegestao='$unidade', hodometro = '$hodometro' WHERE placa ='$placa' ";
    $resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
}else{
    $sql4 = "UPDATE bdfrota.tbveiculo SET  matcond='$matricula', statusvel='$statusveic', status='$status', datamovimentacao='$datafinal', ccusto='$centrocusto', unidade='$unidadef', basegestao='$unidade' WHERE placa ='$placa';";
	$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
}

$sql6="SELECT MAX(idtbvistoria) FROM bdfrota.tbvistoria WHERE placa='$placa'; ";
$resultado6 = mysqli_query($con, $sql6) or die(mysqli_error($con));
$row = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
    $idtbvistoria = $row[0];

$sql7="INSERT INTO bdfrota.tbvistoriafotos(placa,extra1, extra2, extra3, extra4, datavistoriaf, idtbvistoria) values('$placa','$pdfvist','$imagem1','$imagem2', '$imagem3', '$datafinal', '$idtbvistoria') ;";
$resultado7 = mysqli_query($con, $sql7) or die(mysqli_error($con));
//
//print $sql7;

	/*
	 * TIPOS DE VISTORIA/CHECKLIST MANUAL - SIGNIFICADO DOS LOGS:
	 * 
	 * Tipo 2: RECEBIMENTO/ASSOCIAÇÃO DO VEÍCULO
	 *   - Log gerado: "Realizou vistoria/checklist manual - PEGANDO/RECEBENDO"
	 *   - Ação: Funcionário está PEGANDO/RECEBENDO um veículo da empresa
	 *   - Sistema vincula a matrícula do condutor à placa do veículo
	 *   - Atualiza status do veículo para "EM USO"
	 *   - Configura base de gestão e unidade
	 * 
	 * Tipo 3: DEVOLUÇÃO/DESVINCULAÇÃO DO VEÍCULO  
	 *   - Log gerado: "Realizou vistoria/checklist manual - DEVOLVENDO"
	 *   - Ação: Funcionário está DEVOLVENDO um veículo para a empresa
	 *   - Sistema remove a vinculação da matrícula com a placa
	 *   - Atualiza status do veículo conforme condições encontradas na vistoria
	 * 
	 * Outros Tipos: VISTORIA COMUM
	 *   - Log gerado: "Realizou vistoria/checklist manual comum-[TIPO]"
	 *   - Ação: Vistoria de rotina ou manutenção sem mudança de vinculação
	 * 
	 * Importante: Este é um checklist manual com upload de arquivos
	 */

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$tipo_original = $tipo; // Preserva o valor original antes de alterar para o log

if($tipo_original == '2'){
	$acao='Realizou vistoria/checklist manual - PEGANDO/RECEBENDO';
} elseif($tipo_original == '3'){
	$acao='Realizou vistoria/checklist manual - DEVOLVENDO';
} else {
	$acao='Realizou vistoria/checklist manual comum-'.$tipo_original;
}
$tipo = 'checklist-manual';
$logresult = enviarlognovo($hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa);
//print "$hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa";*

echo "

<script language='javascript' type='text/javascript'>window.alert('Vistoria finalizada com sucesso!');  window.location=\"../checklistinicio-manual.php\"	</script>";
//mysqli_close($con);

?>