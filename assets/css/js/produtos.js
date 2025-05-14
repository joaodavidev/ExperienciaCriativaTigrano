const modal = document.getElementById('modalProduto');
    const abrirModalBtn = document.getElementById('btnNovoProduto');
    const fecharModalBtn = document.getElementById('fecharModal');
    const form = document.querySelector('form.form-produto');

    function abrirModal(dados = {}) {
      modal.style.display = 'flex';
      form.reset();
      form.id.value = dados.id || '';
      form.nome.value = dados.nome || '';
      form.categoria.value = dados.categoria || '';
      form.preco.value = dados.preco || '';
      form.icone.value = dados.icone || '';
      form.descricao.value = dados.descricao || '';
      form.status.value = dados.status || 'Ativo'; // ← adicionado aqui
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