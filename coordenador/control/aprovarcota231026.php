<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php
    date_default_timezone_set('America/Sao_Paulo');
    $hoje = date('Y-m-d H:i:s');
    session_start();
    $nome = $_SESSION['nome'];
    $usuariof = $_SESSION['usuario'];
    $matricula = $_SESSION['matricula'];
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;
    $ccusto = $_SESSION['ccusto'];
    $_SESSION['ccusto'] = $ccusto;
    include("../../conecta.php");
    include_once "../../func/log.php";
    include_once "../../func/saldocartao.php";
    include_once "../../func/consultacartao.php";

    // Função para normalizar strings: remove acentos, converte para minúsculas e colapsa espaços
    function normalize_string($str)
    {
        if ($str === null)
            return '';
        // garante encoding correto
        $str = mb_convert_encoding($str, 'UTF-8', mb_detect_encoding($str));
        // trim e lowercase
        $str = mb_strtolower(trim($str), 'UTF-8');
        // translitera caracteres acentuados para ASCII (ex: 'á' -> 'a')
        $trans = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        if ($trans !== false) {
            $str = $trans;
        }
        // substitui quaisquer caracteres que não sejam letras ou números por espaço
        $str = preg_replace('/[^a-z0-9]+/', ' ', $str);
        // colapsa múltiplos espaços
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }

    $matriculaf = $_POST['matriculatec'];
    $id = $_POST['id'];
    $idtbsaldo = $_POST['idtbsaldo'];
    $decisao = $_POST['decisao'];
    $valor = $_POST["valor"];
    $valor = str_replace(",", ".", $valor);
    $valor_ant = $_POST["valor_ant"];
    $valor_ant = str_replace(",", ".", $valor_ant);
    $orcpedido = $_POST["orcpedido"];
    $orcpedido = str_replace(",", ".", $orcpedido);
    $tipocota = $_POST["tipocota"];
    $valor_desc = $_POST["valor_desc"];
    $valor_desc = str_replace(",", ".", $valor_desc);
    $orcsem = $_POST["orcsem"];
    $orcsem = str_replace(",", ".", $orcsem);
    $kmproj = $_POST["kmproj"];
    $kmproj = str_replace(",", ".", $kmproj);
    $kmos = $_POST["kmos"];
    $kmos = str_replace(",", ".", $kmos);
    $kmsem = $_POST["kmsem"];
    $kmsem = str_replace(",", ".", $kmsem);
    $totextra = $_POST["totextra"];
    $totextra = str_replace(",", ".", $totextra);
    $dataplantao = $_POST["dataplantao"];
    $justsup = $_POST["justificativa"];
    $justsup = str_replace("'", "", $justsup);
    $desc = $_POST["descontocombustivel"];
    $valdesc = $_POST["valdescontocombustivel"];
    $valdesc = str_replace(",", ".", $valdesc);

    if ($valor == '') {
        $valor = $orcpedido;
    }


    $sqlaux = "SELECT ccusto, codempresa, uf_trabalho FROM bdaniel.tbfuncionario WHERE matricula = '$matriculaf'";
    $resultadoaux = mysql_query($sqlaux) or die(mysql_error());
    $rowaux = mysql_fetch_array($resultadoaux);
    $ccusto = $rowaux[0];
    $codempresatec = $rowaux[1];
    $uf = $rowaux[2];
    if ($codempresatec == "") {
        if ($uf == "SP") {
            $codempresatec = 2;
        } else {
            $codempresatec = 1;
        }
    }

    // Valor máximo padrão
    $max = 51;

    // Verifica se o centro de custo indica rede externa ou similar
    $ccusto_normalizado = normalize_string($ccusto);
    if (
        strpos($ccusto_normalizado, 'rede externa') !== false ||
        strpos($ccusto_normalizado, 'implanta') !== false ||
        strpos($ccusto_normalizado, 'obra') !== false
    ) {
        $max = 81;
    }

    // Se o valor ultrapassar o limite, exibe a mensagem e redireciona
    if ($valor > $max) {
        // O supervisor tem autonomia até R$ ($max - 1), conforme a regra anterior
        $autonomia = $max - 1;
        echo "<script language='javascript' type='text/javascript'>
            alert('Prezado coordenador. Sua autonomia para aprovação de cota extra é de até R\$ {$autonomia},00 - cota extra escalonada para a coordenação.');
            window.location=\"../aprovarcota.php\"
          </script>";
        die();
    }
    $kmprojini = $_POST["kmprojini"];
    $orcsematu = $_POST["orcsematu"];


    $sql = "SELECT valor FROM bdfrota.tbcoord where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = $row[0];



    if ($decisao == 2) {
        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovarcota.php\"</script>";

        } else {

            $sql = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculaf'";
            $resultado = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_array($resultado);
            $placa = $row[0];

            $cartaotec = consultarCartoes($placa, null, $codempresatec);

            $placaNormalizada = strtoupper(trim($placa));
            $placaNormalizada = str_replace("-", "", $placaNormalizada);
            $saldo = null;
            $operadora = 'Operadora de cartão. Verifique o veículo e o cartão vinculado ao colaborador, e';

            if (isset($cartaotec[$codempresatec])) { // Valecard
                if (isset($cartaotec[$codempresatec][$placaNormalizada])) {
                    $saldo = $cartaotec[$codempresatec][$placaNormalizada]['saldo'];
                    // Identifica qual operadora com base no código
                    if ($codempresatec == 2) {
                        $operadora = 'Valecard';
                    } else {
                        $operadora = 'TicketLog';
                    }
                }
            }

            // Formatar
            if ($saldo !== null) {
                if ($saldo >= 51) {
                    echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: 'Colaborador possui saldo no cartão, não é possível inserir o saldo solicitado',
                                confirmButtonText: 'Voltar'
                            }).then(() => {
                                window.location=\"../aprovarcota.php\"
                            });
                        </script>
                        ";
                    exit;
                } else {
                    $resultado = inserirSaldo($codempresatec, $placa, $valor);
                    if (!$resultado) {
                        $mensagemErro = 'Erro de comunicação com ' . $operadora . ', tente novamente mais tarde, caso o erro persista favor entrar em contato com a frota.';
                        echo "
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro!',
                                    text: '$mensagemErro',
                                    confirmButtonText: 'Voltar'
                                }).then(() => {
                                    window.location=\"../aprovarcota.php\"
                                });
                            </script>
                            ";
                        exit;
                    } else {

                        $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valor where idtbpedidostec = $id";
                        $resultado = mysql_query($sql) or die(mysql_error());

                        $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc) SELECT matricula, '$matricula', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valor_desc', '$valor_ant', '$totextra', '$cota_validade', 'desc', '$valdesc' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
                        $resultado = mysql_query($sql) or die(mysql_error());

                        $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
                        $resultado = mysql_query($sql) or die(mysql_error());

                        $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
                        $resultado = mysql_query($sql) or die(mysql_error());

                        $sql = "UPDATE bdfrota.tbcoord SET valor = valor - $valor where matricula = '$matricula'";
                        $resultado = mysql_query($sql) or die(mysql_error());

                        $acao = 'Aceitou cota extra';
                        $flag = '2';
                        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldo, $valor);

                        echo "
                            <script>
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso!',
                                    text: 'Saldo inserido com sucesso.',
                                    confirmButtonText: 'Voltar'
                                }).then(() => {
                                    window.location=\"../aprovarcota.php\"
                                });
                            </script>
                            ";
                        exit;
                    }
                }
            } else {
                $mensagemErro = 'Erro de comunicação com ' . $operadora . ', tente novamente mais tarde, caso o erro persista favor entrar em contato com a frota.';
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: '$mensagemErro',
                            confirmButtonText: 'Voltar'
                        }).then(() => {
                            window.location=\"../aprovarcota.php\"
                        });
                    </script>
                    ";
            }
        }
    } else {
        $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());
        $acao = 'Não aceitou cota extra';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

        echo "<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../aprovarcota.php\"</script>";
    }

    ?>
</body>