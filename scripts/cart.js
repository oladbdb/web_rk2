document.addEventListener('DOMContentLoaded', function() {
  
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
            const productId = this.dataset.id;
            
            this.classList.add('adding');
            this.textContent = 'Добавляем...';

            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCounter = document.querySelector('.cart-counter');
                    if (cartCounter) {
                        cartCounter.textContent = data.total_items;
                        cartCounter.style.display = data.total_items > 0 ? 'block' : 'none';
                    }
                    
                    setTimeout(() => {
                        this.classList.remove('adding');
                        this.textContent = 'В корзине';
                        this.classList.add('in-cart');
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                this.textContent = 'Ошибка';
                setTimeout(() => {
                    this.classList.remove('adding');
                    this.textContent = 'В корзину';
                }, 1000);
            });
        });
    });
}); 