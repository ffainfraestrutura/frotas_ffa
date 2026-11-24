<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

// Verifica permissões
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location='../../index.php'</script>";
    exit;
}

include "../../conecta2.php";
header("Content-type: text/html; charset=utf-8");

// Recebe os dados do formulário - Informações Pessoais
$nome = mysqli_real_escape_string($conexao, $_POST['nome']);
$datanascimento = mysqli_real_escape_string($conexao, $_POST['datanascimento']);
$sexo = mysqli_real_escape_string($conexao, $_POST['sexo']);
$nacionalidade = mysqli_real_escape_string($conexao, $_POST['nacionalidade']);
$naturalidade = mysqli_real_escape_string($conexao, $_POST['naturalidade']);
$instrucao = mysqli_real_escape_string($conexao, $_POST['instrucao']);
$estadocivil = mysqli_real_escape_string($conexao, $_POST['estadocivil']);
$nomepai = mysqli_real_escape_string($conexao, $_POST['nomepai']);
$nomemae = mysqli_real_escape_string($conexao, $_POST['nomemae']);
$deficiente = isset($_POST['deficiente']) ? 1 : 0;
$deficiencia = mysqli_real_escape_string($conexao, $_POST['deficiencia']);

// Documentos
$cpf = mysqli_real_escape_string($conexao, $_POST['cpf']);
$rg = mysqli_real_escape_string($conexao, $_POST['rg']);
$ufrg = mysqli_real_escape_string($conexao, $_POST['ufrg']);
$emissorrg = mysqli_real_escape_string($conexao, $_POST['emissorrg']);
$datarg = mysqli_real_escape_string($conexao, $_POST['datarg']);

// Informações de Contato
$telefone = mysqli_real_escape_string($conexao, $_POST['telefone']);
$celular = mysqli_real_escape_string($conexao, $_POST['celular']);
$email = mysqli_real_escape_string($conexao, $_POST['email']);
$emailcorp = mysqli_real_escape_string($conexao, $_POST['emailcorp']);
$cep = mysqli_real_escape_string($conexao, $_POST['cep']);
$logradouro = mysqli_real_escape_string($conexao, $_POST['logradouro']);
$numcasa = mysqli_real_escape_string($conexao, $_POST['numcasa']);
$complem = mysqli_real_escape_string($conexao, $_POST['complem']);
$bairro = mysqli_real_escape_string($conexao, $_POST['bairro']);
$cidade = mysqli_real_escape_string($conexao, $_POST['cidade']);
$estado = mysqli_real_escape_string($conexao, $_POST['estado']);

// Dados Contratuais
$matfunc = mysqli_real_escape_string($conexao, $_POST['matfunc']);
$mataux = mysqli_real_escape_string($conexao, $_POST['mataux']);
$situacao = mysqli_real_escape_string($conexao, $_POST['situacao']);
$dtadmissao = mysqli_real_escape_string($conexao, $_POST['dtadmissao']);
$empresa = mysqli_real_escape_string($conexao, $_POST['empresa']);
$filial = mysqli_real_escape_string($conexao, $_POST['filial']);
$departamento = mysqli_real_escape_string($conexao, $_POST['departamento']);
$gestor = mysqli_real_escape_string($conexao, $_POST['gestor']);
$estadotrab = mysqli_real_escape_string($conexao, $_POST['estadotrab']);
$cidadetrab = mysqli_real_escape_string($conexao, $_POST['cidadetrab']);
$cargo = mysqli_real_escape_string($conexao, $_POST['cargo']);
$jornada = mysqli_real_escape_string($conexao, $_POST['jornada']);
$semmarcacao = 1; // Sempre marcado conforme solicitado

// Monta o endereço completo
$endereco = $logradouro . ', ' . $numcasa;

// Verifica se a matrícula já existe
$sql_verifica = "SELECT matricula FROM bdaniel.tbfuncionario_condutor WHERE matricula = '$matfunc'";
$resultado_verifica = mysqli_query($conexao, $sql_verifica);

if (mysqli_num_rows($resultado_verifica) > 0) {
    echo "<script language='javascript' type='text/javascript'>
        alert('Matrícula já cadastrada no sistema!');
        window.location='../cadastroCondutores.php';
    </script>";
    exit;
}

// Prepara a query de inserção
$sql = "INSERT INTO bdaniel.tbfuncionario_condutor (
    matricula,
    nome,
    sexo,
    nacionalidade,
    naturalidade,
    grau_instrucao,
    estado_civil,
    nome_pai,
    nome_mae,
    deficiente,
    qual_deficiencia,
    dtnasc,
    cpf,
    rg,
    uf_rg,
    emissor_rg,
    data_emissao_rg,
    email,
    telefone,
    celular,
    email_corporativo,
    cep,
    endereco,
    complemento,
    bairro,
    cidade,
    estado,
    dtadmissao,
    mataux,
    situacao,
    cargo,
    ccusto,
    codempresa,
    codfilial,
    gestor_funcionario,
    id_jornada,
    uf_trabalho,
    municipio_trabalho,
    sem_marcacao_ponto,
    status
) VALUES (
    '$matfunc',
    '$nome',
    " . ($sexo ? "'$sexo'" : "NULL") . ",
    " . ($nacionalidade ? "'$nacionalidade'" : "NULL") . ",
    " . ($naturalidade ? "'$naturalidade'" : "NULL") . ",
    " . ($instrucao ? "'$instrucao'" : "NULL") . ",
    " . ($estadocivil ? "'$estadocivil'" : "NULL") . ",
    " . ($nomepai ? "'$nomepai'" : "NULL") . ",
    " . ($nomemae ? "'$nomemae'" : "NULL") . ",
    $deficiente,
    " . ($deficiencia ? "'$deficiencia'" : "NULL") . ",
    '$datanascimento',
    '$cpf',
    " . ($rg ? "'$rg'" : "NULL") . ",
    " . ($ufrg ? "'$ufrg'" : "NULL") . ",
    " . ($emissorrg ? "'$emissorrg'" : "NULL") . ",
    " . ($datarg ? "'$datarg'" : "NULL") . ",
    " . ($email ? "'$email'" : "NULL") . ",
    " . ($telefone ? "'$telefone'" : "NULL") . ",
    " . ($celular ? "'$celular'" : "NULL") . ",
    " . ($emailcorp ? "'$emailcorp'" : "NULL") . ",
    '$cep',
    '$endereco',
    " . ($complem ? "'$complem'" : "NULL") . ",
    '$bairro',
    '$cidade',
    '$estado',
    '$dtadmissao',
    " . ($mataux ? "'$mataux'" : "NULL") . ",
    " . ($situacao ? "'$situacao'" : "'ATIVO'") . ",
    '$cargo',
    '$departamento',
    '$empresa',
    '$filial',
    " . ($gestor && $gestor != '0' ? "'$gestor'" : "NULL") . ",
    " . ($jornada == '0' ? "NULL" : "'$jornada'") . ",
    '$estadotrab',
    " . ($cidadetrab ? "'$cidadetrab'" : "NULL") . ",
    $semmarcacao,
    'ATIVO'
)";

// Executa a query
if (mysqli_query($conexao, $sql)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('Condutor cadastrado com sucesso!');
        window.location='../listagemcondutores.php';
    </script>";
} else {
    $erro = mysqli_error($conexao);
    echo "<script language='javascript' type='text/javascript'>
        alert('Erro ao cadastrar condutor: $erro');
        window.location='../cadastroCondutores.php';
    </script>";
}

mysqli_close($conexao);
?>
