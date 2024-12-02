// Constantes para las rutas de la API
const API_BASE_URL = '/SkinCare/api/v1/orders';

async function loadOrders() {
    try {
        console.log('Cargando pedidos...');
        const response = await fetch(`${API_BASE_URL}/read.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Datos recibidos:', data);
        
        const tableBody = document.getElementById('ordersTableBody');
        if (!tableBody) {
            console.error('No se encontró el elemento ordersTableBody');
            return;
        }

        if (data.status === 'success') {
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay pedidos disponibles</td></tr>';
                return;
            }

            tableBody.innerHTML = '';
            data.data.forEach(order => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${order.id}</td>
                        <td>${order.fecha_pedido}</td>
                        <td>${order.id_proveedor}</td>
                        <td><span class="badge bg-${getStatusBadgeColor(order.estado_pedido)}">${order.estado_pedido}</span></td>
                        <td>$${parseFloat(order.total || 0).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-info me-2" onclick="showOrderDetails(${order.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="editOrder(${order.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteOrder(${order.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            throw new Error(data.message || 'Error al cargar pedidos');
        }
    } catch (error) {
        console.error('Error al cargar pedidos:', error);
        const tableBody = document.getElementById('ordersTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Error al cargar pedidos: ${error.message}
                    </td>
                </tr>
            `;
        }
    }
}        

// Función para cargar proveedores
async function loadSuppliers() {
    try {
        const response = await fetch('/SkinCare/api/v1/suppliers/read.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        if (data.status === 'success' && data.data) {
            populateSupplierSelect(data.data);
        } else {
            throw new Error(data.message || 'Error al cargar los proveedores');
        }
    } catch (error) {
        console.error('Error al cargar proveedores:', error);
        alert('Error al cargar los proveedores: ' + error.message);
    }
}

// Esta función se llama cuando hacemos clic en el botón de editar
async function editOrder(id) {
    console.log('Iniciando edición del pedido:', id);
    try {
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        const data = await response.json();
        
        if (data.status === 'success' && data.data) {
            const order = data.data;
            
            // Establecer explícitamente el ID en el campo oculto
            const orderIdInput = document.getElementById('orderId');
            if (orderIdInput) {
                orderIdInput.value = id;
                console.log('ID establecido en el formulario:', id); // Debug
            } else {
                console.error('Campo orderId no encontrado');
            }
            
            // Establecer los demás valores
            document.getElementById('proveedor').value = order.id_proveedor;
            document.getElementById('estado').value = order.estado_pedido;
            
            // Mostrar el modal
            window.showOrderModal();
        } else {
            throw new Error('Error al cargar el pedido');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar el pedido: ' + error.message);
    }
}


// Función para eliminar pedido
async function deleteOrder(id) {
    if (!confirm('¿Está seguro de eliminar este pedido?')) return;

    try {
        const response = await fetch(`${API_BASE_URL}/delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: parseInt(id) })
        });

        const data = await response.json();
        if (data.status === 'success') {
            alert('Pedido eliminado exitosamente');
            await loadOrders();
        } else {
            throw new Error(data.message || 'Error al eliminar el pedido');
        }
    } catch (error) {
        console.error('Error al eliminar:', error);
        alert('Error al eliminar el pedido: ' + error.message);
    }
}



async function handleFormSubmit(event) {
    event.preventDefault();
    
    try {
        // Obtener el ID y validarlo explícitamente
        const orderIdInput = document.getElementById('orderId');
        const orderId = orderIdInput ? orderIdInput.value : null;
        
        console.log('ID del pedido en el formulario:', orderId); // Debug
        
        if (!orderId) {
            throw new Error('No se encontró el ID del pedido');
        }
        
        const formData = {
            id: parseInt(orderId), // Asegurarnos de que sea un número
            estado_pedido: document.getElementById('estado').value.trim(),
            id_proveedor: document.getElementById('proveedor').value
        };

        console.log('Datos a enviar:', formData); // Debug
        
        const response = await fetch(`${API_BASE_URL}/update.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        console.log('Respuesta del servidor:', data); // Debug
        
        if (data.status === 'success') {
            orderIdInput.value = ''; // Limpiar el ID
            window.hideOrderModal();
            await loadOrders();
            alert('Pedido actualizado exitosamente');
        } else {
            throw new Error(data.message || 'Error al actualizar el pedido');
        }
    } catch (error) {
        console.error('Error en el formulario:', error);
        alert(error.message);
    }
}

// Aseguramos que el botón de editar tenga el evento correcto
document.querySelectorAll('.edit-order-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const orderId = e.target.closest('button').dataset.orderId;
        editOrder(orderId);
    });
});



async function agregarProducto(event) {
    event.preventDefault();
    try {
        // Cambiar a un endpoint que traiga la información del inventario
        const response = await fetch('/SkinCare/api/v1/products/read.php');
        const result = await response.json();
        
        if (result.status === 'success') {
            const selectProducto = document.createElement('select');
            selectProducto.className = 'form-select mb-2';
            
            // Modificar para usar ID_Inventario en lugar de ID_Producto
            result.data.forEach(producto => {
                const option = document.createElement('option');
                // Usar ID_Inventario en lugar de id
                option.value = producto.id_inventario; // Este cambio es clave
                option.textContent = `${producto.nombre} - Stock: ${producto.cantidad_stock}`;
                selectProducto.appendChild(option);
            });
            
            const inputCantidad = document.createElement('input');
            inputCantidad.type = 'number';
            inputCantidad.className = 'form-control mb-2';
            inputCantidad.placeholder = 'Cantidad';
            
            const productosLista = document.querySelector('#productos-lista');
            const productoDiv = document.createElement('div');
            productoDiv.className = 'producto-item mb-3';
            productoDiv.appendChild(selectProducto);
            productoDiv.appendChild(inputCantidad);
            
            productosLista.appendChild(productoDiv);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
function showOrderDetails(id) {
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    
    fetch(`/SkinCare/api/v1/orders/details.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const detailsTable = document.getElementById('orderDetailsTable');
                detailsTable.innerHTML = data.data.detalles.map(detail => `
                    <tr>
                        <td>${detail.nombre}</td>
                        <td>${detail.cantidad}</td>
                        <td>$${parseFloat(detail.precio).toFixed(2)}</td>
                        <td>$${(detail.cantidad * detail.precio).toFixed(2)}</td>
                    </tr>
                `).join('');
                
                document.getElementById('orderTotal').textContent = 
                    `Total: $${parseFloat(data.data.total).toFixed(2)}`;
                
                modal.show();
            }
        })
        .catch(error => console.error('Error:', error));
}

// Funciones de utilidad
function renderOrdersTable(orders) {
    const tbody = document.getElementById('ordersTableBody');
    tbody.innerHTML = '';

    orders.forEach(order => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${order.id}</td>
            <td>${order.fecha_pedido}</td>
            <td>${order.id_proveedor}</td>
            <td><span class="badge bg-${getStatusBadgeColor(order.estado_pedido)}">${order.estado_pedido}</span></td>
            <td>
                <button class="btn btn-sm btn-primary" onclick="editOrder(${order.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteOrder(${order.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function populateSupplierSelect(suppliers) {
    const select = document.getElementById('proveedor');
    select.innerHTML = '<option value="">Seleccione un proveedor</option>';
    suppliers.forEach(supplier => {
        select.innerHTML += `<option value="${supplier.id}">${supplier.nombre}</option>`;
    });
}

function getStatusBadgeColor(status) {
    const colors = {
        'pendiente': 'warning',
        'enviado': 'info',
        'entregado': 'success'
    };
    return colors[status] || 'secondary';
}

function showCreateOrderModal() {
    document.getElementById('orderForm').reset();
    document.getElementById('orderId').value = '';
    document.querySelector('.modal-title').textContent = 'Nuevo Pedido';
    orderModal.show();
}

window.showOrderModal = function() {
    const modal = document.querySelector('#orderModal');
    if (modal) {
        // Crear backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
        
        // Mostrar modal
        modal.classList.add('show');
        modal.style.display = 'block';
        document.body.classList.add('modal-open');
        document.body.style.overflow = 'hidden';
    }
};

window.hideOrderModal = function() {
    const modal = document.querySelector('#orderModal');
    if (modal) {
        // Ocultar modal
        modal.classList.remove('show');
        modal.style.display = 'none';
        
        // Limpiar backdrop
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Restaurar el body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
};
function setupFormListener() {
    const form = document.getElementById('orderForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

function setupModalListeners() {
    const cancelarBtn = document.querySelector('.modal .cancelar, .modal .btn-secondary');
    if (cancelarBtn) {
        cancelarBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.hideOrderModal();
        });
    }
}

async function filterOrders() {
    try {
        const estado = document.getElementById('filterStatus').value;
        const fecha = document.getElementById('filterDate').value;
        
        console.log('Aplicando filtros:', { estado, fecha });
        
        // Construir URL con parámetros solo si tienen valor
        let url = `${API_BASE_URL}/read.php`;
        const params = new URLSearchParams();
        
        if (estado) params.append('estado', estado);
        if (fecha) params.append('fecha', fecha);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.status === 'success') {
            renderOrdersTable(data.data);
        } else {
            throw new Error(data.message || 'Error al filtrar pedidos');
        }
    } catch (error) {
        console.error('Error al filtrar:', error);
        alert('Error al filtrar pedidos: ' + error.message);
    }
}


document.addEventListener('DOMContentLoaded', () => {
    // Asignar eventos
    const nuevoPedidoBtn = document.querySelector('.nuevo-pedido');
    if (nuevoPedidoBtn) {
        nuevoPedidoBtn.addEventListener('click', () => {
            document.getElementById('orderForm').reset();
            document.getElementById('orderId').value = '';
            document.querySelector('.modal-title').textContent = 'Nuevo Pedido';
            window.showOrderModal();
        });
    }

    const cancelarBtn = document.querySelector('.cancelar');
    if (cancelarBtn) {
        cancelarBtn.addEventListener('click', () => window.hideOrderModal());
    }

    const orderForm = document.querySelector('#orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', handleFormSubmit);
    }

    // Cargar datos iniciales
    loadOrders();
    loadSuppliers();    
});


// Exponer funciones necesarias globalmente
window.editOrder = editOrder;
window.deleteOrder = deleteOrder;
window.handleFormSubmit = handleFormSubmit;
window.showCreateOrderModal = showCreateOrderModal;
window.agregarProducto = agregarProducto;
window.showOrderDetails = showOrderDetails;
window.filterOrders = filterOrders;