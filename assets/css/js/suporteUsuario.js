const modalSuporte = document.getElementById('modalSuporte');
const fecharModalSuporte = document.getElementById('fecharModalSuporte');

function abrirModalSuporte(dados) {
    modalSuporte.style.display = 'flex';

    document.getElementById('modalAssunto').textContent = dados.assunto;
    document.getElementById('modalMensagem').textContent = dados.descricao;
    document.getElementById('modalData').textContent = dados.data_envio;
    document.getElementById('modalStatus').textContent = dados.status || 'Pendente';
}

function fecharModal() {
    modalSuporte.style.display = 'none';
}

fecharModalSuporte.addEventListener('click', fecharModal);
window.addEventListener('click', e => { if (e.target === modalSuporte) fecharModal(); });
