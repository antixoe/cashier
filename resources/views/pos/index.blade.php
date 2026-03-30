@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card">
            <h1> SupriMart</h1>
            <p>Use the product list to add items to the cart, then checkout.</p>
        </section>

        <section class="glass-card" style="display: grid; grid-template-columns: 1fr 360px; gap: 20px;">
            <div>
                <h2 style="margin-top:0;">Products</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Name</th><th>Price</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td><button class="btn" onclick="addToCart({{ $product->id }})">Add to Cart</button></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h2 style="margin-top:0;">Cart</h2>
                <div class="glass-card" style="padding: 16px; margin-bottom: 14px;">
                    <div id="cartArea">
                        @if(count($cartData) > 0)
                            <table>
                                <thead>
                                <tr><th>Product</th><th>Qty</th><th>Price</th><th></th></tr>
                                </thead>
                                <tbody>
                                @php $total = 0; @endphp
                                @foreach($cartData as $productId => $item)
                                    @php $line = $item['price'] * $item['quantity']; $total += $line; @endphp
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['quantity'] }}</td>
                                        <td>{{ number_format($line,2) }}</td>
                                        <td><button class="btn btn-danger" style="font-size:12px; padding: 4px 8px;" onclick="removeFromCart({{ $productId }})">-</button></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <p class="text-right" style="margin:10px 0; font-weight: 700;">Total: {{ number_format($total,2) }}</p>
                        @else
                            <p style="margin: 0;">Cart is empty.</p>
                        @endif
                    </div>
                    <button class="btn" style="width:100%;" onclick="checkout()">Checkout</button>
                    <p id="checkoutMessage" style="margin-top: 10px; color: #f8fafc;"></p>
                </div>
            </div>
        </section>
    </main>

    <script>
        function csrfHeaders() {
            return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Content-Type': 'application/json' };
        }
        function addToCart(id) {
            fetch('{{ route('pos.addToCart') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({ product_id: id }) }).then(() => location.reload());
        }
        function removeFromCart(id) {
            fetch('{{ route('pos.removeFromCart') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({ product_id: id }) }).then(() => location.reload());
        }
        function checkout() {
            fetch('{{ route('pos.checkout') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({}) }).then(r => r.json()).then(data => {
                const el = document.getElementById('checkoutMessage');
                if (data.success) { el.innerText = 'Checkout successful: $' + parseFloat(data.total).toFixed(2); setTimeout(() => location.reload(), 1000); }
                else { el.innerText = data.message || 'Checkout failed'; }
            });
        }
    </script>
@endsection
