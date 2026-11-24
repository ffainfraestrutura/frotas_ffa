<?php
include"../conecta2.php";

?>

<div class="modal fade" id="apagaros" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Apagar Registro</h5>
        <button type="button" class="btn btn-danger close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="p-2 modal-body"><!---->        
        <form class='mt-2 ms-1' method='post' action='https://ffa.painel-telecom.com:4545/frotas/gestao/control/apagaros.php' target="_blank">                    
          <?php print"<input type='hidden' name='idtbmanprev' id='inputform'>
          <input type='hidden' name='matr_autor' value='$matricula'>
          <input type='hidden' name='placa' id='inputplaca'>
          ";?>
            
          <p class='mb-0 form-label'>Deseja apagar este registro de manutenção?<span style='color:red;'>*</span></p>

          <div>
            <input type="radio" id="sim" name="escolha" value="1">
            <label for="sim">Sim</label><br>
            <input type="radio" id="nao" name="escolha" value="0">
            <label for="nao">Não</label><br>
          </div>
                              
          <button type='submit' class='mt-2 btn btn-success'> Confirmar </button>
        </form>        
      </div>
      
      <div class="modal-footer justify-content-center" id="modal-includejust">

      </div>
    </div>
  </div>
</div>
