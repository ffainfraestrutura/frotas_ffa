<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../conecta.php';

if (!isset($_GET['id'])) {
    $_SESSION['erro'] = "Cargo não informada";
    header('Location: index.php');
    exit;
}

$id = $_GET['id'];

// Busca curso
$stmt = $pdo->prepare("SELECT * FROM bdcombustivel.tbconfiguracoes_cargo WHERE idtbconfiguracoes_cargo = ? LIMIT 1");
$stmt->execute([$id]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
    $_SESSION['erro'] = "Informações do cargo não encontrados!";
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $limite_cota_indevida = filter_input(INPUT_POST, 'limite_cota_indevida', FILTER_SANITIZE_STRING);
    $percentagem_repasse = filter_input(INPUT_POST, 'percentagem_repasse', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'idtbconfiguracoes_cargo', FILTER_SANITIZE_STRING);

    try {
        $stmt = $pdo->prepare("UPDATE bdcombustivel.tbconfiguracoes_cargo SET limite_cota_indevida = ?, percentagem_repasse = ? WHERE idtbconfiguracoes_cargo = ?");
        $stmt->execute([$limite_cota_indevida, $percentagem_repasse / 100, $_GET['id']]);

        $_SESSION['sucesso'] = "Informações do cargo atualizadas com sucesso!";
        header('Location: index.php');
        exit;

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
    <title>Portal FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<body>
    <?php include '../menu.php'; ?>

    <div class="container mt-4">
        <h2><i class="bi bi-journal-text"></i> Editar Limite de Cota Indevida</h2>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="matricula" class="form-label">Cargo</label>
                <input type="text" class="form-control" id="Cargo" name="cargo" placeholder=""
                    value="<?= $dados['cargo'] ?>" disabled>
                <input type="text" class="form-control" id="Cargo" name="idtbconfiguracoes_cargo" placeholder=""
                    value="<?= $dados['idtbconfiguracoes_cargo'] ?>" hidden disabled>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="maxCotaDiaria" class="form-label">Limite de Cota Indevida</label>
                    <input type="number" class="form-control" id="maxCotaDiaria" name="limite_cota_indevida"
                        placeholder="Ex: 100" step="0.01" min="0" value="<?= $dados['limite_cota_indevida'] ?? 100 ?>"
                        required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="minCotaDiaria" class="form-label">Porcentagem de Repasse</label>
                    <input type="number" class="form-control" id="minCotaDiaria" name="percentagem_repasse"
                        placeholder="Ex: 10" step="0.01" min="0"
                        value="<?= $dados['percentagem_repasse'] * 100 ?? 10 ?>" required>
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