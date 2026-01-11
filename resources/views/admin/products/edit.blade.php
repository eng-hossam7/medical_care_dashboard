@extends('layouts.admin')

@section('title', 'تعديل المنتج: ' . $product->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">المنتجات</a></li>
    <li class="breadcrumb-item active">تعديل المنتج</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i> تعديل المنتج
                        <small class="text-muted">#{{ $product->sku }}</small>
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i> عرض
                        </a>
                    </div>
                </div>
            </div>
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- المعلومات الأساسية -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> المعلومات الأساسية</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                                            @error('name')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}" required>
                                            @error('sku')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">الرابط (Slug)</label>
                                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $product->slug) }}">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">التصنيف <span class="text-danger">*</span></label>
                                            <select name="category_id" class="form-select" required>
                                                <option value="">اختر تصنيف</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label class="form-label">الوصف المختصر</label>
                                            <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                                        </div>
                                        
                                        <div class="col-12 mb-3">
                                            <label class="form-label">الوصف الكامل</label>
                                            <textarea name="description" class="form-control" rows="5" id="description">{{ old('description', $product->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الصور الحالية -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-images me-2"></i> الصور الحالية</h6>
                                </div>
                                <div class="card-body">
                                    @if($product->images->count() > 0)
                                        <div class="row">
                                            @foreach($product->images as $image)
                                                <div class="col-md-3 col-6 mb-3">
                                                    <div class="image-preview">
                                                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="{{ $image->alt_text }}" class="img-fluid rounded">
                                                        <div class="image-actions">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="primary_image" 
                                                                       value="{{ $image->id }}" id="primary_{{ $image->id }}" 
                                                                       {{ $image->is_primary ? 'checked' : '' }}>
                                                                <label class="form-check-label text-white" for="primary_{{ $image->id }}">
                                                                    أساسية
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="delete_images[]" 
                                                                       value="{{ $image->id }}" id="delete_{{ $image->id }}">
                                                                <label class="form-check-label text-white" for="delete_{{ $image->id }}">
                                                                    حذف
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="existing_images[{{ $image->id }}][order]" value="{{ $loop->index }}">
                                                        <input type="text" name="existing_images[{{ $image->id }}][alt_text]" 
                                                               class="form-control form-control-sm mt-2" 
                                                               placeholder="نص بديل" value="{{ $image->alt_text }}">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-image fa-3x mb-3"></i>
                                            <p>لا توجد صور للمنتج</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- إضافة صور جديدة -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i> إضافة صور جديدة</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">اختر الصور</label>
                                        <input type="file" name="new_images[]" class="form-control" multiple accept="image/*">
                                        <div class="form-text">يمكنك اختيار أكثر من صورة</div>
                                    </div>
                                    
                                    <div id="newImagesPreview" class="row mt-3">
                                        <!-- ستظهر معاينة الصور الجديدة هنا -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الجانب الأيمن -->
                        <div class="col-lg-4">
                            <!-- الحالة -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i> الحالة</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">المنتج نشط</label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" 
                                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">منتج مميز</label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_prescription_required" id="is_prescription_required" 
                                                   {{ old('is_prescription_required', $product->is_prescription_required) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_prescription_required">يحتاج وصفة طبية</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الأسعار والمخزون -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-tags me-2"></i> الأسعار والمخزون</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">السعر العادي <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" name="regular_price" class="form-control" step="0.01" min="0" 
                                                   value="{{ old('regular_price', $product->regular_price) }}" required>
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">سعر البيع</label>
                                        <div class="input-group">
                                            <input type="number" name="sale_price" class="form-control" step="0.01" min="0" 
                                                   value="{{ old('sale_price', $product->sale_price) }}">
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">سعر التكلفة</label>
                                        <div class="input-group">
                                            <input type="number" name="cost_price" class="form-control" step="0.01" min="0" 
                                                   value="{{ old('cost_price', $product->cost_price) }}">
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">الكمية <span class="text-danger">*</span></label>
                                        <input type="number" name="quantity" class="form-control" min="0" 
                                               value="{{ old('quantity', $product->quantity) }}" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">حد الإنذار بالمخزون المنخفض</label>
                                        <input type="number" name="low_stock_threshold" class="form-control" min="1" 
                                               value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- معلومات إضافية -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات إضافية</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">الوزن (كجم)</label>
                                        <div class="input-group">
                                            <input type="number" name="weight" class="form-control" step="0.01" min="0" 
                                                   value="{{ old('weight', $product->weight) }}">
                                            <span class="input-group-text">كجم</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">الأبعاد</label>
                                        <input type="text" name="dimensions" class="form-control" 
                                               value="{{ old('dimensions', $product->dimensions) }}" placeholder="طول × عرض × ارتفاع">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">الشركة المصنعة</label>
                                        <input type="text" name="manufacturer" class="form-control" 
                                               value="{{ old('manufacturer', $product->manufacturer) }}">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">فئة الضريبة</label>
                                        <select name="tax_class" class="form-select">
                                            <option value="standard" {{ old('tax_class', $product->tax_class) == 'standard' ? 'selected' : '' }}>قياسي</option>
                                            <option value="reduced" {{ old('tax_class', $product->tax_class) == 'reduced' ? 'selected' : '' }}>مخفض</option>
                                            <option value="zero" {{ old('tax_class', $product->tax_class) == 'zero' ? 'selected' : '' }}>صفر</option>
                                            <option value="exempt" {{ old('tax_class', $product->tax_class) == 'exempt' ? 'selected' : '' }}>معفى</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- العروض -->
                            @if($product->offers->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-percentage me-2"></i> العروض الحالية</h6>
                                </div>
                                <div class="card-body">
                                    @foreach($product->offers as $offer)
                                        <div class="alert alert-{{ $offer->is_active ? 'success' : 'secondary' }} mb-2">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>
                                                        {{ $offer->offer_type == 'percentage' ? $offer->discount_value . '%' : $offer->discount_value . ' ر.س' }}
                                                    </strong>
                                                    <div class="small">
                                                        {{ $offer->start_date }} إلى {{ $offer->end_date }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-sm {{ $offer->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                            onclick="toggleOfferStatus({{ $offer->id }}, this)">
                                                        {{ $offer->is_active ? 'نشط' : 'معطل' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-times me-2"></i> إلغاء
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-outline-primary btn-custom">
                                <i class="fas fa-eye me-2"></i> معاينة
                            </a>
                            <button type="submit" class="btn btn-primary-custom btn-custom">
                                <i class="fas fa-save me-2"></i> حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    // تهيئة CKEditor
    CKEDITOR.replace('description', {
        language: 'ar',
        height: 200,
        toolbar: [
            ['Bold', 'Italic', 'Underline', 'Strike'],
            ['NumberedList', 'BulletedList'],
            ['Link', 'Unlink'],
            ['RemoveFormat']
        ]
    });
    
    // معاينة الصور الجديدة
    document.querySelector('input[name="new_images[]"]').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('newImagesPreview');
        previewContainer.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-6 mb-3';
                col.innerHTML = `
                    <div class="image-preview">
                        <img src="${e.target.result}" alt="Preview" class="img-fluid rounded">
                        <div class="image-actions">
                            <input type="text" name="new_alt_text[]" class="form-control form-control-sm" placeholder="نص بديل">
                        </div>
                    </div>
                `;
                previewContainer.appendChild(col);
            }
            
            reader.readAsDataURL(file);
        });
    });
    
    // تحديث حالة العرض
    function toggleOfferStatus(offerId, element) {
        const url = "{{ route('admin.products.offers.toggle-status', ':id') }}".replace(':id', offerId);
        
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
                element.textContent = data.is_active ? 'نشط' : 'معطل';
                
                // تحديث لون الـ Alert
                const alert = element.closest('.alert');
                alert.classList.toggle('alert-success');
                alert.classList.toggle('alert-secondary');
            }
        });
    }
</script>
@endpush