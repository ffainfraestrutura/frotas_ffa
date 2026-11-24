<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


require_once '../conecta.php';

$sql_combustivel = "SELECT 
                        *
                    FROM
                        bdcombustivel.tbconfiguracoes_cargo;";
$limites = $pdo->query($sql_combustivel)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Cota Indevida</title>
    <link rel="icon" type="image/png" href="/ffaead/src/images/logoredonda.png" /> <!-- Adjust path if needed -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .status-badge {
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <?php include '../menu.php'; ?>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-journal-bookmark"></i>Gerenciar Limite de Cota Indevida</h2>
        </div>

        <?php if (isset($_SESSION['sucesso'])): ?>
            <div class="alert alert-success"><?= $_SESSION['sucesso'] ?></div>
            <?php unset($_SESSION['sucesso']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>
            <div class='alert alert-danger'><?= $_SESSION['erro'] ?></div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>


        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabelacondutores">
                        <thead>
                            <tr>
                                <th>Cargo</th>
                                <th>Limite de Cota Indevida</th>
                                <th>Porcentagem de Repasse</th>
                                <th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($limites as $limite): ?>
                                <tr ondblclick="editar_info('editar.php?id=<?= $limite['idtbconfiguracoes_cargo'] ?>')">
                                    <th><?= $limite['cargo'] ?></th>
                                    <th><?= $limite['limite_cota_indevida'] ?></th>
                                    <th><?= $limite['percentagem_repasse'] * 100 . '%' ?></th>
                                    <th>
                                        <div class="btn-group">
                                            <a href="editar.php?id=<?= $limite['idtbconfiguracoes_cargo'] ?>"
                                                class="btn btn-sm btn-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </th>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tabelacondutores').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                }
                order: [[1, 'desc']]
            });
        });
    </script>
    <script>
        function editar_info(url) {
            window.location.href = url;
        }
    </script>
</body>

</html>