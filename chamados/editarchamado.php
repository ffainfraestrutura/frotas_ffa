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
} elseif ($perfil <> '4' && $perfil <> '5' && $perfil <> '6'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
}

$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;
$_SESSION['tipo']=$tipo;

$idtbchamados=$_POST['idtbchamados'];

include_once"../func/log.php";
include"conecta.php";
//include_once"../func/log.php";
header("Content-type: text/html; charset=utf-8");
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
        <title>Responder Chamado</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="../src/css/styles.css" rel="stylesheet" />
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous">
        </script>
        <!-- jquery e masks -->
          <script src='https://code.jquery.com/jquery-3.6.0.min.js' integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>

          <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>

    </head>

    <body class="sb-nav-fixed vsc-initialized">
    	<!--nav-->
    	<?php include"./menu-teste.php"; ?>

<div id="layoutSidenav_content">
    <main>

        <div class="container-fluid px-4">    	
    		<h1 class="h1 pt-1"> Responder Chamado nº <?php echo"$idtbchamados"; ?> </h1>

            <div>
            <form action='./control/editarchamado.php' method="post" >
            <?php
            $sql="SELECT * FROM bdfrota.tbchamados WHERE idtbchamados='".$idtbchamados."'; ";
            $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
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
                $prevcord= $row['prevcord'];
                $tipomanucor= $row['tipomanucor'];
                $data_abert=$row['data_abert'];
                $resposta=$row['resposta'];
                $status=$row['status'];
                $enderecog = $row['enderecog'];
                $observacao = $row['observacao'];

            }

            print"
            <input type='hidden' value='$idtbchamados' name='idtbchamados'>
            <input type='hidden' value='$placa' name='placa'>

            <div class='d-flex mt-4 col-12'>
                <div class='row col-7'>
                    <label class='col-1 col-form-label' for='nome'> Cliente:</label>
                    <div class=''>
                      <input type='text' name='nome' id='nome' class='form-control'  readonly value='$nome'>
                    </div>
                </div>

                <div class='row ms-2'>
                    <label class='col-form-label' for='nome'> Matrícula:</label>
                    <div class='col-7'>
                      <input type='text' name='matricula' id='matricula' class='form-control'  readonly value='$matricula'>
                    </div>
                </div>
            </div>

            <div class='d-flex mt-1 col-12 align-items-end'>
                <div class='row col-4'>
                    <label class='ol-form-label' for='nome'> Placa:</label>
                    <div class=''>
                      <input type='text' name='placa' id='placa' class='form-control'  readonly value='$placa'>
                    </div>
                </div>

                <div class='row ms-2'>
                    <label class='col-form-label' for='nome'>Telefone:</label>
                    <div class=''>
                      <input type='text' name='telefone' id='telefone' class='form-control'  readonly value='$telefone'>
                    </div>
                </div>
            </div>

            <div class='d-flex mt-1 col-12 align-items-end'>
                <div class='row'>
                    <label class='col-form-label' for='nome'>Centro de Custo:</label>
                    <div class=''>
                      <input type='text' name='ccusto' id='ccusto' class='form-control'  readonly value='$ccusto'>
                    </div>
                </div>

                <div class='ms-2 row col-7'>
                    <label class='col-form-label' for='nome'>Tipo:</label>
                    <div class=''>
                      <input type='text' name='tipodechamado' id='tipodechamado' class='form-control'  readonly value='$tipodechamado'>
                    </div>
                </div>
            </div>

            <div class='d-flex mt-2 col-12'>
                <div style='width:86.5%;'>
                    <label class='col-form-label' for='nome'>Detalhes:</label>                
                    <textarea name='detalhes' id='detalhes' class='form-control'  readonly>$detalhes</textarea>
                </div>                
            </div>

            <div class='row mt-2 col-12'>
                <div style='width:88.5%;'>
                    <label class='col-form-label' for='observacao'>Observação:</label>                
                    <textarea rows=6 name='observacao' id='observacao' class='form-control' readonly>$observacao</textarea>
                </div>              
            </div>";
            ?>
            <div class="mt-1 d-flex col-12 align-items-end">
                <div class='ms-1 row me-2'>
                    <label class='form-label' style='width: 50%;'>Status: </label>
                    <select name="status" id='status' class='form-select form-select' required onchange="verifica();">
                        <option value=' '> </option>
                    <?php //idtbstatuscham, status
                    $sql2 = "SELECT * FROM bdfrota.tbastatuscham;";
                    $resultado2 = mysqli_query($con, $sql2) or die(mysqli_error($con));
                    while ($row2 = mysqli_fetch_array($resultado2, MYSQLI_ASSOC)) {
                        $idtbstatuscham=$row2['idtbstatuscham'];
                        $status1=$row2['status'];

                        $selected = ($status == $idtbstatuscham) ? "selected=\"selected\"" : null;
                       
                        print"<option value='$idtbstatuscham' $selected> $status1 </option>";
                                                
                    }
                    ?>
                    </select>
                </div>
            <?php
            if($tipodechamado == 'Solicitação de Manutenção Preventiva'){
                print"
                <div class='ms-2 d-flex' style='height: 70%;'>
                    <a href='./control/redirman.php?placa=$placa' target='_blank' class='btn btn-primary'>Cadastrar Manutenção</a>
                </div>";
            }elseif($tipodechamado == 'Solicitação de Guincho'){
                print"
                <div class='ms-2 d-flex' style='height: 70%;'>
                    <a href='./control/redirman.php?placa=$placa' target='_blank' class='btn btn-primary'>Cadastrar Manutenção</a>
                </div>";
            }elseif($tipodechamado == 'Solicitação de Manutenção Corretiva'){
                print"
                <div class='ms-2 d-flex' style='height: 70%;'>
                    <a href='./control/redirman.php?placa=$placa' target='_blank' class='btn btn-primary'>Cadastrar Manutenção</a>
                </div>";
            } elseif($tipodechamado == 'Comunicado de Sinistro'){
                print"
                <div class='ms-2 d-flex' style='height: 70%;'>
                    <a href='./control/rededitarcad.php?placa=$placa' target='_blank' class='btn btn-primary'>Editar veículo</a>
                </div>";
            }        
            ?>
            </div>

            <?php //
            if(strlen($resposta)<=3){
             print"
            <div style='width:86.5%;'>
                <label class='control-label' for='resposta'>Resposta: </label>
                <textarea name='resposta' id='resposta' class='form-control' rows=6></textarea>
            </div>";
            } else {
                print"
            <div style='width:86.5%;'>
                <label class='control-label' for='resposta'>Resposta: </label>
                <textarea name='resposta' id='resposta' class='form-control' rows=6> $resposta</textarea>
            </div>";
            }
            ?>

            <div class='mt-2'>
                <button class='btn btn-success' type="submit"> Atualizar Chamado </button>
            </div>
            
            <div class='mt-3 alert alert-warning' role="alert">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Atenção:</strong>
                <ul class="mb-0 mt-2">
                    <li>Em caso de necessitar perguntar algo, entre em contato com o usuário pelo telefone. O sistema não permite que o usuário responda.</li>
                    <li>Evite expressões como "amanhã", "hoje", "semana que vem". Use datas específicas (ex: 05/02/2026).</li>
                </ul>
            </div>
        </form>
        </div>
        </div>
    </main>

                
    <footer class="py-4 bg-light mt-auto">
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
            

    	<script type="text/javascript">
            //função para habilitar ou desabilitar input
            function verifica(){
              var status = document.getElementById("status");
              var resposta = document.getElementById("resposta");

              console.log(status.value);
             if(status.value == '2'){
                resposta.required = true;
                
              }else {
                resposta.required = false;
              }
            };

    	</script>

    	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="../src/js/simple-datatable.js" crossorigin="anonymous"></script>
        <script src="../src/js/datatables-simple-demo.js"></script>
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->
    </body>

</html>