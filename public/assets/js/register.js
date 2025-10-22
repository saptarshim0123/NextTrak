document.addEventListener("DOMContentLoaded", function() {
    const registerForm = document.getElementById('registerForm');
    const messageDiv = document.getElementById('password-match-message');

    //Validation of equal password

    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const passwordMatchMessage = document.getElementById('password-match-message');
    const passwordMatchIcon = document.getElementById('password-match-icon');

    function validatePassword() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword.length > 0) {
            if (password === confirmPassword) {
                passwordMatchMessage.textContent = '';
                passwordMatchMessage.classList.remove('text-danger');
                confirmPasswordInput.classList.remove('is-invalid');
                confirmPasswordInput.classList.add('is-valid');
                passwordMatchIcon.classList.add('text-success');
            } else {
                passwordMatchMessage.textContent = 'Passwords do not match.';
                passwordMatchMessage.classList.add('text-danger');
                confirmPasswordInput.classList.add('is-invalid');
                confirmPasswordInput.classList.remove('is-valid');
                confirmPasswordInput.classList.remove('text-success');
                passwordMatchIcon.classList.add('text-danger');
            }
        } else {
            passwordMatchMessage.textContent = '';
            confirmPasswordInput.classList.remove('is-invalid', 'is-valid');
        }
    }

    if (passwordInput && confirmPasswordInput) {
        passwordInput.addEventListener('keyup', validatePassword);
        confirmPasswordInput.addEventListener('keyup', validatePassword);
    }
});