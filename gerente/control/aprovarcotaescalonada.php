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

    if ($valor == '') {
        $valor = $orcpedido;
    }

    // ============================================
// PROTEÇÃO CONTRA DUPLO CLIQUE / DUPLICAÇÃO
// ============================================
// Verifica se o pedido já foi processado (flag != 0)
    $sql_verifica = "SELECT flag FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
    $resultado_verifica = mysql_query($sql_verifica) or die(mysql_error());
    $row_verifica = mysql_fetch_array($resultado_verifica);
    $flag_atual = $row_verifica[0];

    if ($flag_atual != 0) {
        // Pedido já foi processado anteriormente
        echo "<script language='javascript' type='text/javascript'>alert('Este pedido já foi processado anteriormente!');window.location=\"../aprovarcotaescalonada.php\"</script>";
        exit();
    }

    // ============================================
    

    $sql = "SELECT valor FROM bdfrota.tbgerente where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = $row[0];



    if ($decisao == 2) {

        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovarcotaescalonada.php\"</script>";

        } else {

            $sql = "SELECT placa, codempresa FROM bdfrota.tbveiculo tv join bdaniel.tbfuncionario tf on tv.matcond = tf.matricula where matcond = '$matriculaf'";
            $resultado = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_array($resultado);
            $placa = $row[0];
            $codempresa = $row[1];

            $resultado = inserirSaldo($codempresa, $placa, $valor);

            if (!$resultado) {
                // Nota: Este erro frequentemente ocorre quando há divergência entre a localização 
                // atual do colaborador e o cadastro da operadora do cartão. Por exemplo: o colaborador 
                // está em SP mas o cartão foi cadastrado para a região Vale Card. É necessário 
                // verificar a localização do colaborador em tbfuncionario (campo 'estado') e confirmar 
                // se o cartão está associado à operadora correta para essa região.
                echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro de comunicação com a operadora do cartão. Tente novamente mais tarde. Caso o problema persista, verifique o cadastro do colaborador e entre em contato com a frota.',
                        confirmButtonText: 'Voltar'
                    }).then(() => {
                        window.location=\"../aprovarcotaescalonada.php\"
                    });
                </script>
                ";
                exit;
            } else {

                $acao = 'Aceitou cota extra';
                $flag = '2';
                $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valor);

                $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valor where idtbpedidostec = $id";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade) SELECT matricula, '$matricula', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmproj', '$kmos', '$kmsem', '$valor_desc', '$valor_ant', '$totextra', '2' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbgerente SET valor = valor - $valor where matricula = '$matricula'";
                $resultado = mysql_query($sql) or die(mysql_error());

                echo "
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Saldo inserido com sucesso.',
                        confirmButtonText: 'Voltar'
                    }).then(() => {
                        window.location=\"../aprovarcotaescalonada.php\"
                    });
                </script>
                ";
                exit;
            }
        }

    } else {
        $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());
        $acao = 'N�o aceitou cota extra';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

        echo "<script language='javascript' type='text/javascript'>alert('Pedido n�o Aceito!');window.location=\"../aprovarcotaescalonada.php\"</script>";
    }
    ?>
</body>