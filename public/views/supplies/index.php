<main class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-boxes"></i> Gestión de Suministros</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary nuevo-suministro">
                <i class="fas fa-plus"></i> Nuevo Suministro
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
                            <th>Artículo</th>
                            <th>Cantidad</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="suppliesTableBody">
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

<script src="/SkinCare/public/assets/js/supplies.js"></script>