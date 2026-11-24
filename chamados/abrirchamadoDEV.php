<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje= date('Y-m-d H:i:s');
include_once"../../func/log.php";
include("../../conecta.php");
// Tentar outra solução para evitar chamados duplicados
// =========================================================================
// PARTE 1: CAPTURAR OS DADOS DO FORMUL�RIO (COMO VOC� J� FAZIA)
// =========================================================================
$nome = $_POST['nome'];
$matricula = $_POST['matricula'];
$telefone = $_POST['telefone'];
$placa = $_POST['placa'];
$hodometro = $_POST['hodometro'];
$ccusto = $_POST['ccusto'];
$tipochamado = $_POST['tipochamado'];
$tiposinistro = $_POST['tiposinistro'];
$corientesini = isset($_POST['corientesini']) ? $_POST['corientesini'] : ''; // Evita erro se o checkbox n�o for marcado
$manuprev = $_POST['manuprev'];
$prevcor = $_POST['prevcor'];
$manucor = $_POST['manucor'];
$enderecog = $_POST['enderecog'];
$observacao = $_POST['observacao'];

// =========================================================================
// VERIFICA��O: Impedir mais de dois chamados consecutivos da mesma matr�cula
// =========================================================================
$sqlUltimos = "SELECT matricula FROM bdfrota.tbchamados ORDER BY idtbchamados DESC LIMIT 2";
$resultadoUltimos = mysql_query($sqlUltimos);

if ($resultadoUltimos && mysql_num_rows($resultadoUltimos) >= 2) {
    $chamados = array();
    while ($row = mysql_fetch_assoc($resultadoUltimos)) {
        $chamados[] = $row['matricula'];
    }
    
    // Verifica se os 2 últimos chamados são da mesma matrícula que está tentando abrir
    if (count($chamados) >= 2 && $chamados[0] == $matricula && $chamados[1] == $matricula) {
        echo "<script>alert('N�o � permitido criar mais de dois chamados consecutivos da mesma matr�cula!\\n�ltimos chamados criados: Matr�cula $matricula\\n\\nPor favor, aguarde outro chamado de outra matr�cula ser criado antes de abrir um novo para esta matr�cula.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }
}

// =========================================================================
// PARTE 2: PROCESSAR O UPLOAD DAS IMAGENS
// =========================================================================

// Define o caminho para a pasta de upload. '..' significa 'subir um n�vel'.
// Como este script est� em /control/, ../img/ aponta para a pasta /img/ na raiz.
$caminhoUpload = '../image/';


// Garante que o diret�rio de upload exista. Se n�o, tenta cri�-lo.
if (!is_dir($caminhoUpload)) {
    mkdir($caminhoUpload, 0775, true);
}

// Inicializa as vari�veis que guardar�o os nomes dos arquivos. � importante come�ar com elas vazias.
$nomeImg1 = '';
$nomeImg2 = '';
$nomeImg3 = '';

// Verifica se algum arquivo foi enviado no campo 'fotos'
if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
    $totalArquivos = count($_FILES['fotos']['name']);

    // Loop para processar cada arquivo (no m�ximo 3)
    for ($i = 0; $i < $totalArquivos && $i < 3; $i++) {
        // Verifica se n�o houve erro no upload do arquivo atual
        if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
            
            $nomeTemporario = $_FILES['fotos']['tmp_name'][$i];
            $nomeOriginal = $_FILES['fotos']['name'][$i];
            
            // Pega a extens�o do arquivo (ex: .jpg, .png)
            $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
            
            // Gera um nome de arquivo �nico para evitar que uma foto substitua outra
            // Exemplo de nome gerado: chamado_635ae1b2c3d4e.jpg
            $novoNomeArquivo = 'chamado_' . uniqid() . '.' . strtolower($extensao);
            
            $caminhoDestino = $caminhoUpload . $novoNomeArquivo;

            // Move o arquivo da pasta tempor�ria do servidor para a nossa pasta 'img'
            if (move_uploaded_file($nomeTemporario, $caminhoDestino)) {
                // Se o upload deu certo, atribui o nome do arquivo � vari�vel correspondente
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

// SQL modificado para incluir as novas colunas img1, img2, img3 e suas vari�veis
$sql1 = "INSERT INTO bdfrota.tbchamados 
    (matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente, data_abert, enderecog, observacao, img1, img2, img3) 
    VALUES 
	('$matricula', '$nome', '$telefone', '$ccusto', '$placa', '$hodometro', '$tipochamado', '$tiposinistro', '$manuprev', '$prevcor', '$manucor', 1, '$corientesini', '$hoje', '$enderecog', '$observacao', '$nomeImg1', '$nomeImg2', '$nomeImg3')";

$resultado1 = mysqli_query($con, $sql1) or die("Erro ao inserir no banco de dados: " . mysqli_error($con));

// Enviar log (seu c�digo original)
$acao = 'Abriu chamado';
$tipo = 'chamado';
$logresult = enviarlognovo($hoje, $acao, '', $matricula, $tipo, $placa);

echo "<script language='javascript' type='text/javascript'> alert ('Chamado cadastrado com sucesso.');window.location=\"../listagemchamadoscliente.php\"	</script>";

mysqli_close($con);
?>
