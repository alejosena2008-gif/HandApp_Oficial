// login.js — HandApp

// toggle mostrar/ocultar contraseña
const togglePass = document.getElementById('togglePass');
const passInput  = document.getElementById('password');
const eyeIcon    = document.getElementById('eyeIcon');

const eyeOpen   = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
const eyeClosed = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

togglePass.addEventListener('click', () => {
    const isPass = passInput.type === 'password';
    passInput.type = isPass ? 'text' : 'password';
    eyeIcon.innerHTML = isPass ? eyeClosed : eyeOpen;
});

// validacion del formulario
const form        = document.getElementById('loginForm');
const btnLogin    = document.getElementById('btnLogin');
const inputUser   = document.getElementById('usuario');
const inputPass   = document.getElementById('password');
const errUsuario  = document.getElementById('errorUsuario');
const errPassword = document.getElementById('errorPassword');

function limpiarError(input, msg) {
    input.style.borderColor = '';
    msg.classList.remove('show');
}

inputUser.addEventListener('input', () => limpiarError(inputUser, errUsuario));
inputPass.addEventListener('input', () => limpiarError(inputPass, errPassword));

form.addEventListener('submit', function(e) {
    let valido = true;

    if (!inputUser.value.trim()) {
        e.preventDefault();
        inputUser.style.borderColor = 'var(--error)';
        errUsuario.classList.add('show');
        valido = false;
    }

    if (!inputPass.value.trim()) {
        e.preventDefault();
        inputPass.style.borderColor = 'var(--error)';
        errPassword.classList.add('show');
        valido = false;
    }

    if (valido) {
        btnLogin.classList.add('loading');
        btnLogin.disabled = true;
    }
});