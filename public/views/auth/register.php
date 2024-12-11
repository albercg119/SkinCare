<!-- register.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - SkinCare</title>
    <link rel="stylesheet" href="/SkinCare/public/assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>SkinCare</h1>
        </div>
        
        <!-- Aquí agregamos el método POST y la acción correcta -->
        <form id="registerForm" method="POST" action="/SkinCare/api/v1/auth/register.php">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Registrarse</button>
        </form>

        <div id="errorMessage" class="error-message"></div>
        
        <div class="auth-links">
            <div class="register-prompt">
                ¿Ya tienes cuenta? <a href="/SkinCare/public/login" class="register-link">Iniciar sesión</a>
            </div>
        </div>
    </div>

    <!-- Script de manejo del formulario -->
    <script>
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            username: document.getElementById('username').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        };

        try {
            const response = await fetch('/SkinCare/api/v1/auth/register.php', {
                method: 'POST', // Aseguramos que sea POST
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                alert('Registro exitoso. Por favor inicia sesión.');
                window.location.href = '/SkinCare/public/login';
            } else {
                const errorMessage = document.getElementById('errorMessage');
                errorMessage.textContent = data.message;
                errorMessage.style.display = 'block';
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('errorMessage').textContent = 'Error de conexión. Por favor, intente nuevamente.';
        }
    });
    </script>
</body>
</html>