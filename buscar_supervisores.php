<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

$perfil = $_SESSION['perfil'];

// Validação de acesso
if ($perfil == null || $perfil != 2) {
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

include 'conecta.php';

$idCoordenador = isset($_GET['coordenador']) ? intval($_GET['coordenador']) : 0;

if($idCoordenador <= 0) {
    echo json_encode([]);
    exit;
}

// Busca supervisores do coordenador
$sql_supervisores = "SELECT s.idtbsupervisor, f.nome, f.matricula
                     FROM bdfrota.tbsupervisor s
                     INNER JOIN bdaniel.tbfuncionario f ON s.matricula = f.matricula
                     WHERE s.idtbcoordenador = $idCoordenador
                     AND f.status = 'Ativo'
                     ORDER BY f.nome";

$result_supervisores = mysql_query($sql_supervisores);

$supervisores = [];

if($result_supervisores) {
    while($row_sup = mysql_fetch_array($result_supervisores)) {
        $supervisores[] = [
            'id' => $row_sup[0],
            'nome' => $row_sup[1],
            'matricula' => $row_sup[2]
        ];
    }
}

echo json_encode($supervisores);
?>
