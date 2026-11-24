<div class="modal fade" id="cadastromodalgestcol" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Alterar KMs</h5>
        <button type="button" class="btn btn-danger close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" id="form_gestcol" action="./control/alterarkmsreclaro.php">
          <input type="hidden" id="mattec" name="mattec"/>
          <input type="hidden" id="datadia" name="datadia"/>
          <input type="hidden" id="matop" name="matop"value="<?php echo "$matricula"; ?>" />
          <input type="hidden" id="dataagora" name="dataagora" value="<?php echo "$hoje"; ?>" />

<label for="decisao" class="form-label">Escolha a operação</label>
<div class="form-check form-check-inline ms-3">
  <input class="form-check-input" type="radio" name="decisaocol" id="decisaoscol" value="1">
  <label class="form-check-label" for="inlineRadio1">Adicionar</label>
</div>
<div class="form-check form-check-inline mb-3">
  <input class="form-check-input" type="radio" name="decisaocol" id="decisaoncol" value="0">
  <label class="form-check-label" for="inlineRadio2">Diminuir</label>
</div>

<div class="form-floating mb-3 numeric-input">
  <input class="form-control" type="number" name="numericInput" id="numericInput" min="0" step="0.1" placeholder="Número com uma casa decimal" required>
  <label for="numericInput">Valor KMs</label>
</div>

      <div class="form-floating mb-3">
        <textarea class="form-control" name='jutificativacol' id="jutificativacol" type="text" placeholder="Justificativa" rows="4" cols="50" style="height: 200px !important;" required></textarea>
        <label for="narrafat">Motivo</label>
      </div>
        
        <div class="text-danger" id="error_msggestcol" style="  text-align: center;
        font-size: 17px;
        margin-bottom: 10px;
        font-style: italic;"></div>
        <div class="text-sucess" id="enviar_msggestcol" style="  text-align: center;
        font-size: 17px;
        margin-bottom: 10px;
        font-style: italic;"></div>
      </div>
      <div class="modal-footer justify-content-center" id="modal-includegestcol">
        <input type="submit" id="button_editar" class="btn btn-success"></button>
</form>
      </div>
    </div>
  </div>
</div>
