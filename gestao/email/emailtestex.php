<?php


// Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
require("phpmailer/class.phpmailer.php");

$htmlmailcorpo = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//PT>
<html xmlns='https://www.mailee.me/pt'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
</head>
<body>

<p>Mariene, segue o Recibo de multa do Lucas de matricula: 002811 para Desconto</p>
<br>
<br>
<p> Email enviado automáticamente pelo Sistema de Multas FFA </p>
</body>
</html>";

// Inicia a classe PHPMailer
$mail = new PHPMailer();

// Define os dados do servidor e tipo de conexão
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->IsSMTP(); // Define que a mensagem será SMTP
$mail->Port = 587;
$mail->Host = "smtp.ffainfraestrutura.com.br"; // Endereço do servidor SMTP
$mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
$mail->Username = 'informatica@ffainfraestrutura.com.br'; // Usuário do servidor SMTP
$mail->Password = 'Inf424218!'; // Senha do servidor SMTP

// Define o remetente
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->From = "informatica@ffainfraestrutura.com.br"; // Seu e-mail
$mail->FromName = "Sistema de Multas"; // Seu nome

// Define os destinatário(s)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->AddAddress('lucasabmoreira@gmail.com', 'Lucas');
//$mail->AddAddress($email_vendedor,  addslashes($_REQUEST['nome_vendedor']));
//$mail->AddCC('lucasducaster@gmail.com', 'Ciclano'); // Copia
//$mail->AddBCC('fulano@dominio.com.br', 'Fulano da Silva'); // Cópia Oculta

// Define os dados técnicos da Mensagem
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->IsHTML(true); // Define que o e-mail será enviado como HTML
$mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)

// Define a mensagem (Texto e Assunto)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$assunto = "Multa para Desconto"; 
$mail->Subject  = $assunto; // Assunto da mensagem

$mail->Body = $htmlmailcorpo;


//$mail->AltBody = 'Este é o corpo da mensagem de teste, em Texto Plano! \r\n <img src="http://blog.thiagobelem.net/wp-includes/images/smilies/icon_smile.gif" alt=":)" class="wp-smiley"> ';

// Define os anexos (opcional)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
$mail->AddAttachment("../docs/$nome_final", "$nome_final");  // Insere um anexo

// Envia o e-mail
$enviado = $mail->Send();

// Limpa os destinatários e os anexos
$mail->ClearAllRecipients();
$mail->ClearAttachments();

// Exibe uma mensagem de resultado
if ($enviado) {
echo "<script>alert('E-mail enviado sucesso !!!');</script>";
echo "<script>window.location=\"google.com\"</script>";

} else {
echo "Não foi possível enviar o e-mail.<br /><br />";
echo "<b>Informações do erro:</b> <br />" . $mail->ErrorInfo;
}

?>