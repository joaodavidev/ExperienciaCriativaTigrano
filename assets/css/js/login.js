const toggleSenhaBtn = document.getElementById('toggleSenha');
const senhaInput = document.getElementById('senha');
const toggleIcon = toggleSenhaBtn.querySelector('i');

toggleSenhaBtn.addEventListener('click', (e) => {
    e.preventDefault();
    const tipoAtual = senhaInput.getAttribute('type');
    if (tipoAtual === 'password') {
        senhaInput.setAttribute('type', 'text');
        toggleIcon.classList.remove('bx-show');
        toggleIcon.classList.add('bx-hide');
    } else {
        senhaInput.setAttribute('type', 'password');
        toggleIcon.classList.remove('bx-hide');
        toggleIcon.classList.add('bx-show');
    }
});

// Funcionalidade de modo claro/escuro
const btnModo = document.getElementById('btnModo');
if (btnModo) {
    // Aplicar tema salvo no carregamento
    const temaSalvo = localStorage.getItem('tema');
    if (temaSalvo === 'claro') {
        document.body.classList.add('light-mode');
        btnModo.querySelector('i').classList.remove('bx-moon');
        btnModo.querySelector('i').classList.add('bx-sun');
    }

    btnModo.addEventListener('click', () => {
        document.body.classList.toggle('light-mode');
        const icon = btnModo.querySelector('i');
        
        if (document.body.classList.contains('light-mode')) {
            icon.classList.remove('bx-moon');
            icon.classList.add('bx-sun');
            localStorage.setItem('tema', 'claro');
        } else {
            icon.classList.remove('bx-sun');
            icon.classList.add('bx-moon');
            localStorage.setItem('tema', 'escuro');
        }
    });
}
