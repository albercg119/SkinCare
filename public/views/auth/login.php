<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SkinCare</title>
    <link rel="stylesheet" href="/SkinCare/public/assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>SkinCare</h1>
        </div>
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Ingrese su correo" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
            </div>
            <button type="submit" class="btn-login">Iniciar Sesión</button>
            
            <div id="errorMessage"></div>
            
            <div class="auth-links">
                <div class="register-prompt">
                    ¿No tienes cuenta? <a href="/SkinCare/public/register" class="register-link">Regístrate</a>
                </div>
            </div>
        </form>
    </div>

    <script src="/SkinCare/public/assets/js/auth.js"></script>
</body>

<script>
document.getElementById('logoutBtn')?.addEventListener('click', async function(e) {
    e.preventDefault();
    
    try {
        const response = await fetch('/SkinCare/api/v1/auth/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = '/SkinCare/public/login';
        } else {
            alert('Error al cerrar sesión');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cerrar sesión');
    }
});
</script>
</html>