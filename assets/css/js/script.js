const sidebar = document.querySelector('.sidebar');
const togglebtn = document.querySelector('.toggle-btn');

togglebtn.addEventListener('click', () => {
    sidebar.classList.toggle('active');
})

const toggleThemeBtn = document.querySelector('.btn-toggle-tema');
const themeIcon = toggleThemeBtn.querySelector('i');
const body = document.body;

// Verifica tema salvo ao carregar a página
const savedTheme = localStorage.getItem('tema');
if (savedTheme === 'claro') {
  body.classList.add('light-mode');
  themeIcon.classList.remove('bx-moon');
  themeIcon.classList.add('bx-sun');
}

// Alterna tema e salva preferência
toggleThemeBtn.addEventListener('click', (e) => {
  e.preventDefault();
  body.classList.toggle('light-mode');

  if (body.classList.contains('light-mode')) {
    themeIcon.classList.remove('bx-moon');
    themeIcon.classList.add('bx-sun');
    localStorage.setItem('tema', 'claro');
  } else {
    themeIcon.classList.remove('bx-sun');
    themeIcon.classList.add('bx-moon');
    localStorage.setItem('tema', 'escuro');
  }
});
