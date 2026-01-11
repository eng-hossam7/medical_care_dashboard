@extends('layouts.admin')

@section('title', $category->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">التصنيفات</a></li>
    <li class="breadcrumb-item active">تفاصيل التصنيف</li>
@endsection

@section('content')
<div class="row">
    <!-- معلومات التصنيف -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <!-- الصورة والمعلومات الأساسية -->
                    <div class="col-md-4">
                        <div class="text-center">
                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" 
                                     alt="{{ $category->name }}" 
                                     class="img-fluid rounded mb-3" 
                                     style="max-height: 250px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                                     style="height: 250px;">
                                    <i class="fas fa-folder fa-5x text-secondary"></i>
                                </div>
                            @endif
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="btn btn-primary-custom btn-custom">
                                    <i class="fas fa-edit me-2"></i> تعديل
                                </a>
                                
                                <button onclick="toggleCategoryStatus('{{ route('admin.categories.toggle-status', $category) }}', this)" 
                                        class="btn btn-custom {{ $category->is_active ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $category->is_active ? '<i class="fas fa-check me-2"></i> نشط' : '<i class="fas fa-times me-2"></i> معطل' }}
                                </button>
                                
                                <form id="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger-custom btn-custom w-100" 
                                            onclick="confirmDelete(event, 'delete-form')">
                                        <i class="fas fa-trash me-2"></i> حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- التفاصيل -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h2 class="mb-1">{{ $category->name }}</h2>
                                <div class="text-muted">
                                    <span class="me-3">
                                        <i class="fas fa-link me-1"></i> {{ $category->slug }}
                                    </span>
                                    @if($category->parent)
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-level-up-alt me-1"></i> {{ $category->parent->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.products.create', ['category_id' => $category->id]) }}">
                                            <i class="fas fa-plus me-2"></i> إضافة منتج
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}">
                                            <i class="fas fa-plus me-2"></i> إضافة تصنيف فرعي
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.categories.index') }}">
                                            <i class="fas fa-list me-2"></i> جميع التصنيفات
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- حالة التصنيف -->
                        <div class="mb-4">
                            <div class="d-flex gap-2 mb-2">
                                @if($category->is_active)
                                    <span class="badge bg-success badge-custom">
                                        <i class="fas fa-check me-1"></i> نشط
                                    </span>
                                @else
                                    <span class="badge bg-secondary badge-custom">
                                        <i class="fas fa-times me-1"></i> معطل
                                    </span>
                                @endif
                                
                                <span class="badge bg-info badge-custom">
                                    <i class="fas fa-sort-numeric-up me-1"></i> الترتيب: {{ $category->order }}
                                </span>
                                
                                <span class="badge bg-primary badge-custom">
                                    <i class="fas fa-box me-1"></i> {{ $category->products_count }} منتج
                                </span>
                            </div>
                        </div>
                        
                        <!-- الوصف -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-align-left me-2"></i> الوصف</h5>
                            @if($category->description)
                                <div class="border rounded p-3 bg-light">
                                    {{ $category->description }}
                                </div>
                            @else
                                <p class="text-muted text-center py-3">لا يوجد وصف</p>
                            @endif
                        </div>
                        
                        <!-- معلومات إضافية -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-info-circle me-2"></i> معلومات إضافية</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">تاريخ الإنشاء:</span>
                                        <span class="fw-bold">{{ $category->created_at->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">آخر تحديث:</span>
                                        <span class="fw-bold">{{ $category->updated_at->format('Y-m-d') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO -->
                        @if($category->meta_title || $category->meta_description)
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-search me-2"></i> تحسين محركات البحث</h5>
                            <div class="bg-light p-3 rounded">
                                @if($category->meta_title)
                                    <div class="mb-2">
                                        <strong>العنوان:</strong> {{ $category->meta_title }}
                                    </div>
                                @endif
                                @if($category->meta_description)
                                    <div>
                                        <strong>الوصف:</strong> {{ $category->meta_description }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- التصنيفات الفرعية -->
        @if($category->children->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i> التصنيفات الفرعية</h5>
                <span class="badge bg-primary">{{ $category->children->count() }} تصنيف</span>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($category->children as $child)
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    @if($child->image)
                                        <img src="{{ asset('storage/' . $child->image) }}" 
                                             alt="{{ $child->name }}" 
                                             class="rounded me-2" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-folder text-secondary"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $child->name }}</h6>
                                        <small class="text-muted">{{ $child->slug }}</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-info">
                                        {{ $child->products_count }} منتج
                                    </span>
                                    @if($child->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-secondary">معطل</span>
                                    @endif
                                </div>
                                
                                <div class="d-grid gap-2 mt-3">
                                    <a href="{{ route('admin.categories.show', $child) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> عرض
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- الجانب الأيمن -->
    <div class="col-lg-4">
        <!-- إحصائيات -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> إحصائيات</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="display-4 fw-bold text-primary">{{ $category->products_count }}</div>
                    <div class="text-muted">إجمالي المنتجات</div>
                </div>
                
                @if($category->products_count > 0)
                    @php
                        $activeProducts = $category->products()->where('is_active', true)->count();
                        $featuredProducts = $category->products()->where('is_featured', true)->count();
                        $inStockProducts = $category->products()->where('quantity', '>', 0)->count();
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">المنتجات النشطة</span>
                            <span>{{ $activeProducts }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ ($activeProducts / $category->products_count) * 100 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">المنتجات المميزة</span>
                            <span>{{ $featuredProducts }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ ($featuredProducts / $category->products_count) * 100 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">المنتجات المتوفرة</span>
                            <span>{{ $inStockProducts }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ ($inStockProducts / $category->products_count) * 100 }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- منتجات التصنيف -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i> أحدث المنتجات</h5>
                <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" 
                   class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                @if($category->products->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($category->products()->latest()->take(5)->get() as $product)
                            <a href="{{ route('admin.products.show', $product) }}" 
                               class="list-group-item list-group-item-action d-flex align-items-center">
                                @if($product->images->first())
                                    <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                                         alt="{{ $product->name }}" 
                                         class="rounded me-3" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-box text-secondary"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong class="mb-1">{{ Str::limit($product->name, 20) }}</strong>
                                        <small class="text-muted">{{ number_format($product->regular_price) }} ر.س</small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ $product->sku }}</small>
                                        <small class="{{ $product->quantity > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $product->quantity > 0 ? $product->quantity . ' وحدة' : 'نفذ' }}
                                        </small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">لا توجد منتجات في هذا التصنيف</p>
                        <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" 
                           class="btn btn-sm btn-primary-custom mt-3">
                            <i class="fas fa-plus me-2"></i> إضافة منتج
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- إجراءات سريعة -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> إجراءات سريعة</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-edit me-2"></i> تعديل التصنيف
                    </a>
                    <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" 
                       class="btn btn-outline-primary btn-custom">
                        <i class="fas fa-plus me-2"></i> إضافة منتج
                    </a>
                    <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" 
                       class="btn btn-outline-secondary btn-custom">
                        <i class="fas fa-plus-circle me-2"></i> إضافة تصنيف فرعي
                    </a>
                    <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" 
                       class="btn btn-outline-info btn-custom">
                        <i class="fas fa-list me-2"></i> عرض جميع المنتجات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                
                element.classList.toggle('btn-success');
                element.classList.toggle('btn-secondary');
                element.innerHTML = data.is_active ? 
                    '<i class="fas fa-check me-2"></i> نشط' : 
                    '<i class="fas fa-times me-2"></i> معطل';
            }
        });
    }
</script>
@endpush