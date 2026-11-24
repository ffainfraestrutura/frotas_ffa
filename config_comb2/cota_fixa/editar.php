<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../conecta.php';

if (!isset($_GET['matricula'])) {
    $_SESSION['erro'] = "Matricula não informada";
    header('Location: index.php');
    exit;
}

$matricula = $_GET['matricula'];
$nome = $_GET['nome'];
$placa = $_GET['placa'];

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
    $valor = filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_STRING);
    $placa = filter_input(INPUT_POST, 'min_cota_diaria', FILTER_SANITIZE_STRING);
    $nome = filter_input(INPUT_POST, 'max_cota_semanal', FILTER_VALIDATE_INT);
    $matricula = filter_input(INPUT_POST, 'matricula', FILTER_VALIDATE_INT);

    try {
        $stmt = $pdo->prepare("SELECT * from bdcombustivel.tbcotafixa where matricula = ?");
        $stmt->execute([$_GET['matricula']]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            $stmt = $pdo->prepare("INSERT INTO bdcombustivel.tbcotafixa (matricula, valor, placa, nome) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_GET['matricula'], $valor, $placa, $nome]);

            $_SESSION['sucesso'] = "Informações do condutor atualizadas com sucesso!";
            header('Location: index.php');
            exit;
        } else {
            $stmt = $pdo->prepare("UPDATE bdcombustivel.tbcotafixa SET valor = ?, placa = ?, nome = ? WHERE matricula = ?");
            $stmt->execute([$valor, $placa, $nome, $_GET['matricula']]);

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
    <?php include '../menu.php'; ?>

    <div class="container mt-4">
        <h2><i class="bi bi-journal-text"></i> Editar Cota Fixa</h2>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="matricula" class="form-label">Matrícula</label>
                <input type="text" class="form-control" id="matricula" name="matricula" placeholder="Digite a matrícula"
                    value="<?= $matricula ?>" disabled>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="maxCotaDiaria" class="form-label">Máximo de Cota Diária</label>
                    <input type="number" class="form-control" id="maxCotaDiaria" name="valor" placeholder="Ex: 100"
                        step="0.01" min="0" value="<?= $dados['valor'] ?? 100 ?>" required>
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