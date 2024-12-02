<main class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-truck"></i> Gestión de Proveedores</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary nuevo-proveedor">
                <i class="fas fa-plus"></i> Nuevo Proveedor
            </button>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo Electrónico</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="suppliersTableBody">
                        <!-- Se cargará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal" id="suppliesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Suministro</h5>
                <button type="button" class="btn-close cancelar" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="suppliesForm">
                    <input type="hidden" id="supplyId" name="supplyId">                 
                    <div class="mb-3">
                        <label for="article" class="form-label">Artículo</label>
                        <select id="article" name="article" class="form-control" required>
                            <option value="">Seleccione un artículo</option>
                        </select>
                    </div>                        
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required min="1">
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