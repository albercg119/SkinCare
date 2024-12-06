<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores</title>
    <!-- Bootstrap y FontAwesome ya incluidos en el head principal -->
</head>
<body>
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

        <!-- Modal para Gestionar Proveedor -->
        <div class="modal fade" id="supplierModal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gestionar Proveedor</h5>
                        <button type="button" class="btn-close cancelar" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="supplierForm">
                            <input type="hidden" id="supplierId" name="supplierId">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
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
</body>
</html>