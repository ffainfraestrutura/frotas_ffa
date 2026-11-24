<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();

$perfil = isset($_SESSION['perfil']) ? (string) $_SESSION['perfil'] : '';
$matricula = isset($_SESSION['matricula']) ? (string) $_SESSION['matricula'] : '';

if ($perfil !== '10') {
    echo "<script>alert('Acesso permitido apenas para diretor');window.location='../index.php'</script>";
    exit;
}

include '../conecta.php';
header('Content-type: text/html; charset=utf-8');

$matriculaSql = mysql_real_escape_string($matricula);
$sql = "SELECT p.idtbpedidosdiretor AS id_pedido,
               p.matricula,
               p.valor,
               p.justificativa,
               p.data,
               p.flag,
               COALESCE(f.nome, p.matricula) AS solicitante
          FROM bdfrota.tbpedidosdiretor p
    INNER JOIN bdfrota.tbgerente g ON g.matricula = p.matricula
    INNER JOIN bdfrota.tbdiretor d ON d.id = g.idtbdiretor
     LEFT JOIN bdaniel.tbfuncionario f ON f.matricula = p.matricula
         WHERE d.matricula = '$matriculaSql'
           AND p.flag IN (1, 2)
      ORDER BY p.idtbpedidosdiretor DESC, p.data DESC";

$resultado = mysql_query($sql) or die(mysql_error());
$historicoPedidos = array();
while ($row = mysql_fetch_assoc($resultado)) {
    $historicoPedidos[] = $row;
}

$historicoTitulo = 'Histórico de Pedidos - Diretor';
$historicoBaseUrl = '../';
$historicoBaseDir = dirname(__DIR__) . '/';
include '../func/historico_pedidos_view.php';