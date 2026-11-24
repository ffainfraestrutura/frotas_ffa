<?php
//include("../conecta.php");

$senha     =  addslashes($_POST['pass']);

session_start();
$servidor="10.27.0.100";
$usuario="blvp";
$senhacon="qrdrps";
//$db="bdfrota";,'$db';

$conexao=mysqlI_connect($servidor,$usuario,$senhacon) or die(mysqli_error());

$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;

if ($matricula == '' or $senha == '') {
  echo "<script>alert('Erro na criação');</script>";
  echo "<script>window.location=\"../\"</script>";
} else {

  $sql = "SELECT matriculasup FROM bdfrota.tbtecnico where matricula = '$matricula'";
  $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
  $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
  $matriculasup = $row['matriculasup'];

  $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matriculasup'";
  $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
  $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
  $idtbsupervisor = $row['idtbsupervisor'];

  $sql = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$matricula'";
  $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
  $row = mysqli_fetch_array($resultado, MYSQLI_ASSOC);
  $nome = $row['nome'];

  $perfil = 0;


  $sql = "SELECT * FROM bdfrota.tbsupervisor where matricula = '$matricula'";
  $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
  if (mysqli_num_rows($resultado)>0)
      {
        $perfil = "1";
      }


  $sql = "SELECT * FROM bdfrota.tbcoord where matricula = '$matricula'";
  $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());
  if (mysqli_num_rows($resultado)>0)
      {
        $perfil = "2";
      }

  $sql = "INSERT INTO bdfrota.tbusuario (matricula, usuario, senha, idtbsupervisor, nome, perfil) VALUES ('".$matricula."','".$matricula."','".$senha."','".$idtbsupervisor."','".$nome."','".$perfil."')";
  $resultado = mysqli_query($conexao, $sql) or die (mysqli_error());

    echo "<script>alert('Usuário criado com sucesso');</script>";
  echo "<script>window.location=\"../\"</script>";
}


?>
