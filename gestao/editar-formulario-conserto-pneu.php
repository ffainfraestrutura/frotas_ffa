<?php
$idEdicao = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idEdicao) {
    http_response_code(404);
    exit('Formulário não encontrado.');
}

define('FORMULARIO_CONSERTO_PNEU_EDICAO', true);
require __DIR__ . '/preencher-formulario-conserto-pneu.php';
