</main>
    <footer class="bg-light py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 SkinCare Management. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-dark me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-dark me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-dark"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php 
    // Cargar scripts específicos según la página
    $current_page = $_SERVER['REQUEST_URI'];
    if (strpos($current_page, '/products') !== false) {
        echo '<script src="/SkinCare/public/assets/js/products.js"></script>';
    } else if (strpos($current_page, '/suppliers') !== false) {
        echo '<script src="/SkinCare/public/assets/js/suppliers.js"></script>';
    } else if (strpos($current_page, '/orders') !== false) {
        echo '<script src="/SkinCare/public/assets/js/orders.js"></script>';
    }
    ?>
    <script src="/SkinCare/public/assets/js/main.js"></script>
</body>
</html>