<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');
session_start();

$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];

if ($perfil == null) {
    echo "<script>alert('Você deve logar para ter acesso');window.location='index.php'</script>";
    exit;
} elseif ($perfil == '0') {
    echo "<script>alert('Você não tem permissão');window.location='index.php'</script>";
    exit;
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

    <title>Relatório de Inativos no Ponto</title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
    <link href="src/css/styles.css" rel="stylesheet" />

    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">

    <?php include "menu.php"; ?>

    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4">
                <div id="loading" class="text-center mt-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3">
                        Gerando relatório, aguarde...
                    </p>
                </div>

                <div id="conteudo" style="display:none;">
                    <h1 class="mt-4">
                        <i class="fas fa-user-clock"></i>
                        Relatório de Condutores Inativos no Ponto
                    </h1>

                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">
                            Condutores sem registro de ponto nos últimos 3 dias úteis
                        </li>
                    </ol>

                    <!-- Cards -->
                    <div class="row">

                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">
                                    <h2 id="totalInativos">0</h2>
                                    Condutores Inativos
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span>Total encontrado</span>
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">
                                    <h2 id="totalSupervisores">0</h2>
                                    Supervisores Impactados
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span>Com equipes afetadas</span>
                                    <i class="fas fa-user-tie"></i>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">
                                    <h2 id="dataRelatorio">--/--/----</h2>
                                    Data do Relatório
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span>Atualização automática</span>
                                    <i class="fas fa-calendar"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tabela -->
                    <div class="card mb-4">

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-table me-1"></i>
                                Condutores sem registro de ponto
                            </div>

                            <button class="btn btn-success btn-sm" onclick="exportarExcel()">
                                <i class="fas fa-file-excel"></i>
                                Exportar Excel
                            </button>
                        </div>

                        <div class="card-body">

                            <table id="datatablesSimple">

                                <thead>
                                    <tr>
                                        <th>Matrícula</th>
                                        <th>Nome</th>
                                        <th>Status</th>
                                        <th>Placa</th>
                                        <th>Centro de Custo</th>
                                        <th>Supervisor</th>
                                        <th>Coordenador</th>
                                        <th>Gerente</th>
                                        <th>Dias sem Registro</th>
                                    </tr>
                                </thead>

                                <tbody id="tbodyRelatorio"></tbody>

                            </table>

                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
    <script>

        let tabela = null;
        let dadosRelatorio = [];
        async function carregarRelatorio() {

            try {

                const response = await fetch('./control/api_relatorio_condutores.php');

                const resultado = await response.json();
                dadosRelatorio = resultado.dados;
                document.getElementById('totalInativos').innerText =
                    resultado.totalInativos;

                document.getElementById('totalSupervisores').innerText =
                    resultado.totalSupervisores;

                document.getElementById('dataRelatorio').innerText =
                    resultado.dataRelatorio;

                const tbody = document.getElementById('tbodyRelatorio');

                resultado.dados.forEach(colaborador => {

                    const dias = (colaborador.dias_sem_ponto || [])
                        .map(data => {
                            const [ano, mes, dia] = data.split('-');
                            return `${dia}/${mes}/${ano}`;
                        })
                        .join('<br>');

                    tbody.innerHTML += `
                <tr>
                    <td>${colaborador.matricula ?? ''}</td>
                    <td>${colaborador.nome ?? ''}</td>
                    <td>${colaborador.status ?? ''}</td>
                    <td>${colaborador.placa ?? ''}</td>
                    <td>${colaborador.ccusto ?? ''}</td>
                    <td>${colaborador.supervisor_nome ?? ''}</td>
                    <td>${colaborador.coordenador_nome ?? ''}</td>
                    <td>${colaborador.gerente_nome ?? ''}</td>
                    <td>${dias}</td>
                </tr>
            `;
                });

                document.getElementById('loading').style.display = 'none';
                document.getElementById('conteudo').style.display = 'block';

                tabela = new simpleDatatables.DataTable(
                    document.getElementById('datatablesSimple'),
                    {
                        searchable: true,
                        fixedHeight: true,
                        perPage: 25,
                        labels: {
                            placeholder: "Pesquisar...",
                            perPage: "{select} registros por página",
                            noRows: "Nenhum registro encontrado",
                            info: "Mostrando {start} até {end} de {rows} registros"
                        }
                    }
                );



            } catch (erro) {

                document.getElementById('loading').innerHTML = `
            <div class="alert alert-danger">
                Erro ao carregar relatório.
            </div>
        `;

                console.error(erro);
            }
        }

        window.addEventListener('DOMContentLoaded', carregarRelatorio);
        function exportarExcel() {

            if (!dadosRelatorio.length) {
                alert("Nenhum dado para exportar.");
                return;
            }

            const dados = dadosRelatorio.map(colaborador => ({
                "Matrícula": colaborador.matricula ?? "",
                "Nome": colaborador.nome ?? "",
                "Placa": colaborador.placa ?? "",
                "Centro de Custo": colaborador.ccusto ?? "",
                "Supervisor": colaborador.supervisor_nome ?? "",
                "Coordenador": colaborador.coordenador_nome ?? "",
                "Gerente": colaborador.gerente_nome ?? "",
                "Dias sem Registro": (colaborador.dias_sem_ponto || [])
                    .map(data => {
                        const [ano, mes, dia] = data.split("-");
                        return `${dia}/${mes}/${ano}`;
                    })
                    .join(" | ")
            }));

            const worksheet = XLSX.utils.json_to_sheet(dados);

            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Inativos");

            XLSX.writeFile(
                workbook,
                `Relatorio_Condutores_Inativos_${new Date().toISOString().slice(0, 10)}.xlsx`
            );
        }
    </script>
</body>

</html>