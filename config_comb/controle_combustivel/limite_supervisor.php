<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../conecta.php';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Combustivel</title>
    <link rel="icon" type="image/png" href="/ffaead/src/images/logoredonda.png" /> <!-- Adjust path if needed -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .status-badge {
            font-size: 0.8rem;
        }
    </style>
</head>
<body> 
    <?php include'../../menu.php';?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-journal-bookmark"></i>Gerenciar Combustivel</h2>
            <a href="cadastrar.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Condutor
            </a>
        </div>

        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="alert alert-success"><?= $_SESSION['sucesso'] ?></div>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class='alert alert-danger'><?= $_SESSION['erro']?></div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>


        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Matricula</th>
                                <th>Nome</th>
                                <th>Centro de Custo</th>
                                <th>Minimo Diario</th>
                                <th>Maximo Diario</th>
                                <th>Minimo Semanal</th>
                                <th>Maximo Semanal</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>