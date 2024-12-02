<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="fas fa-shopping-cart"></i> Gestión de Pedidos</h2>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal">
            <i class="fas fa-plus"></i> Nuevo Pedido
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="filterStatus" class="form-label">Estado</label>
                    <select class="form-control" id="filterStatus">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregado">Entregado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="filterDate" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="filterDate">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary d-block w-100" onclick="filterOrders()">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Proveedor</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <!-- Se cargará dinámicamente con JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Pedido -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="orderForm">
            <input type="hidden" id="orderId" name="orderId">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="proveedor" class="form-label">Proveedor</label>
                            <select class="form-control" id="proveedor" required>
                                <!-- Se cargará dinámicamente -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-control" id="estado" required>
                                <option value="pendiente">Pendiente</option>
                                <option value="enviado">Enviado</option>
                                <option value="entregado">Entregado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="productos-container">
                        <h6>Productos</h6>
                        <div id="productos-lista">
                            <!-- Se agregarán dinámicamente -->
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="agregarProducto(event)">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="orderDetailsTable">
                        </tbody>
                    </table>
                    <div class="text-end">
                        <h4 id="orderTotal"></h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>