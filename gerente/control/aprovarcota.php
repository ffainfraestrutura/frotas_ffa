

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
    include("../../conecta.php");
    include_once "../../func/log.php";
    include_once "../../func/saldocartao.php";
    include_once "../../func/consultacartao.php";

    $matriculaf = $_POST['matriculasup'];
    $id = $_POST['id'];
    $idtbsaldo = $_POST['idtbsaldo'];
    $decisao = $_POST['decisao'];
    function normalizarValorMoeda($valor)
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return '';
        }

        if (strpos($valor, ',') !== false) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }

        return $valor;
    }

    $valor = normalizarValorMoeda($_POST["valor"]);
    $valor_ant = normalizarValorMoeda($_POST["valor_ant"]);
    $orcpedido = normalizarValorMoeda($_POST["orcpedido"]);
    $tipocota = $_POST["tipocota"];
    $dataplantao = $_POST["dataplantao"];

    if ($valor == '') {
        $valor = $orcpedido;
    }

    $sql = "SELECT valor FROM bdfrota.tbgerente where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = (float) $row[0];
    $valor = (float) $valor;
    $orcpedido = (float) $orcpedido;

    if ($decisao == 2) {

        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovarcota.php\"</script>";

        } else {

            $sql = "SELECT placa, codempresa FROM bdfrota.tbveiculo tv join bdaniel.tbfuncionario tf on tv.matcond = tf.matricula where matcond = '$matriculaf'";
            $resultado = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_array($resultado);
            $placa = $row[0];
            $codempresa = $row[1];

            // Identifica a operadora do cartão
            $cartaosup = consultarCartoes($placa, null, $codempresa);

            $placaNormalizada = strtoupper(trim($placa));
            $placaNormalizada = str_replace("-", "", $placaNormalizada);
            $saldo = null;
            $operadora = 'desconhecida';

            if (isset($cartaosup[$codempresa])) {
                if (isset($cartaosup[$codempresa][$placaNormalizada])) {
                    $saldo = $cartaosup[$codempresa][$placaNormalizada]['saldo'];
                    // Identifica qual operadora com base no código
                    if ($codempresa == 2) {
                        $operadora = 'Valecard';
                    } else {
                        $operadora = 'TicketLog';
                    }
                }
            }

            $resultado = inserirSaldo($codempresa, $placa, $valor);

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
                $acao = 'Aceitou cota extra';
                $flag = '2';

                $sql = "UPDATE bdfrota.tbpedidoscoordcota SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidoscoordcota = $id";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbcoord SET saldo = saldo + $valor, totcotaextra = totcotaextra + $valor where matricula = '$matriculaf'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbgerente SET valor = valor - $valor where matricula = '$matricula'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

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
        $sql = "UPDATE bdfrota.tbpedidoscoordcota SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidoscoordcota = $id";
        $resultado = mysql_query($sql) or die(mysql_error());
        $acao = 'Não aceitou cota extra';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

        echo "<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../aprovarcota.php\"</script>";
    }
    ?>
</body>