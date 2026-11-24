<?php

require("phpmailer/class.phpmailer.php");

$mailer = new PHPMailer();
$mailer->IsSMTP();
$mailer->SMTPDebug = 1;
$mailer->Port = 587; //Indica a porta de conexгo para a saнda de e-mails
$mailer->Host = 'smtp.ffainfraestrutura.com.br';
$mailer->SMTPAuth = true; //define se haverб ou nгo autenticaзгo no SMTP
$mailer->Username = 'informatica@ffainfraestrutura.com.br'; //Informe o e-mai o completo
$mailer->Password = 'Inf424218!'; //Senha da caixa postal
$mailer->FromName = 'Lucas'; //Nome que serб exibido para o destinatбrio
$mailer->From = 'informatica@ffainfraestrutura.com.br'; //Obrigatуrio ser a mesma caixa postal indicada em "username"
$mailer->AddAddress('lucasabmoreira@gmail.com','Lucas Moreira'); //Destinatбrios
$mailer->Subject = 'Teste enviado atravйs do PHP Mailer';
$mailer->Body = 'Este й um teste realizado com o PHP Mailer';
if(!$mailer->Send())
{
echo "Erro no Envio!";
echo "Mailer Error: " . $mailer->ErrorInfo; exit; }
print "E-mail enviado!"
?>