// Funciones de utilidad
const API_URL = '/api/v1';

// Manejador de errores genérico
const handleError = (error) => {
    console.error('Error:', error);
    alert('Ha ocurrido un error. Por favor, intenta nuevamente.');
};

// Función para realizar peticiones fetch
const fetchData = async (url, options = {}) => {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        handleError(error);
        throw error;
    }
};

// Funciones para productos
const productsFunctions = {
    getAll: async () => {
        return await fetchData(`${API_URL}/products/read.php`);
    },
    
    create: async (productData) => {
        return await fetchData(`${API_URL}/products/create.php`, {
            method: 'POST',
            body: JSON.stringify(productData)
        });
    },
    
    update: async (id, productData) => {
        return await fetchData(`${API_URL}/products/update.php?id=${id}`, {
            method: 'PUT',
            body: JSON.stringify(productData)
        });
    },
    
    delete: async (id) => {
        return await fetchData(`${API_URL}/products/delete.php?id=${id}`, {
            method: 'DELETE'
        });
    }
};

// Funciones para proveedores
const suppliersFunctions = {
    getAll: async () => {
        return await fetchData(`${API_URL}/suppliers/read.php`);
    },
    
    create: async (supplierData) => {
        return await fetchData(`${API_URL}/suppliers/create.php`, {
            method: 'POST',
            body: JSON.stringify(supplierData)
        });
    },
    
    update: async (id, supplierData) => {
        return await fetchData(`${API_URL}/suppliers/update.php?id=${id}`, {
            method: 'PUT',
            body: JSON.stringify(supplierData)
        });
    },
    
    delete: async (id) => {
        return await fetchData(`${API_URL}/suppliers/delete.php?id=${id}`, {
            method: 'DELETE'
        });
    }
};

// Funciones para pedidos
const ordersFunctions = {
    getAll: async () => {
        return await fetchData(`${API_URL}/orders/read.php`);
    },
    
    create: async (orderData) => {
        return await fetchData(`${API_URL}/orders/create.php`, {
            method: 'POST',
            body: JSON.stringify(orderData)
        });
    },
    
    update: async (id, orderData) => {
        return await fetchData(`${API_URL}/orders/update.php?id=${id}`, {
            method: 'PUT',
            body: JSON.stringify(orderData)
        });
    },
    
    delete: async (id) => {
        return await fetchData(`${API_URL}/orders/delete.php?id=${id}`, {
            method: 'DELETE'
        });
    }
};



// Eventos DOM
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar modales de Bootstrap
    const modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
    modalTriggerList.map(function (modalTriggerEl) {
        return new bootstrap.Modal(modalTriggerEl);
    });
});