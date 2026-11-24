<?php
declare(strict_types=1);

// Somente formatação; utiliza os mesmos campos e textos do worker_resposta_manualv2.py.
function frotas_fila_resumo(array $row): string
{
    $html = preg_replace('~<(script|style)\b[^>]*>.*?</\1>~is', '', (string)($row['resposta'] ?? ''));
    $texto = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(preg_replace('/\s+/u', ' ', $texto) ?? $texto);
}

function frotas_fila_texto(array $row): string
{
    $fechado = !empty($row['data_fech']);
    $fechamento = '';
    if ($fechado) {
        $timestamp = strtotime((string)$row['data_fech']);
        $data = $timestamp !== false ? date('d/m/Y H:i:s', $timestamp) : (string)$row['data_fech'];
        $fechamento = 'Chamado fechado: ' . $data . "\n";
    }
    return "Notificação de atualização em chamado na Frota!\n" . $fechamento
        . ($fechado ? 'Última resposta' : 'Houve uma nova resposta')
        . ' no sistema Frota referente ao chamado nº ' . ($row['idtbchamados'] ?? '-') . ":\n\n"
        . '  ' . (string)($row['resposta'] ?? '') . "\n\n"
        . "Para mais detalhes, acesse seus chamados pelo link:\n"
        . "https://ffa.painel-telecom.com:4242/frotas/chamados/listagemchamadoscliente.php\n\n"
        . 'Esta é uma mensagem automática de resposta ou fechamento de chamado— não é possível respondê-la e as atualizações não ocorrem em tempo real. '
        . 'Em caso de dúvidas, entre em contato com sua equipe na Frota ou verifique o chamado.'
        . ($fechado ? ' Em caso de não ter aberto chamado, desconsidere esta notificação.' : '');
}

function frotas_fila_whatsapp_url(array $row): ?string
{
    $telefone = preg_replace('/\D/', '', (string)($row['telefone'] ?? ''));
    if (substr($telefone, 0, 4) === '0055') $telefone = substr($telefone, 2);
    if (in_array(strlen($telefone), [10, 11], true)) $telefone = '55' . $telefone;
    if ($telefone === '' || trim((string)($row['resposta'] ?? '')) === '') return null;
    return 'https://wa.me/' . $telefone . '?text=' . rawurlencode(frotas_fila_texto($row));
}
