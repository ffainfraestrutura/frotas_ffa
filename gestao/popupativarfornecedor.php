<div class="modal fade" id="cadastromodaldir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Decisão de Desconto</h5>
        <button type="button" class="btn btn-danger close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" id="form_dir" action="./control/diretoriaup.php">
          <input type="hidden" id="idprocesso" name="idprocesso"/>
<label for="decisao" class="form-label">Deve Descontar?</label>
<div class="form-check form-check-inline ms-3">
  <input class="form-check-input" type="radio" name="decisaodir" id="decisaosdir" value="1">
  <label class="form-check-label" for="inlineRadio1">Sim</label>
</div>
<div class="form-check form-check-inline mb-3">
  <input class="form-check-input" type="radio" name="decisaodir" id="decisaondir" value="0">
  <label class="form-check-label" for="inlineRadio2">Não</label>
</div>
      <div class="form-floating mb-3">
        <textarea class="form-control" name='jutificativadir' id="jutificativadir" type="text" placeholder="Justificativa" rows="4" cols="50" style="height: 200px !important;"></textarea>
        <label for="narrafat">Justificativa</label>
      </div>
        </form>
        <div class="text-danger" id="error_msgdir" style="  text-align: center;
        font-size: 17px;
        margin-bottom: 10px;
        font-style: italic;"></div>
        <div class="text-sucess" id="enviar_msgdir" style="  text-align: center;
        font-size: 17px;
        margin-bottom: 10px;
        font-style: italic;"></div>
      </div>
      <div class="modal-footer justify-content-center" id="modal-includedir">
        <button type="button" id="button_editar" class="btn btn-success" onclick="valida_formulariodir()">Enviar</button>
      </div>
    </div>
  </div>
</div>
