<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
$data = date('Y-m-d');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");
include_once "../func/log.php";

$aceite = $_POST['aceite'];
$decisao = $_POST['decisao'];
$mysqli2 = new mysqli("10.17.0.5", "blvp", "@F3r7n6c2", "bdaniel");
if ($mysqli2->connect_error) {
    throw new Exception('Erro na conexão com o banco de dados: ' . $mysqli2->connect_error);
}
try {
    $ids = implode(',', array_map('intval', $id));

    $sql = "
        UPDATE bdfrota.tbtoaqui
        SET aceite = '$decisao',
            dthoraaceite = '$hoje'
        WHERE idtbtoaqui IN ($ids)
    ";
    $resultado = mysql_query($sql);

    if (!$resultado) {
        throw new Exception('Erro ao atualizar registro: ' . mysql_error());
    }
    
    echo "Registro $id atualizado com decisão $decisao.<br>";

    if ($decisao == '1') {
        foreach ($aceite as $id) {

        }
    }

    echo "<script language='javascript' type='text/javascript'>alert('Operação realizada com sucesso!');window.location=\"../aceitetoaqui.php\"</script>";

} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}

?>