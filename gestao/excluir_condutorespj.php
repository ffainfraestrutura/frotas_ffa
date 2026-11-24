<?php
date_default_timezone_set('America/Sao_Paulo');
// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600;
session_set_cookie_params($lifetime);
session_start();

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];

if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
  exit();
} elseif ($perfil == '0'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  exit();
} elseif($perfil == '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  exit();
} elseif($perfil == '2'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  exit();
} elseif ($matricula1 == '500422'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
  exit();
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

include "../conecta2.php";
header("Content-type: text/html; charset=utf-8");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo "<script>
    alert('Requisição inválida para exclusão!');
    window.location.href = './listar_condutorespj.php';
  </script>";
  exit();
}

$matricula = isset($_POST['matricula']) ? trim($_POST['matricula']) : '';

if (empty($matricula)) {
  echo "<script>
    alert('Matrícula não informada!');
    window.location.href = './listar_condutorespj.php';
  </script>";
  exit();
}

if (!preg_match('/^62[0-9A-Za-z_-]*$/', $matricula)) {
  echo "<script>
    alert('A matrícula informada não pertence a um condutor PJ.');
    window.location.href = './listar_condutorespj.php';
  </script>";
  exit();
}

$matricula_sql = mysqli_real_escape_string($conexao, $matricula);
$mat_autor_sql = mysqli_real_escape_string($conexao, $matricula1);
$data_hora = date('Y-m-d H:i:s');

// Buscar dados do condutor antes da exclusão para registrar o histórico no log
$sql_condutor = "SELECT matricula, nome, cpf, ccusto, cargo, status
                FROM bdaniel.tbfuncionario
                WHERE matricula = '$matricula_sql'
                AND matricula LIKE '62%'
                LIMIT 1";
$result_condutor = mysqli_query($conexao, $sql_condutor);

if (!$result_condutor || mysqli_num_rows($result_condutor) == 0) {
  echo "<script>
    alert('Condutor PJ não encontrado!');
    window.location.href = './listar_condutorespj.php';
  </script>";
  exit();
}

$condutor = mysqli_fetch_array($result_condutor, MYSQLI_ASSOC);
$nome_condutor = $condutor['nome'];
$cpf_condutor = $condutor['cpf'];
$ccusto_condutor = $condutor['ccusto'];
$cargo_condutor = $condutor['cargo'];
$status_condutor = $condutor['status'];

$acao_log = "Excluiu condutor PJ: $nome_condutor (Mat: $matricula, CPF: $cpf_condutor, Centro de custo: $ccusto_condutor, Cargo: $cargo_condutor, Status: $status_condutor)";
$acao_log_sql = mysqli_real_escape_string($conexao, $acao_log);

mysqli_begin_transaction($conexao);

try {
  $sql_delete_funcionario = "DELETE FROM bdaniel.tbfuncionario
                            WHERE matricula = '$matricula_sql'
                            AND matricula LIKE '62%'";
  $resultado_delete_funcionario = mysqli_query($conexao, $sql_delete_funcionario);

  if (!$resultado_delete_funcionario || mysqli_affected_rows($conexao) == 0) {
    throw new Exception('Não foi possível excluir o cadastro do condutor PJ.');
  }

  // Deletar também da tabela de usuários se existir cadastro vinculado
  $sql_delete_usuario = "DELETE FROM bdfrota.tbusuario WHERE matricula = '$matricula_sql'";
  $resultado_delete_usuario = mysqli_query($conexao, $sql_delete_usuario);

  if (!$resultado_delete_usuario) {
    throw new Exception('Não foi possível excluir o usuário vinculado ao condutor PJ.');
  }

  $sql_log = "INSERT INTO bdfrota.tblog (data_e_hora, acao, matricula, mat_autor, tipo, placa)
              VALUES ('$data_hora', '$acao_log_sql', '$matricula_sql', '$mat_autor_sql', 'exclusão', '')";
  $resultado_log = mysqli_query($conexao, $sql_log);

  if (!$resultado_log) {
    throw new Exception('Não foi possível registrar o log da exclusão.');
  }

  mysqli_commit($conexao);

  echo "<script>
    alert('Condutor PJ excluído com sucesso! A exclusão foi registrada no log.');
    window.location.href = './listar_condutorespj.php';
  </script>";
} catch (Exception $e) {
  mysqli_rollback($conexao);
  $mensagem = mysqli_real_escape_string($conexao, $e->getMessage());

  echo "<script>
    alert('Erro ao excluir condutor PJ: $mensagem');
    window.location.href = './listar_condutorespj.php';
  </script>";
}

mysqli_close($conexao);
?>