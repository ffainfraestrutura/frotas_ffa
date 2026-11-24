<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');

session_start();

$servidor="10.27.0.100";
$usuario="blvp";
$senha="qrdrps";
//$db="bdfrota";,'$db';

$conexao=mysqlI_connect($servidor,$usuario,$senha) or die(mysqli_error());

//include"../conecta.php";
//include_once"../func/log.php";

$usuariof = $_POST['login'];
$pass = $_POST['pass'];

//echo $hoje, $usuariof, $pass;

    $sql = "SELECT nome, matricula, perfil, pacesso FROM bdfrota.tbusuario WHERE usuario = '$usuariof' AND senha = '$pass';";
    $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
      if (mysqli_num_rows($resultado)<=0)
      {
        $sql = "SELECT matricula FROM bdfrota.tbusuario WHERE usuario = '$usuariof';";
        $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
        if (mysqli_num_rows($resultado)<=0)
        {
          $sql = "SELECT idtbfuncionario FROM bdaniel.tbfuncionario where matricula = '$usuariof';";
          $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
          if (mysqli_num_rows($resultado)>=1)
          {
            $_SESSION['matricula'] = $usuariof;
            echo"<script language='javascript' type='text/javascript'>alert('PRIMEIRO ACESSO: Criar usuário');window.location=\"../cadastro.php\"</script>";
            die();
          } else {
            echo"<script language='javascript' type='text/javascript'>alert('matricula não existe');window.location=\"../index.php\"</script>";
          } 
        } else {
          echo"<script language='javascript' type='text/javascript'>alert('Senha incorreta');window.location=\"../index.php\"</script>";
          die();
        }
      } else {

        $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
        $pacesso = $row['pacesso'];
        if ($pacesso == 0) {
          echo"<script language='javascript' type='text/javascript'>alert('PRIMEIRO ACESSO. É necessário alterar a sua senha para acesso ao sistema.');window.location=\"../altsenha.php\"</script>";
        }
        $nome = $row['nome'];
        $matricula = $row['matricula'];
        $perfil = $row['perfil'];

        $_SESSION['nome'] = $nome;
        $_SESSION['usuario'] = $usuariof;
        $_SESSION['matricula'] = $matricula;
        $_SESSION['perfil'] = $perfil;
        
        if ($perfil == '0') {
          //$datahora = $hoje;
          //$acao = 'logou';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboardinicial.php\"</script>";

        } elseif ($perfil == '1') {
          //$datahora = $hoje;
          //$acao = 'logou';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboardinicial.php\"</script>";
        } elseif ($perfil == '2') {
          //$datahora = $hoje;
          //$acao = 'logou';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboardinicial.php\"</script>";
        } elseif ($perfil == '3') {
          //$datahora = $hoje;
          //$acao = 'logou';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboardinicial.php\"</script>";
        } elseif ($perfil == '4') {
          //$datahora = $hoje;
          //$acao = 'logou';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboardinicial.php\"</script>";
        } elseif ($perfil == '5') {
          //$datahora = $hoje;
          //$acao = 'logou';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboardinicial.php\"</script>";
        } elseif ($perfil == '6') {
          //$datahora = $hoje;
          //$acao = 'logou';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboardinicial.php\"</script>";
        } else {
          //$datahora = $hoje;
          //$acao = 'erro login';
          //$flag = '0';
          //$logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>alert('Erro no perfil do usuário');window.location=\"../index.php\"</script>";
        }


      }
?>
