const modalSuporte = document.getElementById('modalSuporte');
const fecharModalSuporte = document.getElementById('fecharModalSuporte');
let modoEdicao = false;

function abrirModalSuporte(dados) {
    modalSuporte.style.display = 'flex';
    modoEdicao = false;

    document.getElementById('modalAssunto').textContent = dados.assunto;
    document.getElementById('modalMensagem').textContent = dados.descricao;
    document.getElementById('modalData').textContent = dados.data_envio;
    document.getElementById('modalStatus').textContent = dados.status || 'Pendente';
    document.getElementById('modalResposta').textContent = dados.resposta || 'Ainda sem resposta.';

    if(document.getElementById('modalTicketId')) {
        document.getElementById('modalTicketId').value = dados.id;
    }

    document.getElementById('btnEditarTicket').className = 'bx bx-edit';
}

function fecharModal() {
    modalSuporte.style.display = 'none';
}

fecharModalSuporte.addEventListener('click', fecharModal);
window.addEventListener('click', e => { if (e.target === modalSuporte) fecharModal(); });

document.getElementById('btnEditarTicket').onclick = function() {
    const ticketId = document.getElementById('modalTicketId').value;
    
    if (!modoEdicao) {
        // Entrar no modo de edição
        modoEdicao = true;
        this.className = 'bx bx-save';
        
        const containerAssunto = document.querySelector('#modalAssunto').parentNode;
        const containerMensagem = document.querySelector('#modalMensagem').parentNode;
        
        // Adicionar classe de edição aos containers
        containerAssunto.classList.add('editable-mode');
        containerMensagem.classList.add('editable-mode');
        
        // Criar elementos editáveis
        const inputAssunto = document.createElement('input');
        inputAssunto.type = 'text';
        inputAssunto.value = document.getElementById('modalAssunto').textContent;
        inputAssunto.id = 'editAssunto';
        
        const textareaMensagem = document.createElement('textarea');
        textareaMensagem.value = document.getElementById('modalMensagem').textContent;
        textareaMensagem.id = 'editMensagem';
        
        // Substituir os textos pelos campos editáveis
        document.getElementById('modalAssunto').replaceWith(inputAssunto);
        document.getElementById('modalMensagem').replaceWith(textareaMensagem);
        
        // Focar no primeiro campo
        inputAssunto.focus();
    } else {
        // Salvar as alterações
        const assunto = document.getElementById('editAssunto').value;
        const descricao = document.getElementById('editMensagem').value;
        
        // Enviar para o servidor via AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../includes/updateTicket.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            if (this.status === 200) {
                window.location.reload();
            } else {
                alert('Erro ao atualizar o ticket.');
                // Restaurar visualização em caso de erro
                modoEdicao = false;
                document.getElementById('btnEditarTicket').className = 'bx bx-edit';
            }
        };
        
        xhr.send(`id=${ticketId}&assunto=${encodeURIComponent(assunto)}&descricao=${encodeURIComponent(descricao)}`);
    }
};