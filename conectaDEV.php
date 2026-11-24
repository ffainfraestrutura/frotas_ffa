<?php
/**
 * Configurações exclusivas da conexão DEV com o banco BdPonto.
 *
 * Variáveis com sufixo "BdPonto" evitam sobrescrever globais do conecta legado.
 */
$servidorBdPonto = '10.17.0.2';
$usuarioDbBdPonto = 'blvp';
$senhaDbBdPonto = '@F3r7n6c2';
$bancoBdPonto = 'BdPonto';

/**
 * Nome do schema usado nas consultas DEV de presença.
 *
 * @var string $databaseNameBdPonto
 */
$databaseNameBdPonto = $bancoBdPonto;

/**
 * Conexão MySQLi exclusiva para consultas no BdPonto.
 *
 * @var mysqli|false $connBdPonto
 */
$connBdPonto = mysqli_connect($servidorBdPonto, $usuarioDbBdPonto, $senhaDbBdPonto, $bancoBdPonto);
?>
