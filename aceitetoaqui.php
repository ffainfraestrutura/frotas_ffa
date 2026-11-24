<?php
// Configurações de localidade e timezone
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

// Inicia sessão
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$filial = $_SESSION['filial'];

// Verifica se o usuário está logado
if ($perfil == null) {
    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '1') {
    echo "<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}

// Define variáveis de sessão
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['filial'] = $filial;

$data = date('Y-m-d');

// Inclui arquivos de conexão e funções
include "conecta.php";
include_once "./func/log.php";

// Calcula as datas da semana atual
if (strftime('%A') == "domingo") {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d');
} elseif (strftime('%A') == "segunda") {
    $segunda = date('Y-m-d');
    $domingo = date('Y-m-d', strtotime('next sunday'));
} else {
    $segunda = date('Y-m-d', strtotime('last monday'));
    $domingo = date('Y-m-d', strtotime('next sunday'));
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="src/images/favicon.png" />
    <title>Aceite To Aqui</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
</head>

<body class="sb-nav-fixed vsc-initialized">

    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">
        <main style="width: 100%;">
            <div class="container-fluid px-4" style="width: 100%;">
                <h1 class="h1 pt-2 pb-2"> Aprovação de Rota <?php
                $sql = "SELECT idtbsupervisor FROM bdfrota.tbsupervisor where matricula = '$matricula'";
                $resultado = mysql_query($sql) or die(mysql_error());
                $row = mysql_fetch_array($resultado);
                $idtbsupervisor = $row[0];

                if ($idtbsupervisor == '') {
                    echo "<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
                }

                $sql = "SELECT * FROM bdfrota.tbcotaextraindevida where matricula in (SELECT matricula FROM bdfrota.tbusuario where idtbsupervisor = '$idtbsupervisor') and dataac >= '$data 00:00:00'";
                $resultado = mysql_query($sql) or die(mysql_error());
                $row = mysql_fetch_array($resultado);
                if (mysql_num_rows($resultado) >= 1) {
                    print "<a href='./cotasdescontosup.php' style='text-decoration: none;'>
                <img src='./src/images/email.svg' alt='Ícone de e-mail' class='mb-2 ms-2' style='height: 2em; vertical-align: middle;'>
                </a>";
                }
                ?>
                </h1>
                <!-- <div class="mb-3">
                    <a href="converter-latlong-toaqui.php" class="btn btn-outline-primary btn-sm">Converter latitude/longitude sem endereço</a>
                </div> -->
                <div id="containertab">
                    <table id="datatablesSimple" class="table table-striped table-bordered"
                        style="font-size: 12px; width:100%">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th style="white-space: nowrap;">Nome</th>
                                <th>Ver Detalhes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT a.matricula, b.nome, COUNT(c.idtbtoaqui) as total 
                        FROM bdfrota.tbusuario a 
                        INNER JOIN bdaniel.tbfuncionario b ON a.matricula = b.matricula 
                        INNER JOIN bdfrota.tbtoaqui c ON a.matricula = c.matricula 
                        WHERE a.idtbsupervisor = $idtbsupervisor 
                        AND c.aceite = 0 
                        GROUP BY a.matricula, b.nome";
                            $resultado = mysql_query($sql) or die(mysql_error());
                            while ($row = mysql_fetch_array($resultado)) {
                                $matriculatec = $row[0];
                                $nometec = $row[1];
                                echo "<tr>
                            <td>$matriculatec</td>
                            <td style=\"white-space: nowrap;\">$nometec</td>
                            <td><button class='btn btn-outline-info' onclick='verDetalhes(\"$matriculatec\", \"$nometec\")'>Ver Detalhes</button></td>
                          </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detalhesModal" tabindex="-1" aria-labelledby="detalhesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detalhesModalLabel">Detalhes de "To Aqui"</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="detalhesForm" action='./control/aceitetoaquinh.php' method='post'>
                        <input type="hidden" name="matricula" id="matricula">
                        <table id="detalhesTable" class="table table-striped table-bordered"
                            style="font-size: 12px; width:100%">
                            <thead>
                                <tr>
                                    <th>Seleção</th>
                                    <th>Motivo</th>
                                    <th>Endereço</th>
                                    <th>OBS:</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Os detalhes serão carregados aqui via JavaScript -->
                            </tbody>
                        </table>
                        <button type="submit" name="decisao" value="1" class="btn btn-outline-success">Aceitar
                            Selecionado</button>
                        <button type="submit" name="decisao" value="2" class="btn btn-outline-danger">Rejeitar
                            Selecionado</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

        function verDetalhes(matricula, nome) {
            $('#detalhesModalLabel').text('Detalhes de "To Aqui" - ' + nome);
            $('#matricula').val(matricula);
            $('#detalhesTable tbody').empty();

            $.ajax({
                url: 'get_detalhes.php',
                method: 'GET',
                data: { matricula: matricula },
                dataType: 'json',
                success: function (response) {
                    response.forEach(function (item) {
                        $('#detalhesTable tbody').append(
                            '<tr>' +
                            '<td><input type="checkbox" name="aceite[]" value="' + item.idtbtoaqui + '"></td>' +
                            '<td>' + item.motivo + '</td>' +
                            '<td id="endereco-toaqui-' + item.idtbtoaqui + '">' + montarEnderecoToAqui(item) + '</td>' +
                            '<td>' + item.obs + '</td>' +
                            '<td>' + item.hora + '</td>' +
                            '</tr>'
                        );
                    });
                    $('#detalhesModal').modal('show');
                }
            });
        }

        function montarEnderecoToAqui(item) {
            if (item.endereco && item.endereco.trim() !== '') {
                return item.endereco;
            }

            if (item.latitude && item.longitude) {
                return '<button type="button" class="btn btn-outline-primary btn-sm" onclick="converterEnderecoToAqui(' + item.idtbtoaqui + ', this)">Visualizar endereço</button>';
            }

            return '<span class="text-danger">Sem coordenada para converter</span>';
        }

        function converterEnderecoToAqui(idtbtoaqui, botao) {
            const textoOriginal = $(botao).text();
            $(botao).prop('disabled', true).text('Convertendo...');

            $.ajax({
                url: './control/converter_endereco_toaqui.php',
                method: 'POST',
                data: { idtbtoaqui: idtbtoaqui },
                dataType: 'json',
                success: function (response) {
                    if (response.sucesso) {
                        $('#endereco-toaqui-' + idtbtoaqui).text(response.endereco);
                    } else {
                        alert(response.mensagem);
                        $(botao).prop('disabled', false).text(textoOriginal);
                    }
                },
                error: function () {
                    alert('Erro ao converter endereço. Tente novamente.');
                    $(botao).prop('disabled', false).text(textoOriginal);
                }
            });
        }

        const limite = 1;

        $(document).on('change', 'input[name="aceite[]"]', function () {
            const selecionados = $('input[name="aceite[]"]:checked').length;

            if (selecionados > limite) {
                alert("Você só pode selecionar até " + limite + " registros.");
                this.checked = false;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="src/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>
    <script src="src/js/datatables-simple-demo.js"></script>
</body>

</html>