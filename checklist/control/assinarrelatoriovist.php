<?php 
date_default_timezone_set('America/Sao_Paulo');
header('Content-type: text/html; charset=ISO-8859-1');
header("Content-type: text/html; charset=utf-8");

include_once"../../func/log3.php";

include("../../conecta03.php");

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

$placa = $_POST['placa'];
$matricula = $_POST['matricula'];
$datavistoria = $_POST['datavistoria'];
$idtbvistoria = $_POST['idtbvistoria'];

$hoje = date('Y-m-d H:i:s');
$hoje1= str_replace(" ", "", $hoje);
$hoje1= str_replace("-", "", $hoje1);
$hojecerto = str_replace(":", "", $hoje1);



//DEFINIÇÕES GERAIS
// Nome do diretório para salvar
$dir1 = '/docs/ass/';
$dir1 = '/docs_old/ass/';


// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = '../docs/ass/';
$_UP['pasta'] = '../docs_old/ass/';

 
// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 32; // 32Mb
 
// Array com as extensões permitidas
$_UP['extensoes'] = array('jpg', 'jpeg', 'gif', 'pdf');
 
// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
$_UP['renomeia'] = false;
 
// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';

if (is_uploaded_file($_FILES['frontal']['tmp_name'])) {
 
    // Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
    if ($_FILES['frontal']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['frontal']['error']]);
        exit; // Para a execução do script
    }
 
    // Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
 
    // Faz a verificação da extensão do arquivo
    $extensao = strtolower(end(explode('.', $_FILES['frontal']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, pdf ou gif";
    } // Faz a verificação do tamanho do arquivo
    else if ($_UP['tamanho'] < $_FILES['frontal']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
    else { // Primeiro verifica se deve trocar o nome do arquivo
        if ($_UP['renomeia'] == true) {
            // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            $nomeaux = explode(".", $_FILES['frontal']['name']);
            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $matricula."-".$hojecerto."-assinatura.".$extensao;
            $foto = $dir1.$nome_final;
        }

        /*move_uploaded_file($_FILES['frontal']['tmp_name'], $_UP['pasta'] . $nome_final);*/
        
        $qualidade = 30;
        
        $path = $_FILES['frontal']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }        
        else if (stripos($extensao,'gif')!== false){
            $imagemTmp=imagecreatefromgif($nome_final);
        }
        else{
           //return 0; 
        }
        
        //Depois verifica se é possível mover o arquivo para a pasta escolhida
        //if (move_uploaded_file($_FILES['frontal']['tmp_name'], $_UP['pasta'].$nome_final)) {
        if(imagejpeg($imagemTmp, $pathfinal, $qualidade)){
        
            //echo "Upload efetuado com sucesso!";
            //echo $nome_final;

            //control de assinado(para marcar assinar)
            $sql = "UPDATE bdfrota.tbvistoria SET assinadovist=1, dirassvist = '$foto' WHERE idtbvistoria= '$idtbvistoria';";
            
            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

            //enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
            $log = new Log($con);
            $acao= 'Assinou relatorio';
            $tipo = 'checklist';
            $logresult = $log->enviarlognovo($hoje, $acao, $matricula, $matr_autor, $tipo, $placa);


            print "<script language='javascript' type='text/javascript'> 
                    window.alert('Relatório assinado com sucesso.');
                    window.location=\"../verrelatorioassinadovist.php?id=$idtbvistoria\";    </script>";

        } else {
            // Não foi possível fazer o upload, provavelmente a pasta está incorreta
            echo "Não foi possível enviar o arquivo, tente novamente";
            //echo $nome_final;
        }

        
    }
}


//control de assinado(para marcar assinar)
/*$sql = "UPDATE bdfrota.tbvistoria SET assinado=1, dir = '$foto' WHERE datavistoria='$datavistoria' AND placa='$placa';";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));


print "<script language='javascript' type='text/javascript'> 
window.alert('Relatório assinado com sucesso.');
window.location=\"../visualizarrelatorio.php?matricula=$matricula&placa=$placa&datavistoria=$datavistoria\";	</script>";

mysqli_close($con);*/

?>