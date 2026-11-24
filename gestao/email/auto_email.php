<?php
// Inclui o arquivo class.phpmailer.php localizado na pasta phpmailer
require("phpmailer/class.phpmailer.php");
include_once '../model/class.conection.php';
require_once '../model/bean/ocorrencia.php';

			



class Email
{

public function EnviarEmail(Ocorrencia $objOcorrencia) {

	try {
            	$sql = "select Internet, Codinome from cliente where id_cliente = ".$objOcorrencia->getIdCliente()."";
            

$objConn = new Conexao();
    $conexao = $objConn->abreConexao();

                $result = mysql_query($sql) or die (mysql_error());

     $objConn->fechaConexao($conexao);

                $dados = mysql_fetch_array($result);
        	$email_cliente = addslashes($dados['Internet']);
                $nome_cliente = addslashes($dados['Codinome']);                
                

               
              
                //*****************************************************
                //         FIM DA PARTE QUE TRABALHA COM O CLIENTE
                //*****************************************************
                
                $sql = "select email, vendedor from vendedor where vendedor = '".$objOcorrencia->getVendedor()."'";
		 //echo $sql;
                $conexao = $objConn->abreConexao();
                $result = mysql_query($sql) or die (mysql_error());
                $objConn->fechaConexao($conexao);
                $dados = mysql_fetch_array($result);
                $email_vendedor = addslashes($dados['email']);
                $nome_vendedor  = addslashes($dados['vendedor']);
                
                //*****************************************************
                //         FIM DA PARTE QUE TRABALHA COM O VENDEDOR   *
                //*****************************************************

                $problema = $objOcorrencia->getProblema();
                
                 //*****************************************************
                //        PEGA O ID DO CHAMADO PARA ENVIAR POR EMAIl   *
                //******************************************************               
                $sql = "select max(idtbocorrencia) as id from tbocorrencia";
                $conexao = $objConn->abreConexao();
                $result = mysql_query($sql) or die (mysql_error());
                $objConn->fechaConexao($conexao);
                $dados = mysql_fetch_array($result);
                $idtbocorrencia = $dados['id'];

                $css = '<style type="text/css">
                       .bluephone {
                        font-family: "Times New Roman", Times, serif;
                        }
                        .bluephone p {
                        color: #039;
                        }
                        </style>
                        <body class="bluephone"><p>';

                $mail = new PHPMailer();

                $mail->IsSMTP(); // Define que a mensagem será SMTP
                $mail->Port = 587;
                $mail->Host = "smtp.ffainfraestrutura.com.br"; // Endereço do servidor SMTP
                $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
                $mail->Username = 'informatica@ffainfraestrutura.com.br'; // Usuário do servidor SMTP
                $mail->Password = 'Inf424218!'; // Senha do servidor SMTP

                $mail->From = "informatica@ffainfraestrutura.com.br"; // Seu e-mail
                $mail->FromName = "FFA"; // Seu nome

                $mail->AddAddress($email_cliente, $nome_cliente);
                //$mail->AddAddress($email_vendedor,  $nome_vendedor);
                $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
                //$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
                $assunto = "FFA CHAMADO ABERTO N° ".$idtbocorrencia.' !'; 
                $mail->Subject  = $assunto; // Assunto da mensagem

$msg = "<style type='text/css'>
strong {
	font-family: Arial;
	color: #006;
}
</style>

<div>
<table width='595' border='0' align='left' cellspacing='1'>
    <tr>
      <td width='194' bgcolor='#99CCFF'><img src='http://www.bluephone.com.br/CHAMADOS2/blue_call/public/img/logo.png' width='50' height='50' alt='LOGO' /></td>
      <td width='391' bgcolor='#99CCFF'> <p class='TEXT'>&nbsp; </p>
        <p align='center' class='TEXT'><strong>CHAMADO $msg_status N° $idtbocorrencia. !</strong></p>
      <p>&nbsp;</p></td>
    </tr> 
        <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066' ><b>Cliente:</b></font></td>
      <td><font face='Arial' color='#000066'>$nome_cliente</font></td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066' ><b>Problema:</b></font></td>
      <td><font face='Arial' color='#000066'>$problema</font></td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066'><strong>Solução:</strong></font></td>
      <td><font face='Arial' color='#000066'>$solucao</font></td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066'><strong>Resposta:</strong></font></td>
      <td><font face='Arial' color='#000066'>$resposta</font></td>
    </tr>
  </table>
</div>";




                $mail->Body = $msg;


                $mail->AltBody = 'Este é o corpo da mensagem de teste, em Texto Plano! \r\n <img src="http://blog.thiagobelem.net/wp-includes/images/smilies/icon_smile.gif" alt=":)" class="wp-smiley"> ';

                // Define os anexos (opcional)
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                //$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo

                // Envia o e-mail
                $enviado = $mail->Send();

                // Limpa os destinatários e os anexos
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();

                // Exibe uma mensagem de resultado
                if ($enviado) {
                return true;
                } 
                else 
                {
                echo "<script>alert('Não foi possível enviar o e-mail');</script>";
		return false;
                echo "Não foi possível enviar o e-mail.<br /><br />";
                echo "<b>Informações do erro:</b> <br />" . $mail->ErrorInfo;
                }

     } catch (Exception $e) {
			
        	if (MOSTRA_ERRO) 
		{ 
		echo $e->getMessage(); 
		} 
		echo "<script>alert('Ocorreram erros durante a execução. Erro: ".$e->getCode()."');</script>";
		exit; 
		}
        }
        
        
        
        //ss
        public function EnviarEmailAlterado(Ocorrencia $objOcorrencia) {

	try {
            	$sql = "select Internet, Codinome from cliente where id_cliente = ".$objOcorrencia->getIdCliente()."";
            

$objConn = new Conexao();
    $conexao = $objConn->abreConexao();

                $result = mysql_query($sql) or die (mysql_error());

     $objConn->fechaConexao($conexao);

                $dados = mysql_fetch_array($result);
        	$email_cliente = $dados['Internet'];
                $nome_cliente = $dados['Codinome'];                
                

               
              
                //*****************************************************
                //         FIM DA PARTE QUE TRABALHA COM O CLIENTE
                //*****************************************************
                
                $sql = "select email, vendedor from vendedor where vendedor = '".$objOcorrencia->getVendedor()."'";
		 //echo $sql;
                $conexao = $objConn->abreConexao();
                $result = mysql_query($sql) or die (mysql_error());
                $objConn->fechaConexao($conexao);
                $dados = mysql_fetch_array($result);
                $email_vendedor = $dados['email'];
                $nome_vendedor  = $dados['vendedor'];
                
                //*****************************************************
                //         FIM DA PARTE QUE TRABALHA COM O VENDEDOR   *
                //*****************************************************

                $problema = $objOcorrencia->getProblema();
                $solucao = $objOcorrencia->getSolucao();
                 $resposta = $objOcorrencia->getResposta();
       
                $idtbocorrencia =  $objOcorrencia->getIdtbocorrencia();

                $css = '<style type="text/css">
                       .bluephone {
                        font-family: "Times New Roman", Times, serif;
                        }
                        .bluephone p {
                        color: #039;
                        }
                        </style>
                        <body class="bluephone"><p>';

                $mail = new PHPMailer();

                $mail->IsSMTP(); // Define que a mensagem será SMTP
                $mail->Port = 465;
                $mail->Host = "smtps.uhserver.com"; // Endereço do servidor SMTP
                $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
                $mail->Username = 'informatica@ffainfraestrutura.com.br'; // Usuário do servidor SMTP
                $mail->Password = 'Inf424218!'; // Senha do servidor SMTP

                $mail->From = "informatica@ffainfraestrutura.com.br"; // Seu e-mail
                $mail->FromName = "FFA"; // Seu nome

                $mail->AddAddress($email_cliente, $nome_cliente);
                //$mail->AddAddress($email_vendedor,  $nome_vendedor);
                $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
                //$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
                $status = $objOcorrencia->getStatus();
                if ($status == 0)
                {
                   $msg_status = "ABERTO"; 
                    
                }
                elseif ($status == 1)
                {                    
                   $msg_status = "FECHADO";                     
                }
                elseif ($status == 2)
                {                    
                   $msg_status = "CANCELADO";                     
                }
                elseif ($status == 3)
                {                    
                   $msg_status = "ABERTO";                     
                }
                elseif ($status == 4)
                {                    
                   $msg_status = "VERIFICADO E FECHADO";                     
                }
                elseif ($status == 5)
                {                    
                   $msg_status = "PENDENTE";                     
                }
                elseif ($status == 6)
                {                    
                   $msg_status = "ADIADO";                     
                }
                
                	                                                
                                                                         
                
                
                $assunto = "FFA CHAMADO ".$msg_status." N° ".$idtbocorrencia.' !'; 
                $mail->Subject  = $assunto; // Assunto da mensagem

$msg = "<style type='text/css'>
strong {
	font-family: Arial;
	color: #006;
}
</style>

<div>
<table width='595' border='0' align='left' cellspacing='1'>
    <tr>
      <td width='194' bgcolor='#99CCFF'><img src='http://www.bluephone.com.br/CHAMADOS/blue_call/public/img/logo.png' width='194' height='33' alt='LOGO' /></td>
      <td width='391' bgcolor='#99CCFF'> <p class='TEXT'>&nbsp; </p>
        <p align='center' class='TEXT'><strong>CHAMADO $msg_status N° $idtbocorrencia. !</strong></p>
      <p>&nbsp;</p></td>
    </tr>
        <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066' ><b>Cliente:</b></font></td>
      <td><font face='Arial' color='#000066'>$nome_cliente</font></td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066' ><b>Problema:</b></font></td>
      <td><font face='Arial' color='#000066'>$problema</font></td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066'><strong>Solução:</strong></font></td>
      <td><font face='Arial' color='#000066'>$solucao</font></td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td bgcolor='#0099FF'><font face='Arial' color='#000066'><strong>Resposta:</strong></font></td>
      <td><font face='Arial' color='#000066'>$resposta</font></td>
    </tr>
  </table>
</div>";

                $mail->Body = $msg;
                $mail->AltBody = 'Este é o corpo da mensagem de teste, em Texto Plano! \r\n <img src="http://blog.thiagobelem.net/wp-includes/images/smilies/icon_smile.gif" alt=":)" class="wp-smiley"> ';

                // Define os anexos (opcional)
                // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
                //$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo

                // Envia o e-mail
                $enviado = $mail->Send();

                // Limpa os destinatários e os anexos
                $mail->ClearAllRecipients();
                $mail->ClearAttachments();

                // Exibe uma mensagem de resultado
                if ($enviado) {
                return true;
                } 
                else 
                {
                echo "<script>alert('Não foi possível enviar o e-mail');</script>";
		return false;
                echo "Não foi possível enviar o e-mail.<br /><br />";
                echo "<b>Informações do erro:</b> <br />" . $mail->ErrorInfo;
                }

     } catch (Exception $e) {
			
        	if (MOSTRA_ERRO) 
		{ 
		echo $e->getMessage(); 
		} 
		echo "<script>alert('Ocorreram erros durante a execução. Erro: ".$e->getCode()."');</script>";
		exit; 
		}
        }

}//end class
?>