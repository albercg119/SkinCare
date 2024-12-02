// Constantes para las rutas de la API
const API_BASE_URL = '/SkinCare/api/v1/inventory';

// Función para cargar inventario
async function loadInventory() {
    try {
        const response = await fetch(`${API_BASE_URL}/read.php`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        
        const tableBody = document.getElementById('inventoryTableBody');
        if (!tableBody) {
            console.error('No se encontró el elemento inventoryTableBody');
            return;
        }

        if (data.status === 'success') {
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay registros disponibles</td></tr>';
                return;
            }

            tableBody.innerHTML = '';
            data.data.forEach(item => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${item.id_inventario}</td>
                        <td>${item.nombre_producto}</td>
                        <td>${item.ubicacion_tienda}</td>
                        <td>${new Date(item.fecha_ultima_actualizacion).toLocaleString()}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="editInventory(${item.id_inventario})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteInventory(${item.id_inventario})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            throw new Error(data.message || 'Error al cargar inventario');
        }
    } catch (error) {
        console.error('Error al cargar inventario:', error);
        document.getElementById('inventoryTableBody').innerHTML = `
            <tr><td colspan="5" class="text-center text-danger">Error al cargar inventario: ${error.message}</td></tr>
        `;
    }
}

// Funciones del modal
function showModal() {
    const modal = document.querySelector('#inventoryModal');
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'block';
    }
}

function hideModal() {
    const modal = document.querySelector('#inventoryModal');
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        resetForm();
    }
}

function resetForm() {
    const form = document.getElementById('inventoryForm');
    if (form) {
        form.reset();
        document.getElementById('inventoryId').value = '';
    }
}

// Cargar productos en el select
async function loadProductSelect() {
    try {
        const response = await fetch('/SkinCare/api/v1/products/read.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        const select = document.getElementById('producto');
        
        if (data.status === 'success' && data.data) {
            select.innerHTML = '<option value="">Seleccione un producto</option>';
            data.data.forEach(product => {
                select.innerHTML += `<option value="${product.id_producto}">${product.nombre}</option>`;
            });
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

// Función para editar inventario
async function editInventory(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        if (data.status === 'success' && data.data) {
            document.getElementById('inventoryId').value = data.data.id_inventario;
            document.getElementById('producto').value = data.data.id_producto;
            document.getElementById('ubicacion').value = data.data.ubicacion_tienda;
            showModal();
        }
    } catch (error) {
        console.error('Error al cargar registro:', error);
        alert('Error al cargar el registro para editar: ' + error.message);
    }
}

// Función para eliminar inventario
async function deleteInventory(id) {
    if (!confirm('¿Está seguro de eliminar este registro?')) return;

    try {
        const response = await fetch(`${API_BASE_URL}/delete.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });

        const data = await response.json();
        if (data.status === 'success') {
            alert('Registro eliminado exitosamente');
            await loadInventory();
        } else {
            throw new Error(data.message || 'Error al eliminar registro');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el registro: ' + error.message);
    }
}

// Manejar envío del formulario
async function handleFormSubmit(event) {
    event.preventDefault();
    
    try {
        const inventoryId = document.getElementById('inventoryId').value;
        const formData = {
            id_producto: document.getElementById('producto').value,
            ubicacion_tienda: document.getElementById('ubicacion').value
        };

        const endpoint = inventoryId ? 'update.php' : 'create.php';
        if (inventoryId) formData.id_inventario = inventoryId;

        const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.status === 'success') {
            alert(inventoryId ? 'Registro actualizado exitosamente' : 'Registro creado exitosamente');
            hideModal();
            await loadInventory();
        } else {
            throw new Error(data.message || 'Error al procesar el registro');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el registro: ' + error.message);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    loadInventory();
    loadProductSelect();
    setupEventListeners();
});

function setupEventListeners() {
    // Formulario
    const form = document.getElementById('inventoryForm');
    if (form) form.addEventListener('submit', handleFormSubmit);

    // Botón Nuevo Registro
    const newBtn = document.querySelector('.nuevo-inventario');
    if (newBtn) newBtn.addEventListener('click', () => {
        resetForm();
        showModal();
    });

    // Botón Cancelar
    const cancelBtn = document.querySelector('.cancelar');
    if (cancelBtn) cancelBtn.addEventListener('click', hideModal);

    // Click fuera del modal
    const modal = document.querySelector('#inventoryModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) hideModal();
        });
    }

    // Tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hideModal();
    });
}