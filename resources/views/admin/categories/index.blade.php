@extends('layouts.admin')

@section('title', 'إدارة التصنيفات')
@section('breadcrumb')
    <li class="breadcrumb-item active">التصنيفات</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-0">التصنيفات</h2>
    </div>
    <div class="col-md-6 text-start">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary-custom btn-custom">
            <i class="fas fa-plus me-2"></i> إضافة تصنيف
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div id="categoryTree">
                @foreach($categories as $category)
                    @include('admin.categories.partials.category-item', ['category' => $category, 'level' => 0])
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                <h5>لا توجد تصنيفات</h5>
                <p class="text-muted">ابدأ بإضافة تصنيفات جديدة للمنتجات.</p>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary-custom btn-custom">
                    <i class="fas fa-plus me-2"></i> إضافة تصنيف جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .category-item {
        padding: 12px 15px;
        margin-bottom: 5px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .category-item:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
    }
    
    .category-children {
        margin-right: 30px;
        margin-top: 10px;
    }
    
    .category-handle {
        cursor: move;
        color: #6c757d;
    }
    
    .category-badge {
        font-size: 0.8rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    // جعل التصنيفات قابلة للسحب والإفلات
    const categoryTree = document.getElementById('categoryTree');
    
    if (categoryTree) {
        new Sortable(categoryTree, {
            handle: '.category-handle',
            animation: 150,
            onEnd: function(evt) {
                // تحديث الترتيب في قاعدة البيانات
                const categories = [];
                const items = categoryTree.querySelectorAll('.category-item');
                
                items.forEach((item, index) => {
                    const categoryId = item.getAttribute('data-id');
                    const parentId = item.parentElement.classList.contains('category-children') ? 
                        item.parentElement.previousElementSibling.getAttribute('data-id') : null;
                    
                    categories.push({
                        id: categoryId,
                        parent_id: parentId,
                        order: index
                    });
                });
                
                // إرسال تحديث الترتيب
                fetch('{{ route("admin.categories.update-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ categories: categories })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // يمكنك إضافة رسالة نجاح هنا
                        console.log('تم تحديث الترتيب بنجاح');
                    }
                });
            }
        });
    }
    
    // تبديل حالة التصنيف
    function toggleCategoryStatus(url, element) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.classList.toggle('btn-success');
                element.classList.toggle('btn-secondary');
                element.innerHTML = data.is_active ? 
                    '<i class="fas fa-check"></i> نشط' : 
                    '<i class="fas fa-times"></i> معطل';
            }
        });
    }
</script>
@endpush