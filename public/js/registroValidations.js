document.addEventListener("DOMContentLoaded", function() {
    // Elementos del DOM
    const form = document.getElementById('registerForm');
    const nombreInput = document.getElementById('nombre');
    const apellidoInput = document.getElementById('apellido');
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    
    // Todos los elementos de error
    const errorElements = {
        nombre: document.getElementById('nombre-error'),
        apellido: document.getElementById('apellido-error'),
        email: document.getElementById('email-error'),
        username: document.getElementById('username-error'),
        password: document.getElementById('password-error'),
        passwordConfirm: document.getElementById('password-confirm-error')
    };
    
    // Animación inicial
    const registerContainer = document.querySelector(".register-container");
    if (registerContainer) {
        registerContainer.style.opacity = "0";
        registerContainer.style.transform = "scale(0.8)";
        
        setTimeout(() => {
            registerContainer.style.transition = "opacity 1s ease-in-out, transform 1s ease-in-out";
            registerContainer.style.opacity = "1";
            registerContainer.style.transform = "scale(1)";
        }, 300);
    }
    
    // Funciones de validación
    function validateNombre(nombre) {
        return nombre.length >= 3;
    }
    
    function validateApellido(apellido) {
        return apellido.length >= 3;
    }
    
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function validateUsername(username) {
        return username.length >= 4;
    }
    
    function validatePassword(password) {
        return password.length >= 6;
    }
    
    function validatePasswordMatch(password, confirmation) {
        return password === confirmation && password.length > 0;
    }
    
    function validateForm() {
        const isNombreValid = validateNombre(nombreInput.value);
        const isApellidoValid = validateApellido(apellidoInput.value);
        const isEmailValid = validateEmail(emailInput.value);
        const isUsernameValid = validateUsername(usernameInput.value);
        const isPasswordValid = validatePassword(passwordInput.value);
        const isPasswordMatchValid = validatePasswordMatch(passwordInput.value, passwordConfirmInput.value);
        
        submitBtn.disabled = !(isNombreValid && isApellidoValid && isEmailValid && 
                              isUsernameValid && isPasswordValid && isPasswordMatchValid);
    }
    
    // Función genérica para manejar validación de campos
    function setupValidation(inputElement, validationFn, errorElement, errorMessage) {
        inputElement.addEventListener('input', function() {
            validateForm();
            if (this.value.length === 0) {
                this.classList.remove('error-border', 'success-border');
                errorElement.style.display = 'none';
            }
        });
        
        inputElement.addEventListener('blur', function() {
            if (this.value.length === 0) {
                this.classList.remove('error-border', 'success-border');
                errorElement.style.display = 'none';
            } else if (!validationFn(this.value)) {
                this.classList.add('error-border');
                this.classList.remove('success-border');
                errorElement.textContent = errorMessage;
                errorElement.style.display = 'block';
            } else {
                this.classList.remove('error-border');
                this.classList.add('success-border');
                errorElement.style.display = 'none';
            }
            validateForm();
        });
    }
    
    // Configurar validaciones para cada campo
    setupValidation(nombreInput, validateNombre, errorElements.nombre, 'El nombre es requerido (mín. 3 caracteres)');
    setupValidation(apellidoInput, validateApellido, errorElements.apellido, 'El apellido es requerido (mín. 3 caracteres)');
    setupValidation(emailInput, validateEmail, errorElements.email, 'Debe ser un email válido');
    setupValidation(usernameInput, validateUsername, errorElements.username, 'Usuario requerido (mín. 4 caracteres)');
    setupValidation(passwordInput, validatePassword, errorElements.password, 'La contraseña debe tener al menos 6 caracteres');
    
    // Validación especial para confirmación de contraseña
    passwordInput.addEventListener('input', validatePasswordConfirmation);
    passwordConfirmInput.addEventListener('input', validatePasswordConfirmation);
    
    function validatePasswordConfirmation() {
        if (passwordConfirmInput.value.length === 0) {
            passwordConfirmInput.classList.remove('error-border', 'success-border');
            errorElements.passwordConfirm.style.display = 'none';
        } else if (!validatePasswordMatch(passwordInput.value, passwordConfirmInput.value)) {
            passwordConfirmInput.classList.add('error-border');
            passwordConfirmInput.classList.remove('success-border');
            errorElements.passwordConfirm.style.display = 'block';
        } else {
            passwordConfirmInput.classList.remove('error-border');
            passwordConfirmInput.classList.add('success-border');
            errorElements.passwordConfirm.style.display = 'none';
        }
        validateForm();
    }
    
    // Validación al enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validar todos los campos
        if (!validateNombre(nombreInput.value)) {
            nombreInput.classList.add('error-border');
            errorElements.nombre.style.display = 'block';
            isValid = false;
        }
        
        if (!validateApellido(apellidoInput.value)) {
            apellidoInput.classList.add('error-border');
            errorElements.apellido.style.display = 'block';
            isValid = false;
        }
        
        if (!validateEmail(emailInput.value)) {
            emailInput.classList.add('error-border');
            errorElements.email.style.display = 'block';
            isValid = false;
        }
        
        if (!validateUsername(usernameInput.value)) {
            usernameInput.classList.add('error-border');
            errorElements.username.style.display = 'block';
            isValid = false;
        }
        
        if (!validatePassword(passwordInput.value)) {
            passwordInput.classList.add('error-border');
            errorElements.password.style.display = 'block';
            isValid = false;
        }
        
        if (!validatePasswordMatch(passwordInput.value, passwordConfirmInput.value)) {
            passwordConfirmInput.classList.add('error-border');
            errorElements.passwordConfirm.style.display = 'block';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Enfocar el primer campo con error
            if (!validateNombre(nombreInput.value)) {
                nombreInput.focus();
            } else if (!validateApellido(apellidoInput.value)) {
                apellidoInput.focus();
            } else if (!validateEmail(emailInput.value)) {
                emailInput.focus();
            } else if (!validateUsername(usernameInput.value)) {
                usernameInput.focus();
            } else if (!validatePassword(passwordInput.value)) {
                passwordInput.focus();
            } else {
                passwordConfirmInput.focus();
            }
        }
    });
});