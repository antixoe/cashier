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
            <form action="{{ route('management.products.store') }}" method="POST">
                @csrf

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
    </main>

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
