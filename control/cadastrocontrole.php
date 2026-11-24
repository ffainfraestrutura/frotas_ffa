<?php
include("../conecta.php");
$senha     =  addslashes($_POST['pass']);
session_start();
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;


if ($matricula == '' or $senha == '') {
    echo "<script>alert('Erro na criação');</script>";
  echo "<script>window.location=\"../\"</script>";
} else {

  $sql = "SELECT matriculasup FROM bdfrota.tbtecnico where matricula = '$matricula'";
  $resultado = mysql_query($sql) or die (mysql_error());
  $row = mysql_fetch_array($resultado);
  $matriculasup = $row[0];

  $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matriculasup'";
  $resultado = mysql_query($sql) or die (mysql_error());
  $row = mysql_fetch_array($resultado);
  $idtbsupervisor = $row[0];

  $sql = "SELECT nome, codempresa, codfilial FROM bdaniel.tbfuncionario where matricula = '$matricula'";
  $resultado = mysql_query($sql) or die (mysql_error());
  $row = mysql_fetch_array($resultado);
  $nome = $row[0];
  $codempresa = $row[1];
  $codfilial = $row[2];

  if ($codempresa == '2') {
    $filial = '2';
  } elseif ($codfilial == '11' or $codfilial == '10') {
    $filial = '3';
  } else {
    $filial = '1';
  }

  $perfil = 0;


  $sql = "SELECT * FROM bdfrota.tbsupervisor where matricula = '$matricula'";
  $resultado = mysql_query($sql) or die (mysql_error());
  if (mysql_num_rows($resultado)>0)
      {
        $perfil = "1";
      }


  $sql = "SELECT * FROM bdfrota.tbcoord where matricula = '$matricula'";
  $resultado = mysql_query($sql) or die (mysql_error());
  if (mysql_num_rows($resultado)>0)
      {
        $perfil = "2";
      }

  $sql = "INSERT INTO bdfrota.tbusuario (matricula, usuario, senha, idtbsupervisor, nome, perfil, pacesso, filial) VALUES ('".$matricula."','".$matricula."','".$senha."','".$idtbsupervisor."','".$nome."','".$perfil."', '1', '".$filial."')";
  $resultado = mysql_query($sql) or die (mysql_error());

    echo "<script>alert('Usuário criado com sucesso');</script>";
  echo "<script>window.location=\"../\"</script>";
}


?>
