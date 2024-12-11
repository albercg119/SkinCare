// public/assets/js/auth.js
loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };

    try {
        console.log('Enviando datos:', formData); // Para depuración

        const response = await fetch('/SkinCare/api/v1/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        // Para depuración
        const responseText = await response.text();
        console.log('Respuesta cruda:', responseText);

        try {
            const data = JSON.parse(responseText);
            console.log('Respuesta parseada:', data);

            if (data.success) {
                window.location.href = '/SkinCare/public/';
            } else {
                document.getElementById('errorMessage').textContent = data.message;
            }
        } catch (parseError) {
            console.error('Error al parsear respuesta:', parseError);
            document.getElementById('errorMessage').textContent = 
                'Error en la respuesta del servidor';
        }
    } catch (error) {
        console.error('Error en la petición:', error);
        document.getElementById('errorMessage').textContent = 
            'Error de conexión. Por favor, intente nuevamente.';
    }
});