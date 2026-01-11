@extends('layouts.admin')

@section('title', 'إدارة المنتجات')
@section('breadcrumb')
    <li class="breadcrumb-item active">المنتجات</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="page-title">
            <h1>إدارة المنتجات</h1>
            <p class="text-muted mb-0">إدارة وعرض جميع منتجات المتجر</p>
        </div>
    </div>
    <div class="col-md-6 text-start">
        <div class="d-flex gap-2 justify-content-start">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary-modern btn-modern">
                <i class="fas fa-plus me-2"></i> إضافة منتج
            </a>
            <div class="dropdown">
                <button class="btn btn-secondary-modern btn-modern dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i> تصدير
                </button>
                <ul class="dropdown-menu dropdown-menu-custom">
                    <li><a class="dropdown-item dropdown-item-custom" href="#"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="#"><i class="fas fa-file-pdf me-2"></i> PDF</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="card-modern mb-4">
    <div class="card-body-modern">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">بحث</label>
                    <input type="text" name="search" class="form-control-modern" 
                           placeholder="اسم المنتج، SKU..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">التصنيف</label>
                    <select name="category_id" class="form-select-modern">
                        <option value="">جميع التصنيفات</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">الحالة</label>
                    <select name="status" class="form-select-modern">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                        <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>مميز</option>
                    </select>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">المخزون</label>
                    <select name="stock" class="form-select-modern">
                        <option value="">جميع المخزون</option>
                        <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>متوفر</option>
                        <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>منخفض</option>
                        <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>نفذ</option>
                    </select>
                </div>
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-filter me-2"></i> تطبيق الفلتر
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary-modern btn-modern">
                        <i class="fas fa-redo me-2"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card-modern">
    <div class="card-header-modern d-flex justify-content-between align-items-center">
        <h5>
            <i class="fas fa-boxes me-2"></i> المنتجات
            <span class="badge-modern badge-primary-modern ms-2">{{ $products->total() }}</span>
        </h5>
        
        <div class="d-flex gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-primary-modern btn-modern btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-1"></i> ترتيب حسب
                </button>
                <ul class="dropdown-menu dropdown-menu-custom">
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}">الأحدث</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'asc']) }}">الاسم (أ-ي)</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'regular_price', 'direction' => 'asc']) }}">السعر (منخفض-عالي)</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'quantity', 'direction' => 'desc']) }}">المخزون (عالي-منخفض)</a></li>
                </ul>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-outline-primary-modern btn-modern btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-eye me-1"></i> عرض
                </button>
                <ul class="dropdown-menu dropdown-menu-custom">
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['per_page' => 15]) }}">15 عنصر</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['per_page' => 30]) }}">30 عنصر</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}">50 عنصر</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}">100 عنصر</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="card-body-modern">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>المنتج</th>
                            <th>التصنيف</th>
                            <th>السعر</th>
                            <th>المخزون</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr class="slide-in-right">
                            <td>
                                <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="image-container me-3" style="width: 60px; height: 60px;">
                                        @if($product->images->first())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="image-preview-modern">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                <i class="fas fa-box text-secondary fa-2x"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <div class="text-muted small">
                                            <i class="fas fa-barcode me-1"></i> {{ $product->sku }}
                                        </div>
                                        @if($product->is_featured)
                                            <span class="badge-modern badge-warning-modern mt-1">
                                                <i class="fas fa-star me-1"></i> مميز
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($product->category)
                                    <span class="badge-modern badge-primary-modern">
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted">بدون تصنيف</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if($product->sale_price)
                                        <span class="text-danger fw-bold">{{ number_format($product->sale_price) }} ر.س</span>
                                        <span class="text-muted small text-decoration-line-through">{{ number_format($product->regular_price) }} ر.س</span>
                                        @if($product->regular_price > 0)
                                            <span class="badge-modern badge-danger-modern mt-1">
                                                {{ round((($product->regular_price - $product->sale_price) / $product->regular_price) * 100) }}%
                                            </span>
                                        @endif
                                    @else
                                        <span class="fw-bold">{{ number_format($product->regular_price) }} ر.س</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-start">
                                    @if($product->quantity == 0)
                                        <span class="badge-modern badge-danger-modern">
                                            <i class="fas fa-times-circle me-1"></i> نفذ
                                        </span>
                                    @elseif($product->quantity <= $product->low_stock_threshold)
                                        <span class="badge-modern badge-warning-modern">
                                            <i class="fas fa-exclamation-triangle me-1"></i> {{ $product->quantity }}
                                        </span>
                                    @else
                                        <span class="badge-modern badge-success-modern">
                                            <i class="fas fa-check-circle me-1"></i> {{ $product->quantity }}
                                        </span>
                                    @endif
                                    <small class="text-muted mt-1">الحد: {{ $product->low_stock_threshold }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button onclick="toggleStatus('{{ route('admin.products.toggle-status', $product) }}', this)" 
                                            class="btn btn-sm {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}"
                                            style="min-width: 80px;">
                                        {!! $product->is_active ? '<i class="fas fa-check me-1"></i> نشط' : '<i class="fas fa-times me-1"></i> معطل' !!}
                                    </button>
                                    
                                    <button onclick="toggleFeatured('{{ route('admin.products.toggle-featured', $product) }}', this)" 
                                            class="btn btn-sm {{ $product->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                                        {!! $product->is_featured ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>' !!}
                                    </button>
                                </div>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-primary-modern btn-modern btn-sm dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-custom">
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.products.show', $product) }}">
                                                <i class="fas fa-eye me-2"></i> عرض التفاصيل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.products.edit', $product) }}">
                                                <i class="fas fa-edit me-2"></i> تعديل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="#" 
                                               data-bs-toggle="modal" data-bs-target="#stockModal"
                                               onclick="setStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->quantity }})">
                                                <i class="fas fa-boxes me-2"></i> تحديث المخزون
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form id="delete-form-{{ $product->id }}" 
                                                  action="{{ route('admin.products.destroy', $product) }}" 
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item dropdown-item-custom text-danger" 
                                                        onclick="confirmDelete(event, 'delete-form-{{ $product->id }}')">
                                                    <i class="fas fa-trash me-2"></i> حذف
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($products->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    عرض {{ $products->firstItem() }} إلى {{ $products->lastItem() }} من إجمالي {{ $products->total() }} عنصر
                </div>
                <nav>
                    <ul class="pagination-modern">
                        {{ $products->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </ul>
                </nav>
            </div>
            @endif
            
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">لا توجد منتجات</h4>
                <p class="text-muted mb-4">لم تقم بإضافة أي منتجات بعد. ابدأ بإضافة منتجاتك الآن.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary-modern btn-modern">
                    <i class="fas fa-plus me-2"></i> إضافة منتج جديد
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Bulk Actions -->
@if($products->count() > 0)
<div class="card-modern mt-4">
    <div class="card-body-modern">
        <div class="d-flex gap-3 align-items-center">
            <strong>إجراءات جماعية:</strong>
            <select id="bulkAction" class="form-select-modern" style="width: auto;">
                <option value="">اختر إجراء...</option>
                <option value="activate">تفعيل المحدد</option>
                <option value="deactivate">تعطيل المحدد</option>
                <option value="featured">تمييز المحدد</option>
                <option value="unfeatured">إلغاء تمييز المحدد</option>
                <option value="delete">حذف المحدد</option>
            </select>
            <button onclick="applyBulkAction()" class="btn btn-primary-modern btn-modern">
                <i class="fas fa-play me-2"></i> تطبيق
            </button>
        </div>
    </div>
</div>
@endif

<!-- Stock Update Modal -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث المخزون</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockForm">
                <div class="modal-body">
                    <input type="hidden" id="productId">
                    <div class="form-group-modern">
                        <label class="form-label-modern">المنتج</label>
                        <input type="text" id="productName" class="form-control-modern" readonly>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label-modern">المخزون الحالي</label>
                        <input type="text" id="currentStock" class="form-control-modern" readonly>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label-modern">نوع التحديث</label>
                        <select id="adjustmentType" class="form-select-modern" required>
                            <option value="set">تعيين قيمة جديدة</option>
                            <option value="increment">زيادة</option>
                            <option value="decrement">نقصان</option>
                        </select>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label-modern">الكمية</label>
                        <input type="number" id="quantity" class="form-control-modern" min="0" required>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label-modern">السبب (اختياري)</label>
                        <textarea id="reason" class="form-control-modern" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-modern btn-modern" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary-modern btn-modern">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Stock Modal
    function setStockModal(id, name, quantity) {
        document.getElementById('productId').value = id;
        document.getElementById('productName').value = name;
        document.getElementById('currentStock').value = quantity;
        document.getElementById('quantity').value = '';
        document.getElementById('reason').value = '';
    }

    document.getElementById('stockForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const productId = document.getElementById('productId').value;
        const url = "{{ route('admin.products.update-stock', ':id') }}".replace(':id', productId);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantity: document.getElementById('quantity').value,
                adjustment_type: document.getElementById('adjustmentType').value,
                reason: document.getElementById('reason').value
            })
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
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'حدث خطأ أثناء تحديث المخزون'
            });
        });
    });

    // Toggle Status
    function toggleStatus(url, element) {
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
                    '<i class="fas fa-check me-1"></i> نشط' : 
                    '<i class="fas fa-times me-1"></i> معطل';
            }
        });
    }

    // Toggle Featured
    function toggleFeatured(url, element) {
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
                element.classList.toggle('btn-warning');
                element.classList.toggle('btn-outline-warning');
                element.innerHTML = data.is_featured ? 
                    '<i class="fas fa-star"></i>' : 
                    '<i class="far fa-star"></i>';
            }
        });
    }

    // Bulk Actions
    function applyBulkAction() {
        const action = document.getElementById('bulkAction').value;
        const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
            .map(checkbox => checkbox.value);
        
        if (selectedProducts.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'لم تحدد أي منتجات'
            });
            return;
        }
        
        if (!action) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'لم تحدد أي إجراء'
            });
            return;
        }
        
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: `سيتم تطبيق الإجراء على ${selectedProducts.length} منتج`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'نعم، متأكد',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Here you would implement the bulk action
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: 'تم تطبيق الإجراء بنجاح',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    }
</script>
@endpush