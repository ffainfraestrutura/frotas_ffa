<?php
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
// Defina o tempo de vida da sessão para 1 hora
$lifetime = 3600; // 1 hora em segundos

// Configurar os parâmetros do cookie da sessão
session_set_cookie_params($lifetime);

// Iniciar a sessão
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
$tipo = $_SESSION['tipo'];

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo'] = $tipo;

//include"conecta.php";
include "../conecta2.php";
//include_once"./func/log.php";
header("Content-type: text/html; charset=utf-8");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aws-sdk/2.1040.0/aws-sdk.min.js"></script>
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.tailwindcss.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/searchbuilder/1.6.0/css/searchBuilder.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../src/css/style.css" rel="stylesheet">
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="FFA" />
    <meta name="author" content="FFA" />
    <link rel="icon" type="image/png" href="../src/images/favicon.png" />
    <title>Portal FFA</title>
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

    <!--link Material Symbols - simbolos do input-->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link href="../src/css/styles.css" rel="stylesheet" />

</head>

<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">
    <!--nav-->
    <?php include "menu-teste.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <h1 class="h1 mt-2"> Relatório Cota Projetada x Cota Média Gasta </h1>

                <!--tabela para entrada de dados-->
                <table id='relbaixas' class='mt-4 table table-bordered'>
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nome</th>
                            <th>Data</th>
                            <th>Média Semanal de Gasto</th>
                            <th>Cota Projetada</th>
                            <th>Diferença</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php

                        $sql = "select tb_abastecimento.*, round(abs(media_semanal - sldprjtd), 2) as diferenca ,round(sldprjtd, 2) as sldprjtd from (SELECT
                            round(sum(valor)/3, 2) as media_semanal,
                            a.matricula,
                            tbf.nome,
                            b.min_data,
                            b.max_data
                            FROM
                              bdfrota.tbabastecimento a 
                              join bdaniel.tbfuncionario tbf 
                              on a.matricula = tbf.matricula, 
                              (SELECT  max(data) as max_data,
                               date_sub(max(data), interval 21 day) as min_data 
                              from bdfrota.tbsaldo) as b 
                              where a.data >= b.min_data and a.data <= date_sub(max_data, interval 1 day)
                              group by matricula) as tb_abastecimento 
                              join bdfrota.tbsaldo as saldo 
                              on tb_abastecimento.matricula = saldo.matricula 
                              where saldo.data = tb_abastecimento.max_data;";
                        $resultado = mysqli_query($conexao, $sql) or die(mysqli_error($conexao));
                        while ($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)) {
                            ?>
                            <tr>
                                <td>
                                    <?= $row["matricula"] ?>
                                </td>
                                <td>
                                    <?= $row["nome"] ?>
                                </td>
                                <td>
                                    <?= $row["max_data"] ?>
                                </td>
                                <td>
                                    <?= $row["media_semanal"] ?>
                                </td>
                                <td>
                                    <?= $row["sldprjtd"] ?>
                                </td>
                                <td>
                                    <?= $row["diferenca"] ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>

                    </tbody>
                </table>




            </div>


        </main>

        <footer class="py-4 bg-light mt-auto" style="bottom: 0; start: 0; width: 100%;">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
                    <!--<div>
                <a href="#">Privacy Policy</a>
                                &middot;
                <a href="#">Terms &amp; Conditions</a>
            </div>!-->
                </div>
            </div>
        </footer>

    </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/searchbuilder/1.6.0/js/dataTables.searchBuilder.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript"
        src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script>
        $(document).ready(function () {
            var dataTable = new DataTable('#relbaixas', {
                columns: [
                    { title: 'Matrícula' },
                    { title: 'Nome' },
                    { title: 'Data' },
                    { title: 'Média Semanal de Gasto' },
                    { title: 'Cota Projetada' },
                    { title: 'Diferença' }
                    // Adicione títulos para cada coluna conforme necessário
                ],
                columnDefs: [
                    { type: 'num', targets: [3, 4, 5] }, // colunas numéricas
                    { type: 'date', targets: [2] }, // coluna de data
                    { type: 'string', targets: [0, 1] },
                ],

                dom: 'fBr<"toolbar h-min mt-2 gap-4 flex-row md:flex justify-end">tilp',
                language: {
                    info: "Mostrando _START_ até _END_ de _TOTAL_ entradas",
                    search: '',
                    searchPlaceholder: '   Pesquisar',
                    lengthMenu: 'Mostrar _MENU_ resultados por página',
                    paginate: {
                        previous: 'Anterior',
                        next: 'Próximo'
                    },
                    searchBuilder: {
                        conditions: {
                            string: {
                                equals: "Igual a",
                                not: "Diferente de",
                                contains: 'Contém',
                                notContains: 'Não Contém',
                                startsWith: "Começa Com",
                                notStartsWith: "Não Começa Com",
                                endsWith: "Termina Com",
                                notEndsWith: "Não Termina Com",
                                empty: "Nulo",
                                notEmpty: "Não Nulo"
                            },
                            html: {
                                equals: "Igual a",
                                not: "Diferente de",
                                contains: 'Contém',
                                notContains: 'Não Contém',
                                startsWith: "Começa Com",
                                notStartsWith: "Não Começa Com",
                                endsWith: "Termina Com",
                                notEndsWith: "Não Termina Com",
                                empty: "Nulo",
                                notEmpty: "Não Nulo"
                            },
                            date: {
                                equals: "Igual A",
                                not: "Diferente De",
                                before: 'Antes De',
                                after: 'Após',
                                between: "No Período de",
                                notBetween: "Fora Do Período",
                                empty: "Nulo",
                                notEmpty: "Não Nulo"
                            },
                            number: {
                                equals: "Igual A",
                                not: "Diferente De ",
                                lt: 'Menor Que',
                                lte: 'Menor Ou Igual A',
                                gt: 'Maior Que',
                                gte: 'Maior Ou Igual A',
                                between: "Está Entre",
                                notBetween: "Não Está Entre",
                                empty: "Nulo",
                                notEmpty: "Não Nulo"
                            },
                        },
                        button: 'Filtrar <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="none" style="display: inline-block;">' +
                            '<path d="M15 15L21 21M21 15L15 21M10 21V14.6627C10 14.4182 10 14.2959 9.97237 14.1808C9.94787 14.0787 9.90747 13.9812 9.85264 13.8917C9.7908 13.7908 9.70432 13.7043 9.53137 13.5314L3.46863 7.46863C3.29568 7.29568 3.2092 7.2092 3.14736 7.10828C3.09253 7.01881 3.05213 6.92127 3.02763 6.81923C3 6.70414 3 6.58185 3 6.33726V4.6C3 4.03995 3 3.75992 3.10899 3.54601C3.20487 3.35785 3.35785 3.20487 3.54601 3.10899C3.75992 3 4.03995 3 4.6 3H19.4C19.9601 3 20.2401 3 20.454 3.10899C20.6422 3.20487 20.7951 3.35785 20.891 3.54601C21 3.75992 21 4.03995 21 4.6V6.33726C21 6.58185 21 6.70414 20.9724 6.81923C20.9479 6.92127 20.9075 7.01881 20.8526 7.10828C20.7908 7.2092 20.7043 7.29568 20.5314 7.46863L17 11" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>' +
                            '</svg>',
                        condition: 'Condição',
                        data: 'Coluna',
                        add: 'Adicionar Condição',
                        clearAll: 'Desfazer tudo',
                        delete: 'Deletar',
                        deleteTitle: 'Deletar Título',
                        logicAnd: 'E',
                        logicOr: 'OU',
                        title: {
                            0: 'Filtrar Tabela',
                            _: 'Filtrar Tabela (%d)'
                        },
                        value: 'Valor',
                        valueJoiner: 'até'
                    }
                },
                buttons: [
                    'searchBuilder'
                ],
            });
            document.getElementById("relbaixas").classList += "w-full overflow-x-auto whitespace-nowrap";
            $("#relbaixas").wrap("<div style='overflow-x:auto; width:100%; height:100%; position:relative;'></div>");
            filterContainer = document.createElement('div');
            filterContainer.classList = "flex flex-wrap justify-between items-center w-full";
            relbaixas_wrapper = document.getElementById("relbaixas_wrapper");
            relbaixas_filter = document.getElementById("relbaixas_filter");
            filterButton = document.getElementsByClassName("dt-buttons")[0];
            filter = document.getElementsByClassName("dt-button")[0];
            relbaixas_wrapper.insertBefore(filterContainer, relbaixas_wrapper.children[0]);
            filterContainer.appendChild(relbaixas_filter);
            filterContainer.appendChild(filterButton);
            filter.classList = "bg-blue-600 hover:bg-blue-500 rounded py-3 px-4 text-gray-100 font-bold text-md";
            document.getElementById("relbaixas_filter").classList.add("md:w-3/6");
            document.getElementById("relbaixas_filter").classList.add("w-full");
            document.getElementById("relbaixas_filter").classList.add("mt-5");
            document.getElementById("relbaixas_filter").classList.add("md:mt-0");
            document.querySelector('div.toolbar').innerHTML = `

                <div class="flex-row">
                    <button id="exportBtn" class="bg-green-500 hover:bg-green-400 font-bold text-gray-100 py-3 px-6 rounded-lg">
                        Exportar para Excel
                    </button>
                </div>`;

            $('#exportBtn').on('click', function () {
                exportToExcel(dataTable);
            });
        });

        function exportToExcel(dataTable) {
            // Obtém todos os dados da DataTable
            var data = dataTable.rows().data().toArray();

            // Obtém as configurações das colunas da DataTable
            var columns = dataTable.settings().init().columns;

            // Mapeia os títulos das colunas
            var headers = columns.map(function (column) {
                return column.title !== undefined && column.title !== null ? column.title : '';
            });

            // Mapeia os dados para um array de objetos com chaves correspondentes aos títulos das colunas
            var formattedData = data.map(function (row) {
                var rowData = {};
                row.forEach(function (value, index) {
                    rowData[headers[index]] = value;
                });
                return rowData;
            });

            // Cria uma planilha Excel
            var ws = XLSX.utils.json_to_sheet(formattedData);

            // Cria um livro Excel
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

            // Gera um nome de arquivo
            var fileName = 'Projetadas_x_GastosSemanais.xlsx';

            // Salva o arquivo Excel
            XLSX.writeFile(wb, fileName);
        }


    </script>

</body>

</html>