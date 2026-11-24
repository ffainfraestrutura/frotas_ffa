<?php
function carregarClassePresencaLoginDev($nomeClasse, $caminhoArquivo)
{
    if (!is_file($caminhoArquivo)) {
        return false;
    }

    require_once $caminhoArquivo;

    return class_exists($nomeClasse, false);
}

function deveValidarLancamentoPresencaLoginDev(DateTimeInterface $dataReferencia = null)
{
    $dataReferencia = $dataReferencia ?: new DateTimeImmutable('now');
    $limiteDezHoras = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dataReferencia->format('Y-m-d') . ' 10:00:00');

    return $limiteDezHoras instanceof DateTimeInterface && $dataReferencia > $limiteDezHoras;
}

function limparTextoLogPresencaLoginDev($valor)
{
    return str_replace(array("'", "\r", "\n"), array('', ' ', ' '), (string) $valor);
}

function obterSituacaoPresencaLoginDev($registroPresenca)
{
    if (!is_array($registroPresenca) || !isset($registroPresenca['situacau']) || trim((string) $registroPresenca['situacau']) === '') {
        return 'Sem registro';
    }

    return trim((string) $registroPresenca['situacau']);
}

function registrarBloqueioPorPresencaLoginDev($matricula, $registroPresenca, $decisaoPresenca)
{
    $datahora = date('Y-m-d H:i:s');
    $acao = 'deslogado por presença não informada';
    $flag = '0';
    $situacao = limparTextoLogPresencaLoginDev(obterSituacaoPresencaLoginDev($registroPresenca));
    $mensagem = isset($decisaoPresenca['mensagem']) ? limparTextoLogPresencaLoginDev($decisaoPresenca['mensagem']) : '';

    enviarlog($datahora, $acao, $flag, $matricula, '', $situacao, $mensagem);
}

function encerrarSessaoPresencaLoginDev()
{
    $_SESSION = array();

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function bloquearLoginPorPresencaNaoLancadaDev($mensagem)
{
    $mensagem = (string) $mensagem;
    $mensagemAlert = strpos($mensagem, "\n") !== false ? $mensagem : str_replace('. ', ".\n", $mensagem);
    echo "<script>alert(" . json_encode($mensagemAlert) . "); window.location=\"../indexDEV.php\";</script>";
    exit();
}

function validarLancamentoPresencaTecnicoDev($matricula, $connBdPonto)
{
    if (!deveValidarLancamentoPresencaLoginDev()) {
        return;
    }

    $arquivoPresencaService = __DIR__ . '/../classes/PresencaService.php';
    $arquivoPresencaPolicy = __DIR__ . '/../classes/PresencaPolicy.php';

    $presencaServiceCarregada = carregarClassePresencaLoginDev('PresencaService', $arquivoPresencaService);
    $presencaPolicyCarregada = carregarClassePresencaLoginDev('PresencaPolicy', $arquivoPresencaPolicy);

    if (!$presencaServiceCarregada || !$presencaPolicyCarregada) {
        bloquearLoginPorPresencaNaoLancadaDev('Falha ao carregar classes de presença.');
    }

    $presencaService = new PresencaService($connBdPonto);
    $presencaPolicy = new PresencaPolicy();
    $registroPresenca = $presencaService->buscarUltimaPresencaDoDia($matricula);
    $decisaoPresenca = $presencaPolicy->avaliar($registroPresenca);

    if ($decisaoPresenca['permitido']) {
        return;
    }

    registrarBloqueioPorPresencaLoginDev($matricula, $registroPresenca, $decisaoPresenca);
    encerrarSessaoPresencaLoginDev();
    bloquearLoginPorPresencaNaoLancadaDev($decisaoPresenca['mensagem']);
}
?>
