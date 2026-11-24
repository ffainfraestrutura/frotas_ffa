<form method='post' action='./control/checklistp1-manual.php'  enctype='multipart/form-data' id='primeiroform'>

<?php
$sql4="SELECT nome, cpf, ccusto FROM bdaniel.tbfuncionario WHERE matricula='$matrcond'; ";
$resultado4 = mysqli_query($con, $sql4) or die(mysqli_error($con));
$row4 = mysqli_fetch_array($resultado4, MYSQLI_ASSOC);
  $condutor = $row4['nome'];
  $cpf2 = $row4['cpf'];
  $ccusto = utf8_encode($row4['ccusto']);

$sql5="SELECT numcnh, validade, categoria FROM `bdfrota-teste`.tbcnh WHERE matricula='$matrcond'; ";
$resultado5 = mysqli_query($con, $sql5) or die(mysqli_error($con));
$row5 = mysqli_fetch_array($resultado5, MYSQLI_ASSOC);
  $cnh = $row5['numcnh'];
  $validade = $row5['validade'];
  $categoria = $row5['categoria']; 

$validade1 = explode(" ", $validade);
$validade1a = explode("-", $validade1[0]);
$validade1f = $validade1a[2]."/".$validade1a[1]."/".$validade1a[0];

print "
  <!--div informações do condutor-->    
  <div class='mt-4 border-top border-dark pt-2'>
    <h6> Informações do condutor </h6>

    <!--div cpf-->
    <div class='row mt-2 col-12'>
      <input type='hidden' name='placa' value='$placacorreta'>
          
      <label class='col-3 col-form-label' for='cpf'> CPF:<span style='color: red;'>*</span> </label>
      <div class='col-7'>
        <input type='text' name='cpf' id='cpf' class='form-control' placeholder='CPF do Condutor' required value='$cpf2'>
      </div>
    </div>

    <!--div nome--> 
    <div class='row mt-2 col-12'>
      <label class='col-2 col-form-label' for='nome'> Nome:<span style='color: red;'>*</span> </label>
      <div class='col-10'>
        <input type='text' name='nome' id='nome' class='form-control' placeholder='Nome do Condutor' required value='$condutor'>
      </div>
    </div>

    <!--div matricula-->
    <div class='row mt-2 col-12'>
      <label class='col-3 col-form-label' for='matricula'> Matrícula:<span style='color: red;'>*</span> </label>
      <div class='col-9'>
        <input type='text' name='matricula' id='matricula' class='form-control' placeholder='Matricula do Condutor' required value='$matrcond'>
      </div>
    </div>                

    <!--div numero cnh-->
    <div class='row mt-2 col-12'>
      <label class='col-3 col-form-label' for='cnh'> CNH:<span style='color: red;'>*</span> </label>
      <div class='col-9'>
        <input type='text' name='cnh' id='cnh2' class='form-control' placeholder='CNH do Condutor' required value='$cnh'>
      </div>
    </div>

    <!--div categoria cnh-->
    <div class='row mt-2 col-12'>
      <label class='col-7 col-form-label' for='categoriacnh'> Categoria CNH:<span style='color: red;'>*</span> </label>
      <div class='col-5'>
        <input type='text' name='categoriacnh' id='categoriacnh2' class='form-control' placeholder='Categoria' required value='$categoria'>
      </div>
    </div>

    <!--div validade cnh-->
    <div class='row mt-2'>
      <label class='col-5 col-form-label' for='validadecnh'> Vencimento CNH:<span style='color: red;'>*</span> </label>
      <div class='col-6'><!--type='text' $validade1f-->
        <input type='date' name='validadecnh' id='validadecnh2' value='$validade1[0]' class='data form-control' required>
      </div>
    </div>
        

    <div class='row mt-2'>
      <label class='col-5 col-form-label' for='centrocusto'> Centro de Custo: <span style='color: red;'>*</span> </label>
      <div class='col-6'>
        <input type='text' name='centrocusto' id='centrocusto' class='form-control' placeholder='Centro Custo' required value='$ccusto'>
      </div>
    </div>                       
  </div>";

?>

