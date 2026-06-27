document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const form = document.getElementById('registerForm');
    const btnRegister = document.getElementById('btnRegister');
    const togglePassword = document.getElementById('togglePassword');
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordStrength = document.getElementById('passwordStrength');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    // Password strength indicator
    passwordInput.addEventListener('input', function() {
        const strength = calculatePasswordStrength(this.value);
        updatePasswordStrength(strength);
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            showLoading(true);
            // Simular registro
            setTimeout(() => {
                showLoading(false);
                showSuccessMessage();
            }, 2000);
        }
    });

    // Real-time validation
    const inputs = form.querySelectorAll('input[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        input.addEventListener('input', function() {
            clearFieldError(this);
            if (this.id === 'password') updatePasswordStrength(calculatePasswordStrength(this.value));
            if (this.id === 'confirmPassword') validateConfirmPassword();
        });
    });

    // Validar checkbox al hacer click
    document.getElementById('terminos').addEventListener('change', function() {
        validateTerms();
    });

    // Funciones de validación
    function validateForm() {
        let isValid = true;
        
        // Limpiar errores previos
        clearAllErrors();
        
        // Validar cada campo
        isValid = validateField(document.getElementById('nombre')) && isValid;
        isValid = validateField(document.getElementById('email')) && isValid;
        isValid = validateField(document.getElementById('password')) && isValid;
        isValid = validateConfirmPassword() && isValid;
        isValid = validateTerms() && isValid;
        
        return isValid;
    }

    function validateField(input) {
        const errorElement = document.getElementById(`error-${input.id}`);
        let isValid = true;

        if (!input.value.trim()) {
            showFieldError(input, `${getFieldName(input.id)} es requerido`);
            isValid = false;
        } else if (input.id === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                showFieldError(input, 'Ingresa un email válido');
                isValid = false;
            }
        } else if (input.id === 'password') {
            if (input.value.length < 8) {
                showFieldError(input, 'La contraseña debe tener al menos 8 caracteres');
                isValid = false;
            }
        }

        return isValid;
    }

    function validateConfirmPassword() {
        const confirmInput = document.getElementById('confirmPassword');
        const passwordInput = document.getElementById('password');
        const errorElement = document.getElementById('error-confirmPassword');
        
        if (confirmInput.value && confirmInput.value !== passwordInput.value) {
            showFieldError(confirmInput, 'Las contraseñas no coinciden');
            return false;
        }
        return true;
    }

    function validateTerms() {
        const termsInput = document.getElementById('terminos');
        const errorElement = document.getElementById('error-terminos');
        
        if (!termsInput.checked) {
            errorElement.textContent = 'Debes aceptar los términos y condiciones';
            errorElement.classList.add('show');
            return false;
        }
        return true;
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return Math.min(strength, 5);
    }

    function updatePasswordStrength(strength) {
        const bar = passwordStrength.querySelector('.strength-bar') || 
                   document.createElement('div');
        bar.className = 'strength-bar';
        
        if (!passwordStrength.querySelector('.strength-bar')) {
            passwordStrength.appendChild(bar);
        }

        const colors = ['#ffffff', '#f97316', '#eab308', '#10b981', '#00bfa6'];
        const width = (strength / 5) * 100;
        
        bar.style.width = width + '%';
        bar.style.background = colors[strength - 1] || colors[0];
    }

    function getFieldName(fieldId) {
        const names = {
            'nombre': 'El nombre',
            'email': 'El email',
            'password': 'La contraseña',
            'confirmPassword': 'La confirmación'
        };
        return names[fieldId] || 'Este campo';
    }

    function showFieldError(input, message) {
        const errorElement = document.getElementById(`error-${input.id}`);
        errorElement.textContent = message;
        errorElement.classList.add('show');
        input.style.borderColor = 'var(--error)';
    }

    function clearFieldError(input) {
        const errorElement = document.getElementById(`error-${input.id}`);
        errorElement.classList.remove('show');
        input.style.borderColor = '';
    }

    function clearAllErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.classList.remove('show');
        });
        document.querySelectorAll('input').forEach(input => {
            input.style.borderColor = '';
        });
    }

    function showLoading(show) {
        btnRegister.disabled = show;
        if (show) {
            btnRegister.classList.add('loading');
        } else {
            btnRegister.classList.remove('loading');
        }
    }

    function showSuccessMessage() {
        // Crear mensaje de éxito
        const successMsg = document.createElement('div');
        successMsg.className = 'success-message';
        successMsg.innerHTML = `
            <i class="fas fa-check-circle"></i>
            ¡Cuenta creada exitosamente! Redirigiendo...
        `;
        successMsg.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(16,185,129,0.4);
            z-index: 10000;
            animation: slideInRight 0.4s ease-out;
            font-weight: 500;
        `;
        
        document.body.appendChild(successMsg);
        
        setTimeout(() => {
            successMsg.remove();
            // Aquí redirigirías al dashboard
            // window.location.href = 'dashboard.html';
        }, 3000);
    }

    // Animaciones CSS dinámicas
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    document.head.appendChild(style);
});

// Auto-focus primer campo
document.getElementById('nombre').focus();