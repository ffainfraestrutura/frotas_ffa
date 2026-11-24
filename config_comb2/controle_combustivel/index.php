<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../conecta.php';

$sql_combustivel = "SELECT 
                        c.nome,
                        c.matricula AS matricula_condutor,
                        f.ccusto,
                        cf.*,
                        v.categoria
                    FROM
                        tbcondutor c
                            LEFT JOIN
                        bdcombustivel.tbconfiguracoes_funcionarios cf ON c.matricula = cf.matricula
                            JOIN
                        bdaniel.tbfuncionario f ON f.matricula = c.matricula
                            AND f.status != 'demitido'
                            JOIN
                        tbveiculo v ON v.placa = c.placaassoc
                    WHERE
                        c.ativo = 1
                            AND c.matricula NOT IN (999999)
                            AND c.nome NOT IN ('' , 'ABC')
                    GROUP BY c.nome
                    ORDER BY nome";
$condutores = $pdo->query($sql_combustivel)->fetchAll(PDO::FETCH_ASSOC);

$sql_tipo_veiculo = "SELECT DISTINCT
                            (categoria)
                        FROM
                            tbveiculo
                        WHERE
                            categoria != ''";
$tipo_veiculo = $pdo->query($sql_tipo_veiculo)->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $max_cota_diaria = filter_input(INPUT_POST, 'max_cota_diaria', FILTER_SANITIZE_STRING);
    $min_cota_diaria = filter_input(INPUT_POST, 'min_cota_diaria', FILTER_SANITIZE_STRING);
    $max_cota_semanal = filter_input(INPUT_POST, 'max_cota_semanal', FILTER_VALIDATE_INT);
    $min_cota_semanal = filter_input(INPUT_POST, 'min_cota_semanal', FILTER_VALIDATE_INT);
    $tipo_veiculo = filter_input(INPUT_POST, 'tipoVeiculo', FILTER_SANITIZE_STRING);

    try {
        $stmt = $pdo->prepare("UPDATE bdcombustivel.tbconfiguracoes_funcionarios cf
                                                JOIN
                                            tbcondutor c ON c.matricula = cf.matricula
                                                JOIN
                                            tbveiculo v ON v.placa = c.placaassoc 
                                        SET 
                                            limite_superior_cota = ?,
                                            limite_inferior_cota = ?,
                                            limite_superior_semanal_cota = ?,
                                            limite_inferior_semanal_cota = ?
                                        WHERE
                                            v.categoria = ?");
        $stmt->execute([$max_cota_diaria, $min_cota_diaria, $max_cota_semanal, $min_cota_semanal, $tipo_veiculo]);

        $_SESSION['sucesso'] = "Informações do condutor atualizadas com sucesso!";
        header('Location: index.php');
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
    <title>Gerenciar Combustivel</title>
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
<?php include '../menu.php'; ?>

<body class="sb-nav-fixed">
    <div class="container mt-4 ">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-journal-bookmark"></i>Gerenciar Combustivel</h2>
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
                            <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Edição em Massa
                            </button>
                            <tr>
                                <th>Nome</th>
                                <th>Matricula</th>
                                <th>Centro de Custo</th>
                                <th>Minimo Diario</th>
                                <th>Maximo Diario</th>
                                <th>Minimo Semanal</th>
                                <th>Maximo Semanal</th>
                                <th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($condutores as $condutor): ?>
                                <tr ondblclick="editar_info('editar.php?matricula=<?= $condutor['matricula_condutor'] ?>')">
                                    <th><?= $condutor['nome'] ?></th>
                                    <th><?= $condutor['matricula_condutor'] ?></th>
                                    <th><?= $condutor['ccusto'] ?></th>
                                    <th><?= $condutor['limite_inferior_cota'] ?></th>
                                    <th><?= $condutor['limite_superior_cota'] ?></th>
                                    <th><?= $condutor['limite_inferior_semanal_cota'] ?></th>
                                    <th><?= $condutor['limite_superior_semanal_cota'] ?></th>
                                    <th>
                                        <div class="btn-group">
                                            <a href="editar.php?matricula=<?= $condutor['matricula_condutor'] ?>"
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edição em Massa por Tipo de Veículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <form method="POST">
                    <div class="modal-body">
                        <label for="tipoVeiculo">Tipo de Veículo:</label>
                        <select name="tipoVeiculo" id="tipoVeiculo" class="form-select">
                            <?php foreach ($tipo_veiculo as $veiculo): ?>
                                <option value="<?= $veiculo['categoria'] ?>"><?= $veiculo['categoria'] ?></option>
                            <?php endforeach; ?>
                        </select>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="maxCotaDiaria" class="form-label">Máximo de Cota Diária</label>
                                <input type="number" class="form-control" id="maxCotaDiaria" name="max_cota_diaria"
                                    placeholder="Ex: 100" step="0.01" min="0"
                                    value="<?= $dados['limite_superior_cota'] ?? 100 ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="minCotaDiaria" class="form-label">Mínimo de Cota Diária</label>
                                <input type="number" class="form-control" id="minCotaDiaria" name="min_cota_diaria"
                                    placeholder="Ex: 10" step="0.01" min="0"
                                    value="<?= $dados['limite_inferior_cota'] ?? 50 ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="maxCotaSemanal" class="form-label">Máximo de Cota Semanal</label>
                                <input type="number" class="form-control" id="maxCotaSemanal" name="max_cota_semanal"
                                    placeholder="Ex: 500" step="0.01" min="0"
                                    value="<?= $dados['limite_superior_semanal_cota'] ?? 500 ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="minCotaSemanal" class="form-label">Mínimo de Cota Semanal</label>
                                <input type="number" class="form-control" id="minCotaSemanal" name="min_cota_semanal"
                                    placeholder="Ex: 50" step="0.01" min="0"
                                    value="<?= $dados['limite_inferior_semanal_cota'] ?? 250 ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>

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
            });
        });
        function editar_info(url) {
            window.location.href = url;
        }
    </script>
</body>

</html>