
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - New Gloow</title>
    <link rel="icon" href="imagenes/Logo (1).png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #374232;
            --secondary-color: #84b3b0;
            --dark-color: #2c3e50;
        }
        
        body {
            background: linear-gradient(135deg, #66ea71 0%, #84a1a5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 40px 0;
        }
        
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            margin: 20px;
        }
        
        .register-left {
            background: linear-gradient(135deg, var(--primary-color) 0%, #080808ff 100%);
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .register-left i {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .register-left h2 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .register-left p {
            font-size: 1.1rem;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .register-right {
            padding: 60px 50px;
        }
        
        .register-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
        }
        
        .register-subtitle {
            color: #6c757d;
            margin-bottom: 40px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
        }
        
        .input-group-text {
            background: white;
            border: 2px solid #e0e0e0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #6c757d;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--primary-color) 0%, #76cc61 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
            width: 100%;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 233, 57, 0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
        }
        
        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: var(--primary-color);
        }
        
        .benefit-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .benefit-item i {
            font-size: 2.5rem;
            margin-right: 15px;
        }
        
        .benefit-item .text {
            text-align: left;
        }
        
        .benefit-item .text h6 {
            margin: 0;
            font-size: 1rem;
        }
        
        .benefit-item .text p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .register-left {
                display: none;
            }
            
            .register-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="row g-0">
            <!-- Lado Izquierdo -->
            <div class="col-md-5 register-left">
                <i class="bi bi-person-plus-fill"></i>
                <h2>Únete a Nosotros</h2>
                <p class="mb-4">Crea tu cuenta y descubre el mundo del cuidado de la piel</p>
                
                <div class="mt-4 w-100">
                    <div class="benefit-item">
                        <i class="bi bi-gift"></i>
                        <div class="text">
                            <h6>Ofertas Exclusivas</h6>
                            <p>Acceso a descuentos especiales</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-truck"></i>
                        <div class="text">
                            <h6>Envío Gratis</h6>
                            <p>En tu primera compra</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-star"></i>
                        <div class="text">
                            <h6>Programa de Lealtad</h6>
                            <p>Gana descuentos con cada compra</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Lado Derecho - Formulario -->
            <div class="col-md-7 register-right">
                <h1 class="register-title">Crear Cuenta</h1>
                <p class="register-subtitle">Completa el formulario para registrarte</p>
                
                <!-- Mensajes de alerta -->
                <div id="alertContainer"></div>
                
                <form id="registerForm" action="procesar_registro.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Juan Pérez" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" class="form-control" name="email" id="email" placeholder="tu@email.com" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-phone"></i>
                                </span>
                                <input type="tel" class="form-control" name="telefono" id="telefono" placeholder="5551234567" pattern="[0-9]{10}">
                            </div>
                            <small class="text-muted">10 dígitos sin espacios</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group position-relative">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" class="form-control" name="password" id="password" placeholder="••••••••" required minlength="6">
                                <i class="bi bi-eye password-toggle" id="togglePassword"></i>
                            </div>
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-geo-alt"></i>
                            </span>
                            <textarea class="form-control" name="direccion" id="direccion" rows="2" placeholder="Calle, número, colonia, ciudad, código postal"></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terminos" required>
                            <label class="form-check-label" for="terminos">
                                Acepto los <a href="#" style="color: var(--primary-color); text-decoration: none;">Términos y Condiciones</a> y la <a href="#" style="color: var(--primary-color); text-decoration: none;">Política de Privacidad</a>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-register">
                        <i class="bi bi-person-plus"></i> Crear Cuenta
                    </button>
                </form>
                
                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
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
        
        // Validación de teléfono en tiempo real
        document.getElementById('telefono').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
        });
        
        // Procesar formulario con AJAX
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar términos y condiciones
            if(!document.getElementById('terminos').checked) {
                document.getElementById('alertContainer').innerHTML = `
                    <div class="alert alert-warning alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill"></i> Debes aceptar los términos y condiciones
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                return;
            }
            
            const formData = new FormData(this);
            
            // Mostrar loading
            const btnSubmit = this.querySelector('button[type="submit"]');
            const btnText = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            
            fetch('procesar_registro.php', {
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
                    
                    // Limpiar formulario
                    document.getElementById('registerForm').reset();
                    
                    // Redirigir después de 2 segundos
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    
                    // Restaurar botón
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = btnText;
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
                
                // Restaurar botón
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = btnText;
            });
        });
        
        // Auto-cerrar alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>