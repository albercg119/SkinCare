// Constantes para las rutas de la API
const API_BASE_URL = '/SkinCare/api/v1/products';

// Función para cargar productos 
async function loadProducts() {
    try {
        console.log('Cargando productos...');
        const response = await fetch(`${API_BASE_URL}/read.php`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Datos recibidos:', data);
        
        const tableBody = document.getElementById('productsTableBody');
        if (!tableBody) {
            console.error('No se encontró el elemento productsTableBody');
            return;
        }

        if (data.status === 'success') {
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay productos disponibles</td></tr>';
                return;
            }

            tableBody.innerHTML = '';
            data.data.forEach(product => {
                // Aseguramos usar id_producto en lugar de id
                tableBody.innerHTML += `
                    <tr>
                        <td>${product.id_producto}</td>
                        <td>${product.nombre}</td>
                        <td>${product.marca || 'N/A'}</td> 
                        <td>$${parseFloat(product.precio).toFixed(2)}</td>
                        <td>${product.cantidad_stock}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="editProduct(${product.id_producto})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id_producto})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        } else {
            throw new Error(data.message || 'Error al cargar productos');
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
        const tableBody = document.getElementById('productsTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Error al cargar productos: ${error.message}
                    </td>
                </tr>
            `;
        }
    }
}

window.showModal = function() {
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.classList.add('show');
        modal.style.display = 'block';
        // Agregar backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }
};

window.hideModal = function() {
    const modal = document.querySelector('.modal');
    if (modal) {
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

window.resetForm = function() {
    const form = document.getElementById('productForm');
    if (form) {
        form.reset();
        const productIdInput = document.getElementById('productId');
        if (productIdInput) {
            productIdInput.value = '';
        }
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
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

    // Botón Nuevo Producto
    const newProductBtn = document.querySelector('.nuevo-producto');
    if (newProductBtn) {
        newProductBtn.addEventListener('click', (e) => {
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
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
}

async function editProduct(id) {
    try {
        const response = await fetch(`${API_BASE_URL}/read_single.php?id=${id}`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        console.log('Datos del producto a editar:', data);

        if (data.status === 'success' && data.data) {
            const product = data.data;
            
            // Rellenar el formulario
            document.getElementById('productId').value = product.id;
            document.getElementById('nombre').value = product.nombre;
            document.getElementById('marca').value = product.marca;
            document.getElementById('precio').value = product.precio;
            document.getElementById('stock').value = product.cantidad_stock;

            // Manejar el select de ubicación
            const ubicacionSelect = document.getElementById('ubicacion');
            const ubicacionActual = product.ubicacion;

            // Buscar y seleccionar la opción correcta
            for (let option of ubicacionSelect.options) {
                if (option.value === ubicacionActual) {
                    option.selected = true;
                    break;
                }
            }

            showModal();
        } else {
            throw new Error(data.message || 'Error al cargar el producto');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar el producto: ' + error.message);
    }
}

// Función para eliminar producto
async function deleteProduct(id) {
    if (!id || isNaN(id)) {
        alert('ID de producto no válido');
        return;
    }

    if (!confirm('¿Está seguro de eliminar este producto?')) {
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
            alert('Producto eliminado exitosamente');
            await loadProducts();
        } else {
            throw new Error(data.message || 'Error al eliminar el producto');
        }
    } catch (error) {
        console.error('Error al eliminar:', error);
        alert('Error al eliminar el producto: ' + error.message);
    }
}

// En handleFormSubmit

async function handleFormSubmit(event) {
    event.preventDefault();
    
    try {
        // Obtener el ID y asegurarse de que sea un número
        const productId = parseInt(document.getElementById('productId').value) || null;
        console.log('ID del producto a procesar:', productId); // Debug
        
        const formData = {
            nombre: document.getElementById('nombre').value.trim(),
            marca: document.getElementById('marca').value.trim(),
            precio: parseFloat(document.getElementById('precio').value),
            cantidad_stock: parseInt(document.getElementById('stock').value),
            ubicacion: document.getElementById('ubicacion').value.trim()
        };

        // Validaciones del lado del cliente
        const errors = [];
        if (formData.nombre.length < 3) errors.push("El nombre debe tener al menos 3 caracteres");
        if (formData.marca.length < 3) errors.push("La marca debe tener al menos 3 caracteres");
        if (formData.precio <= 0) errors.push("El precio debe ser mayor que 0");
        if (formData.cantidad_stock < 0) errors.push("El stock no puede ser negativo");

        if (errors.length > 0) {
            alert(errors.join('\n'));
            return;
        }

        // Si hay ID, añadirlo al formData y usar update.php
        if (productId) {
            formData.id = productId;
            var endpoint = 'update.php';
        } else {
            var endpoint = 'create.php';
        }

        console.log(`Usando endpoint: ${endpoint}`, formData); // Debug

        const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            alert(productId ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente');
            window.hideModal();
            await loadProducts();
        } else {
            throw new Error(data.message || 'Error desconocido');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el producto: ' + error.message);
    }
}
// Asegurar que las funciones necesarias estén en el objeto window
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;
window.handleFormSubmit = handleFormSubmit;