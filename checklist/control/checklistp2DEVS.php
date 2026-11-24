<?php
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

// Aumentar timeout
set_time_limit(30);
ini_set('default_socket_timeout', 10);

$nome1 = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula1 = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
    exit;
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula1;
$_SESSION['nome'] = $nome1;
$_SESSION['usuario'] = $usuariof;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../checklistp1.php');
    exit;
}

echo "<script language='javascript' type='text/javascript'> alert('Iniciando includes...'); </script>";

// Verificar se os arquivos existem
if (!file_exists('../../func/log.php')) {
    echo "<script language='javascript' type='text/javascript'> alert('ERRO: Arquivo ../../func/log.php não encontrado'); </script>";
    exit;
}

if (!file_exists('../../conecta.php')) {
    echo "<script language='javascript' type='text/javascript'> alert('ERRO: Arquivo ../../conecta.php não encontrado'); </script>";
    exit;
}

if (!file_exists('../../gestao/email/phpmailer/src/PHPMailer.php')) {
    echo "<script language='javascript' type='text/javascript'> alert('ERRO: Arquivo ../../gestao/email/phpmailer/src/PHPMailer.php não encontrado'); </script>";
    exit;
}

echo "<script language='javascript' type='text/javascript'> alert('Arquivos encontrados, carregando...'); </script>";

include_once '../../func/log.php';
echo "<script language='javascript' type='text/javascript'> alert('log.php carregado'); </script>";

include_once '../../conecta.php';
echo "<script language='javascript' type='text/javascript'> alert('conecta.php carregado'); </script>";

require_once '../../gestao/email/phpmailer/src/Exception.php';
require_once '../../gestao/email/phpmailer/src/PHPMailer.php';
require_once '../../gestao/email/phpmailer/src/SMTP.php';
echo "<script language='javascript' type='text/javascript'> alert('phpmailer (src) carregado'); </script>";

$hoje = date('Y-m-d H:i:s');
$placa = isset($_POST['placa']) ? trim($_POST['placa']) : '';
$matricula = isset($_POST['matriculac']) ? trim($_POST['matriculac']) : '';
$matrvistoriador = isset($_POST['matrvistoriador']) ? trim($_POST['matrvistoriador']) : '';
if ($matrvistoriador === '') {
    $matrvistoriador = isset($_POST['vistoriador']) ? trim($_POST['vistoriador']) : '';
}
$idtbvistoria = isset($_POST['idinserido']) ? (int) $_POST['idinserido'] : 0;

if ($idtbvistoria <= 0) {
    error_log("[checklistp2] ID de vistoria inválido para finalizar. placa={$placa}, matrvistoriador={$matrvistoriador}");
    echo "<script language='javascript' type='text/javascript'> window.alert('Não foi possível finalizar a vistoria.'); </script>";
    echo "<script language='javascript' type='text/javascript'> window.location=\"../checklistp1.php\"; </script>";
    exit;
}

echo "<script language='javascript' type='text/javascript'> alert('ETAPA 1: Iniciando processo\\nID: $idtbvistoria\\nPlaca: $placa'); </script>";

$sqlUpdate = "UPDATE bdfrota.tbvistoria SET statusreg = 1 WHERE idtbvistoria = '$idtbvistoria'";
mysqli_query($con, $sqlUpdate);
if (mysqli_error($con)) {
    error_log('[checklistp2] Erro ao atualizar statusreg da vistoria ' . $idtbvistoria . ': ' . mysqli_error($con));
    echo "<script language='javascript' type='text/javascript'> alert('ERRO: Falha ao atualizar banco de dados'); </script>";
    exit;
}

echo "<script language='javascript' type='text/javascript'> alert('ETAPA 2: Banco de dados atualizado com sucesso'); </script>";

$emailDestinatario = 'suportedesenvolvimento@ffainfraestrutura.com.br'; // Email fixo para testes
echo "<script language='javascript' type='text/javascript'> alert('ETAPA 3: Email definido\\n' + '$emailDestinatario'); </script>";

$emailValido = filter_var(trim($emailDestinatario), FILTER_VALIDATE_EMAIL);
echo "<script language='javascript' type='text/javascript'> alert('ETAPA 4: Validação de email\\nVálido: " . ($emailValido ? 'SIM' : 'NÃO') . "'); </script>";

if ($emailValido) {
    echo "<script language='javascript' type='text/javascript'> alert('ETAPA 5: Email válido, preparando dados'); </script>";
    
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $urlRelatorio = $host !== ''
        ? $scheme . '://' . $host . '/frotas/checklist/gerandorelatorio.php?id=' . $idtbvistoria
        : '../gerandorelatorio.php?id=' . $idtbvistoria;
    
    echo "<script language='javascript' type='text/javascript'> alert('ETAPA 6: URL do relatório pronta'); </script>";

    try {
        echo "<script language='javascript' type='text/javascript'> alert('ETAPA 7: Instanciando PHPMailer'); </script>";
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        echo "<script language='javascript' type='text/javascript'> alert('ETAPA 8: PHPMailer instanciado, configurando SMTP'); </script>";
        $mail->isSMTP();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->SMTPDebug = 0;
        $mail->Timeout = 5; // Timeout de 5 segundos
        $mail->SMTPKeepAlive = false;
        $mail->Port = 587;
        $mail->Host = 'smtp.ffainfraestrutura.com.br';
        $mail->SMTPAuth = true;
        $mail->Username = 'informatica@ffainfraestrutura.com.br';
        // Senha alinhada com os fluxos de e-mail atualmente funcionais no servidor05
        $mail->Password = 'PFBaeX1V32yk9Ttq';
        $mail->setFrom('informatica@ffainfraestrutura.com.br', 'Sistema de Checklist');
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        echo "<script language='javascript' type='text/javascript'> alert('ETAPA 9: SMTP configurado, adicionando destinatário'); </script>";
        $mail->addAddress($emailValido);
        
        echo "<script language='javascript' type='text/javascript'> alert('ETAPA 10: Destinatário adicionado, configurando mensagem'); </script>";
        $mail->Subject = 'Checklist finalizado - ' . strtoupper($placa);
        $mail->Body = "<p>A vistoria da placa <strong>" . htmlspecialchars(strtoupper($placa), ENT_QUOTES, 'UTF-8') . "</strong> foi finalizada.</p>"
            . "<p>Para visualizar o relatório, acesse: <a href='" . htmlspecialchars($urlRelatorio, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($urlRelatorio, ENT_QUOTES, 'UTF-8') . "</a></p>";
        
        echo "<script language='javascript' type='text/javascript'> alert('ETAPA 11: Mensagem OK, enviando com PHPMailer'); </script>";

        // Mantém o mesmo perfil de conexão dos scripts que já enviam com sucesso (porta 587 sem forçar SMTPSecure)
        $mail->Port = 587;
        $mail->WordWrap = 70;

        $enviado = $mail->send();
        if ($enviado) {
            echo "<script language='javascript' type='text/javascript'> alert('ETAPA 12: Email enviado com SUCESSO!'); </script>";
            error_log('[checklistp2] Email enviado com sucesso para: ' . $emailValido);
        } else {
            $erroMail = isset($mail->ErrorInfo) ? $mail->ErrorInfo : 'Erro SMTP não detalhado';
            echo "<script language='javascript' type='text/javascript'> alert('ETAPA 12: FALHA ao enviar\\n" . addslashes($erroMail) . "'); </script>";
            error_log('[checklistp2] Falha no envio de email para ' . $emailValido . ': ' . $erroMail);
        }

        echo "<script language='javascript' type='text/javascript'> alert('ETAPA 14: Limpando dados de email'); </script>";
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();
        
    } catch (Exception $e) {
        echo "<script language='javascript' type='text/javascript'> alert('ERRO EXCEPTION: " . addslashes($e->getMessage()) . "'); </script>";
        error_log('[checklistp2] Exceção: ' . $e->getMessage());
    }
} else {
    echo "<script language='javascript' type='text/javascript'> alert('ETAPA 3: Email inválido!\\n' + '$emailDestinatario'); </script>";
}

// Log em background (não bloqueia)
error_log('[checklistp2] Vistoria finalizada: ID=' . $idtbvistoria . ', Placa=' . $placa . ', Email=' . $emailDestinatario . ', Válido=' . ($emailValido ? 'SIM' : 'NÃO'));

echo "<script language='javascript' type='text/javascript'> alert('ETAPA 15: Processamento concluído!\\n\\nRedirecionando para relatório...'); </script>";

// Redirecionar imediatamente
if ($perfil == '4' || $perfil == '5' || $perfil == '6') {
    echo "<script language='javascript' type='text/javascript'> window.location=\"../verrelatorio.php?id=$idtbvistoria\"; </script>";
} elseif ($perfil == '0' || $perfil == '1' || $perfil == '2') {
    echo "<script language='javascript' type='text/javascript'> window.location=\"../gerandorelatorio.php?id=$idtbvistoria\"; </script>";
} else {
    echo "<script language='javascript' type='text/javascript'> window.location=\"../checklistp1.php\"; </script>";
}

exit;
?>