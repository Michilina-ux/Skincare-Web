<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - New Gloow</title>
    <link rel="icon" href="imagenes/Logo (1).png">
    <link rel="stylesheet" href="css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="login-container">
        <div class="row g-0">
            <!-- Lado Izquierdo -->
            <div class="col-md-5 login-left">
                <i class="bi bi-flower1"></i>
                <h2>Bienvenido de vuelta</h2>
                <p>Accede a tu cuenta para descubrir productos increíbles para el cuidado de tu piel</p>
                <div class="mt-4">
                    <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
                    <p class="mt-2" style="font-size: 0.9rem;">Tus datos están seguros con nosotros</p>
                </div>
            </div>
            
            <!-- Lado Derecho - Formulario -->
            <div class="col-md-7 login-right">
                <h1 class="login-title">   Iniciar Sesión</h1>
                <p class="login-subtitle">    Ingresa tus datos para continuar</p>
                
                <!-- Mensajes de alerta -->
                <div id="alertContainer"></div>
                
                <form id="loginForm" action="procesar_login.php" method="POST">
                    <div class="mb-4">
                        <label class="form-label">ㅤEmail</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-envelope"></i>
                            </span>
                            <input type="email" class="form-control" name="email" id="email" placeholder="tu@email.com" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">ㅤContraseña</label>
                        <div class="input-group position-relative">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required>
                            <i class="bi bi-eye password-toggle" id="togglePassword"></i>
                        </div>
                    </div>
                    
                    <div class="mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                            Recordarme
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none" style="color: var(--primary-color);">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right">  Iniciar Sesión</i> 
                    </button>
                </form>
                
                <div class="divider">
                    <span>Encuentranos en</span>
                </div>
                
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-100" style="border-radius: 10px;">
                            <i class="bi bi-instagram"></i> Instagram
                        </button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-outline-secondary w-100" style="border-radius: 10px;">
                            <i class="bi bi-facebook"></i><a href="https://www.facebook.com/profile.php?id=61583747990511"> Facebook</a> 
                        </button>
                    </div>
                </div>
                
                <div class="register-link">
                    ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
        
        // Procesar formulario con AJAX
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('procesar_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alertContainer = document.getElementById('alertContainer');
                
                if(data.success) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    
                    // Redirigir después de 1 segundo
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('alertContainer').innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill"></i> Error al procesar la solicitud
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
            });
        });
    </script>
</body>
</html>