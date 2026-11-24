<?php
$servidor05="10.17.0.5";
$usuario05="blvp";
$senha05="@F3r7n6c2";
$db05 = "bdfrota";

$conexao=mysqli_connect("$servidor05","$usuario05","$senha05","$db05") or die (mysqli_connect_error());

// Define o charset para UTF-8
mysqli_set_charset($conexao, "utf8mb4");
?>
