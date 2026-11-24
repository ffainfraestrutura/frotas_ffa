<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '3') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

include "../conecta.php";
include_once "./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA - Teste de Conexão com API" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Verificando Conexão - Aprovação de Cota</title>
    <link href="../src/css/styles.css" rel="stylesheet" />
</head>

<body class="sb-nav-fixed vsc-initialized">

    <!--nav-->
    <?php include_once "../menu.php"; ?>

    <div id="layoutSidenav_content">

        <main style="width: 100%;">

            <div class="container-fluid px-4" style="width: 100%;">

                <h1 class="h1 pt-2 pb-2">Aprovação de Cota</h1>
                <p class="text-muted">Verificando conexão com a API de saldo... por favor, aguarde.</p>

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
        const API_TESTE_URL = '../func/saldocartao.php?test=1';
        const DESTINO_APROVACAO = './control/aprovarcotaescalonada.php';
        const DESTINO_ERRO = './control/operadora_indisponivel.php';
        const TIMEOUT_MS = 5000;

        document.addEventListener('DOMContentLoaded', async function() {
            const resultado = await testarConexaoSaldo();

            if (resultado.sucesso) {
                window.location.href = DESTINO_APROVACAO;
                return;
            }

            localStorage.setItem('api_errors', JSON.stringify([{
                operadora: 'Cartão',
                mensagem: resultado.mensagem
            }]));
            window.location.href = DESTINO_ERRO;
        });

        async function testarConexaoSaldo() {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), TIMEOUT_MS);

            try {
                const resposta = await fetch(API_TESTE_URL, {
                    method: 'GET',
                    signal: controller.signal
                });

                if (!resposta.ok) {
                    return {
                        sucesso: false,
                        mensagem: `API retornou erro: ${resposta.status}`
                    };
                }

                return {
                    sucesso: true,
                    mensagem: 'Conectado com sucesso'
                };
            } catch (error) {
                if (error.name === 'AbortError') {
                    return {
                        sucesso: false,
                        mensagem: 'Tempo limite excedido ao conectar com a API'
                    };
                }

                return {
                    sucesso: false,
                    mensagem: error.message || 'Erro ao conectar com a API'
                };
            } finally {
                clearTimeout(timeoutId);
            }
        }

    </script>

</body>

</html>