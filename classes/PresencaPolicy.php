<?php
class PresencaPolicy
{
    public function avaliar($registroPresenca, $dataReferencia = null)
    {
        $dataReferencia = $dataReferencia ?: new DateTime('now');
        $horaAtual = $dataReferencia->format('H:i:s');

        $situacao = 'Sem registro';
        $temRegistro = false;

        if (is_array($registroPresenca)) {
            $temRegistro = true;
            $situacao = isset($registroPresenca['situacau']) && trim((string) $registroPresenca['situacau']) !== ''
                ? trim((string) $registroPresenca['situacau'])
                : 'Sem registro';
        }

        if ($temRegistro && strcasecmp($situacao, 'Presente') !== 0) {
            return array(
                'permitido' => false,
                'motivo' => 'situacao_diferente',
                'mensagem' => 'Foi informado que você não está presente hoje. Você será deslogado. Entre em contato com seu supervisor.',
            );
        }

        if ($temRegistro) {
            return array(
                'permitido' => true,
                'motivo' => 'presente',
                'mensagem' => '',
            );
        }

        if ($horaAtual > '06:00:00') {
            return array(
                'permitido' => false,
                'motivo' => 'sem_presenca',
                'mensagem' => 'Você não recebeu presença hoje. Você será deslogado. Entre em contato com seu supervisor.',
            );
        }

        return array(
            'permitido' => true,
            'motivo' => 'sem_registro_antes_das_10',
            'mensagem' => '',
        );
    }
}