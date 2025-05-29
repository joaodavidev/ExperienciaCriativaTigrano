const modalSuporte = document.getElementById('modalSuporte');
const fecharModalSuporte = document.getElementById('fecharModalSuporte');

function abrirModalSuporte(dados) {
    modalSuporte.style.display = 'flex';

    document.getElementById('modalAssunto').textContent = dados.assunto;
    document.getElementById('modalMensagem').textContent = dados.descricao;
    document.getElementById('modalData').textContent = dados.data_envio;
    document.getElementById('modalStatus').textContent = dados.status || 'Pendente';
    document.getElementById('modalResposta').textContent = dados.resposta || 'Ainda sem resposta.';

    if(document.getElementById('modalTicketId')) {
        document.getElementById('modalTicketId').value = dados.id;
    }

}

function fecharModal() {
    modalSuporte.style.display = 'none';
}

fecharModalSuporte.addEventListener('click', fecharModal);
window.addEventListener('click', e => { if (e.target === modalSuporte) fecharModal(); });

document.getElementById('btnEditarTicket').onclick = function() {
  const ticketId = document.getElementById('modalTicketId').value;
  window.location.href = '../includes/updateTicket.php?id=' + encodeURIComponent(ticketId);
};