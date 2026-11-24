<?php
require_once __DIR__ . '/formularios_funcoes.php';

$tipos = formulariosTipos();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$registro = $id ? formularioBuscar($conexao, $id) : null;
$tipo = $registro ? $registro['tipo'] : (isset($_GET['tipo']) ? $_GET['tipo'] : '');

if (!isset($tipos[$tipo]) || $tipos[$tipo]['tipo'] === 'personalizado' || ($id && !$registro)) {
    http_response_code(404);
    exit('Formulário não encontrado.');
}

$definicao = $tipos[$tipo];
$respostas = $registro ? json_decode($registro['respostas'], true) : array();
$respostas = is_array($respostas) ? $respostas : array();
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    $profissionalNome = trim(isset($_POST['profissional_nome']) ? $_POST['profissional_nome'] : '');
    $profissionalMatricula = trim(isset($_POST['profissional_matricula']) ? $_POST['profissional_matricula'] : '');
    $resposta = trim(isset($_POST[$definicao['campo']]) ? $_POST[$definicao['campo']] : '');

    if (!formularioValidarCsrf($token)) {
        $erro = 'A sessão expirou. Atualize a página e tente novamente.';
    } elseif ($profissionalNome === '' || $profissionalMatricula === '' || $resposta === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif ($definicao['tipo'] === 'sim_nao' && !in_array($resposta, array('Sim', 'Não'), true)) {
        $erro = 'Selecione uma resposta válida.';
    } else {
        $json = json_encode(array($definicao['campo'] => $resposta), JSON_UNESCAPED_UNICODE);
        if ($registro) {
            $sql = 'UPDATE bdfrota.tbformulariopreenchido
                    SET profissional_nome = ?, profissional_matricula = ?, respostas = ?, atualizado_em = NOW()
                    WHERE id = ?';
            $stmt = mysqli_prepare($conexao, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssi', $profissionalNome, $profissionalMatricula, $json, $id);
            }
        } else {
            $autor = isset($_SESSION['matricula']) ? (string) $_SESSION['matricula'] : '';
            $sql = 'INSERT INTO bdfrota.tbformulariopreenchido
                    (tipo, nome_formulario, profissional_nome, profissional_matricula, data_preenchimento, respostas, criado_por_matricula)
                    VALUES (?, ?, ?, ?, NOW(), ?, ?)';
            $stmt = mysqli_prepare($conexao, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssss', $tipo, $definicao['nome'], $profissionalNome, $profissionalMatricula, $json, $autor);
            }
        }

        if ($stmt && mysqli_stmt_execute($stmt)) {
            header('Location: listagem-formularios.php?salvo=1');
            exit;
        }
        $erro = 'Não foi possível salvar o formulário: ' . ($stmt ? mysqli_stmt_error($stmt) : mysqli_error($conexao));
    }

    $registro['profissional_nome'] = $profissionalNome;
    $registro['profissional_matricula'] = $profissionalMatricula;
    $respostas[$definicao['campo']] = $resposta;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/png" href="../src/images/favicon.png">
  <title><?= formularioEscapar($registro ? 'Editar ' . $definicao['nome'] : $definicao['nome']) ?></title>
  <link href="../src/css/styles.css" rel="stylesheet">
</head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
<?php include '../menu.php'; ?>
<div id="layoutSidenav_content"><main class="mb-5" style="width:100%"><div class="container-fluid px-4">
  <h1 class="h1 pt-2 pb-2 text-center"><?= $registro ? 'Editar' : 'Preencher' ?> <?= formularioEscapar($definicao['nome']) ?></h1>
  <?php if ($erro): ?><div class="alert alert-danger"><?= formularioEscapar($erro) ?></div><?php endif; ?>
  <div class="card mx-auto" style="max-width:800px"><div class="card-body">
    <p class="text-muted">Informe abaixo o profissional destinatário do formulário.</p>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= formularioEscapar(formularioCsrfToken()) ?>">
      <div class="row">
        <div class="col-md-8 mb-3"><label class="form-label" for="profissional_nome">Nome do profissional <span class="text-danger">*</span></label><input class="form-control" id="profissional_nome" name="profissional_nome" maxlength="150" required value="<?= formularioEscapar(isset($registro['profissional_nome']) ? $registro['profissional_nome'] : '') ?>"></div>
        <div class="col-md-4 mb-3"><label class="form-label" for="profissional_matricula">Matrícula <span class="text-danger">*</span></label><input class="form-control" id="profissional_matricula" name="profissional_matricula" maxlength="30" required value="<?= formularioEscapar(isset($registro['profissional_matricula']) ? $registro['profissional_matricula'] : '') ?>"></div>
      </div>
      <div class="mb-4">
        <label class="form-label" for="resposta_campo"><?= formularioEscapar($definicao['pergunta']) ?> <span class="text-danger">*</span></label>
        <?php if ($definicao['tipo'] === 'sim_nao'): ?>
          <select class="form-select" id="resposta_campo" name="<?= formularioEscapar($definicao['campo']) ?>" required><option value="">Selecione</option><option value="Sim" <?= (isset($respostas[$definicao['campo']]) && $respostas[$definicao['campo']] === 'Sim') ? 'selected' : '' ?>>Sim</option><option value="Não" <?= (isset($respostas[$definicao['campo']]) && $respostas[$definicao['campo']] === 'Não') ? 'selected' : '' ?>>Não</option></select>
        <?php else: ?>
          <textarea class="form-control" id="resposta_campo" name="<?= formularioEscapar($definicao['campo']) ?>" rows="6" maxlength="5000" required><?= formularioEscapar(isset($respostas[$definicao['campo']]) ? $respostas[$definicao['campo']] : '') ?></textarea>
        <?php endif; ?>
      </div>
      <div class="d-flex justify-content-end gap-2"><a class="btn btn-secondary" href="listagem-formularios.php">Cancelar</a><button class="btn btn-success" type="submit">Salvar formulário</button></div>
    </form>
  </div></div>
</div></main></div>
</body></html>