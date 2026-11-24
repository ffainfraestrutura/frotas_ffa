<?php
    date_default_timezone_set('America/Sao_Paulo');
    session_start();

    $nome = $_SESSION['nome'];
    $usuariof = $_SESSION['usuario'];
    $matricula = $_SESSION['matricula'];
    $perfil = $_SESSION['perfil'];
    $tipo=$_SESSION['tipo'];

    if ($perfil == null){
    echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"../index.php\"</script>";
    } elseif ($perfil == ''){
    echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
    }

    $_SESSION['perfil'] = $perfil;
    $_SESSION['matricula'] = $matricula;
    $_SESSION['nome'] = $nome;
    $_SESSION['usuario'] = $usuariof;
    $_SESSION['tipo']=$tipo;

    include_once"../func/log.php";
    include"conecta.php";
    //include_once"../func/log.php";
    header("Content-type: text/html; charset=utf-8");

    $hoje = date("Y-m-d");//H:i:s
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="FFA" />
        <meta name="author" content="FFA" />
        <link rel="icon" type="image/png" href="../src/images/favicon.png"/>
        <title>Meus Chamados</title>        
        
        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

        <!--data tables - css -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

        <!-- estilos próprios -->
        <link href="../src/css/styles.css" rel="stylesheet" />
    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"../menu.php"; ?>

        <div id="layoutSidenav_content">
            <main class="mb-3">

                <div class="container-fluid" style="width:100%;">
                    <h1 class="h1 pt-1"> Meus Chamados </h1>

                    <!--div tabela-->
                    <div class="mt-4">
                        <table id='tabela1' class="table table-striped">
                            <thead>
                                <tr>
                                    <th> </th>
                                    <th>Placa: </th>
                                    <th>Data de abertura:</th>
                                    <th>Status:</th>
                                    <th>Categoria:</th>
                                    <th>Problema:/Endereço (para gincho): </th>
                                    <th>Resposta:</th>
                                    <th>Data de fechamento:</th>
                                </tr>
                            </thead>

                            <tbody>
                                
                            <?php
                            $sql="SELECT * FROM bdfrota.tbchamados WHERE nome='$nome' ORDER BY data_abert desc;";
                            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                            //idtbchamados, matricula, nome, telefone, ccusto, placa, hodometro, tipodechamado, tiposinistro, tipomanuprev, prevcord, tipomanucor, status, ciente, data_abert, data_fech
                            while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
                                $numero = $row['idtbchamados'];
                                $matricula = $row['matricula'];
                                $nome = $row['nome'];
                                $telefone = $row['telefone'];
                                $ccusto = $row['ccusto'];
                                $placa=$row['placa'];
                                $hodometro=$row['hodometro'];
                                $tipodechamado=$row['tipodechamado'];
                                $tiposinistro=$row['tiposinistro'];
                                $tipomanuprev=$row['tipomanuprev'];
                                $prevcord=$row['prevcord'];
                                $tipomanucor=$row['tipomanucor'];
                                $data_abert=$row['data_abert'];
                                $data_fech=$row['data_fech'];
                                $status = $row['status'];
                                $resposta = $row['resposta'];
                                $enderecog = $row['enderecog'];

                                //tratando datas
                                $data_abert = new DateTime($data_abert);
                                $data_abert1 = $data_abert-> format( 'd/m/Y' );


                                $data_fech = new DateTime($data_fech);
                                $data_fech1 = $data_fech-> format('d/m/Y');

                                //tratando status
                                if($status == 1){
                                    $statusf = 'Aberto';
                                } elseif($status == 2){
                                    $statusf = 'Fechado';
                                } elseif($status == 3){
                                    $statusf = 'Agendado';
                                }elseif($status == 4){
                                    $statusf = 'Cancelado';
                                } else {
                                    $statusf = '-';
                                }

                                if($tipodechamado == 'Comunicado de Sinistro'){
                                    $tipo = "$tiposinistro";
                                } elseif($tipodechamado == 'Solicitação de Manutenção Preventiva'){
                                    $tipo = "$tipomanuprev ";   
                                } elseif($tipodechamado == 'Solicitação de Manutenção Corretiva'){
                                    $tipo= "$tipomanucor";
                                }elseif($tipodechamado == 'Solicitação de Guincho'){
                                    $tipo= "$enderecog";
                                }else{
                                    $tipo= "";
                                }
                                
                                print"
                                <tr> 
                                    <td> <span class='material-symbols-outlined'> add </span> </td>
                                    <td> $placa </td>
                                    <td> $data_abert1 </td>
                                    <td> $statusf </td>
                                    <td> $tipodechamado </td>
                                    <td> $tipo </td>
                                    <td> $resposta </td>
                                    <td> $data_fech1 </td>
                                </tr>";
                            }            
                            ?>
                                
                            </tbody>
                        </table>
                    </div> 	
                </div>
            </main>

                        
            <footer class="mt-4 py-4 bg-light mt-auto" >
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

        <!-- jquery e masks -->
        <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script>

        <!-- fontawesome - js -->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

        <!-- datatables - js -->
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
        <script src="https://cdn.datatables.net/plug-ins/1.10.12/sorting/date-eu.js"></script>

        <!-- scripts próprios -->
        <script src="../src/js/scripts.js"></script>
        
        <!-- script local -->
        <script type="text/javascript">
            $(document).ready(function() { 
                $('#tabela1').DataTable({
                    responsive: true,

                    columns: [
                        null,
                        null,
                        { "type": "date-eu" },
                        null,
                        null,
                        null,
                        null,
                        { "type": "date-eu" }                
                    ],

                    language: {
                        decimal:        "",
                        emptyTable:     "Nada para exibir",
                        info:           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                        infoEmpty:      "Exibindo página 0 de 0 de 0 registros",
                        infoFiltered:   "(filtrado do total de _MAX_ registros)",
                        infoPostFix:    "",
                        thousands:      ",",
                        lengthMenu:     "Exibir _MENU_ registros",
                        loadingRecords: "Carregando...",
                        processing:     "Processando...",
                        search:         "Buscar:",
                        zeroRecords:    "Nenhum resultado encontrado",
                        paginate: {
                            first:      "Primeira",
                            last:       "Última",
                            next:       "Próxima",
                            previous:   "Anterior"
                        }
                    }
                });
            } );
        </script>
    </body>

</html>