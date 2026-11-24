<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

$data = date('Y-m-d');
include_once "./controleacesso.php";
include_once "../conecta.php";

header("Content-type: text/html; charset=utf-8");
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
    <title>Serviço Indisponível - Aprovação de Cota</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="../src/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">

    <!--nav-->
    <?php include_once "../menu.php"; ?>

    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2">
                    <i class="fa-solid fa-wifi" style="color: #ff9800; margin-right: 10px;"></i>
                    Conexão com a operadora do Cartão de Combustível Indisponível
                </h1>

                <div class="card p-4 mt-4">
                    <div class="alert alert-danger" role="alert" style="background-color: #ffebee; border-color: #d32f2f; margin-bottom: 0; text-align: center;">
                        <div class="d-flex align-items-center justify-content-center" style="flex-direction: column;">
                            <i class="fa-solid fa-circle-xmark" style="font-size: 2.5rem; margin-right: 0; margin-bottom: 15px; color: #d32f2f;"></i>
                            <div style="flex: 1; max-width: 500px;">
                                <p class="mb-2">
                                    O serviço de Aprovação de Cota Extra está temporariamente indisponível. 
                                    Isso pode ser um problema de conexão de sua internet ou a operadora do cartão de combustível pode estar indisponível.
                                </p>
                                <p class="mt-3 mb-0"><strong>Tente novamente em alguns instantes</strong></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4 mb-4">
                        <div class="col-md-4 offset-md-4">
                            <button type="button" class="btn btn-outline-secondary btn-lg w-100" onclick="voltarMenuPrincipal()" style="font-weight: 600; font-size: 16px;">
                                <i class="fa-solid fa-arrow-left"></i> Voltar
                            </button>
                        </div>
                    </div>

                    <div class="card mt-4" style="background-color: #e8f5e9; border-color: #4caf50; border-left: 4px solid #4caf50;">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #2e7d32; margin-bottom: 15px;">
                                <i class="fa-solid fa-lightbulb"></i> O que fazer agora?
                            </h5>
                            <ol style="color: #1b5e20; line-height: 2; margin-bottom: 0;">
                                <li><strong>Tentar Novamente em alguns minutos</strong> - A operadora pode estar recuperando o serviço</li>
                                <li><strong>Verifique sua conexão com a internet</strong> - Certifique-se que está conectado</li>
                                <li><strong>Se o erro persistir</strong> - A operadora pode estar indisponível.</li>
                                <li><strong>Aguarde 5-10 minutos</strong> e tente novamente</li>
                                <li><strong>Se erro persistir</strong> - Entre em contato com o plantão da Frota.</li>
                            </ol>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding: 20px; background-color: #f5f5f5; border-radius: 5px; text-align: center; border-left: 4px solid #999;">
                        <div style="color: #333; margin-bottom: 10px;">
                            <strong>📱 Informações da Conexão</strong>
                        </div>
                        <small style="color: #666; line-height: 1.8;">
                            <strong>Última tentativa:</strong> <span id="ultimaTentativa" style="color: #d32f2f;"></span><br>
                            <strong>Status:</strong> Aguardando reconexão com a operadora<br>
                            <strong>Suporte:</strong> Se persistir, reporte à equipe de TI
                        </small>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="../src/js/scripts.js"></script>

    <script>
        /**
         * Carrega os erros da sessão ou do localStorage
         * NOTA: A div de erros foi removida da interface
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Exibe a hora da última tentativa
            const agora = new Date();
            const hora = agora.toLocaleTimeString('pt-BR');
            document.getElementById('ultimaTentativa').textContent = hora;

            // Limpa o localStorage (se houver erros salvos)
            const errosJSON = localStorage.getItem('api_errors');
            if (errosJSON) {
                localStorage.removeItem('api_errors');
            }
        });

        /**
         * Volta ao menu principal
         */
        function voltarMenuPrincipal() {
            window.location.href = 'https://ffa.painel-telecom.com:4242/frotas/gerenciar-equipe-coord.php';
        }
    </script>

</body>

</html>
