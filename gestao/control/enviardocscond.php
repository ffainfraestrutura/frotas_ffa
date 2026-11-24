<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log3.php";
include("../../conecta03.php");

$hoje= date('Y-m-d H:i:s');
$h = str_replace(" ","",$hoje);
$h1 = str_replace("-","",$h);
$h2 = str_replace(":","",$h1);

$matcond = $_POST['matcond'];
$mat_autor = $_POST['mat_autor'];

//print $matcond;
$_UP['pasta'] = '../docs/condutor/';
$dir1 = '/docs/condutor/';
 
// Tamanho máximo do arquivo (em Bytes)
$_UP['tamanho'] = 1024 * 1024 * 32; // 32Mb
 
// Array com as extensões permitidas
$_UP['extensoes'] = array('pdf', 'doc', 'docx');
 
// Renomeia o arquivo? (Se true, o arquivo será salvo como .jpg e um nome único)
$_UP['renomeia'] = false;
 
// Array com os tipos de erros de upload do PHP
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';

//politica de uso
if($_FILES["politicauso"]["name"] != ''){
    if (is_uploaded_file($_FILES['politicauso']['tmp_name'])) {
     
        // Verifica se houve algum erro com o upload. 
        if ($_FILES['politicauso']['error'] != 0) {
            die("Não foi possível fazer o upload do arquivo de política de uso, erro:<br />" . $_UP['erros'][$_FILES['politicauso']['error']]);
            exit; // Para a execução do script
        }
     
        // Faz a verificação da extensão do arquivo
        $extensao = strtolower(end(explode('.', $_FILES['politicauso']['name'])));
        if (array_search($extensao, $_UP['extensoes']) === false) {
            echo "Por favor, envie o arquivo de política de uso com as seguintes extensões: pdf, doc, docx.";
        } // Faz a verificação do tamanho do arquivo
        else if ($_UP['tamanho'] < $_FILES['politicauso']['size']) {
            echo "O arquivo de política de uso enviado é muito grande, envie arquivos de até 32Mb.";
        } // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
        else { // Primeiro verifica se deve trocar o nome do arquivo
            if ($_UP['renomeia'] == true) {
                // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
                //$nome_final = time().'.jpg';
            } else { 
                $nomeaux = explode(".", $_FILES['politicauso']['name']);

                $nomearq = str_replace(" ", "-", $nome );

                $nome_final = $matcond."-".$h2."-politicauso.".$extensao;
                $politicauso = $dir1.$nome_final;
            }

            //move_uploaded_file($_FILES['politicauso']['tmp_name'], $_UP['pasta'] . $nome_final);
        
            //Depois verifica se é possível mover o arquivo para a pasta escolhida
            if (move_uploaded_file($_FILES['politicauso']['tmp_name'], $_UP['pasta'].$nome_final)) {
                $sql1 = "UPDATE bdfrota.tbcnh SET politicauso='$politicauso' WHERE matricula= '$matcond'";
                $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
                
            } else {
                // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                echo "Não foi possível enviar o arquivo de política de uso, tente novamente ";
                
            }
        }
    }
}

// Refazendo o processo para termocombustivel
if($_FILES["termocombustivel"]["name"] != ''){
    if (is_uploaded_file($_FILES['termocombustivel']['tmp_name'])) {
     
        if ($_FILES['termocombustivel']['error'] != 0) {
            die("Não foi possível fazer o upload do arquivo de termo de combustível, erro:<br />" . $_UP['erros'][$_FILES['crv']['error']]);
            exit; // Para a execução do script
        }
     
        $extensao = strtolower(end(explode('.', $_FILES['termocombustivel']['name'])));
        if (array_search($extensao, $_UP['extensoes']) === false) {
            echo "Por favor, envie arquivo de termo de combustível com as seguintes extensões: pdf, doc, docx.";
        }
        else if ($_UP['tamanho'] < $_FILES['termocombustivel']['size']) {
            echo "O arquivo enviado de termo de combustível é muito grande, envie arquivos de até 32Mb.";
        } 
        else { 
            if ($_UP['renomeia'] == true) {
                //$nome_final = time().'.jpg';
            } else {

                $nomeaux = explode(".", $_FILES['termocombustivel']['name']);
                $nomearq = str_replace(" ", "-", $nome );
                $nome_final = $matcond."-".$h2."-termocombustivel.".$extensao;
                $termocombustivel = $dir1.$nome_final;
            }

            //move_uploaded_file($_FILES['termocombustivel']['tmp_name'], $_UP['pasta'] . $nome_final);

            //Depois verifica se é possível mover o arquivo para a pasta escolhida
            if (move_uploaded_file($_FILES['termocombustivel']['tmp_name'], $_UP['pasta'].$nome_final)) {
                $sql1 = "UPDATE bdfrota.tbcnh SET termocombust='$termocombustivel' WHERE matricula= '$matcond'";
                $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
                
            } else {
                // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                echo "Não foi possível enviar o arquivo de termo de combustível, tente novamente ";
                
            }
        }
    }
}

// Refazendo o processo para contrato de agregamento
if($_FILES["contrato"]["name"] != ''){
    if (is_uploaded_file($_FILES['contrato']['tmp_name'])) {
     
        if ($_FILES['contrato']['error'] != 0) {
            die("Não foi possível fazer o upload do arquivo de  Contrato de Agregamento, erro:<br />" . $_UP['erros'][$_FILES['contrato']['error']]);
            exit; // Para a execução do script
        }
     
        $extensao = strtolower(end(explode('.', $_FILES['contrato']['name'])));
        if (array_search($extensao, $_UP['extensoes']) === false) {
            echo "Por favor, envie arquivos de  Contrato de Agregamento com as seguintes extensões: pdf, doc, docx.";
        }
        else if ($_UP['tamanho'] < $_FILES['contrato']['size']) {
            echo "O arquivo de Contrato de Agregamento enviado é muito grande, envie arquivos de até 32Mb.";
        } 
        else { 
            if ($_UP['renomeia'] == true) {
                //$nome_final = time().'.jpg';
            } else {

                $nomeaux = explode(".", $_FILES['contrato']['name']);
                $nomearq = str_replace(" ", "-", $nome );
                $nome_final = $matcond."-".$h2."-contrato.".$extensao;
                $contrato = $dir1.$nome_final;
            }

            //move_uploaded_file($_FILES['contrato']['tmp_name'], $_UP['pasta'] . $nome_final);

            //Depois verifica se é possível mover o arquivo para a pasta escolhida
            if (move_uploaded_file($_FILES['contrato']['tmp_name'], $_UP['pasta'].$nome_final)) {
                $sql1 = "UPDATE bdfrota.tbcnh SET contratoagregamento='$contrato' WHERE matricula= '$matcond'";
                $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
                
            } else {
                // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                echo "Não foi possível enviar o arquivo de Contrato de Agregamento, tente novamente ";
                
            }
        }
    }
}

// Refazendo o processo para termo de rescisão
if($_FILES["rescisao"]["name"] != ''){
    if (is_uploaded_file($_FILES['rescisao']['tmp_name'])) {
     
        if ($_FILES['rescisao']['error'] != 0) {
            die("Não foi possível fazer o upload do arquivo de Termo de Rescisão, erro:<br />" . $_UP['erros'][$_FILES['rescisao']['error']]);
            exit; // Para a execução do script
        }
     
        $extensao = strtolower(end(explode('.', $_FILES['rescisao']['name'])));
        if (array_search($extensao, $_UP['extensoes']) === false) {
            echo "Por favor, envie arquivo de Termo de Rescisão com as seguintes extensões: pdf, doc, docx.";
        }
        else if ($_UP['tamanho'] < $_FILES['rescisao']['size']) {
            echo "O arquivo de Termo de Rescisão enviado é muito grande, envie arquivos de até 32Mb.";
        } 
        else { 
            if ($_UP['renomeia'] == true) {
                //$nome_final = time().'.jpg';
            } else {

                $nomeaux = explode(".", $_FILES['rescisao']['name']);
                $nomearq = str_replace(" ", "-", $nome );
                $nome_final = $matcond."-".$h2."-rescisao.".$extensao;
                $rescisao = $dir1.$nome_final;
            }

            //move_uploaded_file($_FILES['rescisao']['tmp_name'], $_UP['pasta'] . $nome_final);

            //Depois verifica se é possível mover o arquivo para a pasta escolhida
            if (move_uploaded_file($_FILES['rescisao']['tmp_name'], $_UP['pasta'].$nome_final)) {
                $sql1 = "UPDATE bdfrota.tbcnh SET termorescisao='$rescisao' WHERE matricula= '$matcond'";
                $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
                
            } else {
                // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                echo "Não foi possível enviar o arquivo de Termo de Rescisão, tente novamente ";
                
            }
        }
    }
}

if($_FILES["ultrecibo"]["name"] != ''){
    if (is_uploaded_file($_FILES['ultrecibo']['tmp_name'])) {
     
        if ($_FILES['ultrecibo']['error'] != 0) {
            die("Não foi possível fazer o upload do arquivo de Último Recibo, erro:<br />" . $_UP['erros'][$_FILES['rescisao']['error']]);
            exit; // Para a execução do script
        }
     
        $extensao = strtolower(end(explode('.', $_FILES['ultrecibo']['name'])));
        if (array_search($extensao, $_UP['extensoes']) === false) {
            echo "Por favor, envie arquivo de Último Recibo com as seguintes extensões: pdf, doc, docx.";
        }
        else if ($_UP['tamanho'] < $_FILES['ultrecibo']['size']) {
            echo "O arquivo de Último Recibo enviado é muito grande, envie arquivos de até 32Mb.";
        } 
        else { 
            if ($_UP['renomeia'] == true) {
                //$nome_final = time().'.jpg';
            } else {

                $nomeaux = explode(".", $_FILES['ultrecibo']['name']);
                $nomearq = str_replace(" ", "-", $nome );
                $nome_final = $matcond."-".$h2."-ultrecibo.".$extensao;
                $ultrecibo = $dir1.$nome_final;
            }

            //move_uploaded_file($_FILES['rescisao']['tmp_name'], $_UP['pasta'] . $nome_final);

            //Depois verifica se é possível mover o arquivo para a pasta escolhida
            if (move_uploaded_file($_FILES['ultrecibo']['tmp_name'], $_UP['pasta'].$nome_final)) {
                $sql1 = "UPDATE bdfrota.tbcnh SET ultrecibo='$ultrecibo' WHERE matricula= '$matcond'";
                $resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
                
            } else {
                // Não foi possível fazer o upload, provavelmente a pasta está incorreta
                echo "Não foi possível enviar o arquivo de Último Recibo, tente novamente ";
                
            }

            //print $matcond;
        }
    }
}

/* Refazendo o processo para termoinfra
if (is_uploaded_file($_FILES['termoinfra']['tmp_name'])) {
 
    if ($_FILES['termoinfra']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['termoinfra']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['termoinfra']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: pdf, doc, docx.";
    }
    else if ($_UP['tamanho'] < $_FILES['termoinfra']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            //$nome_final = time().'.jpg';
        } else {  

            $nomeaux = explode(".", $_FILES['termoinfra']['name']);
            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $matcond."-".$h2."-termoinfra.".$extensao;
            $termoinfra = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['termoinfra']['tmp_name'], $_UP['pasta'] . $nome_final);
    }
}*/

/*$sql1 = "UPDATE bdfrota.tbcnh SET politicauso='$politicauso', termocombust='$termocombustivel', contratoagregamento='$contrato' WHERE matricula= '$matcond'";
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));*/

$log = new Log($con);
//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa);
$acao='Enviou documentos de condutor';
$tipo = 'cadastro';
$logresult = $log->enviarlognovo($hoje, $acao, $matcond, $mat_autor, $tipo, '');
                        
echo "<script> window.alert('Documentos enviados com sucesso.');window.close();  </script>";

//window.location=\"../listagemgeralcondutores.php\"

//mysqli_close($con);
?>