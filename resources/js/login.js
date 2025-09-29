function togglePassword() {
    const password = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');

    if (password.type === 'password') {
        password.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        password.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}
window.togglePassword = togglePassword;

// Efectos de focus
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('focus', function () {
        this.parentElement.classList.add('ring-2', 'ring-farm-green');
    });

    input.addEventListener('blur', function () {
        this.parentElement.classList.remove('ring-2', 'ring-farm-green');
    });
});
