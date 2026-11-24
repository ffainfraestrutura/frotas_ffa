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

// Define charset para UTF-8
mysqli_set_charset($conexao, "utf8");

header("Content-type: text/html; charset=utf-8");

// Recebe os dados do formulário com tratamento UTF-8
// Informações Pessoais
$nome = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['nome'], 'UTF-8'));
$datanascimento = mysqli_real_escape_string($conexao, $_POST['datanascimento']);

// Documentos
$cpf = mysqli_real_escape_string($conexao, $_POST['cpf']);
$rg = mysqli_real_escape_string($conexao, $_POST['rg']);

// Informações de Endereço
$endereco = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['endereco'], 'UTF-8'));
$numres = mysqli_real_escape_string($conexao, $_POST['numres']);
$bairro = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['bairro'], 'UTF-8'));
$cidade = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['cidade'], 'UTF-8'));
$cep = mysqli_real_escape_string($conexao, $_POST['cep']);
$estado = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['estado'], 'UTF-8'));
$email = mysqli_real_escape_string($conexao, $_POST['email']);

// Dados Contratuais
$matfunc = mysqli_real_escape_string($conexao, $_POST['matfunc']);
$mataux = mysqli_real_escape_string($conexao, $_POST['mataux']);
$status = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['status'], 'UTF-8'));
$dtadmissao = mysqli_real_escape_string($conexao, $_POST['dtadmissao']);
$codempresa = mysqli_real_escape_string($conexao, $_POST['codempresa']); // idtbfilial
$ccusto = mysqli_real_escape_string($conexao, mb_strtoupper(trim($_POST['ccusto']), 'UTF-8'));
$cargo = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['cargo'], 'UTF-8'));
$projeto = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['projeto'], 'UTF-8'));
$uf_trabalho = mysqli_real_escape_string($conexao, mb_strtoupper($_POST['uf_trabalho'], 'UTF-8'));

// Validação de campos obrigatórios MÍNIMOS
if (empty($nome)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('O campo Nome é obrigatório!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

if (empty($matfunc)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('O campo Matrícula é obrigatório!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

if (empty($cpf)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('O campo CPF é obrigatório!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

if (empty($datanascimento)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('O campo Data de Nascimento é obrigatório!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

if (empty($dtadmissao)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('O campo Data de Admissão é obrigatório!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

if (empty($codempresa)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('O campo Empresa é obrigatório!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

// Validação do CPF - 11 dígitos e não pode ser tudo igual
$cpf_numeros = preg_replace('/\D/', '', $cpf);
if (strlen($cpf_numeros) !== 11) {
    echo "<script language='javascript' type='text/javascript'>
        alert('CPF deve conter exatamente 11 dígitos!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

// Verifica se todos os dígitos são iguais (CPF inválido)
if (preg_match('/^(\d)\1{10}$/', $cpf_numeros)) {
    echo "<script language='javascript' type='text/javascript'>
        alert('CPF inválido! Não é permitido CPF com todos os dígitos iguais.');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

// Validação do RG - entre 6 e 11 dígitos se preenchido
if (!empty($rg)) {
    $rg_numeros = preg_replace('/\D/', '', $rg);
    if (strlen($rg_numeros) < 6 || strlen($rg_numeros) > 11) {
        echo "<script language='javascript' type='text/javascript'>
            alert('RG deve conter entre 6 e 11 dígitos!');
            window.location='../cadastrar_condutorespj.php';
        </script>";
        exit;
    }
    
    // Verifica se todos os dígitos são iguais (RG inválido)
    if (preg_match('/^(\d)\1+$/', $rg_numeros)) {
        echo "<script language='javascript' type='text/javascript'>
            alert('RG inválido! Não é permitido RG com todos os dígitos iguais.');
            window.location='../cadastrar_condutorespj.php';
        </script>";
        exit;
    }
}


// DEBUG: Verificar dados recebidos
error_log("=== DADOS RECEBIDOS - CADASTRO CONDUTOR ===");
error_log("Nome: " . $nome);
error_log("Matrícula: " . $matfunc);
error_log("CPF: " . $cpf);
error_log("Data Nasc: " . $datanascimento);
error_log("Data Admissão: " . $dtadmissao);
error_log("Cargo: " . $cargo);
error_log("Empresa (idtbfilial): " . $codempresa);

// Verifica se a matrícula já existe
$sql_verifica = "SELECT matricula FROM bdaniel.tbfuncionario WHERE matricula = '$matfunc'";
$resultado_verifica = mysqli_query($conexao, $sql_verifica);

if ($resultado_verifica && mysqli_num_rows($resultado_verifica) > 0) {
    echo "<script language='javascript' type='text/javascript'>
        alert('Matrícula já cadastrada no sistema!');
        window.location='../cadastrar_condutorespj.php';
    </script>";
    exit;
}

// Verifica se houve erro na consulta
if (!$resultado_verifica) {
    error_log("Erro na verificação de matrícula: " . mysqli_error($conexao));
}

// Prepara valores tratados para evitar SQL vazio
$val_nome = !empty($nome) ? "'$nome'" : "NULL";
$val_cpf = !empty($cpf) ? "'$cpf'" : "NULL";
$val_matfunc = !empty($matfunc) ? "'$matfunc'" : "NULL";
$val_dtnasc = !empty($datanascimento) ? "'$datanascimento'" : "NULL";
$val_dtadmissao = !empty($dtadmissao) ? "'$dtadmissao'" : "NULL";
$val_codempresa = !empty($codempresa) ? "'$codempresa'" : "NULL"; // Salva o idtbfilial

// Prepara a query de inserção com apenas os campos da tabela tbfuncionario
$sql = "INSERT INTO bdaniel.tbfuncionario (
    matricula,
    nome,
    dtnasc,
    cpf,
    rg,
    endereco,
    numres,
    bairro,
    cidade,
    cep,
    estado,
    email,
    dtadmissao,
    mataux,
    status,
    cargo,
    ccusto,
    codempresa,
    projeto,
    uf_trabalho
) VALUES (
    $val_matfunc,
    $val_nome,
    $val_dtnasc,
    $val_cpf,
    " . (!empty($rg) ? "'$rg'" : "NULL") . ",
    " . (!empty($endereco) ? "'$endereco'" : "NULL") . ",
    " . (!empty($numres) ? "'$numres'" : "NULL") . ",
    " . (!empty($bairro) ? "'$bairro'" : "NULL") . ",
    " . (!empty($cidade) ? "'$cidade'" : "NULL") . ",
    " . (!empty($cep) ? "'$cep'" : "NULL") . ",
    " . (!empty($estado) ? "'$estado'" : "NULL") . ",
    " . (!empty($email) ? "'$email'" : "NULL") . ",
    $val_dtadmissao,
    " . (!empty($mataux) ? "'$mataux'" : "NULL") . ",
    " . (!empty($status) ? "'$status'" : "'ATIVO'") . ",
    " . (!empty($cargo) ? "'$cargo'" : "NULL") . ",
    " . (!empty($ccusto) ? "'$ccusto'" : "NULL") . ",
    $val_codempresa,
    " . (!empty($projeto) ? "'$projeto'" : "NULL") . ",
    " . (!empty($uf_trabalho) ? "'$uf_trabalho'" : "NULL") . "
)";

// DEBUG: Log do SQL gerado
error_log("=== SQL GERADO ===");
error_log($sql);

// O cadastro e o respectivo log devem ser gravados juntos.
mysqli_begin_transaction($conexao);

// Executa a query
$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
    // Verifica se realmente foi inserido
    $linhas_afetadas = mysqli_affected_rows($conexao);
    error_log("Linhas afetadas: " . $linhas_afetadas);
    
    if ($linhas_afetadas > 0) {
        $ultimo_id = mysqli_insert_id($conexao);
        error_log("ID inserido: " . $ultimo_id);

        // Log da operação de cadastro
        $data_hora = date('Y-m-d H:i:s');
        $mat_autor_log = mysqli_real_escape_string($conexao, $matricula1);
        $sql_log = "INSERT INTO bdfrota.tblog (data_e_hora, acao, matricula, mat_autor, tipo, placa)
                    VALUES ('$data_hora', 'mat_autor cadastrou condutor PJ matricula', '$matfunc', '$mat_autor_log', 'cadastro', '')";
        $resultado_log = mysqli_query($conexao, $sql_log);

        if (!$resultado_log) {
            $erro_log = mysqli_error($conexao);
            mysqli_rollback($conexao);
            error_log("Erro ao gravar log de cadastro: " . $erro_log);
            echo "<script language='javascript' type='text/javascript'>
                alert('Não foi possível registrar o cadastro e seu log. Nenhuma alteração foi gravada.');
                window.location='../cadastrar_condutorespj.php';
            </script>";
            mysqli_close($conexao);
            exit();
        }

        mysqli_commit($conexao);

        echo "<script language='javascript' type='text/javascript'>
            if (confirm('Condutor cadastrado com sucesso! Matrícula: $matfunc\\n\\nDeseja cadastrar a CNH agora?')) {
                window.open('https://ffa.painel-telecom.com:4545/frotas/gestao/cadastrocnh.php?matricula=$matfunc', '_blank');
                window.location='../listar_condutorespj.php';
            } else {
                window.location='../listar_condutorespj.php';
            }
        </script>";
    } else {
        mysqli_rollback($conexao);
        error_log("AVISO: Query executada mas nenhuma linha foi afetada");
        echo "<script language='javascript' type='text/javascript'>
            alert('Nenhum registro foi inserido. Verifique os dados informados.');
            window.location='../cadastrar_condutorespj.php';
        </script>";
    }
} else {
    $erro = mysqli_error($conexao);
    $errno = mysqli_errno($conexao);
    mysqli_rollback($conexao);
    error_log("ERRO MySQL [$errno]: " . $erro);
    error_log("SQL com erro: " . $sql);
    
    // Mensagem de erro mais detalhada
    $msg_erro = "Erro ao cadastrar condutor: $erro";
    if ($errno == 1062) {
        $msg_erro = "Matrícula já cadastrada no sistema!";
    } elseif ($errno == 1054) {
        $msg_erro = "Erro de estrutura da tabela. Verifique se todas as colunas existem.";
    } elseif ($errno == 1146) {
        $msg_erro = "Tabela tbfuncionario não encontrada!";
    }
    
    echo "<script language='javascript' type='text/javascript'>
        alert('$msg_erro');
        window.location='../cadastrar_condutorespj.php';
    </script>";
}

mysqli_close($conexao);
?>
<!--  -->