<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
include("../conecta.php");
include_once"../func/log.php";
$usuariof = $_POST['login'];
$pass = $_POST['pass'];
    $sql = "SELECT nome, matricula, perfil, pacesso, tipo, filial FROM bdfrota.tbusuario WHERE usuario = '$usuariof' AND senha = '$pass'";
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
            echo"<script language='javascript' type='text/javascript'>alert('matricula não existe');window.location=\"../index.php\"</script>";
          } 
        } else
        {
          echo"<script language='javascript' type='text/javascript'>alert('Senha incorreta');window.location=\"../index.php\"</script>";
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
        $filial = $row['filial'];
        $sql = "SELECT ccusto FROM bdaniel.tbfuncionario WHERE matricula = '$matricula'";
        $resultado = mysql_query($sql) or die (mysql_error());
        $row = mysql_fetch_array($resultado);
        $ccusto = $row['ccusto'];
        if (stripos($ccusto , "claro") !== false) {
          $ccusto = 'claro';
        } 

        if (stripos($ccusto , "tim") !== false) {
            $ccusto = 'tim';
        }

        if (stripos($ccusto , "ligga") !== false) {
           $ccusto = 'ligga';
        }

        $_SESSION['filial'] = $filial;
        $_SESSION['nome'] = $nome;
        $_SESSION['usuario'] = $usuariof;
        $_SESSION['matricula'] = $matricula;
        $_SESSION['perfil'] = $perfil;
        $_SESSION['tipo']=$tipo;
        $_SESSION['ccusto']=$ccusto;
        
        if ($perfil == '0') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../tecnico.php\"</script>";

        } elseif ($perfil == '1') {
                    $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../aprovacaocota.php\"</script>";
        } elseif ($perfil == '2') {
                    $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../coordenador.php\"</script>";
        } elseif ($perfil == '3') {
                    $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboard.php\"</script>";
        } elseif ($perfil == '4') {
                    $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboard.php\"</script>";
        } elseif ($perfil == '5') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');

          if($matricula=='002625'){
            echo"<script language='javascript' type='text/javascript'>window.location=\"../gestao/relinventario.php\"</script>";
          }else{
            echo"<script language='javascript' type='text/javascript'>window.location=\"../dashboard.php\"</script>";
          }
          
        } elseif ($perfil == '6') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');

          if ($ccusto == 'tim') {
            echo"<script language='javascript' type='text/javascript'>window.location=\"../atividadecomplementartim.php\"</script>";
          } elseif ($ccusto == 'claro') {
            
            echo"<script language='javascript' type='text/javascript'>window.location=\"../atividadecomplementarclaro.php\"</script>";
          } else {
            echo"<script language='javascript' type='text/javascript'>window.location=\"../atividadecomplementar.php\"</script>";
          }

          
        }elseif ($perfil == '7') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../gestao/relinventario.php\"</script>";
        }elseif ($perfil == '8') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          echo"<script language='javascript' type='text/javascript'>window.location=\"../checklist/checklistinicio.php\"</script>";
        } elseif ($perfil == '10') {
          $datahora = $hoje;
          $acao = 'logou';
          $flag = '0';
          $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
          //echo"<script language='javascript' type='text/javascript'>window.location=\"../atividadecomplementar-teste.php\"</script>";
          echo"<script language='javascript' type='text/javascript'>window.location=\"../importoperadora.php\"</script>";
        }  else {
                  $datahora = $hoje;
        $acao = 'erro login';
        $flag = '0';
        $logresult = enviarlog($datahora, $acao, $flag, $matricula, '', '', '');
        echo"<script language='javascript' type='text/javascript'>alert('Erro no perfil do usuário');window.location=\"../index.php\"</script>";
        }


        }
?>
