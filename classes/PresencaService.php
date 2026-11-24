<?php
class PresencaService
{
    private $connBdPonto;

    public function __construct($connBdPonto)
    {
        $this->connBdPonto = $connBdPonto;
    }

    public function buscarUltimaPresencaDoDia($matricula, $dataReferencia = null)
    {
        $matricula = trim((string) $matricula);
        $dataReferencia = $dataReferencia ?: new DateTime('now');
                $dataHoje = $dataReferencia->format('Y-m-d');
                $dataDoDia = $dataReferencia->format('Y-m-d 00:00:00');

                $sql = "SELECT situacau, datahora, `data`
                  FROM tbpresenca
                 WHERE matricula = ?
                                     AND UPPER(TRIM(situacau)) = 'PRESENTE'
                                     AND `data` = ?
                                 ORDER BY idtbpresenca DESC
                 LIMIT 1";

        if (!($this->connBdPonto instanceof mysqli)) {
            return null;
        }

        $stmt = mysqli_prepare($this->connBdPonto, $sql);
        if (!$stmt) {
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'ss', $matricula, $dataDoDia);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $situacau, $datahora, $dataRegistro);

        $registro = null;
        if (mysqli_stmt_fetch($stmt)) {
            $datahoraRegistro = trim((string) $datahora) !== '' ? (string) $datahora : (string) $dataRegistro;
            $registro = array(
                'situacau' => trim((string) $situacau),
                'datahora' => $datahoraRegistro,
                'classificacao' => $this->classificarHorario($datahoraRegistro, $dataHoje),
            );
        }

        mysqli_stmt_close($stmt);

        return $registro;
    }

    private function classificarHorario($datahora, $dataHoje)
    {
        $timestampRegistro = strtotime($datahora);
        if ($timestampRegistro === false) {
            return 'sem horario';
        }

        $limiteSeisHoras = strtotime($dataHoje . ' 06:00:00');
        if ($timestampRegistro <= $limiteSeisHoras) {
            return 'antes ou igual 6h';
        }

        return 'depois de 6h';
    }
}
