<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../conecta.php';

if (!isset($_GET['matricula'])) {
    $_SESSION['erro'] = "Matricula não informada";
    header('Location: index.php');
    exit;
}

$matricula = $_GET['matricula'];

// Busca curso
$stmt = $pdo->prepare("SELECT * FROM tbcondutor WHERE matricula = ? LIMIT 1");
$stmt->execute([$matricula]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
    $_SESSION['erro'] = "Dados do funcionario não encontrado!";
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $max_cota_diaria = filter_input(INPUT_POST, 'max_cota_diaria', FILTER_SANITIZE_STRING);
    $min_cota_diaria = filter_input(INPUT_POST, 'min_cota_diaria', FILTER_SANITIZE_STRING);
    $max_cota_semanal = filter_input(INPUT_POST, 'max_cota_semanal', FILTER_VALIDATE_INT);
    $min_cota_semanal = filter_input(INPUT_POST, 'min_cota_semanal', FILTER_VALIDATE_INT);
    $matricula = filter_input(INPUT_POST, 'matricula', FILTER_VALIDATE_INT);

    try {
        $stmt = $pdo->prepare("SELECT * from bdcombustivel.tbconfiguracoes_funcionarios where matricula = ?");
        $stmt->execute([$_GET['matricula']]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            $stmt = $pdo->prepare("INSERT INTO bdcombustivel.tbconfiguracoes_funcionarios (matricula, limite_superior_cota, limite_inferior_cota, limite_superior_semanal_cota, limite_inferior_semanal_cota) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_GET['matricula'], $max_cota_diaria, $min_cota_diaria, $max_cota_semanal, $min_cota_semanal]);

            $_SESSION['sucesso'] = "Informações do condutor atualizadas com sucesso!";
            header('Location: index.php');
            exit;
        } else {
            $stmt = $pdo->prepare("UPDATE bdcombustivel.tbconfiguracoes_funcionarios SET limite_superior_cota = ?, limite_inferior_cota = ?, limite_superior_semanal_cota = ?, limite_inferior_semanal_cota = ? WHERE matricula = ?");
            $stmt->execute([$max_cota_diaria, $min_cota_diaria, $max_cota_semanal, $min_cota_semanal, $_GET['matricula']]);

            $_SESSION['sucesso'] = "Informações do condutor atualizadas com sucesso!";
            header('Location: index.php');
            exit;
        }


    } catch (PDOException $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body>
    
    <?php include '../menu.php'?>

    <div class="container mt-4">
        <h2><i class="bi bi-journal-text"></i> Editar Condutor</h2>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="matricula" class="form-label">Matrícula</label>
                <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Digite a matrícula"
                    value="<?=$matricula?>" disabled>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="maxCotaDiaria" class="form-label">Máximo de Cota Diária</label>
                    <input type="number" class="form-control" id="maxCotaDiaria" name="max_cota_diaria"
                        placeholder="Ex: 100" step="0.01" min="0" value="<?=$dados['limite_superior_cota'] ?? 100 ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="minCotaDiaria" class="form-label">Mínimo de Cota Diária</label>
                    <input type="number" class="form-control" id="minCotaDiaria" name="min_cota_diaria"
                        placeholder="Ex: 10" step="0.01" min="0" value="<?=$dados['limite_inferior_cota'] ?? 50 ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="maxCotaSemanal" class="form-label">Máximo de Cota Semanal</label>
                    <input type="number" class="form-control" id="maxCotaSemanal" name="max_cota_semanal"
                        placeholder="Ex: 500" step="0.01" min="0" value="<?=$dados['limite_superior_semanal_cota'] ?? 500 ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="minCotaSemanal" class="form-label">Mínimo de Cota Semanal</label>
                    <input type="number" class="form-control" id="minCotaSemanal" name="min_cota_semanal"
                        placeholder="Ex: 50" step="0.01" min="0" value="<?=$dados['limite_inferior_semanal_cota'] ?? 250 ?>" required>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <a href="index.php" class="btn btn-secondary me-md-2">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Salvar Configurações
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>