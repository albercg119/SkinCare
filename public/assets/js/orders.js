// Constantes para las rutas de la API
const API_BASE_URL = '/SkinCare/api/v1/orders';

// Función para cargar pedidos
// Modificar loadOrders para incluir logging detallado
async function loadOrders() {
    try {
        console.log('Cargando pedidos...');
        const response = await fetch(`${API_BASE_URL}/read.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Datos completos recibidos:', data);
        
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

            console.log('Datos a renderizar:', data.data);

            tableBody.innerHTML = '';
            data.data.forEach(order => {
                // Asegurarnos de que el total sea un número y tenga el formato correcto
                const total = typeof order.total === 'string' ? 
                            parseFloat(order.total.replace(/[^0-9.-]+/g, "")) : 
                            parseFloat(order.total || 0);
                
                console.log(`Pedido ${order.id} - Total original:`, order.total, 'Total procesado:', total);

                tableBody.innerHTML += `
                    <tr>
                        <td>${order.id}</td>
                        <td>${order.fecha_pedido}</td>
                        <td>${order.id_proveedor}</td>
                        <td><span class="badge bg-${getStatusBadgeColor(order.estado_pedido)}">${order.estado_pedido}</span></td>
                        <td>$${total.toFixed(2)}</td>
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

// Función para renderizar la tabla
function renderOrdersTable(orders) {
    const tbody = document.getElementById('ordersTableBody');
    tbody.innerHTML = '';

    console.log('Orders a renderizar:', orders);

    orders.forEach(order => {
        // Convertir el total a número y manejar casos nulos o undefined
        const total = order.total ? Number(order.total) : 0;
        console.log(`Order ${order.id} total antes de render:`, total);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${order.id}</td>
            <td>${order.fecha_pedido}</td>
            <td>${order.id_proveedor}</td>
            <td><span class="badge bg-${getStatusBadgeColor(order.estado_pedido)}">${order.estado_pedido}</span></td>
            <td>$${total.toFixed(2)}</td>
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
        `;
        tbody.appendChild(tr);
    });

    if (!orders || orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay pedidos disponibles</td></tr>';
    }
}

// Función auxiliar para obtener el color del badge según el estado
function getStatusBadgeColor(status) {
    const colors = {
        'pendiente': 'warning',
        'enviado': 'info',
        'entregado': 'success'
    };
    return colors[status] || 'secondary';
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
        // Obtener datos básicos del pedido
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        const data = await response.json();
        
        // Obtener detalles de los productos del pedido
        const detailsResponse = await fetch(`${API_BASE_URL}/details.php?id=${id}`);
        const detailsData = await detailsResponse.json();
        
        if (data.status === 'success' && data.data) {
            const order = data.data;
            
            // Establecer el ID en el campo oculto
            const orderIdInput = document.getElementById('orderId');
            if (orderIdInput) {
                orderIdInput.value = id;
                console.log('ID establecido en el formulario:', id);
            } else {
                console.error('Campo orderId no encontrado');
            }
            
            // Establecer los demás valores
            document.getElementById('proveedor').value = order.id_proveedor;
            document.getElementById('estado').value = order.estado_pedido;
            
            // Limpiar la lista de productos existente
            const productosLista = document.getElementById('productos-lista');
            productosLista.innerHTML = '';
            
            // Cargar todos los productos disponibles y añadir los del pedido
            const productsResponse = await fetch('/SkinCare/api/v1/products/read.php');
            const productsData = await productsResponse.json();
            
            if (detailsData.status === 'success' && detailsData.data.detalles) {
                for (const detalle of detailsData.data.detalles) {
                    const selectProducto = document.createElement('select');
                    selectProducto.className = 'form-select mb-2';
                    
                    // Poblar el select con todos los productos
                    productsData.data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.id_inventario;
                        option.textContent = `${producto.nombre} - Stock: ${producto.cantidad_stock}`;
                        option.selected = producto.id_inventario === detalle.id_inventario;
                        selectProducto.appendChild(option);
                    });
                    
                    const inputCantidad = document.createElement('input');
                    inputCantidad.type = 'number';
                    inputCantidad.className = 'form-control mb-2';
                    inputCantidad.value = detalle.cantidad;
                    
                    const productoDiv = document.createElement('div');
                    productoDiv.className = 'producto-item mb-3';
                    productoDiv.appendChild(selectProducto);
                    productoDiv.appendChild(inputCantidad);
                    
                    productosLista.appendChild(productoDiv);
                }
            }
            
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



// También necesitamos actualizar la función filterOrders para usar la misma estructura
async function filterOrders() {
    try {
        const estado = document.getElementById('filterStatus').value;
        const fecha = document.getElementById('filterDate').value;
        
        console.log('Aplicando filtros:', { estado, fecha });
        
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
            renderOrdersTable(data.data); // Ahora usará la función actualizada
        } else {
            throw new Error(data.message || 'Error al filtrar pedidos');
        }
    } catch (error) {
        console.error('Error al filtrar:', error);
        const tbody = document.getElementById('ordersTableBody');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Error al filtrar pedidos: ${error.message}
                    </td>
                </tr>
            `;
        }
    }
}

function populateSupplierSelect(suppliers) {
    const select = document.getElementById('proveedor');
    select.innerHTML = '<option value="">Seleccione un proveedor</option>';
    suppliers.forEach(supplier => {
        select.innerHTML += `<option value="${supplier.id}">${supplier.nombre}</option>`;
    });
}



function showCreateOrderModal() {
    document.getElementById('orderForm').reset();
    document.getElementById('orderId').value = '';
    document.querySelector('.modal-title').textContent = 'Nuevo Pedido';
    orderModal.show();
}

// 1. Modificar el evento para nuevo pedido
document.addEventListener('DOMContentLoaded', () => {
    const nuevoPedidoBtn = document.querySelector('[data-bs-target="#orderModal"]');
    if (nuevoPedidoBtn) {
        nuevoPedidoBtn.addEventListener('click', () => {
            // Limpiar el formulario completamente
            const form = document.getElementById('orderForm');
            form.reset();
            
            // Asegurarse de que el ID esté vacío
            document.getElementById('orderId').value = '';
            
            // Limpiar la lista de productos
            document.getElementById('productos-lista').innerHTML = '';
            
            // Cambiar el título del modal
            document.querySelector('.modal-title').textContent = 'Nuevo Pedido';
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('orderModal'));
            modal.show();
        });
    }
});

// Modificar las funciones del modal para usar el mismo enfoque que en productos
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

function setupModalListeners() {
    // Botón Cancelar
    const cancelarBtns = document.querySelectorAll('.modal .btn-secondary, .modal .cancelar');
    cancelarBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            window.hideOrderModal();
        });
    });

    // Botón de cerrar (X)
    const closeBtns = document.querySelectorAll('.modal .btn-close');
    closeBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            window.hideOrderModal();
        });
    });

    // Click fuera del modal
    const modal = document.querySelector('#orderModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                window.hideOrderModal();
            }
        });
    }

    // Tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.hideOrderModal();
        }
    });
}


async function handleFormSubmit(event) {
    // Prevenir el comportamiento predeterminado del formulario
    event.preventDefault();
    
    try {
        // Obtener y validar el proveedor
        const proveedorValue = document.getElementById('proveedor').value;
        if (!proveedorValue) {
            throw new Error('Debe seleccionar un proveedor');
        }

        // Recopilar y validar los productos del pedido
        const productoItems = document.querySelectorAll('.producto-item');
        
        // Verificar que haya al menos un producto
        if (productoItems.length === 0) {
            throw new Error('Debe agregar al menos un producto');
        }

        // Mapear y validar cada producto
        const productos = Array.from(productoItems).map((item, index) => {
            const selectProducto = item.querySelector('select');
            const inputCantidad = item.querySelector('input[type="number"]');
            
            // Validaciones específicas para cada producto
            if (!selectProducto || !selectProducto.value) {
                throw new Error(`Debe seleccionar el producto #${index + 1}`);
            }

            if (!inputCantidad || !inputCantidad.value) {
                throw new Error(`Debe especificar la cantidad para el producto #${index + 1}`);
            }

            const cantidad = parseInt(inputCantidad.value);
            if (isNaN(cantidad) || cantidad <= 0) {
                throw new Error(`La cantidad del producto #${index + 1} debe ser mayor a 0`);
            }

            if (cantidad > 99999) {
                throw new Error(`La cantidad del producto #${index + 1} no puede exceder 99,999 unidades`);
            }

            return {
                id_inventario: parseInt(selectProducto.value),
                cantidad: cantidad
            };
        });

        // Construir el objeto de datos a enviar
        const formData = {
            id_proveedor: parseInt(proveedorValue),
            estado_pedido: document.getElementById('estado').value, // Usar el valor directo sin modificar
            productos: productos
        };

        // Determinar si es una actualización o creación nueva
        const orderIdInput = document.getElementById('orderId');
        const isUpdate = orderIdInput && orderIdInput.value !== '';
        
        if (isUpdate) {
            formData.id = parseInt(orderIdInput.value);
        }

        // Construir la URL correspondiente
        const url = isUpdate ? 
            `${API_BASE_URL}/update.php` : 
            `${API_BASE_URL}/create.php`;

        // Log para depuración
        console.log('Enviando datos:', JSON.stringify(formData));

        // Realizar la petición al servidor
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        // Procesar la respuesta
        const result = await response.json();
        console.log('Respuesta del servidor:', result);
        
        // Manejar el resultado exitoso
        if (result.status === 'success') {
            // Cerrar el modal y actualizar la lista
            window.hideOrderModal();
            
            // Pequeño retardo para asegurar que el modal se cierre correctamente
            setTimeout(async () => {
                await loadOrders();
                alert(isUpdate ? 'Pedido actualizado exitosamente' : 'Pedido creado exitosamente');
            }, 100);
        } else {
            // Manejar errores del servidor
            throw new Error(result.message || 'Error en la operación');
        }
    } catch (error) {
        // Manejar cualquier error que ocurra durante el proceso
        console.error('Error:', error);
        alert('Error: ' + error.message);
    }
}


// 3. Asegurarse de que los event listeners estén correctamente asignados
function setupFormListeners() {
    const form = document.getElementById('orderForm');
    if (form) {
        // Remover listeners anteriores para evitar duplicados
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        
        newForm.addEventListener('submit', handleFormSubmit);
    }
}

// 4. Llamar a setupFormListeners cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    setupFormListeners();
    setupModalListeners(); 
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