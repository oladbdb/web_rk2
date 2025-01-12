
console.log('Auth.js loaded');

function showLoginForm(event) {
    event.preventDefault();
    const authButtons = document.querySelector('.auth-buttons');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (authButtons && loginForm && registerForm) {
        authButtons.style.display = 'none';
        loginForm.classList.add('show');
        registerForm.classList.remove('show');
    }
}

function showRegisterForm(event) {
    event.preventDefault();
    const authButtons = document.querySelector('.auth-buttons');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (authButtons && loginForm && registerForm) {
        authButtons.style.display = 'none';
        loginForm.classList.remove('show');
        registerForm.classList.add('show');
    }
}

function hideLoginForm(event) {
    event.preventDefault();
    const authButtons = document.querySelector('.auth-buttons');
    const loginForm = document.getElementById('loginForm');
    
    if (authButtons && loginForm) {
        authButtons.style.display = 'flex';
        loginForm.classList.remove('show');
    }
}

function hideRegisterForm(event) {
    event.preventDefault();
    const authButtons = document.querySelector('.auth-buttons');
    const registerForm = document.getElementById('registerForm');
    
    if (authButtons && registerForm) {
        authButtons.style.display = 'flex';
        registerForm.classList.remove('show');
    }
}


document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded');
    console.log('Auth buttons:', document.querySelector('.auth-buttons'));
    console.log('Login form:', document.getElementById('loginForm'));
    console.log('Register form:', document.getElementById('registerForm'));
}); 