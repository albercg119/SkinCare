<main class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-box"></i> Gestión de Productos</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary nuevo-producto">
                <i class="fas fa-plus"></i> Nuevo Producto
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
                            <th>Marca</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <!-- Se cargará dinámicamente con JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar productos -->
    <div class="modal" id="productModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gestionar Producto</h5>
                </div>
                <div class="modal-body">
    <form id="productForm">
    <input type="hidden" id="productId" name="productId">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" required>
        </div>
        <div class="mb-3">
            <label for="marca" class="form-label">Marca</label>
            <input type="text" class="form-control" id="marca" required>
        </div>
        <div class="mb-3">
            <label for="precio" class="form-label">Precio</label>
            <input type="number" step="0.01" class="form-control" id="precio" required>
        </div>
        <div class="mb-3">
            <label for="stock" class="form-label">Stock</label>
            <input type="number" class="form-control" id="stock" required>
        </div>
        <!-- Nuevo campo para ubicación -->
        <div class="mb-3">
    <label for="ubicacion" class="form-label">Ubicación</label>
    <select id="ubicacion" name="ubicacion" class="form-control" required>
        <option value="">Seleccione una ubicación</option>
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