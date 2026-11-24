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
    $ccusto = $_SESSION['ccusto'];
    $_SESSION['ccusto'] = $ccusto;
    $desc = 'nao';
    $matriculaf = $_POST['matriculatec'];
    $id = $_POST['id'];
    $idtbsaldo = $_POST['idtbsaldo'];
    $decisao = $_POST['decisao'];
    $valor = $_POST["valor"];
    $valor = str_replace(",", ".", $valor);
    $valorainserir = $valor;
    $valor_ant = $_POST["valor_ant"];
    $valor_ant = str_replace(",", ".", $valor_ant);
    if ($valor_ant >= 40) {

        echo "<script language='javascript' type='text/javascript'>alert('Colaborador possui saldo no cartão, não é possível inserir saldo solicitado');window.location=\"../aprovarcota.php\"</script>";
        die();

    }
    $orcpedido = $_POST["orcpedido"];
    $orcpedido = str_replace(",", ".", $orcpedido);
    $tipocota = $_POST["tipocota"];
    $valor_desc = $valor;
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
    //echo "$valor -<br>";
    $justsup = str_replace("'", "", $justsup);
    $desc = $_POST["descontocombustivel"];
    $foraescala = $_POST["foraescala"];
    if ($foraescala == 'Sim') {
        $foraescala = '1';
    } else {
        $foraescala = '0';
    }
    $forahorario = $_POST["forahorario"];
    if ($forahorario == 'Sim') {
        $forahorario = '1';
    } else {
        $forahorario = '0';
    }
    if ($valor == '') {
        $valor = $orcpedido;
    }
    if ($kmproj > $kmos or $justsup <> '') {
        $cota_validade = '1';
    } else {
        $cota_validade = '0';
    }
    $sqlaux = "SELECT ccusto, codempresa FROM bdaniel.tbfuncionario WHERE matricula = '$matriculaf'";
    $resultadoaux = mysql_query($sqlaux) or die(mysql_error());
    $rowaux = mysql_fetch_array($resultadoaux);
    $ccusto = $rowaux[0];
    $codemptec = $rowaux[1];

    // Valor máximo padrão
    $max = 51;

    // Verifica se o centro de custo indica rede externa ou similar
    if (
        stripos(trim($ccusto), 'rede externa') !== false ||
        stripos(trim($ccusto), 'implanta') !== false ||
        stripos(trim($ccusto), 'obra') !== false
    ) {
        $max = 81;
    }

    // Se o valor ultrapassar o limite, exibe a mensagem e redireciona
    if ($valor > $max) {
        // O supervisor tem autonomia até R$ ($max - 1), conforme a regra anterior
        $autonomia = $max - 1;
        echo "<script language='javascript' type='text/javascript'>
            alert('Prezado supervisor. Sua autonomia para aprovação de cota extra é de até R\$ {$autonomia},00 - cota extra escalonada para a coordenação.');
            window.location=\"../aprovarcota.php\"
          </script>";
        die();
    }
    $kmprojini = $_POST["kmprojini"];
    $orcsematu = $_POST["orcsematu"];
    $orcsematu = str_replace(",", ".", $orcsematu);
    if ($orcsematu == 0 or $orcsematu == '') {
        $orcsematu = $orcsem + $totextra;
    }
    $valdesc = $valor;

    if ($desc == 'nao') {

        $sql = "UPDATE bdfrota.tbpedidostec SET valsup = '$valor', justsup = '$justsup' where idtbpedidostec = $id";
        $resultado = mysql_query($sql) or die(mysql_error());
        echo "<script language='javascript' type='text/javascript'>window.location=\"./escalonarcotaindevida.php?idpedido=$id\"</script>";
        exit();
    }

    $sql = "SELECT valor FROM bdfrota.tbcoord where matricula = '$matricula'";
    $resultado = mysql_query($sql) or die(mysql_error());
    $row = mysql_fetch_array($resultado);
    $saldosup = $row[0];

    if ($valor > $saldosup) {
        echo "<script language='javascript' type='text/javascript'>alert('Saldo Insuficiente!');window.location=\"../aprovarcota.php\"</script>";
    }

    if ($desc == 'sim') {

        // Busca a placa do veículo do técnico
        $sql = "SELECT placa FROM bdfrota.tbveiculo where matcond = '$matriculaf'";
        $resultado = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_array($resultado);
        $placa = $row[0];

        // Consulta o saldo atual do cartão
        $cartaotec = consultarCartoes($placa, null, $codemptec);
        $placaNormalizada = strtoupper(trim($placa));
        $placaNormalizada = str_replace("-", "", $placaNormalizada);
        $saldocartao = null;

        if (isset($cartaotec[$codemptec])) {
            if (isset($cartaotec[$codemptec][$placaNormalizada])) {
                $saldocartao = $cartaotec[$codemptec][$placaNormalizada]['saldo'];
            }
        }

        // Se não conseguiu obter o saldo, define como 0 para o log
        if ($saldocartao === null) {
            $saldocartao = 0;
        }

        $resultado = inserirSaldo($codemptec, $placa, $valorainserir);

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

            $sql = "UPDATE bdfrota.tbpedidostec SET flag = $decisao, tipocota = '$tipocota', dataplantao = '$dataplantao', valorinserido = $valorainserir where idtbpedidostec = $id";
            //echo "$sql <br>";
            $resultado = mysql_query($sql) or die(mysql_error());


            $sql = "INSERT INTO bdfrota.tbcotaextraindevida (matricula, valdesc, idpedidotec, matsup, parcial) SELECT matricula, '$valdesc', '$id', '$matricula', '0' FROM bdfrota.tbpedidostec WHERE idtbpedidostec = $id";
            //echo "$sql <br>";
            $resultado = mysql_query($sql) or die(mysql_error());


            $sql = "UPDATE bdfrota.tbpedidostec SET desctec = '1' where idtbpedidostec = $id";
            //echo "$sql <br>";
            $resultado = mysql_query($sql) or die(mysql_error());

            $acao = 'desconto gerado';
            $flag = '2';
            $logresult = enviarlog($hoje, $acao, $flag, $matriculaf, $matricula, $saldocartao, $valdesc);

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
    } else {
        echo "<script language='javascript' type='text/javascript'>alert('Erro na justificativa');window.location=\"../aprovarcota.php\"</script>";
    }
    ?>
</body>