
document.addEventListener('DOMContentLoaded', function() {
  // Adiciona efeito de transição aos itens do carrinho
  const cartItems = document.querySelectorAll('.carrinho-item');
  if (cartItems) {
    cartItems.forEach(item => {
      // Adiciona efeito hover suave
      item.addEventListener('mouseenter', function() {
        this.style.transition = 'background-color 0.2s';
        this.style.backgroundColor = document.body.classList.contains('light-mode') ? '#f5f7fa' : '#2a2a2a';
      });
      
      item.addEventListener('mouseleave', function() {
        this.style.transition = 'background-color 0.2s';
        this.style.backgroundColor = document.body.classList.contains('light-mode') ? '#fff' : '#262626';
      });
    });
  }
    const checkoutButton = document.querySelector('.btn-finalizar');
  if (checkoutButton) {
    checkoutButton.addEventListener('mouseenter', function() {
      this.style.transition = 'all 0.2s';
      this.style.transform = 'translateY(-2px)';
      this.style.boxShadow = '0 4px 8px rgba(29, 78, 216, 0.3)';
    });
    
    checkoutButton.addEventListener('mouseleave', function() {
      this.style.transition = 'all 0.2s';
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = 'none';
    });
  }
});
