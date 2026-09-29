        </main>
    </div>

    <!-- Modal de confirmacion propio del panel: reemplaza al confirm() del navegador (gris, sin
         marca) en cualquier formulario con data-confirmar="mensaje". Ver assets/js/app.js -->
    <div class="modal fade" id="modalConfirmar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content sa-modal-confirmar">
                <div class="modal-header">
                    <h2 class="modal-title h5"><i class="bi bi-question-circle text-danger"></i> Confirmar</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modalConfirmarTexto"></div>
                <div class="modal-footer">
                    <button type="button" class="btn-linea" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-sa" id="modalConfirmarAceptar">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= url("assets/vendor/bootstrap/bootstrap.bundle.min.js") ?>"></script>
    <script src="<?= recurso("assets/js/app.js") ?>"></script>
</body>

</html>
