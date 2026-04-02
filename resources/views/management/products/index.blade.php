@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div>
                <h1>Product Management</h1>
                <p>Manage products in your inventory.</p>
            </div>
            <div style="display:flex; gap: 8px; align-items:center;">
                @if(auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('management.products.index', ['show_trashed' => !($showTrashed ?? false) ? 1 : 0, 'category_id' => $categoryId ?? null]) }}" class="btn btn-secondary">
                        {{ ($showTrashed ?? false) ? 'Hide Trashed' : 'Show Trashed' }}
                    </a>
                @endif
                <button class="btn" onclick="openAddProductModal()" style="cursor: pointer;">+ Add Product</button>
            </div>
        </section>

        @if(session('success'))
            <div class="glass-card" style="background: rgba(34, 197, 94, 0.35); border-color: rgba(134, 239, 172, 0.4);">
                <p class="message" style="color:#fde68a; margin: 0;">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="glass-card" style="background: rgba(239, 68, 68, 0.35); border-color: rgba(248, 113, 113, 0.4);">
                <p class="message" style="color:#fef9c3; margin: 0;">{{ session('error') }}</p>
            </div>
        @endif

        <section class="glass-card">
            <h2 style="margin-top:0;">Products List</h2>
            <form method="GET" action="{{ route('management.products.index') }}" style="margin-bottom:12px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
                <div>
                    <label for="category_id" style="font-size:12px; color:#9ca3af;">Filter by Category</label>
                    <select name="category_id" id="category_id" class="glass-input" style="height:38px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (int)($categoryId ?? 0) === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn">Apply</button>
                <a href="{{ route('management.products.index') }}" class="btn btn-secondary">Reset</a>
            </form>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->code ?? '-' }}</td>
                                <td><strong>{{ $product->name }}</strong></td>
                                <td>
                                    <span style="display:inline-block;background:rgba(34,197,94,0.35);color:#fef9c3;border-radius:999px;padding:4px 12px;font-size:12px;font-weight:600;">
                                        Rp {{ number_format($product->price, 2) }}
                                    </span>
                                </td>
                                <td style="font-size: 12px; color: #9ca3af;">{{ optional($product->category)->name ?? '-' }}</td>
                                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $product->description ?? '-' }}
                                </td>
                                <td style="font-size: 12px; color: #9ca3af;">{{ $product->created_at->format('Y-m-d') }}</td>
                                <td>
                                    @if($product->trashed())
                                        @if(auth()->user()->hasRole('superadmin'))
                                            <button class="btn btn-success" style="font-size:12px; padding: 4px 8px; margin-right: 4px;" onclick="window.location='{{ route('management.products.restore', $product->id) }}'">Restore</button>
                                            <button class="btn btn-danger" style="font-size:12px; padding: 4px 8px;" onclick="if(confirm('Permanent delete this product?')) { fetch('{{ route('management.products.forceDelete', $product->id) }}', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content') }}).then(r=>r.ok?location.reload():alert('Delete failed')); }">Hard Delete</button>
                                        @else
                                            <span style="font-size:12px; color:#facc15;">Trashed</span>
                                        @endif
                                    @else
                                        <button class="btn btn-secondary" style="font-size:12px; padding: 4px 8px; margin-right: 4px;" onclick="openEditProductModal({{ $product->id }})">Edit</button>
                                        <button class="btn btn-danger" style="font-size:12px; padding: 4px 8px;" onclick="deleteProduct({{ $product->id }})">Delete</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; color:#fde68a;">No products found. <a href="javascript:void(0)" onclick="openAddProductModal()" style="color:#fef9c3; text-decoration: underline; cursor: pointer;">Create one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div style="margin-top:12px;">{{ $products->links() }}</div>
            @endif
        </section>

        <section class="glass-card" style="margin-top: 20px;">
            <h3>Product Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                @php
                    $totalProducts = \App\Models\Product::count();
                    $avgPrice = \App\Models\Product::avg('price');
                    $totalSold = \App\Models\SaleItem::count();
                @endphp
                <div style="background: rgba(59, 130, 246, 0.2); padding: 16px; border-radius: 12px; border-left: 4px solid rgba(59, 130, 246, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Total Products</div>
                    <div style="font-size: 24px; font-weight: 700; color: #fde68a;">{{ $totalProducts }}</div>
                </div>
                <div style="background: rgba(34, 197, 94, 0.2); padding: 16px; border-radius: 12px; border-left: 4px solid rgba(34, 197, 94, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Avg Price</div>
                    <div style="font-size: 24px; font-weight: 700; color: #fde68a;">Rp {{ number_format($avgPrice ?? 0, 2) }}</div>
                </div>
                <div style="background: rgba(168, 85, 247, 0.2); padding: 16px; border-radius: 12px; border-left: 4px solid rgba(168, 85, 247, 0.6);">
                    <div style="font-size: 12px; color: #9ca3af; margin-bottom: 4px;">Total Sold</div>
                    <div style="font-size: 24px; font-weight: 700; color: #fde68a;">{{ $totalSold }}</div>
                </div>
            </div>
        </section>
    </main>

    <!-- Add Product Modal -->
    <div class="modal-overlay" id="addProductModal" style="position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 2000; overflow-y: auto;">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(255, 255, 255, 0.65); box-shadow: 0 14px 40px rgba(69, 10, 10, 0.3); border-radius: 18px; padding: 32px; color: #1f2937; max-width: 520px; margin: 20px;">
            <span class="modal-close" onclick="closeAddProductModal()" style="position: absolute; right: 16px; top: 16px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700;">&times;</span>
            <h3 style="margin: 0 0 24px 0; color: #dc2626; font-size: 22px;">Add New Product</h3>
            <form id="addProductForm" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Product Code *</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="code" class="glass-input" placeholder="Product code (e.g. FRU-001)" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px; flex: 1;">
                        <button type="button" class="btn btn-secondary" onclick="openBarcodeScanner('add')" title="Scan Barcode"><i class="bi bi-upc-scan"></i></button>
                    </div>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Product Name *</label>
                    <input type="text" name="name" class="glass-input" placeholder="Product name" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Price (Rp) *</label>
                    <input type="number" name="price" class="glass-input" placeholder="0" step="0.01" min="0" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Category</label>
                    <select id="addProductCategory" name="category_id" class="glass-input" style="width:100%; height:44px; padding:10px 14px; border-radius:12px; border:1px solid rgba(220,38,38,0.5); background:rgba(255,255,255,0.85); color:#1f2937; outline:none; font-size:16px;">
                        <option value="">No category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Description</label>
                    <textarea name="description" class="glass-input" placeholder="Product description (optional)" style="width: 100%; min-height: 80px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <button type="button" class="btn" style="background: #22c55e; color: white; margin-top: 8px; width: 100%;" onclick="submitAddProduct()">Add Product</button>
                <p id="productMessage" style="margin-top: 10px; color: #f8fafc; font-size: 14px; text-align: center;"></p>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal-overlay" id="editProductModal" style="position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 2000; overflow-y: auto;">
        <div class="modal-content" style="background: rgba(255, 255, 255, 0.88); border: 1px solid rgba(255, 255, 255, 0.65); box-shadow: 0 14px 40px rgba(69, 10, 10, 0.3); border-radius: 18px; padding: 32px; color: #1f2937; max-width: 520px; margin: 20px;">
            <span class="modal-close" onclick="closeEditProductModal()" style="position: absolute; right: 16px; top: 16px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700;">&times;</span>
            <h3 style="margin: 0 0 24px 0; color: #dc2626; font-size: 22px;">Edit Product</h3>
            <form id="editProductForm" style="display: flex; flex-direction: column; gap: 16px;">
                <input type="hidden" id="editProductId" name="product_id" value="">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Product Code *</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="editProductCode" name="code" class="glass-input" placeholder="Product code (e.g. FRU-001)" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px; flex: 1;">
                        <button type="button" class="btn btn-secondary" onclick="openBarcodeScanner('edit')" title="Scan Barcode"><i class="bi bi-upc-scan"></i></button>
                    </div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Product Name *</label>
                    <input type="text" id="editProductName" name="name" class="glass-input" placeholder="Product name" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Price (Rp) *</label>
                    <input type="number" id="editProductPrice" name="price" class="glass-input" placeholder="0" step="0.01" min="0" required style="width: 100%; height: 44px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px;">
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Category</label>
                    <select id="editProductCategory" name="category_id" class="glass-input" style="width:100%; height:44px; padding:10px 14px; border-radius:12px; border:1px solid rgba(220,38,38,0.5); background:rgba(255,255,255,0.85); color:#1f2937; outline:none; font-size:16px;">
                        <option value="">No category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Description</label>
                    <textarea id="editProductDescription" name="description" class="glass-input" placeholder="Product description (optional)" style="width: 100%; min-height: 80px; padding: 10px 14px; border-radius: 12px; border: 1px solid rgba(220, 38, 38, 0.5); background: rgba(255,255,255,0.85); color: #1f2937; outline: none; font-size: 16px; font-family: inherit; resize: vertical;"></textarea>
                </div>

                <button type="button" class="btn" style="background: #f59e0b; color: white; margin-top: 8px; width: 100%;" onclick="submitEditProduct()">Update Product</button>
                <p id="editProductMessage" style="margin-top: 10px; color: #f8fafc; font-size: 14px; text-align: center;"></p>
            </form>
        </div>
    </div>

    <!-- Barcode Scanner Modal -->
    <div id="barcodeModal" class="modal-overlay" style="position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(69, 10, 10, 0.68); backdrop-filter: blur(5px); z-index: 3000;">
        <div class="modal-content" style="max-width: 600px;">
            <span class="modal-close" onclick="closeBarcodeScanner()" style="position: absolute; right: 14px; top: 12px; font-size: 24px; color: #dc2626; cursor: pointer; font-weight: 700;">&times;</span>
            <h3>Scan Barcode</h3>
            <p>Position the barcode in front of your camera.</p>
            <div id="barcode-container" style="width: 100%; height: 300px; background: #000; border-radius: 8px; margin: 20px 0;"></div>
            <button type="button" class="btn btn-danger" onclick="closeBarcodeScanner()">Cancel</button>
        </div>
    </div>

    <script>
        function csrfHeaders() {
            return {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            };
        }

        function openAddProductModal() {
            document.getElementById('addProductModal').style.display = 'flex';
            document.getElementById('productMessage').innerText = '';
            document.getElementById('addProductForm').reset();
        }

        function closeAddProductModal() {
            document.getElementById('addProductModal').style.display = 'none';
            document.getElementById('productMessage').innerText = '';
        }

        function openEditProductModal(productId) {
            fetch('{{ url('/management/products') }}/' + productId, {
                method: 'GET',
                headers: csrfHeaders(),
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch product data');
                    }
                    return response.json();
                })
                .then(product => {
                    document.getElementById('editProductId').value = product.id;
                    document.getElementById('editProductCode').value = product.code || '';
                    document.getElementById('editProductName').value = product.name;
                    document.getElementById('editProductPrice').value = product.price;
                    document.getElementById('editProductCategory').value = product.category_id || '';
                    document.getElementById('editProductDescription').value = product.description ?? '';
                    document.getElementById('editProductMessage').innerText = '';
                    document.getElementById('editProductModal').style.display = 'flex';
                })
                .catch(error => alert('Error: ' + error.message));
        }

        function closeEditProductModal() {
            document.getElementById('editProductModal').style.display = 'none';
            document.getElementById('editProductMessage').innerText = '';
        }

        function submitAddProduct() {
            const form = document.getElementById('addProductForm');
            const formData = new FormData(form);
            const data = {
                code: formData.get('code'),
                name: formData.get('name'),
                price: parseFloat(formData.get('price')),
                category_id: formData.get('category_id') || null,
                description: formData.get('description') || null,
            };

            if (!data.name || isNaN(data.price) || data.price <= 0) {
                alert('Please fill in all required fields with valid values');
                return;
            }

            fetch('{{ route('management.products.store') }}', {
                method: 'POST',
                headers: csrfHeaders(),
                body: JSON.stringify(data)
            }).then(r => {
                if (r.ok) {
                    alert('Product added successfully!');
                    closeAddProductModal();
                    location.reload();
                } else {
                    return r.json().then(data => {
                        alert('Error: ' + (data.message || 'Failed to add product'));
                    });
                }
            }).catch(e => alert('Error: ' + e));
        }

        function submitEditProduct() {
            const productId = document.getElementById('editProductId').value;
            const code = document.getElementById('editProductCode').value.trim();
            const name = document.getElementById('editProductName').value.trim();
            const price = parseFloat(document.getElementById('editProductPrice').value);
            const description = document.getElementById('editProductDescription').value.trim() || null;

            if (!code || !name || isNaN(price) || price <= 0) {
                alert('Please fill in all required fields with valid values');
                return;
            }

            fetch('{{ url('/management/products') }}/' + productId, {
                method: 'PUT',
                headers: csrfHeaders(),
                body: JSON.stringify({ code, name, price, category_id: document.getElementById('editProductCategory').value || null, description })
            }).then(r => {
                if (r.ok) {
                    alert('Product updated successfully!');
                    closeEditProductModal();
                    location.reload();
                } else {
                    return r.json().then(data => {
                        alert('Error: ' + (data.message || 'Failed to update product'));
                    });
                }
            }).catch(e => alert('Error: ' + e));
        }

        function deleteProduct(productId) {
            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }

            fetch('{{ url('/management/products') }}/' + productId, {
                method: 'DELETE',
                headers: csrfHeaders(),
            }).then(r => {
                if (r.ok) {
                    alert('Product deleted successfully!');
                    location.reload();
                } else {
                    return r.json().then(data => {
                        alert('Error: ' + (data.message || 'Failed to delete product'));
                    });
                }
            }).catch(e => alert('Error: ' + e));
        }

        // Close modal when clicking outside
        document.getElementById('addProductModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddProductModal();
            }
        });

        document.getElementById('editProductModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditProductModal();
            }
        });
    </script>

    <!-- QuaggaJS for Barcode Scanning -->
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script>
        let quaggaInitialized = false;
        let scanMode = 'add'; // 'add' or 'edit'

        function openBarcodeScanner(mode) {
            scanMode = mode;
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
                            facingMode: "environment"
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
                    if (scanMode === 'add') {
                        document.querySelector('#addProductModal input[name="code"]').value = code;
                    } else if (scanMode === 'edit') {
                        document.getElementById('editProductCode').value = code;
                    }
                });
            } else {
                Quagga.start();
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
