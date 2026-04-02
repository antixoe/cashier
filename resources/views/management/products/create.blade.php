@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div>
                <h1>Create Product</h1>
                <p>Add a new product to your inventory.</p>
            </div>
            <a href="{{ route('management.products.index') }}" class="btn btn-secondary">← Back to Products</a>
        </section>

        @if($errors->any())
            <div class="glass-card" style="background: rgba(239, 68, 68, 0.35); border-color: rgba(248, 113, 113, 0.4);">
                <h3 style="color: #fef9c3; margin-top: 0;">Validation Errors</h3>
                <ul style="color: #fde68a; margin: 0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="glass-card">
            <form action="{{ route('management.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom: 18px;">
                    <label for="code" style="display: block; margin-bottom: 8px; font-weight: 600; color: #fde68a;">Product Code *</label>
                    <div style="display: flex; gap: 8px;">
                        <input 
                            type="text" 
                            name="code" 
                            id="code" 
                            class="glass-input" 
                            placeholder="Enter product code (e.g. FRU-001)"
                            value="{{ old('code') }}"
                            required
                            style="flex: 1;"
                        >
                        <button type="button" class="btn btn-secondary" onclick="openBarcodeScanner()" title="Scan Barcode"><i class="bi bi-upc-scan"></i></button>
                    </div>
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #fde68a;">Product Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="glass-input" 
                        placeholder="Enter product name"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #fde68a;">Price (Rp) *</label>
                    <input 
                        type="number" 
                        name="price" 
                        id="price" 
                        class="glass-input" 
                        placeholder="0.00"
                        step="0.01"
                        min="0.01"
                        value="{{ old('price') }}"
                        required
                    >
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="category_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #fde68a;">Category</label>
                    <select name="category_id" id="category_id" class="glass-input" style="width:100%; height:40px;">
                        <option value="">None</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 18px;">
                    <label for="image_path" style="display: block; margin-bottom: 8px; font-weight: 600; color: #fde68a;">Product Image</label>
                    <div style="display: flex; gap: 16px; align-items: flex-start;">
                        <div id="productImagePreview" style="width: 120px; height: 120px; border-radius: 12px; background: rgba(220, 38, 38, 0.2); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(220, 38, 38, 0.5); flex-shrink: 0;">
                            <i class="bi bi-image" style="font-size: 40px; color: #dc2626;"></i>
                        </div>
                        <div style="flex: 1;">
                            <input type="file" name="image_path" id="image_path" accept="image/*" style="display: none;">
                            <button type="button" onclick="document.getElementById('image_path').click()" class="btn" style="background: #3b82f6; color: white; margin-bottom: 8px;">
                                <i class="bi bi-upload"></i> Choose Image
                            </button>
                            <p style="font-size: 12px; color: #9ca3af; margin: 0;">JPG, PNG, GIF or WebP format. Max 5MB.</p>
                        </div>
                    </div>
                    @error('image_path')
                        <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-bottom: 24px;">
                    <label for="description" style="display: block; margin-bottom: 8px; font-weight: 600; color: #fde68a;">Description</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        class="glass-textarea" 
                        placeholder="Enter product description"
                        rows="5"
                    >{{ old('description') }}</textarea>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('management.products.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn">Create Product</button>
                </div>
            </form>
        </section>

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

    </main>

    <!-- QuaggaJS for Barcode Scanning -->
    <script src="https://cdn.jsdelivr.net/npm/quagga@0.12.1/dist/quagga.min.js"></script>
    <script>
        let quaggaInitialized = false;

        function openBarcodeScanner() {
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
                    document.getElementById('code').value = code;
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

        // Product Image Preview
        document.getElementById('image_path')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('productImagePreview');
                    preview.innerHTML = `<img src="${event.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <style>
        .glass-textarea {
            width: 100%;
            padding: 12px 16px;
            border-radius: 14px;
            border: 1px solid rgba(220, 38, 38, 0.5);
            background: rgba(255,255,255,0.85);
            color: #1f2937;
            outline: none;
            margin-top: 8px;
            font-size: 16px;
            font-weight: 500;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .glass-textarea::placeholder {
            color: #6b7280;
        }
        .glass-textarea:focus {
            border-color: #dc2626;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.25);
        }
    </style>

@endsection
