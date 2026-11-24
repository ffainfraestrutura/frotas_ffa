<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
include("../conecta.php");
include_once"../func/log.php";
$usuariof = $_POST['login'];
$pass = $_POST['pass'];
    $sql = "SELECT nome, matricula, perfil, pacesso, tipo FROM bdfrota.tbusuario WHERE usuario = '$usuariof' AND senha = '$pass'";
    $resultado = mysql_query($sql) or die (mysql_error());
      if (mysql_num_rows($resultado)<=0)
      {
        $sql = "SELECT matricula FROM bdfrota.tbusuario WHERE usuario = '$usuariof'";
        $resultado = mysql_query($sql) or die (mysql_error());
        if (mysql_num_rows($resultado)<=0)
        {
          $sql = "SELECT idtbfuncionario FROM bdaniel.tbfuncionario where matricula = '$usuariof'";
          $resultado = mysql_query($sql) or die (mysql_error());
          if (mysql_num_rows($resultado)>=1)
          {
            $_SESSION['matricula'] = $usuariof;
            echo"<script language='javascript' type='text/javascript'>alert('PRIMEIRO ACESSO: Criar usuário');window.location=\"../cadastro.php\"</script>";
            die();
          } else 
          {
            echo"<script language='javascript' type='text/javascript'>alert('matricula não existe');window.location=\"../index-teste.php\"</script>";
          } 
        } else
        {
          echo"<script language='javascript' type='text/javascript'>alert('Senha incorreta');window.location=\"../index-teste.php\"</script>";
          die();
        }
      } else {

        $row = mysql_fetch_array($resultado);
        $pacesso = $row['pacesso'];
        if ($pacesso == 0) {
          echo"<script language='javascript' type='text/javascript'>alert('PRIMEIRO ACESSO. É necessário alterar a sua senha para acesso ao sistema.');window.location=\"../altsenha.php\"</script>";
        }
        $nome = $row['nome'];
        $matricula = $row['matricula'];
        $perfil = $row['perfil'];
        $tipo = $row['tipo'];

        $_SESSION['nome'] = $nome;
        $_SESSION['usuario'] = $usuariof;
        $_SESSION['matricula'] = $matricula;
        $_SESSION['perfil'] = $perfil;
        $_SESSION['tipo']=$tipo;
        echo "$perfil <br>";
        if ($perfil == '0') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../tecnico-teste.php\"</script>";

        } elseif ($perfil == '1') {
                    $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../aprovacaocota-teste.php\"</script>";
        } elseif ($perfil == '2') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../coordenador-teste.php\"</script>";
        } elseif ($perfil == '3') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../telainicio2.php\"</script>";
        } elseif ($perfil == '4') {
                    $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../telainicio2.php\"</script>"; //tela precisa ser para celular
        } elseif ($perfil == '5') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../telainicio2.php\"</script>";
        } elseif ($perfil == '6') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          //echo"<script language='javascript' type='text/javascript'>window.location=\"../atividadecomplementar-teste.php\"</script>";
          echo"<script language='javascript' type='text/javascript'>window.location=\"../telainicio2.php\"</script>";
        }elseif ($perfil == '9') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          //echo"<script language='javascript' type='text/javascript'>window.location=\"../atividadecomplementar-teste.php\"</script>";
          echo"<script language='javascript' type='text/javascript'>window.location=\"../telainicio2.php\"</script>";
        } elseif ($perfil == '10') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          //echo"<script language='javascript' type='text/javascript'>window.location=\"../atividadecomplementar-teste.php\"</script>";
          echo"<script language='javascript' type='text/javascript'>window.location=\"../rotasredeexterna.php\"</script>";
        } else {
                  $datahora = $hoje;
        $acao = 'erro login';
        $flag = '0';
        $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
        echo"<script language='javascript' type='text/javascript'>alert('Erro no perfil do usuário');window.location=\"../index.php\"</script>";
        }


        }
?>
