<?php
date_default_timezone_set('America/Sao_Paulo');
session_start();
$nome = $_SESSION['nome'];
$usuariof = $_SESSION['usuario'];
$matricula = $_SESSION['matricula'];
$perfil = $_SESSION['perfil'];
/*if ($perfil == null){
  echo"<script language='javascript' type='text/javascript'>alert('Você deve logar para ter acesso');window.location=\"index.php\"</script>";
} elseif ($perfil <> '1'){
  echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"index.php\"</script>";
}*/
$_SESSION['perfil'] = $perfil;
$_SESSION['matricula'] = $matricula;
$_SESSION['nome'] = $nome;
$_SESSION['usuario'] = $usuariof;

$matriculasuper = $_POST['coordenador'];

include"conecta.php";
include_once"./func/log.php";
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
        <link rel="icon" type="image/png" href="src/images/favicon.png"/>
        <title>Portal FFA</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="src/css/styles.css" rel="stylesheet" />
        <!--link Material Symbols - simbolos do input-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
        <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

        <!-- vendor jquery js -->
        <!--<script type='text/javascript' src='src/vendor/jquery/jquery-3.2.1.min.js'></script>
        <script type='text/javascript' src='src/vendor/jquery/jquery.min.js'></script>-->

        <!--data tables - css e js-->
        <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script> 
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css">

        <!--<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->
        
    </head>


    <body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

    	<!--nav-->
    	<?php include"menu.php"; ?>

<div id="layoutSidenav_content">

<main style="width: 100%;">

<div class="container-fluid px-4" style="width: 100%;">

    	
    	<h1 class="h1 pt-2 pb-2"> Cota Emergencial </h1>
        <!--div input superior-->
            <form method="post" action="./emergencialcoord.php" class="row mb-5 pb-3 border-bottom">
                <div class="input-group" style="width: 30%">
                    <label for="exampleInputEmail1" class="input-group-text" style="background-color: white; border: none;">Supervisor:</label>
                    <select class="form-select form-select-sm" name="coordenador" id="selectcoordenador">
                        <option value=" "> Escolha um supervisor </option>
                <?php
                  $sql1 = "SELECT idtbcoordenador FROM bdfrota.tbcoord where matricula = '$matricula'";
                    $resultado1 = mysql_query($sql1) or die (mysql_error());
                    $row1 = mysql_fetch_array($resultado1);
                    $idtbcoord = $row1[0];

                  $sql = "SELECT b.nome, a.matricula from bdfrota.tbsupervisor a, bdaniel.tbfuncionario b where a.matricula=b.matricula and a.idtbcoordenador = '$idtbcoord'";
                  $resultado = mysql_query($sql) or die (mysql_error());
                  while($row = mysql_fetch_array($resultado)){ //para cada linha do banco de dados é criado um array chamado row de duas posições
                    $nome = $row[0];
                    $matriculasup = $row[1]; //essa linha é o value do option
                    print"<option value='$matriculasup'>$nome</option>";
                  };
          
                ?>
            </select>
          </div>

                <!--div botão confirmar-->
                <div class="col-2 ms-5 me-3">
                    <button class="btn btn-success" type="submit" style="border-radius: 0.4rem; width: 100%;"> Confirmar </button>
                </div>
            </form>    	

            <!--div tabela - entrar dados do sistema-->
    		<div id="containertab">
                <table id="tabela1" class="display table table-striped" style="font-size: 12px; width: 100%;">
                    <thead>
                        <tr>
                            <th class="border-bottom border-dark" style="white-space: nowrap;">Matrícula</th>
                            <th class="border-bottom border-dark" style="white-space: nowrap;">Nome</th>
                            <th class="border-bottom border-dark" style="white-space: nowrap;">Placa</th>            
                            <th class="border-bottom border-dark" style="white-space: nowrap;">Saldo</th>
                            <th class="border-bottom border-dark" style="white-space: nowrap;">Total Extra</th>
                            <th class="border-bottom border-dark" style="white-space: nowrap;"> </th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php
                    $sql = "SELECT * FROM bdfrota.tbsupervisor where matricula = '$matriculasuper'";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    $row = mysql_fetch_array($resultado);
                    $idtbsupervisor = $row[0];                     

                      
                    $sql = "SELECT a.matricula, b.nome, b.codempresa FROM bdfrota.tbusuario a, bdaniel.tbfuncionario b where a.matricula = b.matricula and a.idtbsupervisor = '$idtbsupervisor' and a.perfil = 0 and a.idtbsupervisor <> ''";
                    
                    $resultado = mysql_query($sql) or die (mysql_error());
                    while($row = mysql_fetch_array($resultado)){
                        $matriculatec = $row[0];
                        $nometec = $row[1];
                        $codempresa = $row[2];

                        $sql2 = "SELECT (valoraplicado - orcsemanal) as totalextra, idtbsaldo, kmproj, kmosatual  FROM bdfrota.tbsaldo where matricula = '$matriculatec' order by data desc";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $totalextra = $row2[0];
                        $idtbsaldo = $row2[1];
                        $kmproj = $row2[2];
                        $kmosatual = $row2[3];

                        $sql2 = "SELECT ds_placa FROM bdfrota.tbcarrosxcolaborador where id_colaborador = '$matriculatec'";
                        $resultado2 = mysql_query($sql2) or die (mysql_error());
                        $row2 = mysql_fetch_array($resultado2);
                        $placa = $row2[0];
                        

                        if ($placa <> '') {

                if ($codempresa == '2') {
    
} else{

$curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://srv1.ticketlog.com.br/ticketlog-servicos/ebs/relatorioExtratoSimplificado/search');
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($curl, CURLOPT_POST, TRUE);
                            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                'Content-Type: application/json',
                                'Accept: application/json',
                                'Accept-Charset: utf-8',
                                'Accept-Encoding: gzip',
                                'Authorization: Basic W09wZXJhZG9yV2ViXWFwcDE4MzA2MjQyNDc6RFhYSm5WejA=',
                            ));
                            curl_setopt($curl, CURLOPT_HEADER, 0);

                            $body = '{
  "codigoCliente": 183062,
  "codigoProduto": 4,
  "numeroCartao": null,
  "especieCartao": null,
  "numeroMatricula": null,
  "nomeCompleto": null,
  "placaVeiculo": "'.$placa.'",
  "numeroFrota": null,
  "codigoTipoFrota": null,
  "codigoFamiliaVeiculo": null,
  "codigoVeiculoModelo": null,
  "codigoCidade": null,
  "codigoClienteTCVeiculoResponsavel": null,
  "nomeResponsavel": null,
  "codigoGrupo": 4,
  "codigoTipoCombustivel": null,
  "informacoesAdicionais": [
    {
      "labelIA": null,
      "codigoIA": null,
      "descricaoIA": null
    }
  ],
  "situacaoCartao": "T",
  "ordem": "C",
  "quebra": null,
  "tipoMotorista": null,
  "ativacao": null,
  "tipoCartao": null,
  "controlaHodometro": null,
  "controlaHorimetro": null,
  "situacaoVeiculo": null,
  "trackOnLine": null
}';

                            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

                            $result = json_decode(curl_exec($curl));

                            if (count($result->itens)) {

                                foreach ($result->itens as $itens) {
                                    
                                    $situacao = $itens->situacao;

                                    if ($situacao == 'A') {
                                        $saldocartao = $itens->saldo;
                                        $saldocartao = str_replace(".", ",", $saldocartao);
                                    }

                                }
                            }

}
                        }

                        

                            if($placa!=''){

                            print"                        
                            <tr>
                            <td style='white-space: nowrap;'>$matriculatec</td>
                            <td style='white-space: nowrap;'>$nometec</td>
                            <td style='white-space: nowrap;'>$placa</td>            
                            <td style='white-space: nowrap;'>R$ $saldocartao</td>
                            <td style='white-space: nowrap;'>R$ $totalextra</td>
                            <td>
                            <form action='./";
                            if ($kmproj > $kmosatual) {
                                print"justemeindevidacoord";
                                } else {
                                    print"control/emergencialcoord";
                                }
                            
                            print".php' method='post'>
                            <div class='input-group mb-3'>             
  <input type='text' class='ivalor form-control' placeholder='' aria-label='' aria-describedby='button-addon2' name='valor' onkeyup='formatarMoeda(this);'>
  <div class='input-group-append'>
    <button class='btn  btn-outline-success' type='submit' id='button-addon2'><span class='material-symbols-outlined'>done</span></button>
  </div>
</div>
                            <input type='hidden' name='saldocartao' value='$saldocartao'>
                            <input type='hidden' name='matriculatec' value='$matriculatec'>
                            <input type='hidden' name='idtbsaldo' value='$idtbsaldo'>
                            <input class='tipocota' type='hidden' name='tipocota' value ='emergencial'>
</form>
                            </td>
                        </tr>";
                        }

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
                            <!--<div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>!-->
                        </div>
                    </div>
                </footer>
            </div>
            
        </div>

        <script>
            //função data table para criar barra de rolagem
            $(document).ready(function () {
                $('#tabela1').DataTable({
                    scrollX: true,
                    pageLength: 25,
                    lengthMenu: [[25, 30, 35, -1], [25, 30, 35, 'Todos']],
                    language: {
                        lengthMenu: 'Mostrar _MENU_ registros por página',
                        zeroRecords: 'Nenhum dado encontrado',
                        info: 'Mostrando página _PAGE_ de _PAGES_',
                        infoEmpty: 'Nenhum registro disponível',
                        infoFiltered: '(Filtrado de _MAX_ registros totais)',
                        search:'Procurar:',
                        paginate: {
                            first: 'Primeira',
                            last: 'Última',
                            next:'Próxima',
                            previous:'Anterior'
                       },
                    }
                });
            });

            //função para formatar valor do input - moeda
            function formatarMoeda(i) {
                var v = i.value.replace(/\D/g,'');
                v = (v/100).toFixed(2) + '';
                v = v.replace(".", ",");
                v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
                i.value = v;
            }

        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="src/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        
        <!--<script src="src/js/simple-datatable.js" crossorigin="anonymous"></script>-->
        
        <!--<script src="src/js/datatables-simple-demo.js"></script>-->
        
        <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>-->   
    	
        <!--<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>-->
    </body>

</html>