<?php
date_default_timezone_set('America/Sao_Paulo');
//header('Content-type: text/html; charset=ISO-8859-1');
include_once"../../func/log.php";
include("../../conecta2.php");

$hoje= date('Y-m-d H:i:s');
$mat_autor = $_POST['mat_autor'];

$placaveic = $_POST['placa'];

if(strpos($placaveic, "-") !== false){
  $placa = str_replace("-", "", $placaveic);
} else {
  $placa = str_replace(" ", "", $placaveic);
}

$_UP['pasta'] = '../docs/veiculo/';
$dir1 = './docs/veiculo/';
 
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

//crlv
if (is_uploaded_file($_FILES['crlv']['tmp_name'])) {
 
    // Verifica se houve algum erro com o upload. 
    if ($_FILES['crlv']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['crlv']['error']]);
        exit; // Para a execução do script
    }
 
    // Faz a verificação da extensão do arquivo
    $extensao = strtolower(end(explode('.', $_FILES['crlv']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        //echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    } // Faz a verificação do tamanho do arquivo
    else if ($_UP['tamanho'] < $_FILES['crlv']['size']) {
        //echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
    else { // Primeiro verifica se deve trocar o nome do arquivo
        if ($_UP['renomeia'] == true) {
            // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
            $nome_final = time().'.jpg';
        } else { 
            $nomeaux = explode(".", $_FILES['crlv']['name']);

            $nomearq = str_replace(" ", "-", $nome );

            $nome_final = $placa."-".$hoje."-crlv.".$extensao;
            $crlv = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['crlv']['tmp_name'], $_UP['pasta'] . $nome_final);
    
        //Depois verifica se é possível mover o arquivo para a pasta escolhida
        /*if (move_uploaded_file($_FILES['frontal']['tmp_name'], $_UP['pasta'] . $nome_final)) {
            echo "Upload efetuado com sucesso!";
            echo $nome_final;
        } else {
            // Não foi possível fazer o upload, provavelmente a pasta está incorreta
            echo "Não foi possível enviar o arquivo, tente novamente";
            echo $nome_final;
        }*/
    }
}

// Refazendo o processo para crv
if (is_uploaded_file($_FILES['crv']['tmp_name'])) {
 
    if ($_FILES['crv']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['crv']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['crv']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        //echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['crv']['size']) {
        //echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {

            $nomeaux = explode(".", $_FILES['crv']['name']);
            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$hoje."-crv.".$extensao;
            $crv = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['crv']['tmp_name'], $_UP['pasta'] . $nome_final);
    }
}

// Refazendo o processo para ipva
if (is_uploaded_file($_FILES['certipva']['tmp_name'])) {
 
    if ($_FILES['certipva']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['certipva']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['certipva']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['certipva']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  

            $nomeaux = explode(".", $_FILES['certipva']['name']);
            $nomearq = str_replace(" ", "-", $nome );
            $nome_final = $placa."-".$hoje."-certipva.".$extensao;
            $certipva = $dir1.$nome_final;
        }

        move_uploaded_file($_FILES['certipva']['tmp_name'], $_UP['pasta'] . $nome_final);
    }
}

$sql="SELECT * from bdfrota.tbveicdocs WHERE placa = '$placa' ";
$resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
$linhas = mysqli_num_rows($resultado);

if($linhas <=0){
  $sql1 = "INSERT INTO bdfrota.tbveicdocs (placa, crlv, crv, cert_ipva) VALUES ('$placa', '$crlv','$crv','$certipva') ";
  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));

//print $sql1;

}else{
  $sql1 = "UPDATE bdfrota.tbveicdocs SET crlv='$crlv', crv='$crv', cert_ipva='$certipva' WHERE placa= '$placa'";
  $resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
//print $sql1;
}

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa);
$acao='Enviou documentos de veiculo';
$tipo = 'cadastro';
$logresult = enviarlognovo($hoje, $acao, '', $matricula, $tipo, $placa);

echo "<script> window.alert('Documentos enviados com sucesso.');  window.location=\"../listagemveiculos.php\" </script>";

//mysqli_close($conexao);
?>