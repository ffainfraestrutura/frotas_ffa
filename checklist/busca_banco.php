<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

include"conecta.php";

$cpf = $_GET["cpf"];
$sql = "SELECT a.nome AS condutor, a.matricula AS matrcond, a.cpf AS cpf, c.numcnh AS cnh, c.validade AS vencimento, c.categoria AS categoria
FROM bdaniel.tbfuncionario a, bdfrota.tbveiculo b, bdfrota.tbcnh c,
WHERE a.cpf='$cpf' AND a.matricula = b.matcond AND a.matricula = c.matricula LIMIT 1";

$resultado= mysqli_query($con, $sql) or die(mysqli_error($con));

$linhas = mysqli_num_rows($resultado);

if($linhas) {
    $row = mysqli_fetch_array($resultado, MYSQLI_BOTH); 
    $retorno["condutor"] = $row["condutor"];
    $retorno["matrcond"] = $row["matrcond"];
    $retorno["cpf"] = $row["cpf"];
    $retorno["cnh"] = $row["cnh"];
    $retorno["vencimento"] = $row["vencimento"];
    $retorno["categoria"] = $row["categoria"];
  } else {
    
    $retorno["condutor"] = false;
    $retorno["matrcond"] = false;
    $retorno["cpf"] = false;
    $retorno["cnh"] = false;
    $retorno["vencimento"] = false;
    $retorno["categoria"] = false;
}

/*mysqli_query("SELECT * FROM tabela_lojas WHERE operador = '".$operador."' LIMIT 1");
if(mysql_num_rows($q)) {
    $a = mysql_fetch_assoc($q);
    $retorno["lojaNome"] = $a["lojaNome"];
} else {
    $retorno["lojaNome"] = false;
}*/

header("Content-Type: application/json");
echo json_encode($retorno);

?>