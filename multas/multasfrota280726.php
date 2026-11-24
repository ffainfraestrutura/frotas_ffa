<?php
	// Defina o tempo de vida da sessão para 1 hora
	$lifetime = 3600; // 1 hora em segundos

	// Configurar os parâmetros do cookie da sessão
	session_set_cookie_params($lifetime);

	// Iniciar a sessão
	session_start();

	$hoje=date('Y-m-d H:i:s');

	$usuariof = $_SESSION['usuario'];
	$perfil = $_SESSION['perfil'];
	$nome = $_SESSION['nome'];
	$tipo=$_SESSION['tipo'];

	//$nome=utf8_decode($nome);

	if($perfil == null){
		
		echo "<script language='javascript' type='text/javascript'> window.alert('Você deve logar para ter acesso.');window.location=\"../index.php\"; </script>";
	}elseif($perfil <> '4'){
		echo"<script language='javascript' type='text/javascript'>alert('Você não tem permissão');window.location=\"../index.php\"</script>";
	}

	$_SESSION['usuario']=$usuariof;
	$_SESSION['perfil']=$perfil;
	$_SESSION['nome']=$nome;
	$_SESSION['tipo']=$tipo;

	if($perfil == '5') {$depart= 'Administrador';}
	elseif($tipo =='1') {$depart = 'Supervisor';}
	elseif ($tipo == '4' || $perfil=='4') {$depart = 'Frotas'; }
	elseif ($tipo == '6') {$depart = 'DP';}
	elseif($tipo=='7') {$depart ='Financeiro';}
	elseif ($tipo=='8') { $depart = 'Controladoria';}

	//include"../conecta.php";
	include"../conecta2.php";
	header("Content-type: text/html; charset=utf-8");

	$etapaf = $_POST['etapaf'];
	$etapaf = $etapaf;
?>

<!-- editado para ficar no padrão de frotas -->
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, , shrink-to-fit=no" />
		<meta name="description" content="FFA" />
		<meta name="author" content="FFA" />
		<link rel="icon" type="image/png" href="../src/images/favicon.png"/>

		<title> Acompanhar Assinaturas </title>

		<!--link Material Symbols - simbolos do input-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

		<!--data tables - css -->		
		<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css"> -->
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css">

		<link href="../src/css/styles2.css" rel="stylesheet" />

		<style type='text/css'>
			body{
				font-size: 11px;
			}

			a{
				text-decoration: none;
			}

			.btn{
				font-size: 11px;
			}		
		</style>
		
	</head>

	<body class="sb-nav-fixed vsc-initialized sb-sidenav-toggled">

		<!--nav-->
		<?php include "../menu.php";
		//include"./popupParecer.php";
		?>

				<div class="container-fluid" style="width: 100%;">
				<h2 class="h2 pt-2 pb-2"> Multas </h2>
					<h3 class="h3 pt-2 pb-2"> Acompanhar Assinaturas </h3>
					<?php /*print "<h6 class='h6 pt-2 pb-2 mb-3'> $depart </h6>";*/?>
					
					<!-- div cadastrar nova multa-->
					<div class='mt-1 text-rigth d-flex justify-content-start'>
						<a class='btn btn-success text-rigth' href='../gestao/cadastromulta.php'> Cadastro Unitário </a>

						<a class='ms-2 btn btn-primary text-rigth' href='https://ffa.painel-telecom.com:4545/frotas/gestao/importmultasnovas.php' style='width: 150px;' target="_blank"> Importar Multas Novas </a>

						<a class='ms-2 btn btn-secondary text-rigth' style='width: 150px;' href='https://ffa.painel-telecom.com:4545/frotas/gestao/control/docs/modelos/ImportModeloMultas.xlsx'> Modelo Excel </a>
					</div>

					<!-- Aviso sobre filtro da listagem -->
					<div class="alert alert-info mt-3" role="alert">
						<strong><span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">info</span> Informação sobre a Listagem:</strong>
						<ul class="mb-0 mt-2">
							<li><strong>Filtro aplicado:</strong> Exibindo apenas multas com etapa "AGUARDANDO ASSINATURA" ou "MULTA ASSINADA"</li>
							<!-- <li><strong>Data de Infração:</strong> Mostra quando a infração foi cometida</li> -->
							<!-- <li><strong>Apresentar Condutor em:</strong> Prazo limite para identificar o condutor responsável</li> -->
							<!-- <li><strong>Registro mais recente:</strong> Quando há múltiplos registros da mesma multa (placa + nº auto), apenas o mais recente é exibido</li> -->
						</ul>
					</div>

					<!-- div tabela-->
					<div style="width: 100%;" class="mt-4 m-auto">
						
						<table id="tabela1" class='table table-striped'>
							<thead>       	
								<tr>
									<th>Placa</th>
									<th>Condutor</th>
									<th>Status do Condutor</th>								
									<th>Centro de Custo</th>
									<th>Unidade</th>								
									<th>Locadora</th>
									<th>Nº do Auto</th>														
									<th>Data Infração</th>
									
									<th>Apresentar condutor em</th>

									<th>Etapa</th>
									<th>Trâmite</th>

									<th>Assinou recibos?</th>
									<!-- <th>Assinou recibo locadora?</th> -->

									
									<th>Último Parecer</th>
									<th>Parecer por</th>

									<th>Adicionar Parecer</th>

								</tr>
							</thead>
							<tbody>
								<?php
									//if($etapaf=='TODAS'){
										// Busca apenas o ID mais recente de cada multa (baseado em placa + autoinfracao)
										$sql="SELECT m.* 
										      FROM bdfrota.tbmulta m
										      INNER JOIN (
										          SELECT placa, autoinfracao, MAX(idtbmulta) as id_mais_recente
										          FROM bdfrota.tbmulta 
										          WHERE placa <> 'ABC1234' 
										            AND (etapa LIKE 'AGUARDANDO ASSINATURA%' OR etapa LIKE '%MULTA ASSINADA%') 
										            AND idtbmulta NOT IN('6557', '6495', '6507', '6510', '6560', '6524', '6500', '6498', '6491', '6493', '6523', '6517', '6551', '6553', '6547', '6529', '6536', '6503', '6559', '6549', '6513', '6535', '6555', '6539', '6531', '6527', '3832', '3831', '3836', '3837', '3833', '3835', '3834', '3830', '6461', '6466', '6469', '6455', '6459', '6463', '6453', '6457', '6464', '7269','7271','7273','7275', '6505')
										          GROUP BY placa, autoinfracao
										      ) ultimos ON m.placa = ultimos.placa 
										                 AND m.autoinfracao = ultimos.autoinfracao 
										                 AND m.idtbmulta = ultimos.id_mais_recente;";
									/*}else{
										$sql="SELECT * FROM bdfrota.tbmulta WHERE etapa = '$etapaf' AND placa <> 'ABC1234';";				    	
									}*/

									$resultado =  mysqli_query($conexao, $sql) or die(mysqli_error($conexao));

									while($row = mysqli_fetch_array($resultado, MYSQLI_BOTH)){
										$idtbmulta = $row['idtbmulta'];
										$etapa1= $row['etapa'];
										$datalimitecond = $row['datalimitecond'];
										$placa = $row['placa'];
										$autoinfracao = $row['autoinfracao'];

										if($idmulta != ""){
											$sql1 = "SELECT idtbmovidatramite, matricula,nome,dtinfra,tramite, dtinfra, reciboass, recibolocass, parecer, parecerpor, parecerdp, parecerpordp FROM bdfrota.tbmovidatramite WHERE idmulta = '$idmulta';";
										}else{
											$sql1 = "SELECT idtbmovidatramite, matricula,nome,dtinfra,tramite, dtinfra, reciboass, recibolocass, parecer, parecerpor, parecerdp, parecerpordp, recibo FROM bdfrota.tbmovidatramite WHERE placa='$placa' AND autoinfra='$autoinfracao';";
										}

										$resultado1 = mysqli_query($conexao, $sql1) or die(mysqli_error($conexao));
										
										$row1 = mysqli_fetch_array($resultado1, MYSQLI_BOTH);
											$id = $row1['idtbmovidatramite'];
											$nomecond = $row1['nome'];
											$matcond = $row1['matricula'];
											$datainfra = $row1['dtinfra'];
											$tramite = $row1['tramite'];
											$recibo = $row1['recibo'];
											$reciboass = $row1['reciboass'];
											$recibolocass = $row1['recibolocass'];
											$parecer = $row1['parecer'];
											$parecerpor = $row1['parecerpor'];
											$parecerdp = $row1['parecerdp'];
											$parecerpordp = $row1['parecerpordp'];


										$sql2 = "SELECT status,ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matcond';";	
										$resultado2 = mysqli_query($conexao, $sql2) or die(mysqli_error($conexao));
										$row2 = mysqli_fetch_array($resultado2, MYSQLI_BOTH);
											$statuscond = $row2['status'];
											$ccusto = $row2['ccusto'];

										//selecionando unidade
										$sql3="SELECT unidade, idlocador FROM bdfrota.tbveiculo WHERE placa='$placa';";
										$resultado3 = mysqli_query($conexao, $sql3) or die(mysqli_error($conexao));
										$row3 = mysqli_fetch_array($resultado3, MYSQLI_BOTH);
											$unidade = $row3['unidade'];
											$idlocador = $row3['idlocador'];

										if($idlocador==''){
											$fantasia = 'PRÓPRIO';
										}else{
											$sql4 = "SELECT fantasia FROM bdfrota.tbfornecedor where idtbfornecedor = '$idlocador'";
											$resultado4 = mysqli_query($conexao, $sql4) or die(mysqli_error($conexao));
											$row4 = mysqli_fetch_array($resultado4, MYSQLI_BOTH);
												$fantasia = $row4['fantasia'];
										}

										//recibos por locadora
										if($idlocador == '2' || $idlocador == '9' || $idlocador == '16' || $idlocador == '17' || $idlocador == '19'){
											$recibolocadora = 'recibomovidaass';
										} elseif($idlocador == '3' || $idlocador == '4' || $idlocador == '14' || $idlocador == '15'){											
											$recibolocadora = 'recibolocalizaass';
										} elseif($idlocador == 8){											
											$recibolocadora = 'recibounidasass';										
										}else{											
											$recibolocadora = 'semrecibo';
										}

										//tratando datas
										if($datainfra<>'0000-00-00 00:00:00'){										
											//$datainfra1 = date_create($datainfra);
											$datainfra2 = DateTime::createFromFormat('Y-m-d H:i:s', $datainfra)->format('d/m/Y');
										}else{
											$datainfra2 = '';
										}

										if($datalimitecond<>'0000-00-00 00:00:00'){
											//$datalimitecond1 = date_create($datalimitecond);
											$datalimitecond2 = DateTime::createFromFormat('Y-m-d H:i:s', $datalimitecond)->format('d/m/Y');
										}else{
											$datalimitecond2 = '';
										}

										//tratando recibos
										if($recibolocass == 0){
											$recibolocass1 = 'NÃO';
										
										}elseif($recibolocass == 1){

											$recibolocass1 = 'SIM';

											$recibolocass1 = 
											"<form method='post' action='../pdf/fpdf/$recibolocadora.php' target='_blank'> 
													<input type='hidden' name='id' value='$id'/>  

													<button type='submitt' style='border: none; background-color: transparent;'> SIM </button> 
											</form>";
										}

										if($reciboass<>''){
											$reciboass1 = strtoupper($reciboass);
										}else{
											$reciboass1 = 'NÃO';
										}

										if($reciboass1=='SIM'){
											$reciboass1 = 
											"<a href='http://10.17.0.3/multas$recibo' target='_blank' >SIM </a>";
										}

										//ultimo parecer
										if(!empty($parecerdp)){
											$parecerfinal = $parecerdp;

											$sql5 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$parecerdppor'";
											$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
											$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
												$autorpar = $row5['nome'];

										}else{
											$parecerfinal = $parecer;

											$sql5 = "SELECT nome FROM bdaniel.tbfuncionario where matricula = '$parecerpor'";
											$resultado5 = mysqli_query($conexao, $sql5) or die(mysqli_error($conexao));
											$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
												$autorpar = $row5['nome'];
										}

										$parecer = "
										<form method='post' action='../../multas/parecermulta.php' target='_blank'>
											<input type='hidden' name='idtbmovidatramite' id='nome' value='$id'>
											<button style='border: none; background-color: transparent;' type='submit' title='Informar Parecer'> <span class='material-symbols-outlined'> edit_note </span> </button>
										</form>";

										//informações do condutor
										if($matcond!=''){
											$infocond = "<form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/dadoscondutor.php' target='_blank'> 
													<input type='hidden' name='matcond' value='$matcond'/>  

													<button type='submitt' style='border: none; background-color: transparent;' title='Histórico Placa'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
											</form>";
											
										}else{
											$infocond = "";
										}

										$info = "
										<form method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/dadosplaca.php' target='_blank'> 
												<input type='hidden' name='placa' value='$placa'/> 
												<input type='hidden' name='autoinfra' value='$autoinfra'/> 

												<button type='submitt' style='border: none; background-color: transparent;' title='Histórico Placa'> <span style='font-size:12px;' class='material-symbols-outlined'>info</span></button> 
										</form>";

										if($placa<>''){
											print"
											<tr>
												<td>$placa$info</td>
												<td>$nomecond$infocond</td>
												<td>$statuscond</td>              
												<td>$ccusto</td>
												<td>$unidade</td>              
												<td>$fantasia</td>
												<td>$autoinfracao</td>
												<td>$datainfra2</td>
												<td>$datalimitecond2</td>
												<td>$etapa1</td>
												<td>$tramite</td>
												<td>$reciboass1</td>
												<!--<td>$recibolocass1</td>-->
												
												<td>$parecerfinal</td>								
												<td>$autorpar</td>
												<td>$parecer</td>
											</tr>"; 
										}   
									}
								?>
							</tbody>
						</table>		
					</div>

					<div style="width: 100%;" class="mt-2 m-auto">
						<h6>Legenda tabela: </h6>
						<p class="mb-0" style='color: blue;'>Multas no prazo</p>
						<p style='color: red;'>Multas vencidas</p>	      
					</div>    
			
					<!-- div gerar excel-->
					<div class='mt-2 text-rigth d-flex justify-content-start'>
						<?php

						//ction='./control/gerarrelatoriomultas.php'
						if($etapaf<>''){
							print"
							<form action='https://ffa.painel-telecom.com:4545/frotas/gestao/control/gerarrelatoriomultas.php' method='post'>
								<input type='hidden' name='etapaf' value='$etapaf'>
								<button class='ms-2 btn btn-success text-rigth' type='submit'>Gerar Excel</button>
							</form>";
						}

						?>
					</div>

				</div>  
			</main>
			
			<footer class="py-4 bg-light mt-auto">
				<div class="container-fluid px-4">
					<div class="d-flex align-items-center justify-content-between small">
							<div class="text-muted">Copyright &copy; FFA Infraestrutura</div>
							<!--<div><a href="#">Privacy Policy</a>
									&middot;
									<a href="#">Terms &amp; Conditions</a>
							</div>-->
					</div>
				</div>
			</footer>
		
		</div>  
		</div><!--div para fechar a div que fica aberta no menu.php-->

		<!-- <script type="text/javascript" src='https://code.jquery.com/jquery-3.5.1.js'></script> -->
		<script type="text/javascript" src='https://code.jquery.com/jquery-3.7.1.js'></script>

		<script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

		<script src="https://cdn.datatables.net/2.1.5/js/dataTables.js" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js" crossorigin="anonymous"></script>

		<!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script> -->
		<!--<script type="text/javascript" src="DataTables/datatables.min.js"></script> -->
		<!-- <script src="https://cdn.datatables.net/plug-ins/1.13.4/sorting/date-eu.js"></script> -->

		<script src="../src/js/scripts.js"></script>

		<script type="text/javascript">
			
			$(document).ready(function () {

				jQuery.extend(jQuery.fn.dataTableExt.oSort, {
					"customdatesort-pre": function(a) {
						// returns the "weight" of a cell value
						var r, x;
						if (a === null || a === "") {
							// for empty cells: weight is a "special" value which needs special handling
							r = false;
						} else {
							// otherwise: weight is the "time value" of the date
							x = a.split("/");
							r = +new Date(+x[2], +x[1] - 1, +x[0]);
						}
						//console.log("[PRECALC] " + a + " becomes " + r);
						return r;
					},
					"customdatesort-asc": function(a, b) {
						// return values are explained in Array.prototype.sort documentation
						if (a === false && b === false) {
							// if both are empty cells then order does not matter
							return 0;
						} else if (a === false) {
							// if a is an empty cell then consider a greater than b
							return 1;
						} else if (b === false) {
							// if b is an empty cell then consider a less than b
							return -1;
						} else {
							// common sense
							return a - b;
						}
					},
					"customdatesort-desc": function(a, b) {
						if (a === false && b === false) {
							return 0;
						} else if (a === false) {
							return 1;
						} else if (b === false) {
							return -1;
						} else {
							return b - a;
						}
					}
				});

				// {"type":"date-eu"},
						// {"type":"date-eu"},

				//DataTable.datetime('DD/MM/YYYY');
				//DataTable.datetime('DD/MM/YYYY HH:mm:ss');

				$('#tabela1').DataTable({
					/*columns*/"aoColumns": [
						null, //placa
						null, //condutor
						null, //status condutor
						null, //ccusto
						null, //unidade
						null, //locadore
						null, //num auto
						
						{"sType":"customdatesort"}, //data infra
						{"sType":"customdatesort"},	//apresent cond
						null, //etapa
						null, //tramite
						null, //assinou
						null,  //ult par        
						null,//parecer por
						null, //adicionar parecer
						
					],					
					//"order": [5, 'asc'],
					order: [7, 'desc'],

					language: {
						decimal:        "",
						emptyTable:     "Nada para exibir",
						info:           "Mostrando de _START_ até _END_ de _TOTAL_ registros",
						infoEmpty:      "Exibindo página 0 de 0 de 0 registros",
						infoFiltered:   "(filtrado do total de _MAX_ registros)",
						infoPostFix:    "",
						thousands:      ".",
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
						},
					}
					
				});
			});

			function popupParecer(idtbmovidatramite, parecerpor) {
				var idfim = idtbmovidatramite;
				document.getElementById('id').innerHTML ="Parecer: "+ idfim;

				$('#id').attr('value',idfim);

				var autor = parecerpor;
				document.getElementById('autor').innerHTML = "Autor: "+ autor;

				$('#autor').attr('value',autor);  
			}
		</script>
	</body>
</html>