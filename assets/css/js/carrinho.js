      window.onload = function() {
        const el = document.getElementById('produto-<?php echo $produtoEncontradoID; ?>');
        if (el) {
          el.classList.add('encontrado');
          el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      };