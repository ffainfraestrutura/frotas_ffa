<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

header('Content-Type: application/json; charset=utf-8');

include 'conecta.php';

function periodoEncerrado($diaEncerramento, $horaEncerramento) {
    $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
    $diaSemana = (int)$agora->format('w'); // 0=domingo, 1=segunda ... 6=sábado
    $horaAtual = (int)$agora->format('G');

    if ($diaSemana === $diaEncerramento && $horaAtual >= $horaEncerramento) {
        return true;
    }

    if ($diaEncerramento === 0) {
        return $diaSemana >= 1 && $diaSemana <= 6;
    }

    return $diaSemana > $diaEncerramento || $diaSemana === 0;
}

$diaEncerramento = 5; // padrão: sexta-feira
$horaEncerramento = 15; // padrão: 15h
$sqlConfigEscala = "SELECT dia_encerramento, hora_encerramento FROM tbconfiguracao_escala_tecnicos ORDER BY id DESC LIMIT 1";
$resultConfigEscala = mysqli_query($conn, $sqlConfigEscala);

if ($resultConfigEscala && mysqli_num_rows($resultConfigEscala) > 0) {
    $configEscala = mysqli_fetch_assoc($resultConfigEscala);
    $diaTabela = (int)$configEscala['dia_encerramento'];
    $horaTabela = (int)$configEscala['hora_encerramento'];

    if ($diaTabela >= 0 && $diaTabela <= 6) {
        $diaEncerramento = $diaTabela;
    }

    if ($horaTabela >= 0 && $horaTabela <= 23) {
        $horaEncerramento = $horaTabela;
    }
}

if (periodoEncerrado($diaEncerramento, $horaEncerramento)) {
    echo json_encode(['success' => false, 'msg' => 'Período de edição encerrado pela configuração de dia/hora.']);
    exit;
}

$matricula = trim($_SESSION['matricula']);

$limpar = isset($_POST['limpar']) && ($_POST['limpar'] === true || $_POST['limpar'] === 'true' || $_POST['limpar'] === '1' || $_POST['limpar'] === 1);
$escalas = [];

if (!$limpar) {
    $escalas_json = isset($_POST['escalas']) ? $_POST['escalas'] : '[]';
    $escalas = json_decode($escalas_json, true);

    if (!is_array($escalas)) {
        echo json_encode(['success' => false, 'msg' => 'Payload de escalas inválido.']);
        exit;
    }
}

// Limpa escalas da semana atual
$sabado = date('Y-m-d', strtotime('saturday this week'));
$domingo = date('Y-m-d', strtotime('sunday this week'));
mysql_query("DELETE FROM bdfrota.tbescala WHERE dia IN ('$sabado', '$domingo') AND mat_coord = '{$matricula}'");

// Insere novas escalas
if (!$limpar) {
    foreach($escalas as $escala) {
        mysql_query("INSERT INTO bdfrota.tbescala (matricula, dia, status, mat_coord) VALUES (
            '{$escala['matricula']}', 
            '{$escala['dia']}', 
            '{$escala['status']}',
            '{$matricula}'
        )");
    }
}

echo json_encode(['success' => true]);
?>
