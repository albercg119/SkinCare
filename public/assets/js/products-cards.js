document.addEventListener('DOMContentLoaded', loadProducts);

async function loadProducts() {
    try {
        const response = await fetch('/SkinCare/api/v1/products/read.php');
        const data = await response.json();
        
        if (data.status === 'success' && data.data) {
            const container = document.querySelector('.cards-container');
            container.innerHTML = '';
            
            data.data.forEach(product => {
                container.appendChild(createProductCard(product));
            });
        }
    } catch (error) {
        console.error('Error al cargar productos:', error);
        showError('Error al cargar los productos');
    }
}

function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    
    card.innerHTML = `
        <div class="product-image">
            <img src="/api/placeholder/280/200" alt="${product.nombre}" 
                 style="max-width: 80%; max-height: 80%; object-fit: contain;">
        </div>
        <div class="product-info">
            <h3 class="product-name">${product.nombre}</h3>
            <p class="product-brand">${product.marca}</p>
            <div class="product-details">
                <span class="product-price">$${formatPrice(product.precio)}</span>
                <span class="stock-info ${getStockClass(product.cantidad_stock)}">
                    ${product.cantidad_stock} en stock
                </span>
            </div>
        </div>
    `;
    
    return card;
}

function getStockClass(stock) {
    if (stock > 50) return 'stock-high';
    if (stock > 20) return 'stock-medium';
    return 'stock-low';
}

function formatPrice(price) {
    return parseFloat(price).toFixed(2);
}

function showError(message) {
    const container = document.querySelector('.cards-container');
    container.innerHTML = `
        <div class="alert alert-danger w-100" role="alert">
            ${message}
        </div>
    `;
}