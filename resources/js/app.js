import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    // Add to cart
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            fetch('/add-to-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Simple reload to update cart
                }
            });
        });
    });

    // Remove from cart
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            fetch('/remove-from-cart', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });

    // Checkout
    document.getElementById('checkout').addEventListener('click', function() {
        fetch('/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sale completed! Total: $' + data.total);
                location.reload();
            } else {
                alert(data.message);
            }
        });
    });

    // Calculate total
    function calculateTotal() {
        let subtotal = 0;
        document.querySelectorAll('#cart-items .cart-item').forEach(item => {
            const itemTotal = parseFloat(item.dataset.total || '0');
            subtotal += itemTotal;
        });

        const service = subtotal > 0 ? 1.00 : 0.00;
        const grandTotal = subtotal + service;

        document.getElementById('total').textContent = subtotal.toFixed(2);
        const checkoutTotalEl = document.getElementById('checkout-total');
        if (checkoutTotalEl) {
            checkoutTotalEl.textContent = grandTotal.toFixed(2);
        }
    }
    calculateTotal();

    // Recalculate after a server action
    function reloadCart() {
        location.reload();
    }

    document.querySelectorAll('.add-to-cart, .remove-from-cart').forEach(button =>
        button.addEventListener('click', () => setTimeout(calculateTotal, 300))
    );
});
