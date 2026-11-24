<?php
//Load Composer's autoloader
require '../vendor/autoload.php';

// Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
//require("./src/PHPMailer.php");
//require("./src/SMTP.php");

require("./src/class.phpmailer.php");
require("./src/class.smtp.php");

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


$htmlmailcorpo = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//PT>
<html xmlns='https://www.mailee.me/pt'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
</head>
<body>

<p> Novo veículo cadastrado para vistoria. </p>
<br>
<br>
<p> Email enviado automaticamente pelo Sistema FFA. </p>
</body>
</html>"; 

// Cria uma instância; passar `true` permite exceções
$mail = new PHPMailer(true);


$mail->setLanguage('pt_br');

try {
    //Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    //$mail->Host = "smtp.ffainfraestrutura.com.br"; // Endereço do servidor SMTP
    $mail->Host = "smtp.ffainfraestrutura.com.br";
    $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
    $mail->Username = 'informatica@ffainfraestrutura.com.br'; // Usuário do servidor SMTP
    $mail->Password = 'Inf424218!'; // Senha do servidor SMTP Inf424218!
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    //$mail->Port       = 465;  
    // Se tiver SSL

    //Recipients
    $mail->setFrom('informatica@ffainfraestrutura.com.br', 'Informatica');
    $mail->addAddress('desenvolvimento@ffainfraestrutura.com.br', 'Leticia');
    //$mail->addAddress('informatica@ffainfraestrutura.com.br', 'Lucas');     //Add a recipient
    //$mail->addAddress('ellen@example.com');               //outro envio
    //$mail->addReplyTo('desenvolvimento@ffainfraestrutura.com.br', 'Leticia'); //reposta
    $mail->addCC('lethduarte@gmail.com'); //copia
    //$mail->addBCC('informatica@ffainfraestrutura.com.br'); //copia oculta

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)
    $mail->Subject = "Manutenção veicular";
    $mail->Body    = $htmlmailcorpo;
    $mail->AltBody = 'Erro no envio, entrar em contato com informatica@ffainfraestrutura.com.br';

    $mail->send();
    //echo "<script>alert('E-mail enviado sucesso!!!');</script>";
    
} catch (Exception $e) {
    echo "Não foi possível enviar o e-mail.<br /><br />";
    echo "<b>Informações do erro:</b> <br />" . $mail->ErrorInfo;
}




?>