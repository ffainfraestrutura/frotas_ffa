<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];


if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>
        alert('Você deve logar para ter acesso');
        window.location=\"../index.php\";
    </script>";
} elseif ($matricula != '601000') {
    echo "<script language='javascript' type='text/javascript'>
        alert('Tela em Desenvolvimento');
        window.location=\"../index.php\";
    </script>";
}


// if ($perfil == null) {
//     echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
// } elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '6') {
//     echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
// }
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Portal FFA - Cadastro</title>
    <link href="../src/css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar .nav-item .nav-link {
            padding: 0.75rem 1rem;
        }

        .form-section {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .checkbox-list {
            list-style-type: none;
            padding-left: 0;
        }

        .checkbox-list li {
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <?php include "./menu-teste.php"; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Cadastro de Tipo de solictação de Chamados</h1>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="form-section shadow-sm mb-4">
                            <h2 class="h4 mb-4 text-primary">Novo Cadastro</h2>
                            <form>
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Digite o nome do novo tipo de chamado</label>
                                    <input type="text" class="form-control" id="nome" placeholder="Nome completo" required>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Cadastrar</button>
                                </div>
                            </form>
                        </div>

                        <!-- Listagem de Registros -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">Tipos Cadastrados</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="30%">Descrição</th>
                                                <th width="25%">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>                                
                                        </tbody>
                                    </table>
                                </div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center mt-3">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">Próxima</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura <?php echo date('Y'); ?></div>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>