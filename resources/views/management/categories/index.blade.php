@extends('layout.app')

@section('sidebar')
    @include('partials.sidebar')
@endsection

@section('content')

    <main class="main-content">
        <section class="glass-card" style="display: flex; align-items: center; justify-content: space-between; gap: 16px;">
            <div>
                <h1>Category Management</h1>
                <p>Manage product categories and categorize inventory.</p>
            </div>
            <button class="btn" onclick="openCategoryModal()">+ Add Category</button>
        </section>

        @if(session('success'))
            <div class="glass-card" style="background: rgba(34, 197, 94, 0.35); border-color: rgba(134, 239, 172, 0.4);">
                <p class="message" style="color:#fde68a; margin: 0;">{{ session('success') }}</p>
            </div>
        @endif

        <section class="glass-card">
            <h2 style="margin-top:0;">Categories list</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td>
                                    <button class="btn" style="padding:4px 8px; margin-right:6px;" onclick="openEditCategoryModal({{ $category->id }})">Edit</button>
                                    <button class="btn btn-danger" style="padding:4px 8px;" onclick="deleteCategory({{ $category->id }})">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center; color:#fde68a;">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($categories->hasPages())
                <div style="margin-top: 12px;">{{ $categories->links() }}</div>
            @endif
        </section>
    </main>

    <div class="modal-overlay" id="categoryModal" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);z-index:2000;">
        <div class="modal-content" style="background:#ffffff;padding:24px;border-radius:12px;max-width:480px;position:relative;">
            <span class="modal-close" style="position:absolute;top:12px;right:12px;cursor:pointer;font-size:22px;" onclick="closeCategoryModal()">&times;</span>
            <h3 style="margin-top:0;">Add New Category</h3>
            <form id="categoryForm" action="{{ route('management.categories.store') }}" method="POST">
                @csrf
                <div style="margin-bottom:12px;"><label>Name *</label><input type="text" name="name" class="glass-input" required style="width:100;"></div>
                <div style="margin-bottom:12px;"><label>Description</label><input type="text" name="description" class="glass-input" style="width:100;"></div>
                <div style="text-align:right;"><button type="submit" class="btn">Save</button></div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="editCategoryModal" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);z-index:2000;">
        <div class="modal-content" style="background:#ffffff;padding:24px;border-radius:12px;max-width:480px;position:relative;">
            <span class="modal-close" style="position:absolute;top:12px;right:12px;cursor:pointer;font-size:22px;" onclick="closeEditCategoryModal()">&times;</span>
            <h3 style="margin-top:0;">Edit Category</h3>
            <form id="editCategoryForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editCategoryId" name="category_id">
                <div style="margin-bottom:12px;"><label>Name *</label><input type="text" id="editCategoryName" name="name" class="glass-input" required style="width:100;"></div>
                <div style="margin-bottom:12px;"><label>Description</label><input type="text" id="editCategoryDescription" name="description" class="glass-input" style="width:100;"></div>
                <div style="text-align:right;"><button type="button" class="btn" onclick="submitEditCategory()">Update</button></div>
            </form>
        </div>
    </div>

    <script>
        function openCategoryModal() { document.getElementById('categoryModal').style.display = 'flex'; }
        function closeCategoryModal() { document.getElementById('categoryModal').style.display = 'none'; }

        function openEditCategoryModal(id) {
            fetch('{{ route('management.categories.show', 'ID') }}'.replace('ID', id), { headers: { 'Accept': 'application/json' } })
                .then(r => r.json()).then(category => {
                    document.getElementById('editCategoryId').value = category.id;
                    document.getElementById('editCategoryName').value = category.name;
                    document.getElementById('editCategoryDescription').value = category.description || '';
                    document.getElementById('editCategoryModal').style.display = 'flex';
                }).catch(err => alert('Failed to fetch category'));
        }

        function closeEditCategoryModal() { document.getElementById('editCategoryModal').style.display = 'none'; }

        function submitEditCategory() {
            const id = document.getElementById('editCategoryId').value;
            const data = {
                name: document.getElementById('editCategoryName').value,
                description: document.getElementById('editCategoryDescription').value,
            };
            fetch('{{ route('management.categories.update', 'ID') }}'.replace('ID', id), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            }).then(r => r.json()).then(res => {
                if (res.success) { alert(res.message); closeEditCategoryModal(); location.reload(); }
                else alert(res.message || 'Failed to update category');
            }).catch(err => alert('Failed to update category'));
        }

        function deleteCategory(id) {
            if (!confirm('Delete category?')) return;
            fetch('{{ route('management.categories.destroy', 'ID') }}'.replace('ID', id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(res => {
                if (res.success) { alert(res.message); location.reload(); }
                else alert(res.message || 'Failed to delete category');
            }).catch(err => alert('Failed to delete category'));
        }

        document.getElementById('categoryModal').addEventListener('click', function(e){ if (e.target === this) closeCategoryModal(); });
        document.getElementById('editCategoryModal').addEventListener('click', function(e){ if (e.target === this) closeEditCategoryModal(); });
    </script>
@endsection