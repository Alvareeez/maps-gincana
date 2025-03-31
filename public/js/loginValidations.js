document.addEventListener("DOMContentLoaded", function() {
    // Elementos del DOM
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    
    // Animación inicial
    const loginContainer = document.querySelector(".login-container");
    if (loginContainer) {
        loginContainer.style.opacity = "0";
        loginContainer.style.transform = "scale(0.8)";
        
        setTimeout(() => {
            loginContainer.style.transition = "opacity 1s ease-in-out, transform 1s ease-in-out";
            loginContainer.style.opacity = "1";
            loginContainer.style.transform = "scale(1)";
        }, 300);
    }
    
    // Funciones de validación
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function validatePassword(password) {
        return password.length >= 6;
    }
    
    function validateForm() {
        const isEmailValid = validateEmail(emailInput.value);
        const isPasswordValid = validatePassword(passwordInput.value);
        
        submitBtn.disabled = !(isEmailValid && isPasswordValid);
    }
    
    // Event listeners para email
    emailInput.addEventListener('input', function() {
        validateForm();
        if (this.value.length === 0) {
            this.classList.remove('error-border', 'success-border');
            emailError.style.display = 'none';
        }
    });
    
    emailInput.addEventListener('blur', function() {
        if (this.value.length === 0) {
            this.classList.remove('error-border', 'success-border');
            emailError.style.display = 'none';
        } else if (!validateEmail(this.value)) {
            this.classList.add('error-border');
            this.classList.remove('success-border');
            emailError.style.display = 'block';
        } else {
            this.classList.remove('error-border');
            this.classList.add('success-border');
            emailError.style.display = 'none';
        }
    });
    
    // Event listeners para password
    passwordInput.addEventListener('input', function() {
        validateForm();
        if (this.value.length === 0) {
            this.classList.remove('error-border', 'success-border');
            passwordError.style.display = 'none';
        }
    });
    
    passwordInput.addEventListener('blur', function() {
        if (this.value.length === 0) {
            this.classList.remove('error-border', 'success-border');
            passwordError.style.display = 'none';
        } else if (!validatePassword(this.value)) {
            this.classList.add('error-border');
            this.classList.remove('success-border');
            passwordError.style.display = 'block';
        } else {
            this.classList.remove('error-border');
            this.classList.add('success-border');
            passwordError.style.display = 'none';
        }
    });
    
    // Validación al enviar
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        if (!validateEmail(emailInput.value)) {
            emailInput.classList.add('error-border');
            emailError.style.display = 'block';
            isValid = false;
        }
        
        if (!validatePassword(passwordInput.value)) {
            passwordInput.classList.add('error-border');
            passwordError.style.display = 'block';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Enfocar el primer campo con error
            if (!validateEmail(emailInput.value)) {
                emailInput.focus();
            } else {
                passwordInput.focus();
            }
        }
    });
});