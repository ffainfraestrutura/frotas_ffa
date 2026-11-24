<?php
function getColaboradores()
{
    require_once __DIR__ . '/../conecta2.php';
    // ==========================================
    // 0. EXCEÇÃO: ACESSO TOTAL PARA MATRÍCULAS ESPECÍFICAS
    // ==========================================
    $sql = "
            -- Todos os Supervisores
            SELECT DISTINCT
                f.matricula AS colaborador_matricula,
                f.status AS colaborador_status,
                f.nome AS colaborador_nome,
                f.ccusto AS colaborador_ccusto,
                '' AS supervisor_matricula,
                '' AS supervisor_nome,
                coord.nome AS coordenador_nome,
                ger.nome AS gerente_nome,
                'SUPERVISOR' AS hierarquia,
                v.placa
            FROM bdfrota.tbsupervisor s
            JOIN bdfrota.tbcoord c
                ON s.idtbcoordenador = c.idtbcoordenador
            JOIN bdaniel.tbfuncionario coord
                ON c.matricula = coord.matricula
            JOIN bdfrota.tbgerente g
                ON c.idtbgerente = g.idtbgerente
            JOIN bdaniel.tbfuncionario ger
                ON g.matricula = ger.matricula
            JOIN bdaniel.tbfuncionario f
                ON s.matricula = f.matricula
            JOIN bdfrota.tbveiculo v
                ON v.matcond = f.matricula
            WHERE f.status = 'Ativo'

            UNION

            -- Todos os Operacionais (Usuários)
            SELECT DISTINCT
                f.matricula AS colaborador_matricula,
                f.status AS colaborador_status,
                f.nome AS colaborador_nome,
                f.ccusto AS colaborador_ccusto,
                l.matricula AS supervisor_matricula,
                l.nome AS supervisor_nome,
                coord.nome AS coordenador_nome,
                ger.nome AS gerente_nome,
                'OPERACIONAL' AS hierarquia,
                v.placa
            FROM bdfrota.tbusuario u
            JOIN bdfrota.tbsupervisor s
                ON u.idtbsupervisor = s.idtbsupervisor
            JOIN bdfrota.tbcoord c
                ON s.idtbcoordenador = c.idtbcoordenador
            JOIN bdaniel.tbfuncionario coord
                ON c.matricula = coord.matricula
            JOIN bdfrota.tbgerente g
                ON c.idtbgerente = g.idtbgerente
            JOIN bdaniel.tbfuncionario ger
                ON g.matricula = ger.matricula
            JOIN bdaniel.tbfuncionario f
                ON u.matricula = f.matricula
            JOIN bdfrota.tbveiculo v
                ON v.matcond = f.matricula
            LEFT JOIN bdaniel.tbfuncionario l
                ON s.matricula = l.matricula
            WHERE f.status = 'Ativo';
        ";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPontosColaboradores($startDate, $endDate)
{
    $dadosColaboradores = getColaboradores();
    $matriculas = array_column($dadosColaboradores, 'colaborador_matricula');

    if (empty($matriculas)) {
        return [];
    }

    // 1. Pré-popula o array de pontos com TODOS os colaboradores da sua base
    $pontos = [];
    foreach ($dadosColaboradores as $colaborador) {
        $mat = $colaborador['colaborador_matricula'];
        $pontos[$mat] = [
            'matricula' => $mat,
            'nome' => $colaborador['colaborador_nome'] ?? '',
            'status' => $colaborador['colaborador_status'] ?? '',
            'ccusto' => $colaborador['colaborador_ccusto'] ?? '',
            'placa' => $colaborador['placa'] ?? '',
            'supervisor_matricula' => $colaborador['supervisor_matricula'] ?? '',
            'supervisor_nome' => $colaborador['supervisor_nome'] ?? '',
            'coordenador_nome' => $colaborador['coordenador_nome'] ?? '',
            'gerente_nome' => $colaborador['gerente_nome'] ?? '',
            'hierarquia' => $colaborador['hierarquia'] ?? '',
            'equipe' => '',
            'data' => date('Y-m-d'), // Padrão caso não venha da API
            'turno' => '',
            'marcacoes' => [] // Começa vazio para todos
        ];
    }

    $url = "https://api.pontomais.com.br/external_api/v1/reports/time_cards";

    // NOTA: Mantive o row_filters, mas se você quiser que a API traga dados de turno/equipe 
    // mesmo de quem não bateu ponto, avalie remover ou alterar essa linha do payload.
    $payload = [
        "report" => [
            "start_date" => $startDate,
            "end_date" => $endDate,
            "row_filters" => "has_time_cards",
            "columns" => "employee_name,registration_number,team_name,date,time_cards,shift_name",
            "filter_by_documents" => $matriculas,
            "format" => "json"
        ]
    ];

    $headers = [
        "Content-Type: application/json",
        "access-token: \$2a\$12\$6s1DfJ0Snk9JfZWDJvZDFuoohhNSFW0XD.2b5SCeu00L5Rxk8m/b."
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode >= 400) {
        throw new Exception("Erro Pontomais HTTP {$httpCode}");
    }

    $dados = json_decode($response, true);

    // Se a API não trouxer dados, ainda assim retornamos a lista de colaboradores (vazia de pontos)
    if (!isset($dados['data']) || !isset($dados['data'][0])) {
        return array_values($pontos);
    }

    // 2. Agora apenas alimentamos quem veio no retorno da API
    foreach ($dados['data'][0] as $linha) {
        if (!isset($linha['data'])) {
            continue;
        }

        foreach ($linha['data'] as $registro) {
            $matriculaRegistro = $registro['registration_number'] ?? null;

            // Se a matrícula não existe no nosso array pré-populado, ignora
            if (!$matriculaRegistro || !isset($pontos[$matriculaRegistro])) {
                continue;
            }

            // Atualiza as informações dinâmicas vindas da API
            $pontos[$matriculaRegistro]['equipe'] = $registro['team_name'] ?? $pontos[$matriculaRegistro]['equipe'];
            $pontos[$matriculaRegistro]['data'] = $registro['date'] ?? $pontos[$matriculaRegistro]['data'];
            $pontos[$matriculaRegistro]['turno'] = $registro['shift_name'] ?? $pontos[$matriculaRegistro]['turno'];

            // Adiciona a marcação se ela existir
            $dataRegistro = $registro['date'] ?? null;

            if ($dataRegistro && !empty($registro['time'])) {
                $pontos[$matriculaRegistro]['marcacoes'][$dataRegistro][] = $registro['time'];
            }
        }
    }

    return array_values($pontos);
}

function relatorioCondutoresPresenca()
{
    $dataAtual = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->modify('-1 day');
    // Obtém os 3 últimos dias úteis
    $diasUteis = [];
    $data = clone $dataAtual;

    while (count($diasUteis) < 3) {
        if ($data->format('N') < 6) { // 1=Segunda, 5=Sexta
            $diasUteis[] = $data->format('Y-m-d');
        }

        $data->modify('-1 day');
    }

    $dataInicial = end($diasUteis);

    $pontosColaboradores = getPontosColaboradores(
        $dataInicial,
        $dataAtual->format('Y-m-d')
    );

    $ausentes = [];

    foreach ($pontosColaboradores as $colaborador) {

        $semPonto = true;

        foreach ($colaborador['marcacoes'] as $data => $marcacoes) {
            if (!empty($marcacoes)) {
                $semPonto = false;
                break;
            }
        }

        if ($semPonto) {
            $ausentes[] = [
                'matricula' => $colaborador['matricula'],
                'nome' => $colaborador['nome'],
                'status' => $colaborador['status'],
                'ccusto' => $colaborador['ccusto'],
                'supervisor_nome' => $colaborador['supervisor_nome'],
                'coordenador_nome' => $colaborador['coordenador_nome'],
                'gerente_nome' => $colaborador['gerente_nome'],
                'dias_sem_ponto' => $diasUteis,
                'placa' => $colaborador['placa']
            ];
        }
    }

    return $ausentes;
}

function infoFuncionario($matricula)
{
    include "../conecta2.php";

    $sql = "SELECT * FROM bdaniel.tbfuncionario WHERE matricula='$matricula';";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);

    return $row;
}

function infoStatusVeiculo($id_status)
{
    include "../conecta2.php";

    $sql = "SELECT * FROM bdfrota.tbastatusvel WHERE idtbastatusvel='$id_status'; ";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);

    return $row;
}

function infoAplicacaoVeiculo($id_aplicacao)
{
    include "../conecta2.php";

    $sql = "SELECT aplicacao FROM bdfrota.tbaplicacaoveic WHERE idtbaplicacaoveic = '$id_aplicacao'; ";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);

    return $row;
}

function infoModeloVeiculo($id_modelo)
{
    include "../conecta2.php";

    $sql = "SELECT * FROM bdfrota.tbmodeloveic WHERE idtbmodeloveic = '$id_modelo';";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);

    return $row;
}

function infoFornecedor($id_fornecedor)
{
    include "../conecta2.php";

    $sql = "SELECT * FROM bdfrota.tbfornecedor WHERE idtbfornecedor = '$id_fornecedor'; ";
    $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
    $row = mysqli_fetch_array($resultado, MYSQLI_BOTH);

    return $row;
}

function removerSaldoTicket($placa, $valor)
{
    try {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
            'Accept-Charset: utf-8',
            'Accept-Encoding: gzip',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
        ));
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $body = '{
        "codigoCliente": 183062,
        "codigoProduto": 4,
        "numeroCartao": null,
        "especieCartao": null,
        "numeroMatricula": null,
        "nomeCompleto": null,
        "placaVeiculo": "' . $placa . '",
        "numeroFrota": null,
        "codigoTipoFrota": null,
        "codigoFamiliaVeiculo": null,
        "codigoVeiculoModelo": null,
        "codigoCidade": null,
        "codigoClienteTCVeiculoResponsavel": null,
        "nomeResponsavel": null,
        "codigoGrupo": 4,
        "codigoTipoCombustivel": null,
        "informacoesAdicionais": [
            {
            "labelIA": null,
            "codigoIA": null,
            "descricaoIA": null
            }
        ],
        "situacaoCartao": "T",
        "ordem": "C",
        "quebra": null,
        "tipoMotorista": null,
        "ativacao": null,
        "tipoCartao": null,
        "controlaHodometro": null,
        "controlaHorimetro": null,
        "situacaoVeiculo": null,
        "trackOnLine": null
    }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $result = json_decode(curl_exec($curl));

        if (count($result->itens)) {

            foreach ($result->itens as $itens) {

                $situacao = $itens->situacao;

                if ($situacao == 'A') {
                    $numeroCartao = $itens->numeroCartao;
                }

            }
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $body = '{
        "codigoCliente": 183062,
        "codigoProduto": 4,
        "tipoAlteracao": "DR",
        "tipoLimite": "AS",
        "tipoOperacao": "SP",
        "cartoes": [
            {
            "numeroCartao": "' . $numeroCartao . '",
            "valorLimite": ' . $valor . ',
            "valorLimiteProxPeriodo": null,
            "mensagem": "Controle Combustivel"
            }
        ]
    }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $result = json_decode(curl_exec($curl));
        return $result;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function removerSaldoValeCard($placa, $valor)
{
    try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
        ));
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $body = '{
        "contratoCliente" : "524073",
        "veiculoId" : null,
        "placa" : "' . $placa . '",
        "numeroCartao" : null,
        "valorAlteracao" : "' . $valor . '",
        "tipoAlteracao" : "diminuir",
        "periodo" : "atual",
        "motivo" : "Sistema Combustivel",
        "modulo" : "abastecimento"
    }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $result = json_decode(curl_exec($curl));
        return $result;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function adicionarSaldoTicket($placa, $valor)
{
    try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
        ));
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $body = '{
                "codigoCliente": 183062,
                "codigoProduto": 4,
                "numeroCartao": null,
                "especieCartao": null,
                "numeroMatricula": null,
                "nomeCompleto": null,
                "placaVeiculo": "' . $placa . '",
                "numeroFrota": null,
                "codigoTipoFrota": null,
                "codigoFamiliaVeiculo": null,
                "codigoVeiculoModelo": null,
                "codigoCidade": null,
                "codigoClienteTCVeiculoResponsavel": null,
                "nomeResponsavel": null,
                "codigoGrupo": 4,
                "codigoTipoCombustivel": null,
                "informacoesAdicionais": [
                    {
                    "labelIA": null,
                    "codigoIA": null,
                    "descricaoIA": null
                    }
                ],
                "situacaoCartao": "T",
                "ordem": "C",
                "quebra": null,
                "tipoMotorista": null,
                "ativacao": null,
                "tipoCartao": null,
                "controlaHodometro": null,
                "controlaHorimetro": null,
                "situacaoVeiculo": null,
                "trackOnLine": null
            }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $result = json_decode(curl_exec($curl));

        if (count($result->itens)) {

            foreach ($result->itens as $itens) {

                $situacao = $itens->situacao;

                if ($situacao == 'A') {
                    $numeroCartao = $itens->numeroCartao;
                    $saldocartao = $itens->saldo;

                    /*if ($saldocartao >= 40) {

                        echo"<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovacaocota.php\"</script>";

                    }*/
                }

            }
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/usuarioCartaoLimite');

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $body = '{
                "codigoCliente": 183062,
                "codigoProduto": 4,
                "tipoAlteracao": "AR",
                "tipoLimite": "AS",
                "tipoOperacao": "SP",
                "cartoes": [
                    {
                    "numeroCartao": "' . $numeroCartao . '",
                    "valorLimite": ' . $valor . ',
                    "valorLimiteProxPeriodo": null,
                    "mensagem": "Controle Combustivel"
                    }
                ]
            }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $result = json_decode(curl_exec($curl));
        return $result;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

function adicionarSaldoValeCard($placa, $valor)
{
    try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://kissapi.valecard.com.br/core/services/frotaCliente/alterarLimiteveiculo');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic NTI0MDczQHdlYlNlcnZpY2U6MDY5NDc=',
        ));
        curl_setopt($curl, CURLOPT_HEADER, 0);

        $body = '{
        "contratoCliente" : "524073",
        "veiculoId" : null,
        "placa" : "' . $placa . '",
        "numeroCartao" : null,
        "valorAlteracao" : "' . $valor . '",
        "tipoAlteracao" : "adicionar",
        "periodo" : "atual",
        "motivo" : "Sistema Combustivel",
        "modulo" : "abastecimento"
    }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

        $result = json_decode(curl_exec($curl));
        return $result;
    } catch (Exception $e) {
        return $e->getMessage();
    }
}

?>