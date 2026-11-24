
<?php
$cpf1 = $_POST['cpf1'];

$sql5="SELECT nome, matricula, codempresa, codfilial, ccusto FROM bdaniel.tbfuncionario WHERE cpf='$cpf1' AND status<>'Demitido' ";

$resultado5=mysqli_query($con, $sql5) or die(mysqli_error($con));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_BOTH);
  $condutor2=utf8_encode($row5['nome']);
  $matrcond2=$row5['matricula'];
  $codempresa2=$row5['codempresa'];
  $codfilial2=$row5['codfilial'];
  $ccusto2=utf8_encode($row5['ccusto']);
  
$sql6="SELECT numcnh, categoria, validade FROM bdfrota.tbcnh WHERE matricula='$matrcond2' ";
$resultado6=mysqli_query($con, $sql6) or die(mysqli_error($con));
$row6 = mysqli_fetch_array($resultado6, MYSQLI_BOTH);
  $cnh2=$row6['numcnh'];
  $categoria2=$row6['categoria'];
  $validade2=$row6['validade'];

  $validade2a= explode(" ",$validade2);
  $validade2b = explode("-", $validade2a[0]);
  $validade2f = $validade2b[2]."/".$validade2b[1]."/".$validade2b[0];

$sql7="SELECT * FROM bdaniel.tbfilial WHERE codfilial='$codfilial2' AND codempresa='$codempresa2' ";
$resultado7=mysqli_query($con, $sql7) or die(mysqli_error($con));
$row7 = mysqli_fetch_array($resultado7, MYSQLI_BOTH);
  $filial2=$row7['nome'];

        print "
        <!--div informações do condutor-->
    
        <div class='mt-4 border-top border-dark pt-2'>
          <h6> Informações do condutor </h6>

          <div class=''>
            <form method='post' action='./checklistp1.php' id='cpfachar'>

              <p class=''>Caso as Informações do condutor não venham pré-carregadas, preencha o campo CPF e clice no ícone <span class='material-symbols-outlined mt-1'>arrow_forward</span>. As demais Informações do condutor serão inseridas automaticamente.</p>
              
              <input type='hidden' name='placa' value='$placaveic'>

              <div class='row mt-2 col-12 align-items-center'>    
                <label class='col-3 col-form-label' for='cpf1'> CPF:<span style='color: red;'>*</span> </label>
                <div class='col-7'>
                  <input type='text' name='cpf1' id='cpf1' class='form-control' placeholder='CPF do Condutor' required value='$cpf1'>
                </div>

                <div class='col-2 $dd' id='botaoacharcpf'>
                  <button type='submit' style='background-color: transparent; border: none;'><span class='material-symbols-outlined'>arrow_forward</span>
                  </button>
                </div>
              </div>
            </form>

            ";
?>

		<!--div unidade -->
        <?php print"  <div class='row mt-2 $d'>";?>
        
          <label class='col-5 col-form-label' for='unidade'> Unidade:<span style='color: red;'>*</span> </label>
          <div class='col-6'>
            <select name='unidade' id='unidade' class='form-select' required>
              <option value=''> Selecione a opção </option>
              <?php
	              $sql="SELECT DISTINCT (basegestao) FROM bdfrota.tbveiculo ORDER BY basegestao;";
	              $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));
                
	              while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
	                $basegestao = $row['basegestao'];
                  $selected = ($unidade == $basegestao) ? "selected=\"selected\"" : null;
	                
	                if($basegestao!=''){
	                  print "<option value='$basegestao' $selected> $basegestao </option>";
	                }                    
              	  }

              ?>
            </select>
          </div>
        </div>

<?php

print"
          <!--div informações do condutor-->
            <div class='mt-4 border-top border-dark pt-2'>
              <h6> Informações do veículo </h6>

              <!--div placa --> 
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='placa'> Placa:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='placa' id='placa' class='form-control' placeholder='Placa do Veículo' required value='$placa'>
                </div>
              </div>

              <!--div modelo -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='modelo'> Modelo:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='modelo' id='modelo' class='form-control' placeholder='Modelo do Veículo' required value='$modelo'>
                </div>
              </div>

              <!--div anofabricacao -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='anofabricacao'> Ano - fabricação:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='anofabricacao' id='anofabricacao' class='form-control' placeholder='Ano de fabricação' required value='$anofabricacao'>
                </div>
              </div>
            </div>";
        ?>
        <!-- div informações checklist-->
          <div class='mt-4 border-top border-dark pt-2'>
            <h6> Informações da vistoria </h6>
              <!--div tipo-->
            <?php
              if($idnextfleet <> ''){ print"
                <div class='row mt-2 align-items-center'>
                  <p class='col-5'> Informações da vistoria anterior: </p>
                  <div class='col-6'>
                    <a class='btn btn-secondary' target='_blank' href='$pdf' download> Visualizar </a>
                  </div>
                </div>";
              }
            ?>
              
            <div class='row mt-2'>
              <label class='col-5 col-form-label' for='tipo'> Tipo:<span style='color: red;'>*</span> </label>
              <div class='col-6'>
                <select name="tipo" id='tipo' class="form-select" required>
                    <option value=''> Selecione a opção </option>
                    <?php
                      $sql="SELECT * FROM bdfrota.tbatipovist WHERE idtbatipovist<>'1';";
                      $resultado=mysqli_query($con, $sql) or die(mysqli_error($con));
                      while($row=mysqli_fetch_array($resultado, MYSQLI_BOTH)){
                        $idtbatipovist=$row['idtbatipovist'];
                        $tipo=utf8_encode($row['tipo']);

                        print"
                        <option value='$idtbatipovist'> $tipo </option>";
                      }
                    ?>
                  </select>
                </div>
              </div>

              <!--div vistoriador -->
              <?php
                $sql = "SELECT nome FROM bdaniel.tbfuncionario WHERE matricula = '$matricula1';";

                $resultado = mysqli_query($con, $sql) or die(mysqli_error($con));

                while($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
                  $nomevist = utf8_encode($row['nome']);                
                }

                print "
                <div class='row mt-2'>
                  <label class='col-5 col-form-label' for='vistoriador'> Vistoriador:<span style='color: red;'>*</span> </label>
                  
                  <div class='col-6'>
                    <input type='hidden' name='matrvistoriador' value='$matricula1'>
                    <input type='text' name='vistoriador' id='vistoriador' class='form-control' placeholder='Vistoriador' value='$nomevist' required>
                  </div>
                </div>";
              ?>
              <!--div data da vistoria -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='datavistoria'> Data da Vistoria:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type="date" name='datavistoria' id='datavistoria' class='form-control' maxlength="10" required>
                  <!--<input type='text' name='datavistoria' id='datavistoria' class='form-control' required>-->
                </div>
              </div>

              <!--div hora da vistoria -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='horavistoria'> Hora da Vistoria:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='time' name='horavistoria' id='horavistoria' class='form-control' required>
                </div>
              </div>

              <!--div tipo-->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='tipo'>Status:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <select name="statusveic" id='statusveic' class="form-select" >
                      <option value=''> Selecione a opção </option>
                    <?php
                      $sql3="SELECT * FROM bdfrota.tbastatusvel WHERE idtbastatusvel in ('1','2','5','18','36', '32')"; //idtbastatusvel, status
                      $resultado3 = mysqli_query($con, $sql3) or die(mysqli_error($con));

                      while($row3 = mysqli_fetch_array($resultado3, MYSQLI_ASSOC)){
                        $idtbastatusvel = $row3['idtbastatusvel'];
                        $statusveic = utf8_encode($row3['status']);
                        
                        print"<option value='$idtbastatusvel'> $statusveic </option>";
                      }

                    ?>

                   
                  </select>
                </div>
              </div>

              <!--div estado geral -->
              <div class='column mt-2 align-items-center'>
                <label class='col-form-label' for='estado'> Estado Geral do Veículo:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $estado</p>";?>
                <div class='col-12' style= 'flex-wrap: wrap;'>
                  <input type="radio" id="pessimo" name="estado" value="pessimo" required>
                  <label for="pessimo">Péssimo</label>
                  <input class="ms-1" type="radio" id="ruim" name="estado" value="ruim">
                  <label for="ruim">Ruim</label>
                  <input class="ms-1" type="radio" id="regular" name="estado" value="regular">
                  <label for="regular">Regular</label>
                  <input class="ms-1" type="radio" id="bom" name="estado" value="bom">
                  <label for="bom">Bom</label>
                  <input class="ms-1" type="radio" id="otimo" name="estado" value="otimo">
                  <label for="otimo">Ótimo</label>
                </div>
              </div>

              <!--div avaria -->
              <div class='row mt-2 align-items-center'>
                <label class='col-form-label col-6' for='avaria'> Possui avaria? <span style='color: red;'>*</span> </label>                
                <div class='col-6'>
                  <input type="radio" id="sim" name="avaria" value="1" required>
                  <label for="sim">Sim</label>
                  <input class="ms-1" type="radio" id="nao" name="avaria" value="0">
                  <label for="nao">Não</label>
                </div>                
              </div>
              <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $avaria</p>";?>

              <!--div hodometro -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='hodometro'> Hodômetro:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='hodometro' id='hodometro' class='form-control' placeholder='Hodômetro' required>
                </div>
              </div>

              <!--div nivel do tanque -->
              <div class='row mt-2'>
                <label class='col-5 col-form-label' for='niveltanque'> Nível do Tanque:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='niveltanque' id='niveltanque' class='form-control' placeholder='Nível do tanque' required>
                </div>
              </div>              
            </div>

            <!-- div Itens personalizados da vistoria -->
            <div class='mt-4 border-top border-dark pt-2'>
              <h6> Itens personalizados da vistoria </h6>
              <!--div teto -->
              <div class='column mt-2 align-items-center'>
                <label class='col-form-label' for='teto'> Teto do veículo - Estado Geral:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $teto</p>";?>
                <div class=''>
                  <input type="radio" id="okteto" name="teto" value="ok" required>
                  <label for="okteto">Ok</label>
                  <input class="ms-2" type="radio" id="nokteto" name="teto" value="naook">
                  <label for="nokteto">Não Ok</label>
                </div>
              </div>

              <!--div teto não ok -->
              <div id='tetonaook' hidden>
                <!--div teto não ok - teto geral-->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tetoesp'> Teto geral:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $tetoesp</p>";?>
                  <div class=''>
                    <input type="radio" id="oktetoesp" name="tetoesp" value="ok">
                    <label for="oktetoesp">Ok</label>
                    <input class="ms-2" type="radio" id="amtetoesp" name="tetoesp" value="amassado">
                    <label for="amtetoesp">Amassado</label>
                    <input class="ms-2" type="radio" id="arrtetoesp" name="tetoesp" value="arranhado">
                    <label for="arrtetoesp">Arranhado</label>
                  </div>
                </div>

                <!--div teto não ok - Coluna do teto Lado Esquerdo -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tetoesq'> Coluna do teto - lado esquerdo: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $tetoesq</p>";?>
                  <div class=''>
                    <input type="radio" id="oktetoesq" name="tetoesq" value="ok">
                    <label for="oktetoesq">Ok</label>
                    <input class="ms-2" type="radio" id="amtetoesq" name="tetoesq" value="amassado">
                    <label for="amtetoesq">Amassado</label>
                    <input class="ms-2" type="radio" id="arrtetoesq" name="tetoesq" value="arranhado">
                    <label for="arrtetoesq">Arranhado</label>
                  </div>
                </div>

                <!--div teto não ok - Coluna do teto Lado Direito -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tetoesq'> Coluna do teto - lado direito: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $tetodir</p>";?>
                  <div class=''>
                    <input type="radio" id="oktetodir" name="tetodir" value="ok">
                    <label for="oktetodir">Ok</label>
                    <input class="ms-2" type="radio" id="amtetodir" name="tetodir" value="amassado">
                    <label for="amtetodir">Amassado</label>
                    <input class="ms-2" type="radio" id="arrtetodir" name="tetodir" value="arranhado">
                    <label for="arrtetodir">Arranhado</label>
                  </div>
                </div>
              </div>

              <!--div frente -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='frente'> Frente do veículo - Estado Geral:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $frente</p>";?>
                <div class=''>
                  <input type="radio" id="okfrente" name="frente" value="ok" required>
                  <label for="okfrente">Ok</label>
                  <input class="ms-2" type="radio" id="nokfrente" name="frente" value="naook">
                  <label for="nokfrente">Não Ok</label>
                </div>
              </div> 

              <!--div frente não ok -->
              <div id='frentenaook' hidden>

                <!--div frente não ok - capo  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='capo'> Capô: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $capo</p>";?>
                  <div class=''>
                    <input type="radio" id="okcapo" name="capo" value="ok">
                    <label for="okcapo">Ok</label>
                    <input class="ms-2" type="radio" id="amcapo" name="capo" value="amassado">
                    <label for="amcapo">Amassado</label>
                    <input class="ms-2" type="radio" id="arrcapo" name="capo" value="arranhado">
                    <label for="arrcapo">Arranhado</label>
                  </div>
                </div>

                <!--div frente não ok - parabrisa  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='parabrisa'> Para-brisa: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $parabrisa</p>";?>
                  <div class=''>
                    <input type="radio" id="okparabrisa" name="parabrisa" value="ok">
                    <label for="okparabrisa">Ok</label>
                    <input class="ms-2" type="radio" id="qbparabrisa" name="parabrisa" value="quebrado">
                    <label for="qbparabrisa">Quebrado</label>
                    <input class="ms-2" type="radio" id="trparabrisa" name="parabrisa" value="trincado">
                    <label for="trparabrisa">Trincado</label>
                  </div>
                </div>

                <!--div frente não ok - Farol esquerdo  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='farolesq'> Farol esquerdo: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $farolesq</p>";?>
                  <div class=''>
                    <input type="radio" id="okfesq" name="farolesq" value="ok">
                    <label for="okfesq">Ok</label>
                    <input class="ms-2" type="radio" id="qbfesq" name="farolesq" value="quebrado">
                    <label for="qbfesq">Quebrado</label>
                    <input class="ms-2" type="radio" id="trfesq" name="farolesq" value="trincado">
                    <label for="trfesq">Trincado</label>
                  </div>
                </div>

                <!--div frente não ok - Farol direito  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='faroldir'> Farol direito: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $faroldir</p>";?>
                  <div class=''>
                    <input type="radio" id="okfdir" name="faroldir" value="ok">
                    <label for="okfdir">Ok</label>
                    <input class="ms-2" type="radio" id="qbfdir" name="faroldir" value="quebrado">
                    <label for="qbfdir">Quebrado</label>
                    <input class="ms-2" type="radio" id="trfdir" name="faroldir" value="trincado">
                    <label for="trfdir">Trincado</label>
                  </div>
                </div>

                <!--div frente não ok - para-choque dianteiro  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='parachoque'> Para-choque dianteiro: <span style='color: red;'>*</span></label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $parachoque</p>";?>
                  <div class=''>
                    <input type="radio" id="okpc" name="parachoque" value="ok">
                    <label for="okpc">Ok</label>

                    <input class="ms-2"  type="radio" id="ampc" name="parachoque" value="amassado">
                    <label for="ampc">Amassado</label>
                    <input class="ms-2"  type="radio" id="qbpc" name="parachoque" value="quebrado">
                    <label for="qbpc">Quebrado</label>
                    <input class="ms-2"  type="radio" id="arrpc" name="parachoque" value="arranhado">
                    <label for="arrpc">Arranhado</label>
                  </div>
                </div>

                <!--div frente não ok - Grade Frontal   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='grade'> Grade Frontal: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $grade</p>";?>
                  <div class=''>
                    <input type="radio" id="okgrade" name="grade" value="ok">
                    <label for="okgrade">Ok</label>
                    <input class="ms-2"  type="radio" id="amgrade" name="grade" value="amassado">
                    <label for="amgrade">Amassado</label>
                    <input class="ms-2"  type="radio" id="qbgrade" name="grade" value="quebrado">
                    <label for="qbgrade">Quebrado</label>
                  </div>
                </div>
              </div>

              <!--div Lateral Esquerda -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='latesq'> Lateral esquerda - Estado Geral:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $latesq</p>";?>
                <div class=''>
                  <input type="radio" id="oklesq" name="latesq" value="ok">
                  <label for="oklesq">Ok</label>
                  <input class="ms-2" type="radio" id="noklesq" name="latesq" value="naook">
                  <label for="noklesq">Não Ok</label>                  
                </div>
              </div>

              <!--div latesq não ok -->
              <div id='latesqnaook' hidden>
                <!--div latesq não ok - para-lama -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='paralamaesq'> Para-lama esquerdo:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $paralamaesq</p>";?>
                  <div class=''>
                    <input type="radio" id="okplesq" name="paralamaesq" value="ok">
                    <label for="okplesq">Ok</label>
                    <input class="ms-2" type="radio" id="amplesq" name="paralamaesq" value="amassado">
                    <label for="amplesq">Amassado</label>
                    <input class="ms-2" type="radio" id="arrlesq" name="paralamaesq" value="arranhado">
                    <label for="arrlesq">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok - Retrovisor Lado Esquerdo  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='retrovesq'>Retrovisor Lado Esquerdo: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $retrovesq</p>";?>
                  <div class=''>
                    <input type="radio" id="okrtesq" name="retrovesq" value="ok">
                    <label for="okrtesq">Ok</label>

                    <input class="ms-2" type="radio" id="amrtesq" name="retrovesq" value="amassado">
                    <label for="amrtesq">Amassado</label>
                    <input class="ms-2" type="radio" id="qbrtesq" name="retrovesq" value="quebrado">
                    <label for="qbrtesq">Quebrado</label>
                    <input class="ms-2" type="radio" id="arrrtesq" name="retrovesq" value="arranhado">
                    <label for="arrrtesq">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok -  Caixa de Ar Lado Esquerdo   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='cxaresq'> Caixa de Ar Lado Esquerdo:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $cxaresq</p>";?>
                  <div class=''>
                    <input type="radio" id="okcxesq" name="cxaresq" value="ok">
                    <label for="okcxesq">Ok</label>

                    <input class="ms-2" type="radio" id="amcxesq" name="cxaresq" value="amassado">
                    <label for="amcxesq">Amassado</label>
                    <input class="ms-2" type="radio" id="arrcxesq" name="cxaresq" value="arranhado">
                    <label for="arrcxesq">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Dianteira Esquerda   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='ptdiantesq'> Porta Dianteira Esquerda: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $ptdiantesq</p>";?>
                  <div class=''>
                    <input type="radio" id="okpdesq" name="ptdiantesq" value="ok">
                    <label for="okpdesq">Ok</label>

                    <input class="ms-2" type="radio" id="ampdesq" name="ptdiantesq" value="amassado">
                    <label for="ampdesq">Amassada</label>
                    
                    <input class="ms-2" type="radio" id="qbpdesq" name="ptdiantesq" value="quebrado">
                    <label for="qbpdesq">Quebrada</label>
                    
                    <input class="ms-2" type="radio" id="arrpdesq" name="ptdiantesq" value="arranhado">
                    <label for="arrpdesq">Arranhada</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Traseira Esquerda  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='pttrasesq'>Porta Traseira Esquerda:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $pttrasesq</p>";?>
                  <div class=''>
                    <input type="radio" id="okptesq" name="pttrasesq" value="ok">
                    <label for="okptesq">Ok</label>

                    <input class="ms-2" type="radio" id="amptesq" name="pttrasesq" value="amassado">
                    <label for="amptesq">Amassado</label>
                    
                    <input class="ms-2" type="radio" id="qbptesq" name="pttrasesq" value="quebrado">
                    <label for="qbptesq">Quebrado</label>
                    
                    <input class="ms-2" type="radio" id="arrptesq" name="pttrasesq" value="arranhado">
                    <label for="arrptesq">Arranhado</label>
                  </div>
                </div>
              </div>

              <!--div Lateral Direita -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='latdir'> Lateral Direita - Estado Geral:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $latdir</p>";?>
                <div class=''>
                  <input type="radio" id="okltdir" name="latdir" value="ok">
                  <label for="okltdir">Ok</label>
                  <input class="ms-1" type="radio" id="nokltdir" name="latdir" value="naook">
                  <label for="nokltdir">Não Ok</label>                  
                </div>
              </div>

              <!--div latdir não ok -->
              <div id='latdirnaook' hidden>

                <!--div latdir não ok - para-lama -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='paralamadir'> Para-lama direito:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $paralamadir</p>";?>
                  <div class=''>
                    <input type="radio" id="okpldir" name="paralamadir" value="ok">
                    <label for="okpldir">Ok</label>

                    <input class="ms-2" type="radio" id="ampldir" name="paralamadir" value="amassado">
                    <label for="ampldir">Amassado</label>
                    
                    <input class="ms-2" type="radio" id="arrpldir" name="paralamadir" value="arranhado">
                    <label for="arrpldir">Arranhado</label>
                  </div>
                </div>

                <!--div latdir não ok - Retrovisor Lado Direito  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='retrovdir'>Retrovisor Lado Direito: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $retrovdir</p>";?>
                  <div class=''>
                    <input type="radio" id="okrtdir" name="retrovdir" value="ok">
                    <label for="okrtdir">Ok</label>

                    <input type="radio" id="amrtdir" name="retrovdir" value="amassado">
                    <label for="amrtdir">Amassado</label>
                    
                    <input type="radio" id="qbrtdir" name="retrovdir" value="quebrado">
                    <label for="qbrtdir">Quebrado</label>
                    
                    <input class="ms-1" type="radio" id="arrrtdir" name="retrovdir" value="arranhado">
                    <label for="arrrtdir">Arranhado</label>
                  </div>
                </div>

                <!--div latesq não ok -  Caixa de Ar Lado Direito   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='cxardir'> Caixa de Ar Lado Direito:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $cxaresq</p>";?>
                  <div class=''>
                    <input type="radio" id="okcxdir" name="cxardir" value="ok">
                    <label for="okcxdir">Ok</label>

                    <input class="ms-2" type="radio" id="amcxdir" name="cxardir" value="amassado">
                    <label for="amcxdir">Amassada</label>
                    
                    <input class="ms-2" type="radio" id="arrcxdir" name="cxardir" value="arranhado">
                    <label for="arrcxdir">Arranhada</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Dianteira direita   -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='ptdiantdir'> Porta Dianteira Direita: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $ptdiantdir</p>";?>
                  <div class=''>
                    <input type="radio" id="okpddir" name="ptdiantdir" value="ok">
                    <label for="okpddir">Ok</label>

                    <input class="ms-2" type="radio" id="ampddir" name="ptdiantdir" value="amassado">
                    <label for="ampddir">Amassada</label>
                    
                    <input class="ms-2" type="radio" id="qbpddir" name="ptdiantdir" value="quebrado">
                    <label for="qbpddir">Quebrada</label>
                    
                    <input class="ms-2" type="radio" id="arrpddir" name="ptdiantdir" value="arranhado">
                    <label for="arrpddir">Arranhada</label>
                  </div>
                </div>

                <!--div latesq não ok - Porta Traseira direita  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='pttrasdir'>Porta Traseira Direita:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $pttrasdir</p>";?>
                  <div class=''>
                    <input type="radio" id="okptdir" name="pttrasdir" value="ok">
                    <label for="okptdir">Ok</label>

                    <input class="ms-2" type="radio" id="amptdir" name="pttrasdir" value="amassado">
                    <label for="amptdir">Amassada</label>
                    
                    <input class="ms-2" type="radio" id="qbptdir" name="pttrasdir" value="quebrado">
                    <label for="qbptdir">Quebrada</label>
                    
                    <input class="ms-2" type="radio" id="arrptdir" name="pttrasdir" value="arranhado">
                    <label for="arrptdir">Arranhada</label>
                  </div>
                </div>
              </div>

              <!--div Traseira -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='traseira'> Traseira do veículo - Estado Geral:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $traseira</p>";?>
                <div class=''>
                  <input type="radio" id="oktras" name="traseira" value="ok">
                  <label for="oktras">Ok</label>
                  
                  <input class="ms-1" type="radio" id="noktras" name="traseira" value="naook">
                  <label for="noktras">Não Ok</label>                  
                </div>
              </div>

              <!--div traseira não ok -->
              <div id='traseiranaook' hidden>

                <!--div traseira não ok - lanterna esq -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='lantesq'> Lanterna Esquerda:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $lantesq</p>";?>
                  <div class=''>
                    <input type="radio" id="oklteq" name="lantesq" value="ok">
                    <label for="oklteq">Ok</label>

                    <input class="ms-2" type="radio" id="qblteq" name="lantesq" value="quebrado">
                    <label for="qblteq">Quebrado</label>
                  </div>
                </div>

                <!--div traseira não ok - lanterna dir -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='lantdir'> Lanterna Direita:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $lantdir</p>";?>
                  <div class=''>
                    <input type="radio" id="okltdr" name="lantdir" value="ok">
                    <label for="okltdr">Ok</label>

                    <input class="ms-2" type="radio" id="qbltdr" name="lantdir" value="quebrado">
                    <label for="qbltdr">Quebrado</label>
                  </div>
                </div>

                <!--div traseira não ok -  Tampa da Mala -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tmpmala'>Tampa da Mala:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $tmpmala</p>";?>
                  <div class=''>
                    <input type="radio" id="oktm" name="tmpmala" value="ok">
                    <label for="oktm">Ok</label>

                    <input class="ms-2" type="radio" id="amtm" name="tmpmala" value="amassado">
                    <label for="amtm">Amassado</label>
                    
                    <input class="ms-2" type="radio" id="qbtm" name="tmpmala" value="quebrado">
                    <label for="qbtm">Quebrado</label>
                    
                    <input class="ms-2" type="radio" id="arrtm" name="tmpmala" value="arranhado">
                    <label for="arrtm">Arranhado</label>
                  </div>
                </div>

                <!--div traseira não ok - Para-choque Traseiro -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='parachoquet'>Para-choque Traseiro: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $parachoquet</p>";?>
                  <div class=''>
                    <input type="radio" id="okpct" name="parachoquet" value="ok">
                    <label for="okpct">Ok</label>

                    <input class="ms-2" type="radio" id="ampct" name="parachoquet" value="amassado">
                    <label for="ampct">Amassado</label>
                    
                    <input class="ms-2" type="radio" id="qbpct" name="parachoquet" value="quebrado">
                    <label for="qbpct">Quebrado</label>
                    
                    <input class="ms-2" type="radio" id="arrpct" name="parachoquet" value="arranhado">
                    <label for="arrpct">Arranhado</label>
                  </div>
                </div>
              </div>

              <!--div itens internos -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='itinterno'> Itens Internos - Estado Geral:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $itinterno</p>";?>
                <div class=''>
                  <input type="radio" id="okitin" name="itinterno" value="ok">
                  <label for="okitin">Ok</label>
                  <input class="ms-1" type="radio" id="nokitin" name="itinterno" value="naook">
                  <label for="nokitin">Não Ok</label>                  
                </div>
              </div>

              <!--div itens internos não ok -->
              <div id='itinternonaook' hidden>

                <!--div itens internos não ok - Painel -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='painel'>Painel:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $painel</p>";?>
                  <div class=''>
                    <input type="radio" id="okpainel" name="painel" value="ok">
                    <label for="okpainel">Ok</label>
                    
                    <input class="ms-2" type="radio" id="nokpainel" name="painel" value="naook">
                    <label for="nokpainel">Não Ok</label>
                  </div>
                </div>

                <!--div itens internos não ok - som -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='som'>Som :<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $som</p>";?>
                  <div class=''>
                    <input type="radio" id="oksom" name="som" value="ok">
                    <label for="oksom">Ok</label>
                    <input type="radio" id="noksom" name="som" value="naook">
                    <label for="noksom">Não Ok</label>
                    <input type="radio" id="ausentesom" name="som" value="ausente">
                    <label for="ausentesom">Ausente</label>
                  </div>
                </div>

                <!--div itens internos não ok - Bancos-->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='bancos'>Bancos:<span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $bancos</p>";?>
                  <div class=''>
                    <input type="radio" id="okbancos" name="bancos" value="ok">
                    <label for="okbancos">Ok</label>
                    
                    <input class="ms-2" type="radio" id="nokbancos" name="bancos" value="naook">
                    <label for="nokbancos">Não Ok</label>
                  </div>
                </div>

                <!--div itens internos não ok - Iluminação Interna -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='ilumint'>Iluminação Interna: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $ilumint</p>";?>
                  <div class=''>
                    <input type="radio" id="okilumint" name="ilumint" value="ok">
                    <label for="okilumint">Ok</label>
                    
                    <input class="ms-2" type="radio" id="nokilumint" name="ilumint" value="naook">
                    <label for="nokilumint">Não Ok</label>
                  </div>
                </div>

                <!--div itens internos não ok - Tampa do Bagagito -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tmpbag'>Tampa do Bagagito: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $tmpbag</p>";?>
                  <div class=''>
                    <input type="radio" id="oktmpbag" name="tmpbag" value="ok">
                    <label for="oktmpbag">Ok</label>
                    
                    <input class="ms-2" type="radio" id="quebtmpbag" name="tmpbag" value="quebrado">
                    <label for="quebtmpbag">Quebrado</label>
                    
                    <input class="ms-2" type="radio" id="austmpbag" name="tmpbag" value="ausente">
                    <label for="austmpbag">Ausente</label>
                  </div>
                </div>

                <!--div itens internos não ok - Retrovisor interno  -->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='retrovint'>Retrovisor interno: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $retrovint</p>";?>
                  <div class=''>
                    <input type="radio" id="okretrovint" name="retrovint" value="ok">
                    <label for="okretrovint">Ok</label>
                    
                    <input class="ms-2" type="radio" id="ausretrovint" name="retrovint" value="ausente">
                    <label for="ausretrovint">Ausente</label>
                  </div>
                </div>

                <!--div itens internos não ok - Tapetes-->
                <div class='column mt-2 align-items-center border-top'>
                  <label class='col-form-label' for='tapetes'>Tapetes: <span style='color: red;'>*</span> </label>
                  <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $tapetes</p>";?>
                  <div class=''>
                    <input type="radio" id="oktapetes" name="tapetes" value="ok">
                    <label for="oktapetes">Ok</label>
                    
                    <input class="ms-2" type="radio" id="austapetes" name="tapetes" value="ausente">
                    <label for="austapetes">Ausente</label>
                  </div>
                </div>
              </div>

              <!--div Pneus/Kit Step  -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='pneus'> Pneus:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $pneus</p>";?>
                <div class=''>
                  <input type="radio" id="okpneus" name="pneus" value="ok" required>
                  <label for="okpneus">Ok</label>
                  
                  <input class="ms-2" type="radio" id="naook5" name="pneus" value="naook">
                  <label for="naook5">Não Ok</label>                  
                </div>
              </div>             

              <!--div - Step -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='step'>Step:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $step</p>";?>
                <div class=''>
                  <input type="radio" id="okstep" name="step" value="ok">
                  <label for="okstep">Ok</label>
                  
                  <input class="ms-2" type="radio" id="nokstep" name="step" value="naook">
                  <label for="nokstep">Não Ok</label>
                </div>
              </div>

              <!--div - Marca dos Pneus  -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='marcapneus'>Marca dos Pneus:<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $marcapneus</p>";?>
                  <input type="text" class='form-control' id="marcapneus" name="marcapneus" required>
              </div>

              <!--div - Kit Step -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='kitstep'>Kit Step :<span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $kitstep</p>";?>
                <div class=''>
                  <input type="radio" id="okkitstep" name="kitstep" value="ok" required>
                  <label for="okkitstep">Ok</label>
                  
                  <input class="ms-2" type="radio" id="nokkitstep" name="kitstep" value="naook">
                  <label for="nokkitstep">Não Ok</label>
                </div>
              </div>

              <!--div - Calotas -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='calotas'>Calotas: <span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $calotas</p>";?>
                <div class=''>
                  <input type="radio" id="okcalotas" name="calotas" value="ok">
                  <label for="okcalotas">Ok</label>
                  
                  <input class="ms-2" type="radio" id="nokcalotas" name="calotas" value="naook">
                  <label for="nokcalotas">Não Ok</label>
                </div>
              </div>

              <!--div Bateria-->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='bateria'>Marca da bateria: <span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $bateria</p>";?>                
                  <input type="text" class='form-control' id="bateria" name="bateria" required>
              </div>

              <!--div SafeCar -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='safecar'>SafeCar: <span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $safecar</p>";?>
                <div class=''>
                  <input type="radio" id="oksafecar" name="safecar" value="ok" required>
                  <label for="oksafecar">Ok</label>
                  
                  <input class="ms-2" type="radio" id="aussafecar" name="safecar" value="ausente">
                  <label for="aussafecar">Ausente</label>
                </div>
              </div>

              <!--div Limpeza interna -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='limpint'>Limpeza interna: <span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $limpint</p>";?>
                <div class=''>
                  <input type="radio" id="limpoint" name="limpint" value="limpo" required>
                  <label for="limpoint">Limpo</label>
                  
                  <input class="ms-2" type="radio" id="sujoint" name="limpint" value="sujo">
                  <label for="sujoint">Sujo</label>
                </div>
              </div>

              <!--div Limpeza externa -->
              <div class='column mt-2 align-items-center border-top'>
                <label class='col-form-label' for='limpint'>Limpeza externa: <span style='color: red;'>*</span> </label>
                <?php print"<p class='mt-1 blockquote-footer'> Última vistoria: $limpext</p>";?>
                <div class=''>
                  <input type="radio" id="limpoext" name="limpext" value="limpo" required>
                  <label for="limpoext">Limpo</label>
                  
                  <input class="ms-2" type="radio" id="sujoext" name="limpext" value="sujo">
                  <label for="sujoext">Sujo</label>
                </div>
              </div>   

            
              <!--div observação -->
              <div class='mt-2 border-top'>
                <label class='col-5 col-form-label' for='observacao'> Observação: </label>
                <div class='col-12'>
                  <textarea type='text' name='observacao' id='observacao' class='form-control' placeholder='Observação'> </textarea> 
                </div>
              </div>

              <!-- div campos obrigatórios-->
              <div class="mt-4 col-12">
                <p style='color: red;'> * Campos obrigatórios. </p>
              </div>
            </div>

            <!-- div botão-->
            <div class="mt-4 col-12 text-center">
              <button id='primeirobotao' class="col-4 btn btn-success" type='button'> Próximo </button>
            </div>
          <!--</form>-->