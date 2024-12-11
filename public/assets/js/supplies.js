// Constantes para las rutas de la API
const API_BASE_URL = '/SkinCare/api/v1/supplies';
const PRODUCTS_API_URL = '/SkinCare/api/v1/products'; 

// Función para cargar suministros
async function loadSupplies() {
    try {
        const response = await fetch(`${API_BASE_URL}/read.php`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        
        const tableBody = document.getElementById('suppliesTableBody');
        if (!tableBody) {
            console.error('No se encontró el elemento suppliesTableBody');
            return;
        }

        tableBody.innerHTML = '';

        if (!data.status || data.status !== 'success') {
            throw new Error(data.message || 'Error en la respuesta del servidor');
        }

        if (!data.data || data.data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No hay suministros registrados</td></tr>';
            return;
        }

        data.data.forEach(supply => {
            const date = new Date(supply.supply_date);
            const formattedDate = isNaN(date.getTime()) ? supply.supply_date : date.toLocaleString();
            
            tableBody.innerHTML += `
                <tr>
                    <td>${supply.id}</td>
                    <td>${supply.article_name || 'N/A'}</td>
                    <td>${supply.quantity}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="editSupply(${supply.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteSupply(${supply.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } catch (error) {
        console.error('Error:', error);
        const tableBody = document.getElementById('suppliesTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        Error al cargar suministros: ${error.message}
                    </td>
                </tr>
            `;
        }
    }
}

async function loadArticles() {
    try {
        const response = await fetch(`${PRODUCTS_API_URL}/read.php`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();
        const select = document.getElementById('article');
        
        if (!select) throw new Error('No se encontró el elemento select de artículos');

        select.innerHTML = '<option value="">Seleccione un artículo</option>';

        if (data.status !== 'success' || !data.data || !Array.isArray(data.data)) {
            throw new Error('Formato de datos inválido');
        }

        data.data.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id_producto;
            option.textContent = `${product.nombre} ${product.marca ? `(${product.marca})` : ''}`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error:', error);
        const select = document.getElementById('article');
        if (select) {
            select.innerHTML = '<option value="">Error al cargar artículos</option>';
        }
        alert('Error al cargar la lista de artículos: ' + error.message);
    }
}

// Función para eliminar suministro
async function deleteSupply(id) {
    if (!confirm('¿Está seguro de eliminar este suministro?')) return;

    try {
        const response = await fetch(`${API_BASE_URL}/delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            alert('Suministro eliminado exitosamente');
            await loadSupplies();
        } else {
            throw new Error(data.message || 'Error al eliminar el suministro');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el suministro: ' + error.message);
    }
}

// Función para manejar el envío del formulario
async function handleFormSubmit(event) {
    event.preventDefault();
    
    try {
        const supplyId = document.getElementById('supplyId')?.value;
        const formData = {
            article_id: parseInt(document.getElementById('article').value),
            quantity: parseInt(document.getElementById('quantity').value)
        };

        // Creamos un array para almacenar todos los errores de validación
        const errors = [];

        // Validación del artículo
        if (!formData.article_id) {
            errors.push('Debe seleccionar un artículo');
        }

        // Validación de cantidad con reglas más específicas
        if (!formData.quantity) {
            errors.push('La cantidad es requerida');
        } else if (isNaN(formData.quantity)) {
            errors.push('La cantidad debe ser un número válido');
        } else if (formData.quantity <= 0) {
            errors.push('La cantidad debe ser mayor a 0');
        } else if (formData.quantity > 99999) {
            errors.push('La cantidad no puede exceder 99,999 unidades');
        } else if (!Number.isInteger(formData.quantity)) {
            errors.push('La cantidad debe ser un número entero');
        }

        // Si hay errores, los mostramos y detenemos el proceso
        if (errors.length > 0) {
            alert(errors.join('\n'));
            return;
        }

        // Si hay ID, lo agregamos al formData
        const endpoint = supplyId ? 'update.php' : 'create.php';
        if (supplyId) {
            formData.supply_id = parseInt(supplyId);
        }

        const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.status === 'success') {
            window.hideModal();
            await loadSupplies();
            alert(supplyId ? 'Suministro actualizado exitosamente' : 'Suministro creado exitosamente');
        } else {
            // Mejoramos el manejo de errores del servidor
            if (data.errors && Array.isArray(data.errors)) {
                alert(data.errors.join('\n'));
            } else {
                throw new Error(data.message || 'Error al procesar el suministro');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    }
}

async function editSupply(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        const data = await response.json();
        
        if (data.status === 'success' && data.data) {
            const supply = data.data;
            document.getElementById('supplyId').value = supply.id;
            document.getElementById('article').value = supply.article_id;
            document.getElementById('quantity').value = supply.quantity;
            window.showModal();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar el suministro: ' + error.message);
    }
}

// Funciones para el manejo del modal
window.showModal = function() {
    const modal = document.querySelector('#suppliesModal');
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'block';

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.style.opacity = '0.1'; // Hacerlo más claro
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }
};

window.hideModal = function() {
    const modal = document.querySelector('#suppliesModal');
    if (modal) {
        // Ocultar modal
        modal.classList.remove('show');
        modal.style.display = 'none';
        
         // Remover backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove('modal-open');
        window.resetForm();             
    }
};

function resetForm() {
    const form = document.getElementById('suppliesForm');
    if (form) {
        form.reset();
        const idInput = document.getElementById('supplyId');
        if (idInput) idInput.value = '';
    }
}

// Configuración de event listeners
function setupEventListeners() {
    // Form submit
    const form = document.getElementById('suppliesForm');
    if (form) {
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        newForm.addEventListener('submit', handleFormSubmit);
    }

    // Nuevo Suministro
    const newBtn = document.querySelector('.nuevo-suministro');
    if (newBtn) {
        newBtn.addEventListener('click', () => {
            resetForm();
            window.showModal();
        });
    }

    // Botones de cerrar modal
    const closeButtons = document.querySelectorAll('.modal .btn-close, .modal .cancelar, .modal .btn-secondary');
    closeButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            window.hideModal();
        });
    });

    // Click fuera del modal
    const modal = document.querySelector('#suppliesModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) window.hideModal();
        });
    }

    // Tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') window.hideModal();
    });
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    loadSupplies();
    loadArticles();
    setupEventListeners();
});

// Exponer funciones globalmente
window.editSupply = editSupply;
window.deleteSupply = deleteSupply;
window.showModal = showModal;
window.hideModal = hideModal;