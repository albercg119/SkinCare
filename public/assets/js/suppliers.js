// suppliers.js

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
function showModal() {
    const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
    modal.show();
}

function hideModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('supplierModal'));
    if (modal) {
        modal.hide();
        resetForm();
    }
}

function resetForm() {
    const form = document.getElementById('supplierForm');
    if (form) {
        form.reset();
        document.getElementById('supplierId').value = '';
    }
}

// Función para editar proveedor
async function editSupplier(id) {
    try {
        console.log('Iniciando edición del proveedor:', id);
        
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.status === 'success' && data.data) {
            const supplier = data.data;
            
            document.getElementById('supplierId').value = supplier.id;
            document.getElementById('nombre').value = supplier.nombre;
            document.getElementById('telefono').value = supplier.telefono;
            document.getElementById('correo').value = supplier.correo_electronico;
            
            showModal();
        } else {
            throw new Error(data.message || 'Error al cargar el proveedor');
        }
    } catch (error) {
        console.error('Error en editSupplier:', error);
        alert('Error al cargar el proveedor para editar: ' + error.message);
    }
}

async function deleteSupplier(id) {
    if (!id) {
        console.error('ID de proveedor no proporcionado');
        return;
    }

    if (!confirm('¿Está seguro de eliminar este proveedor?')) return;

    try {
        console.log('Iniciando eliminación del proveedor:', id);
        
        const response = await fetch(`${API_BASE_URL}/delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        // Obtenemos y limpiamos la respuesta
        const responseText = await response.text();
        console.log('Respuesta bruta del servidor:', responseText);
        
        // Limpiamos cualquier contenido extra que pueda haber
        const cleanedResponse = responseText.replace(/^[^{]*/, '').replace(/[^}]*$/, '');
        console.log('Respuesta limpia:', cleanedResponse);

        // Intentamos parsear la respuesta limpia
        const data = JSON.parse(cleanedResponse);

        if (data.status === 'success') {
            // Primero actualizamos la lista
            await loadSuppliers();
            // Luego mostramos el mensaje
            alert('Proveedor eliminado exitosamente');
        } else {
            throw new Error(data.message || 'Error al eliminar el proveedor');
        }
    } catch (error) {
        console.error('Error detallado:', error);
        // Intentamos actualizar la lista de todos modos
        await loadSuppliers();
        alert('Error al eliminar el proveedor: ' + error.message);
    }
}

// Función para manejar el envío del formulario
async function handleFormSubmit(event) {
    event.preventDefault();
    
    try {
        const formData = {
            nombre: document.getElementById('nombre').value.trim(),
            telefono: document.getElementById('telefono').value.trim(),
            correo_electronico: document.getElementById('correo').value.trim()
        };

        // Validación básica
        if (!formData.nombre || !formData.telefono || !formData.correo_electronico) {
            throw new Error('Por favor, complete todos los campos correctamente');
        }

        const supplierId = document.getElementById('supplierId').value;
        
        // Determinar si es creación o actualización
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

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.status === 'success') {
            alert(supplierId ? 'Proveedor actualizado exitosamente' : 'Proveedor creado exitosamente');
            hideModal();
            await loadSuppliers();
        } else {
            throw new Error(data.message || 'Error al procesar el proveedor');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el proveedor: ' + error.message);
    }
}

// Configuración de eventos cuando el DOM está cargado
document.addEventListener('DOMContentLoaded', () => {
    // Cargar proveedores inicialmente
    loadSuppliers();
    
    // Configurar el formulario
    const form = document.getElementById('supplierForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
    
    // Configurar el botón de nuevo proveedor
    const newSupplierBtn = document.querySelector('.nuevo-proveedor');
    if (newSupplierBtn) {
        newSupplierBtn.addEventListener('click', () => {
            resetForm();
            showModal();
        });
    }
    
    // Configurar botones de cancelar
    const cancelButtons = document.querySelectorAll('.cancelar');
    cancelButtons.forEach(button => {
        button.addEventListener('click', hideModal);
    });
    
    // Configurar cierre del modal al hacer clic fuera
    const modal = document.getElementById('supplierModal');
    if (modal) {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                hideModal();
            }
        });
    }
    
    // Configurar cierre del modal con tecla ESC
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideModal();
        }
    });
});

// Exponer funciones necesarias globalmente
window.editSupplier = editSupplier;
window.deleteSupplier = deleteSupplier;
window.showModal = showModal;
window.hideModal = hideModal;
window.resetForm = resetForm;
window.handleFormSubmit = handleFormSubmit;