<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  exit();
}

include "../conecta2.php";
header("Content-type: text/html; charset=utf-8");

// Receber dados do formulário
$matricula_original = $_POST['matricula_original'] ?? '';
$matricula = trim($_POST['matfunc'] ?? '');

// Na edição nenhum campo da tela é obrigatório; se a matrícula vier vazia, mantém a original
if (empty($matricula)) {
  $matricula = $matricula_original;
}

$mataux = $_POST['mataux'] ?? '';
$nome = mb_strtoupper($_POST['nome'] ?? '', 'UTF-8');
$datanascimento = $_POST['datanascimento'] ?? '';
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? ''); // Remove formatação
$rg = preg_replace('/\D/', '', $_POST['rg'] ?? ''); // Remove formatação
$cep = preg_replace('/\D/', '', $_POST['cep'] ?? ''); // Remove formatação
$endereco = mb_strtoupper($_POST['endereco'] ?? '', 'UTF-8');
$numres = $_POST['numres'] ?? '';
$bairro = mb_strtoupper($_POST['bairro'] ?? '', 'UTF-8');
$cidade = mb_strtoupper($_POST['cidade'] ?? '', 'UTF-8');
$estado = mb_strtoupper($_POST['estado'] ?? '', 'UTF-8');
$email = $_POST['email'] ?? '';
$status = mb_strtoupper($_POST['status'] ?? '', 'UTF-8');
$dtadmissao = $_POST['dtadmissao'] ?? '';
$codempresa = $_POST['codempresa'] ?? ''; // Recebe o idtbfilial
$ccusto = mb_strtoupper($_POST['ccusto'] ?? '', 'UTF-8');
$cargo = mb_strtoupper($_POST['cargo'] ?? '', 'UTF-8');
$projeto = mb_strtoupper($_POST['projeto'] ?? '', 'UTF-8');
$uf_trabalho = mb_strtoupper($_POST['uf_trabalho'] ?? '', 'UTF-8');
$matr_autor = $_POST['matr_autor'] ?? '';

// Validar CPF somente se estiver preenchido
if (!empty($cpf) && strlen($cpf) != 11) {
  echo "<script>alert('CPF inválido! Deve conter 11 dígitos.'); window.history.back();</script>";
  exit();
}

if (!empty($cpf) && preg_match('/^(\d)\1+$/', $cpf)) {
  echo "<script>alert('CPF inválido! Não é permitido CPF com todos os dígitos iguais.'); window.history.back();</script>";
  exit();
}

// Validar RG (se fornecido, deve ter entre 6 e 11 dígitos)
if (!empty($rg) && (strlen($rg) < 6 || strlen($rg) > 11)) {
  echo "<script>alert('RG inválido! Deve conter entre 6 e 11 dígitos.'); window.history.back();</script>";
  exit();
}

// Verifica se RG é inválido (todos os dígitos iguais)
if (!empty($rg) && preg_match('/^(\d)\1+$/', $rg)) {
  echo "<script>alert('RG inválido! Não é permitido RG com todos os dígitos iguais.'); window.history.back();</script>";
  exit();
}

// Validar se nova matrícula já existe
if ($matricula != $matricula_original) {
  $sql_mat_check = "SELECT matricula FROM bdaniel.tbfuncionario WHERE matricula = '$matricula' AND matricula != '$matricula_original'";
  $result_mat_check = mysqli_query($conexao, $sql_mat_check);
  
  if (mysqli_num_rows($result_mat_check) > 0) {
    echo "<script>alert('Matrícula já cadastrada!'); window.history.back();</script>";
    exit();
  }
}

// Preparar query de atualização
$sql = "UPDATE bdaniel.tbfuncionario SET 
        matricula = '$matricula',
        mataux = '$mataux',
        nome = '$nome',
        dtnasc = '$datanascimento',
        cpf = '$cpf',
        rg = '$rg',
        cep = '$cep',
        endereco = '$endereco',
        numres = '$numres',
        bairro = '$bairro',
        cidade = '$cidade',
        estado = '$estado',
        email = '$email',
        status = '$status',
        dtadmissao = '$dtadmissao',
        codempresa = '$codempresa',
        ccusto = '$ccusto',
        cargo = '$cargo',
        projeto = '$projeto',
        uf_trabalho = '$uf_trabalho'
        WHERE matricula = '$matricula_original'";

// A atualização e o respectivo log devem ser gravados juntos.
mysqli_begin_transaction($conexao);
$resultado = mysqli_query($conexao, $sql);

if ($resultado) {
  // Se a matrícula foi alterada, atualizar também na tbfrota.tbusuario
  if ($matricula != $matricula_original) {
    $sql_user_check = "SELECT matricula FROM bdfrota.tbusuario WHERE matricula = '$matricula_original'";
    $result_user_check = mysqli_query($conexao, $sql_user_check);
    
    if (mysqli_num_rows($result_user_check) > 0) {
      $sql_user = "UPDATE bdfrota.tbusuario SET 
                   matricula = '$matricula',
                   nome = '$nome'
                   WHERE matricula = '$matricula_original'";
      if (!mysqli_query($conexao, $sql_user)) {
        mysqli_rollback($conexao);
        echo "<script>alert('Erro ao atualizar o usuário vinculado. Nenhuma alteração foi gravada.'); window.history.back();</script>";
        mysqli_close($conexao);
        exit();
      }
    }
  } else {
    // Apenas atualizar o nome se existir
    $sql_user_check = "SELECT matricula FROM bdfrota.tbusuario WHERE matricula = '$matricula'";
    $result_user_check = mysqli_query($conexao, $sql_user_check);
    
    if (mysqli_num_rows($result_user_check) > 0) {
      $sql_user = "UPDATE bdfrota.tbusuario SET nome = '$nome' WHERE matricula = '$matricula'";
      if (!mysqli_query($conexao, $sql_user)) {
        mysqli_rollback($conexao);
        echo "<script>alert('Erro ao atualizar o usuário vinculado. Nenhuma alteração foi gravada.'); window.history.back();</script>";
        mysqli_close($conexao);
        exit();
      }
    }
  }
  
  // Log da operação
  $data_hora = date('Y-m-d H:i:s');
  $matricula_log = mysqli_real_escape_string($conexao, $matricula);
  $mat_autor_log = mysqli_real_escape_string($conexao, $matr_autor);
  $sql_log = "INSERT INTO bdfrota.tblog (data_e_hora, acao, matricula, mat_autor, tipo, placa)
              VALUES ('$data_hora', 'Atualizou condutor PJ', '$matricula_log', '$mat_autor_log', 'edição', '')";
  $resultado_log = mysqli_query($conexao, $sql_log);

  if (!$resultado_log) {
    mysqli_rollback($conexao);
    echo "<script>alert('Não foi possível registrar a atualização e seu log. Nenhuma alteração foi gravada.'); window.history.back();</script>";
    mysqli_close($conexao);
    exit();
  }

  mysqli_commit($conexao);
  
  echo "<script>
    alert('Condutor PJ atualizado com sucesso!');
    window.location.href = './listar_condutorespj.php';
  </script>";
} else {
  mysqli_rollback($conexao);
  echo "<script>
    alert('Erro ao atualizar condutor: " . mysqli_error($conexao) . "');
    window.history.back();
  </script>";
}

mysqli_close($conexao);
?>
<!--  -->