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
