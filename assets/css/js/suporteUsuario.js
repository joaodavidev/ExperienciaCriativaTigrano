document.addEventListener('DOMContentLoaded', function() {
    // Elementos modais
    const modalSuporte = document.getElementById('modalSuporte');
    const fecharModalSuporte = document.getElementById('fecharModalSuporte');
    const modalNovoTicket = document.getElementById('modalNovoTicket');
    const fecharModalNovoTicket = document.getElementById('fecharModalNovoTicket');
    const botaoAbrirTicket = document.getElementById('botaoAbrirTicket');
    
    // Função para abrir modal de detalhes do ticket
    function abrirModalSuporte(dados) {
        modalSuporte.style.display = 'flex';
        
        document.getElementById('modalAssunto').textContent = dados.assunto;
        document.getElementById('modalMensagem').textContent = dados.descricao;
        document.getElementById('modalData').textContent = dados.data_envio;
        document.getElementById('modalStatus').textContent = dados.status || 'Pendente';
        document.getElementById('modalResposta').textContent = dados.resposta || 'Ainda sem resposta.';
        
        // Mostrar ou ocultar a seção de resposta baseado na existência de resposta
        const respostaWrapper = document.getElementById('modalRespostaWrapper');
        if (dados.resposta && dados.resposta.trim() !== '') {
            respostaWrapper.style.display = 'block';
        } else {
            respostaWrapper.style.display = 'none';
        }
    }
    
    // Função para fechar modal de detalhes
    function fecharModalSuporte() {
        modalSuporte.style.display = 'none';
    }
    
    // Função para abrir modal de novo ticket
    function abrirModalNovoTicket() {
        modalNovoTicket.style.display = 'block';
    }
    
    // Função para fechar modal de novo ticket
    function fecharModalNovoTicket() {
        modalNovoTicket.style.display = 'none';
    }
      // Event listeners para os modais
    fecharModalSuporte.addEventListener('click', function() {
        fecharModalSuporte();
    });
    fecharModalNovoTicket.addEventListener('click', function() {
        fecharModalNovoTicket();
    });
    botaoAbrirTicket.addEventListener('click', abrirModalNovoTicket);
    
    // Fechar modais quando clicar fora deles
    window.addEventListener('click', function(event) {
        if (event.target === modalSuporte) {
            fecharModalSuporte();
        }
        if (event.target === modalNovoTicket) {
            fecharModalNovoTicket();
        }
    });
    
    // Expor a função abrirModalSuporte globalmente para o onclick dos itens
    window.abrirModalSuporte = abrirModalSuporte;
});
