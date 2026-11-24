<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje= date('Y-m-d H:i:s');
include_once"../../func/log.php";
include("../../conecta.php");

// =========================================================================
// PARTE 1: CAPTURAR OS DADOS DO FORMULÁRIO (COMO VOCÊ JÁ FAZIA)
// =========================================================================
$nome = $_POST['nome'];
$matricula = $_POST['matricula'];
$telefone = $_POST['telefone'];
$placa = $_POST['placa'];
$hodometro = $_POST['hodometro'];
$ccusto = $_POST['ccusto'];
$tipochamado = $_POST['tipochamado'];
$tiposinistro = $_POST['tiposinistro'];
$corientesini = isset($_POST['corientesini']) ? $_POST['corientesini'] : ''; // Evita erro se o checkbox não for marcado
$manuprev = $_POST['manuprev'];
$prevcor = $_POST['prevcor'];
$manucor = $_POST['manucor'];
$enderecog = $_POST['enderecog'];
$observacao = $_POST['observacao'];

// =========================================================================
// PARTE 2: PROCESSAR O UPLOAD DAS IMAGENS
// =========================================================================

// Define o caminho para a pasta de upload. '..' significa 'subir um nível'.
// Como este script está em /control/, ../img/ aponta para a pasta /img/ na raiz.
$caminhoUpload = '../image/';


// Garante que o diretório de upload exista. Se não, tenta criá-lo.
if (!is_dir($caminhoUpload)) {
    mkdir($caminhoUpload, 0775, true);
}

// Inicializa as variáveis que guardarão os nomes dos arquivos. É importante começar com elas vazias.
$nomeImg1 = '';
$nomeImg2 = '';
$nomeImg3 = '';

// Verifica se algum arquivo foi enviado no campo 'fotos'
if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
    $totalArquivos = count($_FILES['fotos']['name']);

    // Loop para processar cada arquivo (no máximo 3)
    for ($i = 0; $i < $totalArquivos && $i < 3; $i++) {
        // Verifica se não houve erro no upload do arquivo atual
        if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
            
            $nomeTemporario = $_FILES['fotos']['tmp_name'][$i];
            $nomeOriginal = $_FILES['fotos']['name'][$i];
            
            // Pega a extensão do arquivo (ex: .jpg, .png)
            $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
            
            // Gera um nome de arquivo único para evitar que uma foto substitua outra
            // Exemplo de nome gerado: chamado_635ae1b2c3d4e.jpg
            $novoNomeArquivo = 'chamado_' . uniqid() . '.' . strtolower($extensao);
            
            $caminhoDestino = $caminhoUpload . $novoNomeArquivo;

            // Move o arquivo da pasta temporária do servidor para a nossa pasta 'img'
            if (move_uploaded_file($nomeTemporario, $caminhoDestino)) {
                // Se o upload deu certo, atribui o nome do arquivo à variável correspondente
                if ($i == 0) {
                    $nomeImg1 = $novoNomeArquivo;
                } elseif ($i == 1) {
                    $nomeImg2 = $novoNomeArquivo;
                } elseif ($i == 2) {
                    $nomeImg3 = $novoNomeArquivo;
                }
            }
        }
    }
}


// =========================================================================
// PARTE 3: INSERIR TUDO NO BANCO DE DADOS (QUERY MODIFICADA)
// =========================================================================

// SQL modificado para incluir as novas colunas img1, img2, img3 e suas variáveis
$sql1 = "INSERT INTO bdfrota.tbchamados_teste 
    (matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente, data_abert, enderecog, observacao, img1, img2, img3) 
    VALUES 
	('$matricula', '$nome', '$telefone', '$ccusto', '$placa', '$hodometro', '$tipochamado', '$tiposinistro', '$manuprev', '$prevcor', '$manucor', 1, '$corientesini', '$hoje', '$enderecog', '$observacao', '$nomeImg1', '$nomeImg2', '$nomeImg3')";

$resultado1 = mysqli_query($con, $sql1) or die("Erro ao inserir no banco de dados: " . mysqli_error($con));

// Enviar log (seu código original)
$acao = 'Abriu chamado';
$tipo = 'chamado';
$logresult = enviarlognovo($hoje, $acao, '', $matricula, $tipo, $placa);

echo "<script language='javascript' type='text/javascript'> alert ('Chamado cadastrado com sucesso.');window.location=\"../listagemchamadoscliente.php\"	</script>";

mysqli_close($con);
?>
