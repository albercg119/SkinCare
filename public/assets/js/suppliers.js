// Constantes para las rutas de la API
const API_BASE_URL = '/SkinCare/api/v1/suppliers';

// Función para cargar proveedores
async function loadSuppliers() {
    try {
        console.log('Cargando proveedores...');
        const response = await fetch(`${API_BASE_URL}/read.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Datos recibidos:', data);
        
        const tableBody = document.getElementById('suppliersTableBody');
        if (!tableBody) {
            console.error('No se encontró el elemento suppliersTableBody');
            return;
        }

        if (data.status === 'success') {
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay proveedores disponibles</td></tr>';
                return;
            }

            tableBody.innerHTML = '';
            data.data.forEach(supplier => {
                tableBody.innerHTML += `
                    <tr>
                        <td>${supplier.id}</td>
                        <td>${supplier.nombre}</td>
                        <td>${supplier.telefono}</td>
                        <td>${supplier.correo_electronico}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="editSupplier(${supplier.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteSupplier(${supplier.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            throw new Error(data.message || 'Error al cargar proveedores');
        }
    } catch (error) {
        console.error('Error al cargar proveedores:', error);
        const tableBody = document.getElementById('suppliersTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        Error al cargar proveedores: ${error.message}
                    </td>
                </tr>
            `;
        }
    }
}

// Funciones del modal
window.showModal = function() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'block';
    }
};

window.hideModal = function() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        window.resetForm();
    }
};

window.resetForm = function() {
    const form = document.getElementById('supplierForm');
    if (form) {
        form.reset();
        const supplierIdInput = document.getElementById('supplierId');
        if (supplierIdInput) {
            supplierIdInput.value = '';
        }
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    loadSuppliers();
    setupFormListener();
    setupModalListeners();
});

// Configurar listeners del modal
function setupModalListeners() {
    // Botón Cancelar
    const cancelarBtn = document.querySelector('.modal .cancelar, .modal .btn-secondary');
    if (cancelarBtn) {
        cancelarBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.hideModal();
        });
    }

    // Botón Nuevo Proveedor
    const newSupplierBtn = document.querySelector('.nuevo-proveedor');
    if (newSupplierBtn) {
        newSupplierBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.resetForm();
            window.showModal();
        });
    }

    // Click fuera del modal
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                window.hideModal();
            }
        });
    }

    // Tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.hideModal();
        }
    });
}

// Configurar listener del formulario
function setupFormListener() {
    const form = document.getElementById('supplierForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

async function editSupplier(id) {
    try {
        console.log('Iniciando edición del proveedor:', id);
        
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        console.log('Respuesta del servidor:', response);

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('La respuesta del servidor no es JSON válido');
        }

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log('Respuesta texto:', responseText);
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (e) {
            console.error('Error al parsear JSON:', e);
            throw new Error('Error al procesar la respuesta del servidor');
        }
        
        if (data.status === 'success' && data.data) {
            const supplier = data.data;
            console.log('Datos del proveedor:', supplier);
            
            if (!supplier.id || !supplier.nombre || !supplier.telefono || !supplier.correo_electronico) {
                throw new Error('Datos del proveedor incompletos');
            }

            document.getElementById('supplierId').value = supplier.id;
            document.getElementById('nombre').value = supplier.nombre;
            document.getElementById('telefono').value = supplier.telefono;
            document.getElementById('correo').value = supplier.correo_electronico;
            
            window.showModal();
        } else {
            throw new Error(data.message || 'Error al cargar el proveedor');
        }
    } catch (error) {
        console.error('Error en editSupplier:', error);
        alert('Error al cargar el proveedor para editar: ' + error.message);
    }
}

async function deleteSupplier(id) {
    if (!id || isNaN(id)) {
        alert('ID de proveedor no válido');
        return;
    }

    if (!confirm('¿Está seguro de eliminar este proveedor?')) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE_URL}/delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: parseInt(id) })
        });

        const text = await response.text();
        const cleanedText = text.replace(/^\uFEFF/, '').trim();
        
        let data;
        try {
            data = JSON.parse(cleanedText);
        } catch (e) {
            console.error('Texto de respuesta:', cleanedText);
            throw new Error('Error al procesar la respuesta del servidor');
        }
        
        if (data.status === 'success') {
            alert('Proveedor eliminado exitosamente');
            await loadSuppliers();
        } else {
            throw new Error(data.message || 'Error al eliminar el proveedor');
        }
    } catch (error) {
        console.error('Error al eliminar:', error);
        alert('Error al eliminar el proveedor: ' + error.message);
    }
}

async function handleFormSubmit(event) {
    event.preventDefault();
    
    try {
        const formData = {
            nombre: document.getElementById('nombre').value.trim(),
            telefono: document.getElementById('telefono').value.trim(),
            correo_electronico: document.getElementById('correo').value.trim()
        };

        if (!formData.nombre || !formData.telefono || !formData.correo_electronico) {
            throw new Error('Por favor, complete todos los campos correctamente');
        }

        const supplierId = document.getElementById('supplierId')?.value;
        
        let url = `${API_BASE_URL}/create.php`;
        let method = 'POST';
        
        if (supplierId) {
            url = `${API_BASE_URL}/update.php`;
            method = 'PUT';
            formData.id = supplierId;
        }

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        if (data.status === 'success') {
            alert(supplierId ? 'Proveedor actualizado exitosamente' : 'Proveedor creado exitosamente');
            window.hideModal();
            await loadSuppliers();
        } else {
            throw new Error(data.message || 'Error al procesar el proveedor');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el proveedor: ' + error.message);
    }
}

// Asegurar que las funciones necesarias estén en el objeto window
window.editSupplier = editSupplier;
window.deleteSupplier = deleteSupplier;
window.handleFormSubmit = handleFormSubmit;