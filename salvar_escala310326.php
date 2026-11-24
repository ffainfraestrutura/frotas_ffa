<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();


include 'conecta.php';

$matricula = trim($_SESSION['matricula']);

$escalas_json = $_POST['escalas'];
$escalas = json_decode($escalas_json, true);

// Limpa escalas da semana atual
$sabado = date('Y-m-d', strtotime('saturday this week'));
$domingo = date('Y-m-d', strtotime('sunday this week'));
mysql_query("DELETE FROM bdfrota.tbescala WHERE dia IN ('$sabado', '$domingo') AND mat_coord = '{$matricula}'");

// Insere novas escalas
foreach($escalas as $escala) {
    mysql_query("INSERT INTO bdfrota.tbescala (matricula, dia, status, mat_coord) VALUES (
        '{$escala['matricula']}', 
        '{$escala['dia']}', 
        '{$escala['status']}',
        '{$matricula}'
    )");
}

echo json_encode(['success' => true]);
?>
