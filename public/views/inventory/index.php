<main class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-warehouse"></i> Control de Inventario</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary nuevo-inventario">
                <i class="fas fa-plus"></i> Nuevo Registro
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID Inventario</th>
                            <th>Producto</th>
                            <th>Ubicación</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <!-- Se cargará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar registros de inventario -->
    <div class="modal" id="inventoryModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gestionar Inventario</h5>
                </div>
                <div class="modal-body">
                    <form id="inventoryForm">
                        <input type="hidden" id="inventoryId" name="inventoryId">
                        <div class="mb-3">
                            <label for="producto" class="form-label">Producto</label>
                            <select class="form-select" id="producto" required>
                                <!-- Se cargará dinámicamente con JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <select class="form-select" id="ubicacion" required>
                                <option value="">Seleccione ubicación</option>
                                <option value="Almacén Principal - Estantería A1">Almacén Principal - Estantería A1</option>
                                <option value="Almacén Secundario - Estantería B2">Almacén Secundario - Estantería B2</option>
                                <option value="Área de Exhibición - Estante C3">Área de Exhibición - Estante C3</option>
                            </select>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary cancelar">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/SkinCare/public/assets/js/inventory.js"></script>