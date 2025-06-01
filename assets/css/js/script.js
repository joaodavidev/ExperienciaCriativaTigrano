const sidebar = document.querySelector('.sidebar');
const togglebtn = document.querySelector('.toggle-btn');
const toggleThemeBtn = document.querySelector('.btn-toggle-tema');
const themeIcon = toggleThemeBtn.querySelector('i');
const body = document.body;

// === TEMA SALVO === //
const savedTheme = localStorage.getItem('tema');
if (savedTheme === 'claro') {
  body.classList.add('light-mode');
  themeIcon.classList.remove('bx-moon');
  themeIcon.classList.add('bx-sun');
} else {
  themeIcon.classList.remove('bx-sun');
  themeIcon.classList.add('bx-moon');
}

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

// === SIDEBAR SALVA === //
const sidebarStatus = localStorage.getItem('sidebar');
if (sidebarStatus === 'aberta') {
  sidebar.classList.add('active');
} else {
  sidebar.classList.remove('active');
}

togglebtn.addEventListener('click', () => {
  sidebar.classList.toggle('active');

  if (sidebar.classList.contains('active')) {
    localStorage.setItem('sidebar', 'aberta');
  } else {
    localStorage.setItem('sidebar', 'fechada');
  }
});

