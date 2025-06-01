function confirmarExclusao(e) {
  e.preventDefault();

  Swal.fire({
    icon: 'warning',
    title: 'Tem certeza?',
    text: 'Essa ação excluirá permanentemente o administrador.',
    showCancelButton: true,
    confirmButtonText: 'Sim, excluir',
    cancelButtonText: 'Cancelar',
    background: localStorage.getItem("tema") === "claro" ? "#E6E4E4" : "#262626",
    color: localStorage.getItem("tema") === "claro" ? "#121212" : "#ffffff",
    confirmButtonColor: "#1D4ED8",
    cancelButtonColor: "#6B7280"
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('formDeleteAdmin').submit();
    }
  });
}
