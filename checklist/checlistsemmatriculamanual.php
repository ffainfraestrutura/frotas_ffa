
      <?php
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

          <div class=''><!--checklistp1-manual.php-->
            <form method='post' action='./checklistp1-manual.php' id='cpfachar'>

              <p class=''>Caso as Informações do condutor não venham pré-carregadas, preencha o campo CPF e clice no ícone <span class='material-symbols-outlined mt-1'>arrow_forward</span>. As demais Informações do condutor serão inseridas automaticamente.</p>
              
              <input type='hidden' name='placa' value='$placaveic'>

              <div class='row mt-2 col-12 align-items-center'>    
                <label class='col-3 col-form-label' for='cpf1'> CPF:<span style='color: red;'>*</span> </label>
                <div class='col-7'>
                  <input type='text' name='cpf1' id='cpf1' class='inputtexto form-control' placeholder='CPF do Condutor' required value='$cpf1'>
                </div>

                <div class='col-2 $dd' id='botaoacharcpf'>
                  <button type='submit' style='background-color: transparent; border: none;'><span class='material-symbols-outlined'>arrow_forward</span>
                  </button>
                </div>
              </div>
            </form>";

            print"
            <form method='post' action='./control/checklistp1-manual.php'  enctype='multipart/form-data' id='primeiroform'>
              <input type='hidden' name='placa' value='$placacorreta'>
              <input type='hidden' name='cpf' value='$cpf1'>

              <!--div nome--> 
              <div class='row mt-2 col-12 $d'>
                <label class='col-2 col-form-label' for='nome'> Nome:<span style='color: red;'>*</span> </label>
                <div class='col-10'>
                  <input type='text' name='nome' id='nome' class='form-control inputtexto' placeholder='Nome do Condutor' required value='$condutor2'>
                </div>
              </div>

              <!--div matricula-->
              <div class='row mt-2 col-12 $d'>
                <label class='col-3 col-form-label' for='matricula'> Matrícula:<span style='color: red;'>*</span> </label>
                <div class='col-9'>
                  <input type='text' name='matricula' id='matricula' class='form-control inputtexto' placeholder='Matricula do Condutor' required value='$matrcond2'>
                </div>
              </div>                

              <!--div numero cnh-->
              <div class='row mt-2 col-12 $d'>
                <label class='col-3 col-form-label' for='cnh'> CNH:<span style='color: red;'>*</span> </label>
                <div class='col-9'>
                  <input type='text' name='cnh' id='cnh2' class='form-control inputtexto' placeholder='CNH do Condutor' required value='$cnh2'>
                </div>
              </div>

              <!--div categoria cnh-->
              <div class='row mt-2 col-12 $d'>
                <label class='col-7 col-form-label' for='categoriacnh'> Categoria CNH:<span style='color: red;'>*</span> </label>
                <div class='col-5'>
                  <input type='text' name='categoriacnh' id='categoriacnh2' class='form-control inputtexto' placeholder='Categoria' required value='$categoria2'>
                </div>
              </div>

              <!--div validade cnh-->
              <div class='row mt-2 $d'>
                <label class='col-5 col-form-label' for='validadecnh'> Vencimento CNH:<span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='validadecnh' id='validadecnh2' value='$validade2f' class='data form-control inputtexto' required>
                </div>
              </div>
              

              <div class='row mt-2 $d'>
                <label class='col-5 col-form-label' for='centrocusto'> Centro de Custo: <span style='color: red;'>*</span> </label>
                <div class='col-6'>
                  <input type='text' name='centrocusto' id='centrocusto' class='form-control inputtexto' placeholder='Centro Custo' required value='$ccusto2'>
                </div>
              </div>                       
            
          </div>
        </div>";
          
      ?>

      <!--</form>-->