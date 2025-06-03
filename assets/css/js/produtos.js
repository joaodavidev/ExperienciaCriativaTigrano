const modal = document.getElementById('modalProduto');
    const abrirModalBtn = document.getElementById('btnNovoProduto');
    const fecharModalBtn = document.getElementById('fecharModal');
    const form = document.querySelector('form.form-produto');

   function abrirModal(dados = {}) {
    const modal = document.getElementById('modalProduto');
    const form = document.querySelector('.form-produto');
    const titulo = modal.querySelector('h2');

    modal.style.display = 'flex';
    form.reset();

    // Preencher os campos
    form.querySelector('[name="id"]').value = dados.id || '';
    form.querySelector('[name="nome"]').value = dados.nome || '';
    form.querySelector('[name="categoria"]').value = dados.categoria || '';
    form.querySelector('[name="preco"]').value = dados.preco || '';
    form.querySelector('[name="descricao"]').value = dados.descricao || '';
    form.querySelector('[name="status"]').value = dados.status || 'Ativo';

    if (dados.id) {
        form.action = "../includes/updateProduto.php";
        titulo.textContent = "Editar Produto";
        console.log("Modo edição - ID:", dados.id);
    } else {
        form.action = "../includes/createProduto.php";
        titulo.textContent = "Novo Produto";
        console.log("Modo criação");
    }
}


    function fecharModal() {
      modal.style.display = 'none';
    }

    abrirModalBtn.addEventListener('click', () => abrirModal());
    fecharModalBtn.addEventListener('click', fecharModal);
    window.addEventListener('click', e => { if (e.target === modal) fecharModal(); });

    document.querySelectorAll('.editar-produto').forEach(btn => {
      btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        const res = await fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ action: 'buscar', id })
        });
        const data = await res.json();
        abrirModal(data);
      });
    });

    document.querySelectorAll('.deletar-produto').forEach(btn => {
      btn.addEventListener('click', async () => {
        if (!confirm('Deseja realmente excluir este produto?')) return;
        const id = btn.dataset.id;
        const res = await fetch('', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ action: 'excluir', id })
        });
        const result = await res.json();
        if (result.success) {
          btn.closest('.tabela-linha').remove();
        }
      });
    });