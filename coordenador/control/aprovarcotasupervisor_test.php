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
    $valor = $_POST["valor"];
    $valor = str_replace(",", ".", $valor);
    $valor_ant = $_POST["valor_ant"];
    $valor_ant = str_replace(",", ".", $valor_ant);
    $orcpedido = $_POST["orcpedido"];
    $orcpedido = str_replace(",", ".", $orcpedido);
    $tipocota = $_POST["tipocota"];
    $dataplantao = $_POST["dataplantao"];

    if ($valor == '') {
        $valor = $orcpedido;
    }




    $sql = "SELECT valor FROM bdfrota.tbcoord where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = $row[0];



    if ($decisao == 2) {

        if ($valor >= $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovarcota.php\"</script>";

        } else {

            $sql = "SELECT tv.placa, tf.codempresa FROM bdfrota.tbveiculo tv join bdaniel.tbfuncionario tf on tv.matcond = tf.matricula where matcond = '$matriculaf'";
            $resultado = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_array($resultado);
            $placa = $row[0];
            $codempresatec = $row[1];

            $placaNormalizada = strtoupper(trim($placa));
            $placaNormalizada = str_replace("-", "", $placaNormalizada);
            $saldo = null;

            $resultado = inserirSaldo($codempresatec, $placa, $valor);

            if (!$resultado) {
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro de comunicação com a operadora do cartão, tente novamente mais tarde, caso o erro persista favor entrar em contato com a frota.',
                            confirmButtonText: 'Voltar'
                        }).then(() => {
                            window.location=\"../aprovarcota.php\"
                        });
                    </script>
                    ";
                exit;
            } else {
                $sql = "UPDATE bdfrota.tbpedidossupcota SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidossupcota = $id";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, valorinserido) SELECT matricula, '$matricula', $valor, justificativa, data, '$decisao', '$hoje', $valor FROM bdfrota.tbpedidossupcota WHERE idtbpedidossupcota = $id";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbsupervisor SET saldo = saldo + $valor, totcotaextra = totcotaextra + $valor where matricula = '$matriculaf'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbcoord SET valor = valor - $valor where matricula = '$matricula'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $acao = 'Aceitou cota extra';
                $flag = '2';
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
        $sql = "UPDATE bdfrota.tbpedidossupcota SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao' where idtbpedidossupcota = $id";
        $resultado = mysql_query($sql) or die(mysql_error());
        $acao = 'Não aceitou cota extra';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

        echo "<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../aprovarcota.php\"</script>";
    }
    ?>
</body>