const toggleSenha = document.getElementById('toggleSenha');
const senhaInput = document.getElementById('senha');
const icon = toggleSenha.querySelector('i');

toggleSenha.addEventListener('click', () => {
    const tipo = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
    senhaInput.setAttribute('type', tipo);
    icon.className = tipo === 'password' ? 'bx bx-show' : 'bx bx-hide';
});

const btnModo = document.getElementById('btnModo');
if (btnModo) {
  btnModo.addEventListener('click', () => {
    document.body.classList.toggle('light-mode');

    const icon = btnModo.querySelector('i');
    icon.className = document.body.classList.contains('light-mode') ? 'bx bx-sun' : 'bx bx-moon';
  });
}
