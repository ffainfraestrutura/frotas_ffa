<div class="modal fade" id="cadastromodaljustind" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">KM OS menor que KM Projetado, você deve justificar</h5>
        <button type="button" class="btn btn-danger close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" id="form_dir" action="./control/diretoriaup.php">
          <input type="hidden" id="idprocesso" name="idprocesso"/>

      <div class="form-floating mb-3">
        <textarea class="form-control" name='jutificativaind' id="jutificativaind" type="text" placeholder="Justificativa" rows="4" cols="50" style="height: 200px !important;" required></textarea>
        <label for="jutificativaind">Justificativa</label>
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
        <button type="button" id="button_editar" class="btn btn-success" onclick="valida_formulariojustind()">Enviar</button>
      </div>
    </div>
  </div>
</div>
