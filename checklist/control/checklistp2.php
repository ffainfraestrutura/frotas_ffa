<?php

date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

$hoje = date('Y-m-d H:i:s');
$agora = date("H:i:s");

$hora1 = date('H:i');
$hora = str_replace(":", "", $hora1);

$placa = $_POST['placa'];
$matrvistoriador = $_POST['vistoriador'];
$datavistoria = $_POST['datavistoriaf'];
$matricula = $_POST['matriculac'];

$datavistoriaf= $datavistoria." ".$agora;

$datacerta = str_replace("-", "", $datavistoria);

//include_once"../../func/log.php";
include_once"../../conecta03.php";

$sql1="SELECT MAX(idtbvistoria) FROM bdfrota.tbvistoria WHERE placa='$placa'; ";
$resultado1 = mysqli_query($con, $sql1) or die(mysqli_error($con));
$row = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
    $idtbvistoria = $row[0];

//DEFINIÇÕES GERAIS
// Nome do diretório para salvar
$dir1 = '/docs/';
$dir1 = '/docs_old/';


// Pasta onde o arquivo vai ser salvo
$_UP['pasta'] = '../docs/';
$_UP['pasta'] = '../docs_old/';

 
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
        //echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    } // Faz a verificação do tamanho do arquivo
    else if ($_UP['tamanho'] < $_FILES['frontal']['size']) {
        //echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } // O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
    else { // Primeiro verifica se deve trocar o nome do arquivo

        if ($_UP['renomeia'] == true) {
            // Cria um nome baseado no UNIX TIMESTAMP atual e com extensão .jpg
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            //$nomeaux = "frontal";
            //$nomearq = str_replace(" ", "-", $nomeaux );
            //$nome_final = $nomearq.".".$nomeaux[1];
            //$nome_final = $_FILES['frontal']['name'];

            //$nome_final = $placa."-".$nomeaux.".".$extensao;
            //$frontal = $dir1.$nome_final;

            $nomeaux = explode(".", $_FILES['frontal']['name']);
            /*if(stripos($_FILES['frontal']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['frontal']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['frontal']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );

            //$nome_final = $placa."-".$datacerta.$hora."-frontal.".$nomeaux[1];$extensao
            $nome_final = $placa."-".$datacerta.$hora."-frontal.".$extensao;
            $frontal = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['frontal']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['frontal']['tmp_name'];
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

// Refazendo o processo para foto traseira
if (is_uploaded_file($_FILES['traseira']['tmp_name'])) {
 
    if ($_FILES['traseira']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['traseira']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['traseira']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['traseira']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "traseira";
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $traseira = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['traseira']['name']);

            /*if(stripos($_FILES['traseira']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['traseira']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['traseira']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-traseira.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-traseira.".$extensao;
            $traseira = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['traseira']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        $path = $_FILES['traseira']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto do painel
if (is_uploaded_file($_FILES['painel']['tmp_name'])) {
 
    if ($_FILES['painel']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['painel']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['painel']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['painel']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            //$nomeaux = "painel";
            //$nome_final = $placa."-".$nomeaux.".".$extensao;
            //$painel = $dir1.$nome_final;

            $nomeaux = explode(".", $_FILES['painel']['name']);

            /*if(stripos($_FILES['painel']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['painel']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['painel']['name']));
            }*/


            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-painel.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-painel.".$extensao;
            $painel = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['painel']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['painel']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto da lateral esquerda
if (is_uploaded_file($_FILES['esquerda']['tmp_name'])) {
 
    if ($_FILES['esquerda']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['esquerda']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['esquerda']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['esquerda']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "esquerda";
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $esquerda = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['esquerda']['name']);

            /*if(stripos($_FILES['esquerda']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['esquerda']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['esquerda']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-esquerda.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-esquerda.".$extensao;
            $esquerda = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['esquerda']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['esquerda']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp = imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto da lateral direita
if (is_uploaded_file($_FILES['direita']['tmp_name'])) {
 
    if ($_FILES['direita']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['direita']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['direita']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['direita']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "direita";
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $direita = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['direita']['name']);
            /*if(stripos($_FILES['direita']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['direita']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['direita']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-direita.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-direita.".$extensao;
            $direita = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['direita']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['direita']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para selfie
if (is_uploaded_file($_FILES['selfie']['tmp_name'])) {
 
    if ($_FILES['selfie']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['selfie']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['selfie']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['selfie']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "selfie";        
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $selfie = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['selfie']['name']);
            /*if(stripos($_FILES['selfie']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['selfie']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['selfie']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-selfie.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-selfie.".$extensao;

            $selfie = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['selfie']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['selfie']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto do cnh
if (is_uploaded_file($_FILES['cnh']['tmp_name'])) {
 
    if ($_FILES['cnh']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['cnh']['error']]);
        exit; // Para a execução do script
    }
    
    $extensao = strtolower(end(explode('.', $_FILES['cnh']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['cnh']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "cnh";        
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $cnh = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['cnh']['name']);
            /*if(stripos($_FILES['cnh']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['cnh']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['cnh']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-cnh.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-cnh.".$extensao;
            $cnh = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['cnh']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['cnh']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto extra1
if (is_uploaded_file($_FILES['extra1']['tmp_name'])) {
 
    if ($_FILES['extra1']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['extra1']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['extra1']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['extra1']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "extra1";
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $extra1 = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['extra1']['name']);
            /*if(stripos($_FILES['extra1']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['extra1']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['extra1']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-extra1.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-extra1.".$extensao;
            $extra1 = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['extra1']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['extra1']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto extra2
if (is_uploaded_file($_FILES['extra2']['tmp_name'])) {
 
    if ($_FILES['extra2']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['extra2']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['extra2']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['extra2']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "extra2";
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $extra2 = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['extra2']['name']);
            /*if(stripos($_FILES['extra2']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['extra2']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['extra2']['name']));
            }*/

            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-extra2.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-extra2.".$extensao;
            $extra2 = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['extra2']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['extra2']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

// Refazendo o processo para foto extra3
if (is_uploaded_file($_FILES['extra3']['tmp_name'])) {
 
    if ($_FILES['extra3']['error'] != 0) {
        die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['extra3']['error']]);
        exit; // Para a execução do script
    }
 
    $extensao = strtolower(end(explode('.', $_FILES['extra3']['name'])));
    if (array_search($extensao, $_UP['extensoes']) === false) {
        echo "Por favor, envie arquivos com as seguintes extensões: jpg, png, pdf ou gif";
    }
    else if ($_UP['tamanho'] < $_FILES['extra3']['size']) {
        echo "O arquivo enviado é muito grande, envie arquivos de até 32Mb.";
    } 
    else { 
        if ($_UP['renomeia'] == true) {
            $nome_final = time().'.jpg';
        } else {  // Mantém o nome original do arquivo
            /*$nomeaux = "extra3";
            $nome_final = $placa."-".$nomeaux.".".$extensao;
            $extra3 = $dir1.$nome_final;*/

            $nomeaux = explode(".", $_FILES['extra3']['name']);
            /*if(stripos($_FILES['extra3']['name'],"jpeg")!==false){
                
                $nomeaux =end(explode('.', $_FILES['extra3']['name']));
            }else{
                
                $nomeaux =end(explode('.', $_FILES['extra3']['name']));
            }*/
            
            $nomearq = str_replace(" ", "-", $nome );
            //$nome_final = $placa."-".$datacerta.$hora."-extra3.".$nomeaux[1];
            $nome_final = $placa."-".$datacerta.$hora."-extra3.".$extensao;

            $extra3 = $dir1.$nome_final;
        }

        //move_uploaded_file($_FILES['extra3']['tmp_name'], $_UP['pasta'] . $nome_final);

        // O parâmetro "qualidade" é um valor de 0 (baixa) até 100 (alta)
        $qualidade = 30;
        
        $path = $_FILES['extra3']['tmp_name'];
        $pathfinal = $_UP['pasta'] . $nome_final;

        if(stripos($extensao,'jpg') !== false || stripos($extensao,'jpeg') !== false){
            $imagemTmp = imagecreatefromjpeg($path);
            
        }
        else if (stripos($extensao,'png')!== false){
            //$imagemTmp=imagecreatefrompng($nome_final);
            $imagemTmp=imagecreatefrompng($path);
        }
        else if (stripos($extensao,'gif')!== false){
            //$imagemTmp=imagecreatefromgif($nome_final);
            $imagemTmp=imagecreatefromgif($path);
        }
        else{
           //return 0; 
        }

        imagejpeg($imagemTmp, $pathfinal, $qualidade);
    }
}

$sql = "INSERT INTO bdfrota.tbvistoriafotos (placa, frontal, traseira, direita, esquerda, selfie,  extra1, extra2, extra3, datavistoriaf, idtbvistoria) values ('$placa', '$frontal', '$traseira', '$direita', '$esquerda', '$selfie',  '$extra1', '$extra2', '$extra3', '$datavistoriaf', '$idtbvistoria')";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

//cnh,'$cnh',

//enviarlognovo($datahora, $acao, $matricula, $mat_autor, $tipo, $placa)
$acao='Realizou vistoria/checklist';
$tipo = 'checklist';
//$logresult = enviarlognovo($hoje, $acao, $matricula, $vistoriador, $tipo, $placa);

if($perfil == '4' || $perfil == '5' || $perfil == '6'){
   print "<script language='javascript' type='text/javascript'> window.alert('Vistoria finalizada com sucesso!'); </script>
       <script language='javascript' type='text/javascript'> window.location=\"../verrelatorio.php?id=$idtbvistoria\"; </script>
       
       <!--<script language='javascript' type='text/javascript'> window.location=\"../vistoriafinalizada.php\"; </script>-->";
} elseif($perfil == '0' || $perfil == '1' || $perfil == '2'){
   /*print "<script language='javascript' type='text/javascript'> window.alert('Vistoria finalizada com sucesso!'); </script>
       <script language='javascript' type='text/javascript'> window.location=\"../condutorinicial.php\"; </script>"; */
    print "<script language='javascript' type='text/javascript'> window.alert('Vistoria finalizada com sucesso!'); </script>
       <script language='javascript' type='text/javascript'> window.location=\"../gerandorelatorio.php?id=$idtbvistoria\";</script>";
}

/*$sql = "INSERT INTO bdfrota.tbvistoriafotos(placa, frontal, traseira) values ('$placa', '$frontal', '$traseira')";
$resultado = mysqli_query($con, $sql) or die(mysqli_error($con));*/

//print "$hoje, $acao, $matricula, $matrvistoriador, $tipo, $placa";
?>