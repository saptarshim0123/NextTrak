document.addEventListener("DOMContentLoaded", function() {
    const registerForm = document.getElementById('registerForm');
    const messageDiv = document.getElementById('password-match-message');

    //Validation of equal password
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const passwordMatchMessage = document.getElementById('password-match-message');
    
    // Select the icon <i> tag directly, not its parent <span>
    // Note: I updated the <span> ID in the HTML to 'password-match-icon-span' to be clearer
    const passwordMatchIcon = document.querySelector('#password-match-icon-span i');

    function validatePassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Guard clause in case the icon element isn't found
        if (!passwordMatchIcon) return;

        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                // --- Passwords Match ---
                passwordMatchMessage.textContent = '';
                passwordMatchMessage.classList.remove('text-danger');
                
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
                
                // Update icon color
                passwordMatchIcon.classList.remove('text-muted', 'text-danger');
                passwordMatchIcon.classList.add('text-success');

            } else {
                // --- Passwords DO NOT Match ---
                passwordMatchMessage.textContent = 'Passwords do not match.';
                passwordMatchMessage.classList.add('text-danger');
                
                confirmPasswordInput.classList.add('is-invalid');
                confirmPasswordInput.classList.remove('is-valid');
                
                passwordMatchIcon.classList.remove('text-muted', 'text-success');
                passwordMatchIcon.classList.add('text-danger');
            }
        } else {
            passwordMatchMessage.textContent = '';
            confirmPasswordInput.classList.remove('is-invalid', 'is-valid');
            
            passwordMatchIcon.classList.remove('text-success', 'text-danger');
            passwordMatchIcon.classList.add('text-muted');
        }
    }

    if (passwordInput && confirmPasswordInput && passwordMatchIcon) {
        passwordInput.addEventListener('keyup', validatePassword);
        confirmPasswordInput.addEventListener('keyup', validatePassword);
    }
});