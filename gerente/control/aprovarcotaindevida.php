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
    $cota_validade = '1';
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
    $justsup = $_POST["justsup"];

    if ($valor == '') {
        $valor = $orcpedido;
    }




    $sql = "SELECT 
                coord.valor, coord.matricula
            FROM
                bdfrota.tbcoord coord
                    JOIN
                bdfrota.tbsupervisor sup ON coord.idtbcoordenador = sup.idtbcoordenador
                    JOIN
                bdfrota.tbusuario u ON u.idtbsupervisor = sup.idtbsupervisor
            where u.matricula =  '$matriculaf'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = $row[0];
    $matsup = $row[1];



    if ($decisao == 2) {

        if ($valor > $saldosup) {

            echo "<script language='javascript' type='text/javascript'>alert('O Saldo do coordenador Insuficiente!');window.location=\"../aprovarcotaescalonada.php\"</script>";

        } else {

            $sql = "SELECT placa, codempresa FROM bdfrota.tbveiculo tv join bdaniel.tbfuncionario tf on tv.matcond = tf.matricula where matcond = '$matriculaf'";
            $resultado = mysql_query($sql) or die(mysql_error());
            $row = mysql_fetch_array($resultado);
            $placa = $row[0];
            $codempresa = $row[1];


            $resultado = inserirSaldo($codempresa, $placa, $valor);

            if (!$resultado) {
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro de comunicação com a operadora do cartão, tente novamente mais tarde, caso o erro persista favor entrar em contato com a frota.',
                            confirmButtonText: 'Voltar'
                        }).then(() => {
                            window.location=\"../aprovarcotaescalonada.php\"
                        });
                    </script>
                    ";
                exit;
            } else {

                $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valor where idtbpedidostec = $id";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "INSERT INTO bdfrota.tbcotaextraac (matricula, matsup, valor, justificativa, data, flag, dataaceite, dir, valorinserido, kmhodometro, orcsemanal, kmproj, orcsematual, kmprojatual, kmos, gps, valordescontado, sldcartao, totalextra, cota_validade, descontar, valdesc, justsup, idtbpedidostec, forahorario, foraescala) SELECT matricula, '$matsup', valor, justificativa, data, '$decisao', '$hoje', dir, valorinserido, kmhodometro, '$orcsem', '$kmprojini', '$orcsematu', '$kmproj', '$kmos', '$kmsem', '$valorainserir_desc', '$valorainserir_ant', '$totextra', '$cota_validade', 'desc', '$valdesc', '$justsup', '$idtbpedidostec', '$forahorario', '$foraescala' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
                //echo "$sql <br>";
    
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbsaldo SET saldo = saldo + $valor, valoraplicado = valoraplicado + $valor, totalextra = totalextra + $valor where idtbsaldo = '$idtbsaldo'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbsaldo SET kmproj = ROUND(( kmorcsem / orcsemanal ) * valoraplicado, 2) where idtbsaldo = '$idtbsaldo'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $sql = "UPDATE bdfrota.tbcoord SET valor = valor - $valor where matricula = '$matsup'";
                $resultado = mysql_query($sql) or die(mysql_error());

                $acao = 'Aceitou cota extra Escalonada';
                $flag = '2';
                $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valor);

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
        $acao = 'Não aceitou cota extra escalonada';
        $flag = '2';
        $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $valor_ant, $valor);

        echo "<script language='javascript' type='text/javascript'>alert('Pedido não Aceito!');window.location=\"../aprovarcotaescalonada.php\"</script>";
    }

    ?>
</body>