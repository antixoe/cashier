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
                            <tr><th>Image</th><th>Code</th><th>Name</th><th>Price</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td style="text-align: center;">
                                    @if($product->image_path)
                                        <img src="{{ asset($product->image_path) }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;">
                                    @else
                                        <div style="width: 48px; height: 48px; background: rgba(220, 38, 38, 0.2); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-image" style="font-size: 20px; color: #dc2626;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $product->code ?? '-' }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td><button class="btn" onclick="addToCart({{ $product->id }})">Add to Cart</button></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <form method="GET" action="{{ route('pos.index') }}" style="display:flex; flex-wrap:wrap; align-items:flex-end; gap:10px; margin-top:16px;">
                    <div style="flex:1; min-width:200px;">
                        <label for="search" style="display:block; margin-bottom:4px; color:#f8fafc;">Search</label>
                        <input id="search" name="search" type="text" value="{{ old('search', $search ?? '') }}" placeholder="Product name or product code" style="width:100%; padding:8px; border-radius:8px; border:1px solid #ccc;" />
                    </div>
                    <div style="display:flex; gap:8px; align-items:flex-end;">
                        <button type="button" class="btn btn-secondary" style="padding: 9px 12px;" onclick="openBarcodeScanner()" title="Scan Barcode"><i class="bi bi-upc-scan"></i></button>
                        <button type="submit" class="btn" style="padding: 9px 16px;">Apply</button>
                        <a href="{{ route('pos.index') }}" class="btn" style="padding: 9px 16px; background: #6b7280;">Clear</a>
                    </div>
                </form>
            </div>

            <div>
                <h2 style="margin-top:0;">Cart</h2>
                <div class="glass-card" style="padding: 16px; margin-bottom: 14px;">
                    <div id="cartArea">
                        @if($cartData && count($cartData) > 0)
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
                                        <td>
                                            <button class="btn btn-danger" style="font-size:12px; padding: 4px 8px; margin-right:4px;" onclick="removeFromCart({{ $productId }})">-</button>
                                            <button class="btn" style="font-size:12px; padding: 4px 8px;" onclick="addToCart({{ $productId }})">+</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <p class="text-right" style="margin:10px 0; font-weight: 700;">Total: {{ number_format($total,2) }}</p>
                        @else
                            <p style="margin: 0;">Cart is empty.</p>
                        @endif
                    </div>
                    <button type="button" class="btn btn-secondary" style="width:100%; margin-bottom: 10px;" onclick="openBarcodeScannerForCart()" title="Scan Product to Add"><i class="bi bi-upc-scan"></i> Scan Product</button>
                    <button class="btn" style="width:100%;" onclick="checkout()">Checkout</button>
                    <p id="checkoutMessage" style="margin-top: 10px; color: #f8fafc;"></p>
                    <div id="toastNotification" class="toast-notification" style="position: fixed; bottom: 18px; right: 18px; min-width: 220px; padding: 12px 16px; border-radius: 10px; color: #ffffff; font-weight: 700; box-shadow: 0 10px 26px rgba(0,0,0,0.35); opacity: 0; transform: translateY(24px); transition: opacity 0.25s ease, transform 0.25s ease; z-index: 999999; display: none; pointer-events: none;"></div>
                    <div id="invoiceModal" style="position: fixed; inset:0; background: rgba(0,0,0,0.65); display:none; align-items:center; justify-content:center; z-index:100000;">
                        <div style="background:#fff; color:#111; border-radius:16px; padding:24px; width:min(92vw,550px); max-height:87vh; overflow:auto; box-shadow:0 16px 38px rgba(0,0,0,0.35);">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                                <h3 style="margin: 0;">Invoice</h3>
                                <button style="border:none; background:none; font-size:20px; cursor:pointer;" onclick="closeInvoice()">&times;</button>
                            </div>
                            <div id="invoiceContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Barcode Scanner Modal -->
        <div id="barcodeModal" class="modal-overlay">
            <div class="modal-content" style="max-width: 600px;">
                <span class="modal-close" onclick="closeBarcodeScanner()">&times;</span>
                <h3>Scan Barcode</h3>
                <p>Position the barcode in front of your camera.</p>
                <div id="barcode-container" style="width: 100%; height: 300px; background: #000; border-radius: 8px; margin: 20px 0;"></div>
                <button type="button" class="btn btn-danger" onclick="closeBarcodeScanner()">Cancel</button>
            </div>
        </div>

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
        function showToast(message, success = true) {
            const toast = document.getElementById('toastNotification');
            if (!toast) return;

            toast.textContent = message;
            toast.style.background = success ? 'linear-gradient(155deg, #059669, #10b981)' : 'linear-gradient(155deg, #dc2626, #ef4444)';
            toast.style.display = 'block';
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(24px)';
                setTimeout(() => toast.style.display = 'none', 200);
            }, 3000);
        }

        function closeInvoice() {
            document.getElementById('invoiceModal').style.display = 'none';
        }

        function checkout() {
            fetch('{{ route('pos.checkout') }}', { method: 'POST', headers: csrfHeaders(), body: JSON.stringify({}) }).then(r => r.json()).then(data => {
                const el = document.getElementById('checkoutMessage');
                if (data.success) {
                    const message = 'Checkout successful: Rp ' + parseFloat(data.total).toFixed(0);
                    if (el) el.innerText = message;
                    showToast(message, true);

                    const invoiceEl = document.getElementById('invoiceContent');
                    if (invoiceEl) {
                        let itemsHtml = '<table style="width:100%; border-collapse: collapse; margin-top:12px;">';
                        itemsHtml += '<thead><tr><th style="text-align:left; border-bottom:1px solid #ddd; padding: 6px;">Product</th><th style="text-align:right; border-bottom:1px solid #ddd; padding: 6px;">Qty</th><th style="text-align:right; border-bottom:1px solid #ddd; padding: 6px;">Price</th><th style="text-align:right; border-bottom:1px solid #ddd; padding: 6px;">Line</th></tr></thead><tbody>';
                        data.items.forEach(item => {
                            itemsHtml += '<tr>' +
                                '<td style="padding: 6px;">' + item.product + '</td>' +
                                '<td style="padding: 6px; text-align:right;">' + item.quantity + '</td>' +
                                '<td style="padding: 6px; text-align:right;">Rp ' + parseFloat(item.price).toFixed(2) + '</td>' +
                                '<td style="padding: 6px; text-align:right;">Rp ' + parseFloat(item.line).toFixed(2) + '</td>' +
                                '</tr>';
                        });
                        itemsHtml += '</tbody></table>';

                        const invoiceHtml =
                            '<p style="margin: 4px 0;">Sale #: <strong>' + data.sale_id + '</strong></p>' +
                            '<p style="margin: 4px 0;">Date: ' + data.date + '</p>' +
                            itemsHtml +
                            '<p style="margin: 12px 0 0 0; font-weight: 700; text-align:right;">Total: Rp ' + parseFloat(data.total).toFixed(2) + '</p>';

                        invoiceEl.innerHTML = invoiceHtml;
                        document.getElementById('invoiceModal').style.display = 'flex';
                    }

                    // Keep page open so user sees invoice; cart update is handled by backend cleared state.
                    setTimeout(() => {
                        window.location.reload();
                    }, 2200);
                } else {
                    const message = data.message || 'Checkout failed.';
                    if (el) el.innerText = message;
                    showToast(message, false);
                }
            }).catch(err => {
                const message = 'Checkout failed. Check connection or server.';
                const el = document.getElementById('checkoutMessage');
                if (el) el.innerText = message;
                showToast(message, false);
            });
        }
    </script>

    <!-- QuaggaJS for Barcode Scanning -->
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script>
        let quaggaInitialized = false;
        let scanMode = 'search'; // 'search' or 'cart'

        function openBarcodeScanner() {
            scanMode = 'search';
            openScanner();
        }

        function openBarcodeScannerForCart() {
            scanMode = 'cart';
            openScanner();
        }

        function openScanner() {
            document.getElementById('barcodeModal').style.display = 'flex';
            if (!quaggaInitialized) {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.querySelector('#barcode-container'),
                        constraints: {
                            width: 640,
                            height: 480,
                            facingMode: "environment" // Use back camera
                        }
                    },
                    locator: {
                        patchSize: "medium",
                        halfSample: true
                    },
                    numOfWorkers: 2,
                    decoder: {
                        readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "upc_reader", "upc_e_reader"]
                    },
                    locate: true
                }, function(err) {
                    if (err) {
                        console.error(err);
                        alert('Unable to access camera. Please check permissions.');
                        closeBarcodeScanner();
                        return;
                    }
                    quaggaInitialized = true;
                    Quagga.start();
                });

                Quagga.onDetected(function(result) {
                    const code = result.codeResult.code;
                    console.log('Barcode detected:', code);
                    closeBarcodeScanner();
                    handleScannedCode(code);
                });
            } else {
                Quagga.start();
            }
        }

        function handleScannedCode(code) {
            if (scanMode === 'search') {
                // Set the search input and submit
                document.getElementById('search').value = code;
                document.querySelector('form').submit();
            } else if (scanMode === 'cart') {
                // Find product by code and add to cart
                fetch('{{ route('pos.findProductByCode') }}', {
                    method: 'POST',
                    headers: csrfHeaders(),
                    body: JSON.stringify({ code: code })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.product) {
                        addToCart(data.product.id);
                        showToast('Product added to cart: ' + data.product.name, true);
                    } else {
                        showToast('Product not found for code: ' + code, false);
                    }
                })
                .catch(err => {
                    console.error('Error finding product:', err);
                    showToast('Error scanning product', false);
                });
            }
        }

        function closeBarcodeScanner() {
            document.getElementById('barcodeModal').style.display = 'none';
            if (quaggaInitialized) {
                Quagga.stop();
            }
        }
    </script>
@endsection
