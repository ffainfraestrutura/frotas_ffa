<?php
require_once __DIR__ . '/formulario-conserto-pneu-funcoes.php';

$id = defined('FORMULARIO_CONSERTO_PNEU_EDICAO') ? $idEdicao : null;
$registro = $id ? formularioBuscar($conexao, $id) : null;
if ($id && !$registro) {
    http_response_code(404);
    exit('Formulário não encontrado.');
}

$respostas = $registro ?: array();
$autorizadoPor = $registro ? $registro['autorizado_por'] : (string) $_SESSION['nome'];
$erro = '';

// As listas vêm das tabelas oficiais de condutores e veículos da frota.
$motoristas = mysqli_query($conexao, "SELECT matricula, MAX(nome) AS nome FROM bdfrota.tbcondutor WHERE ativo = 1 AND nome <> '' GROUP BY matricula ORDER BY nome");
$veiculos = mysqli_query($conexao, "SELECT DISTINCT placa FROM bdfrota.tbveiculo WHERE visivel = 1 AND status = 1 AND placa <> '' ORDER BY placa");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campos = array('data', 'cc', 'motorista_matricula', 'placa', 'defeito', 'servico_executado', 'valor_total', 'endereco');
    $dados = array();
    foreach ($campos as $campo) {
        $dados[$campo] = trim(isset($_POST[$campo]) ? $_POST[$campo] : '');
    }

    if (!formularioValidarCsrf(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '')) {
        $erro = 'A sessão expirou. Atualize a página e tente novamente.';
    } elseif (in_array('', $dados, true)) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dados['data'], $partesData)
        || !checkdate((int) $partesData[2], (int) $partesData[3], (int) $partesData[1])) {
        $erro = 'Informe uma data válida.';
    } else {
        $stmtMotorista = mysqli_prepare($conexao, "SELECT nome, matricula FROM bdfrota.tbcondutor WHERE ativo = 1 AND matricula = ? ORDER BY idtbcondutor DESC LIMIT 1");
        mysqli_stmt_bind_param($stmtMotorista, 's', $dados['motorista_matricula']);
        mysqli_stmt_execute($stmtMotorista);
        $motorista = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtMotorista));

        $stmtPlaca = mysqli_prepare($conexao, "SELECT placa FROM bdfrota.tbveiculo WHERE visivel = 1 AND status = 1 AND placa = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtPlaca, 's', $dados['placa']);
        mysqli_stmt_execute($stmtPlaca);
        $placa = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtPlaca));

        if (!$motorista || !$placa) {
            $erro = 'Selecione um motorista e uma placa válidos nas listas.';
        } else {
            $dados['motorista_nome'] = $motorista['nome'];
            $dados['placa'] = $placa['placa'];
            $nomeMotorista = $motorista['nome'];
            $matriculaMotorista = $motorista['matricula'];

            if ($registro) {
                $sql = 'UPDATE bdfrota.tbconsertopneu
                        SET data = ?, cc = ?, motorista_nome = ?, motorista_matricula = ?, placa = ?,
                            defeito = ?, servico_executado = ?, valor_total = ?, endereco = ?, atualizado_em = NOW()
                        WHERE id = ?';
                $stmt = mysqli_prepare($conexao, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sssssssssi', $dados['data'], $dados['cc'], $nomeMotorista,
                        $matriculaMotorista, $dados['placa'], $dados['defeito'], $dados['servico_executado'],
                        $dados['valor_total'], $dados['endereco'], $id);
                }
            } else {
                $autorMatricula = (string) $_SESSION['matricula'];
                $sql = 'INSERT INTO bdfrota.tbconsertopneu
                        (data, cc, motorista_nome, motorista_matricula, placa, defeito, servico_executado,
                         valor_total, autorizado_por, autorizado_matricula, endereco)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
                $stmt = mysqli_prepare($conexao, $sql);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sssssssssss', $dados['data'], $dados['cc'], $nomeMotorista,
                        $matriculaMotorista, $dados['placa'], $dados['defeito'], $dados['servico_executado'],
                        $dados['valor_total'], $autorizadoPor, $autorMatricula, $dados['endereco']);
                }
            }

            if ($stmt && mysqli_stmt_execute($stmt)) {
                header('Location: listagem-formularios-conserto-pneu.php?salvo=1');
                exit;
            }
            $erro = 'Não foi possível salvar o formulário: ' . ($stmt ? mysqli_stmt_error($stmt) : mysqli_error($conexao));
        }
    }
    $dados['autorizado_por'] = $autorizadoPor;
    $dados['motorista_nome'] = isset($nomeMotorista) ? $nomeMotorista : '';
    $respostas = $dados;
}

function valorConserto($respostas, $campo, $padrao = '')
{
    return formularioEscapar(isset($respostas[$campo]) ? $respostas[$campo] : $padrao);
}
?>
<!DOCTYPE html>
<html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="../src/images/favicon.png"><title><?= $registro ? 'Editar' : 'Preencher' ?> Conserto de Pneu/Borracharia</title>
<link href="../src/css/styles.css" rel="stylesheet"><link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>.select2-container .select2-selection--single{height:38px;padding:5px}.card-header{background:#2e2851;color:#fff}.form-label{font-weight:600}</style></head>
<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled"><?php include '../menu.php'; ?>
<div id="layoutSidenav_content"><main class="mb-5" style="width:100%"><div class="container-fluid px-4">
<h1 class="h1 pt-2 pb-2">Conserto de Pneu/Borracharia</h1>
<?php if ($erro): ?><div class="alert alert-danger"><?= formularioEscapar($erro) ?></div><?php endif; ?>
<form method="post" class="card"><div class="card-header"><i class="fas fa-wrench me-2"></i>Informações do requerimento</div><div class="card-body">
<input type="hidden" name="csrf_token" value="<?= formularioEscapar(formularioCsrfToken()) ?>">
<div class="row">
  <div class="col-md-4 mb-3"><label class="form-label" for="data">Data *</label><input class="form-control" type="date" id="data" name="data" required value="<?= valorConserto($respostas, 'data') ?>"></div>
  <div class="col-md-8 mb-3"><label class="form-label" for="cc">CC *</label><input class="form-control" id="cc" name="cc" maxlength="100" required value="<?= valorConserto($respostas, 'cc') ?>"></div>
  <div class="col-md-6 mb-3"><label class="form-label" for="motorista_matricula">Motorista *</label><select class="form-select busca" id="motorista_matricula" name="motorista_matricula" required><option value="">Selecione o motorista</option><?php if ($motoristas): while ($motorista = mysqli_fetch_assoc($motoristas)): ?><option value="<?= formularioEscapar($motorista['matricula']) ?>" <?= valorConserto($respostas, 'motorista_matricula') === formularioEscapar($motorista['matricula']) ? 'selected' : '' ?>><?= formularioEscapar($motorista['nome']) ?> — <?= formularioEscapar($motorista['matricula']) ?></option><?php endwhile; endif; ?></select></div>
  <div class="col-md-6 mb-3"><label class="form-label" for="placa">Placa *</label><select class="form-select busca" id="placa" name="placa" required><option value="">Selecione a placa</option><?php if ($veiculos): while ($veiculo = mysqli_fetch_assoc($veiculos)): ?><option value="<?= formularioEscapar($veiculo['placa']) ?>" <?= valorConserto($respostas, 'placa') === formularioEscapar($veiculo['placa']) ? 'selected' : '' ?>><?= formularioEscapar($veiculo['placa']) ?></option><?php endwhile; endif; ?></select></div>
  <div class="col-12 mb-3"><label class="form-label" for="defeito">Defeito *</label><textarea class="form-control" id="defeito" name="defeito" rows="3" maxlength="3000" required><?= valorConserto($respostas, 'defeito') ?></textarea></div>
  <div class="col-12 mb-3"><label class="form-label" for="servico_executado">Serviço executado *</label><textarea class="form-control" id="servico_executado" name="servico_executado" rows="3" maxlength="3000" required><?= valorConserto($respostas, 'servico_executado') ?></textarea></div>
  <div class="col-md-4 mb-3"><label class="form-label" for="valor_total">Valor total *</label><div class="input-group"><span class="input-group-text">R$</span><input class="form-control" id="valor_total" name="valor_total" maxlength="30" required value="<?= valorConserto($respostas, 'valor_total') ?>"></div></div>
  <div class="col-md-8 mb-3"><label class="form-label" for="autorizado_por">Autorizado por</label><input class="form-control" id="autorizado_por" value="<?= formularioEscapar($autorizadoPor) ?>" readonly aria-readonly="true"><div class="form-text">Preenchido automaticamente com o usuário autenticado.</div></div>
  <div class="col-12 mb-3"><label class="form-label" for="endereco">Endereço *</label><textarea class="form-control" id="endereco" name="endereco" rows="2" maxlength="500" required><?= valorConserto($respostas, 'endereco') ?></textarea></div>
</div><div class="d-flex justify-content-end gap-2"><a class="btn btn-secondary" href="listagem-formularios-conserto-pneu.php">Cancelar</a><button class="btn btn-success" type="submit">Salvar formulário</button></div>
</div></form></div></main></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script><script src="../src/js/scripts.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script><script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script><script>$(function(){ $('.busca').select2({width:'100%', language:{noResults:function(){return 'Nenhum resultado encontrado';}}}); });</script>
</body></html>
