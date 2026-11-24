<?php
date_default_timezone_set('America/Sao_Paulo');
$hoje = date('Y-m-d H:i:s');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
include("../conecta.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupere os valores enviados pelo formulário
    $mattec = $_POST['mattec'];
    $datadia = $_POST['datadia'];
    $matop = $_POST['matop'];
    $dataagora = $_POST['dataagora'];
    $decisaocol = $_POST['decisaocol'];
    $numericInput = $_POST['numericInput'];
    $justificativacol = $_POST['jutificativacol'];

    // Faça o que quiser com os dados, por exemplo, exiba-os
    echo "Matrícula do Técnico: $mattec<br>";
    echo "Data do Dia: $datadia<br>";
    echo "Matrícula do Operador: $matop<br>";
    echo "Data Atual: $dataagora<br>";

    if ($decisaocol == 1) {
        echo "Operação: Adicionar<br>";
    } elseif ($decisaocol == 0) {
        echo "Operação: Diminuir<br>";
    }

    echo "Valor KMs: $numericInput<br>";
    echo "Justificativa: $justificativacol";
} else {
    echo "O formulário não foi enviado corretamente.";
}
?>
