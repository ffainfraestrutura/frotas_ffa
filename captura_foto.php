<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$matricula = $_GET['matricula'] ?? ''; // Obtém a matrícula via GET
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png"/>
    <title>Captura de Foto e Aceite - FFA</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">
    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <div class="container-fluid px-4">
            <div class="card mb-4 mt-4 shadow-lg border-0 rounded-lg">
                <div class="card-body">
                    <h3>Captura de Foto e Aceite</h3>
                    <p>Por favor, tire uma foto para confirmar o aceite do documento.</p>

                    <form id="formulario" method="post" action="./control/memorando.php" enctype="multipart/form-data" class="text-center">
                        <!-- Campo para captura de foto direto da câmera -->
                        <label for="arquivo" class="btn btn-primary mt-3 mb-2">
                            <i class="fas fa-camera"></i> Tirar Foto
                        </label>
                        <input type="file" id="arquivo" name="arquivo" accept="image/*" capture="environment" style="display: none;" required onchange="previewFoto(event)">
                        
                        <!-- Exibe a foto capturada -->
                        <img id="foto" style="display: none; width: 100%; max-width: 400px; margin: auto;" alt="Foto Capturada">

                        <!-- Campo oculto para enviar a matrícula -->
                        <input type="hidden" name="matricula" value="<?php echo htmlspecialchars($matricula); ?>">

                        <!-- Botão para confirmar o aceite após a captura da foto -->
                        <button type="submit" class="btn btn-success mt-3 mb-3" id="botaoConfirmar" style="display: none;">
                            Confirmar Aceite
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Função para visualizar a foto capturada antes de enviar
        function previewFoto(event) {
            const foto = document.getElementById('foto');
            const botaoConfirmar = document.getElementById('botaoConfirmar');
            foto.src = URL.createObjectURL(event.target.files[0]);
            foto.style.display = 'block'; // Mostra a prévia da foto
            botaoConfirmar.style.display = 'block'; // Exibe o botão de confirmação
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
</body>
</html>
