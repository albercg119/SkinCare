// También asegúrate de que la URL sea correcta
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
        console.log('Iniciando carga de artículos...');
        
        const response = await fetch(`${PRODUCTS_API_URL}/read.php`);
        console.log('URL de la petición:', `${PRODUCTS_API_URL}/read.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log('Datos recibidos:', data);

        const select = document.getElementById('article');
        if (!select) {
            throw new Error('No se encontró el elemento select de artículos');
        }

        // Limpiar el select
        select.innerHTML = '<option value="">Seleccione un artículo</option>';

        if (data.status !== 'success' || !data.data || !Array.isArray(data.data)) {
            throw new Error('Formato de datos inválido');
        }

        // Agregar las opciones usando id_producto como value
        data.data.forEach(product => {
            console.log('Procesando producto:', product);
            const option = document.createElement('option');
            option.value = product.id_producto; // Usar id_producto como value
            option.textContent = `${product.nombre} ${product.marca ? `(${product.marca})` : ''}`;
            select.appendChild(option);
        });

        console.log(`${data.data.length} artículos cargados exitosamente`);
    } catch (error) {
        console.error('Error detallado:', error);
        const select = document.getElementById('article');
        if (select) {
            select.innerHTML = '<option value="">Error al cargar artículos</option>';
        }
        alert('Error al cargar la lista de artículos: ' + error.message);
    }
}

// Función para eliminar suministro
async function deleteSupply(id) {
    if (!id) {
        console.error('ID de suministro no proporcionado');
        return;
    }

    if (!confirm('¿Está seguro de eliminar este suministro?')) return;

    try {
        const response = await fetch(`${API_BASE_URL}/delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();
        
        if (!data.status || data.status !== 'success') {
            throw new Error(data.message || 'Error al eliminar el suministro');
        }

        alert('Suministro eliminado exitosamente');
        await loadSupplies();
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el suministro: ' + error.message);
    }
}

async function handleFormSubmit(event) {
    event.preventDefault();
    
    try {
        const supplyId = document.getElementById('supplyId')?.value;
        const formData = {
            article_id: parseInt(document.getElementById('article').value),
            quantity: parseInt(document.getElementById('quantity').value)
        };

        const endpoint = supplyId ? 'update.php' : 'create.php';
        
        // Aquí está el cambio clave: asegurar que el ID se envíe con el nombre correcto
        if (supplyId) {
            formData.supply_id = parseInt(supplyId); // Cambiado de 'id' a 'supply_id' para coincidir
        }

        console.log('Datos a enviar:', formData);
        console.log('Endpoint:', endpoint);

        const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.status === 'success') {
            alert(endpoint === 'update.php' ? 'Suministro actualizado exitosamente' : 'Suministro creado exitosamente');
            hideModal();
            await loadSupplies();
        } else {
            throw new Error(data.message || 'Error al procesar el suministro');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el suministro: ' + error.message);
    }
}

async function editSupply(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        const data = await response.json();
        
        if (data.status === 'success' && data.data) {
            const supply = data.data;
            
            // Aquí también usamos el nombre correcto del campo
            document.getElementById('supplyId').value = supply.supply_id || supply.id;
            document.getElementById('article').value = supply.article_id;
            document.getElementById('quantity').value = supply.quantity;
            
            console.log('Datos de suministro cargados:', supply);
            showModal();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar el suministro: ' + error.message);
    }
}

// Event Listeners exactamente igual que inventario
document.addEventListener('DOMContentLoaded', () => {
    loadSupplies();
    loadArticles();
    setupEventListeners();
});

// Misma estructura que inventario
function setupEventListeners() {
    const form = document.getElementById('suppliesForm');
    if (form) {
        // Remover listeners anteriores si existen
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        // Agregar nuevo listener
        document.getElementById('suppliesForm').addEventListener('submit', handleFormSubmit);
    }


    // Botón Nuevo Suministro
    const newBtn = document.querySelector('.nuevo-suministro');
    if (newBtn) {
        newBtn.addEventListener('click', () => {
            resetForm();
            showModal();
        });
    }

    // Botón Cancelar
    const cancelBtn = document.querySelector('.cancelar');
    if (cancelBtn) cancelBtn.addEventListener('click', hideModal);

    // Click fuera del modal
    const modal = document.querySelector('#suppliesModal');
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

// Asegurarnos de limpiar el ID al mostrar el modal para nuevo suministro
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
        resetForm();
    }
};

function resetForm() {
    const form = document.getElementById('suppliesForm');
    if (form) {
        form.reset();
        // Asegurar que el ID se limpie
        const idInput = document.getElementById('supplyId');
        if (idInput) {
            idInput.value = '';
        }
    }
}





// Exponer funciones necesarias globalmente
window.editSupply = editSupply;
window.deleteSupply = deleteSupply;
window.showModal = showModal;
window.hideModal = hideModal;
