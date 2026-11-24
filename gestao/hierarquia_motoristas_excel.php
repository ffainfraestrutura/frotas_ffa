<?php
// Consulta em lote exclusiva da exportacao; nao altera os dados da tela.
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
if (empty($_SESSION['perfil'])) {
    http_response_code(403);
    echo json_encode(array('success' => false));
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(array('success' => false));
    exit;
}
$entrada = json_decode(file_get_contents('php://input'), true);
$matriculas = $entrada['matriculas'] ?? null;
$chamados = $entrada['chamados'] ?? array();
if (!is_array($matriculas) || count($matriculas) > 500 || !is_array($chamados) || count($chamados) > 500) {
    http_response_code(400);
    echo json_encode(array('success' => false));
    exit;
}
foreach (array_merge($matriculas, $chamados) as $matricula) {
    if (!is_string($matricula) || !preg_match('/^[0-9]{1,10}$/D', $matricula)) {
        http_response_code(400);
        echo json_encode(array('success' => false));
        exit;
    }
}
$matriculas = array_values(array_unique($matriculas));
$chamados = array_values(array_unique($chamados));
if (empty($matriculas) && empty($chamados)) {
    echo json_encode(array('success' => true, 'hierarquia' => new stdClass(), 'chamados' => new stdClass()));
    exit;
}
ob_start();
try {
    $nome = $_SESSION['nome'] ?? '';
    $usuariof = $_SESSION['usuario'] ?? '';
    $matricula = $_SESSION['matricula'] ?? '';
    $perfil = $_SESSION['perfil'];
    $filial = $_SESSION['filial'] ?? '';
    // Usa o mesmo controle adicional da tela quando instalado.
    if (is_file(__DIR__ . '/controleacesso.php')) {
        include_once __DIR__ . '/controleacesso.php';
        if (trim(ob_get_contents()) !== '' || empty($_SESSION['perfil'])) {
            ob_end_clean();
            http_response_code(403);
            echo json_encode(array('success' => false));
            exit;
        }
    }
    require __DIR__ . '/../conecta.php';
    $hierarquia = array();
    if (!empty($matriculas)) {
        $marcadores = implode(',', array_fill(0, count($matriculas), '?'));
        $sql = "SELECT u.matricula, fs.nome AS supervisor, fc.nome AS coordenador
            FROM bdfrota.tbusuario u
            LEFT JOIN bdfrota.tbsupervisor s ON s.idtbsupervisor = u.idtbsupervisor
            LEFT JOIN bdfrota.tbcoord c ON c.idtbcoordenador = s.idtbcoordenador
            LEFT JOIN bdaniel.tbfuncionario fs ON fs.matricula = s.matricula
            LEFT JOIN bdaniel.tbfuncionario fc ON fc.matricula = c.matricula
            WHERE u.matricula IN ($marcadores)
            ORDER BY (s.idtbsupervisor IS NOT NULL) DESC";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt || !mysqli_stmt_bind_param($stmt, str_repeat('s', count($matriculas)), ...$matriculas)
            || !mysqli_stmt_execute($stmt)) {
            throw new RuntimeException('Falha ao consultar hierarquia.');
        }
        mysqli_stmt_bind_result($stmt, $matriculaUsuario, $supervisor, $coordenador);
        while (mysqli_stmt_fetch($stmt)) {
            if (!isset($hierarquia[$matriculaUsuario])) {
                $hierarquia[$matriculaUsuario] = array('supervisor' => $supervisor, 'coordenador' => $coordenador);
            }
        }
        mysqli_stmt_close($stmt);
    }
    $dadosChamados = array();
    if (!empty($chamados)) {
        $marcadoresChamados = implode(',', array_fill(0, count($chamados), '?'));
        $sqlChamados = "SELECT idtbchamados, tipodechamado, tipomanuprev
            FROM bdfrota.tbchamados WHERE idtbchamados IN ($marcadoresChamados)";
        $stmtChamados = mysqli_prepare($conn, $sqlChamados);
        if (!$stmtChamados || !mysqli_stmt_bind_param($stmtChamados, str_repeat('s', count($chamados)), ...$chamados)
            || !mysqli_stmt_execute($stmtChamados)) {
            throw new RuntimeException('Falha ao consultar chamados.');
        }
        mysqli_stmt_bind_result($stmtChamados, $idChamado, $tipoChamado, $tipoManutencao);
        while (mysqli_stmt_fetch($stmtChamados)) {
            $dadosChamados[$idChamado] = array('tipodechamado' => $tipoChamado, 'tipomanuprev' => $tipoManutencao);
        }
        mysqli_stmt_close($stmtChamados);
    }
    ob_end_clean();
    echo json_encode(array('success' => true, 'hierarquia' => (object) $hierarquia, 'chamados' => (object) $dadosChamados));
} catch (Throwable $erro) {
    ob_end_clean();
    error_log('[hierarquia_motoristas_excel] ' . $erro->getMessage());
    http_response_code(500);
    echo json_encode(array('success' => false));
}