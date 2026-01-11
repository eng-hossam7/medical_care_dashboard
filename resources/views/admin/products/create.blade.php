@extends('layouts.admin')

@section('title', 'إضافة منتج جديد')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">المنتجات</a></li>
    <li class="breadcrumb-item active">إضافة جديد</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fas fa-plus me-2"></i> إضافة منتج جديد</h5>
            </div>
            
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                <div class="card-body-modern">
                    <div class="row">
                        <!-- المعلومات الأساسية -->
                        <div class="col-lg-8">
                            <div class="card-modern mb-4">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> المعلومات الأساسية</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">اسم المنتج <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control-modern @error('name') is-invalid @enderror" 
                                                       value="{{ old('name') }}" required autofocus>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">كود المنتج (SKU) <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text" name="sku" class="form-control-modern @error('sku') is-invalid @enderror" 
                                                           value="{{ old('sku') }}" required>
                                                    <button type="button" class="btn btn-secondary-modern btn-modern" onclick="generateSKU()">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </div>
                                                @error('sku')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted mt-1">رمز فريد للمنتج في المخزون</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">التصنيف <span class="text-danger">*</span></label>
                                                <select name="category_id" class="form-select-modern @error('category_id') is-invalid @enderror" required>
                                                    <option value="">اختر تصنيف</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">الكمية <span class="text-danger">*</span></label>
                                                <input type="number" name="quantity" class="form-control-modern @error('quantity') is-invalid @enderror" 
                                                       value="{{ old('quantity', 0) }}" min="0" required>
                                                @error('quantity')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-12 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">الوصف المختصر</label>
                                                <textarea name="short_description" class="form-control-modern" rows="2">{{ old('short_description') }}</textarea>
                                                <small class="text-muted mt-1">وصف مختصر يظهر في قائمة المنتجات</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">الوصف الكامل</label>
                                                <textarea name="description" class="form-control-modern" rows="4" id="descriptionEditor">{{ old('description') }}</textarea>
                                                <small class="text-muted mt-1">وصف تفصيلي للمنتج</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- الأسعار -->
                            <div class="card-modern mb-4">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-tags me-2"></i> الأسعار</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="row">
                                        <div class="col-md-4 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">السعر العادي <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" name="regular_price" class="form-control-modern @error('regular_price') is-invalid @enderror" 
                                                           step="0.01" min="0" value="{{ old('regular_price', 0) }}" required>
                                                    <span class="input-group-text">ر.س</span>
                                                </div>
                                                @error('regular_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">سعر البيع</label>
                                                <div class="input-group">
                                                    <input type="number" name="sale_price" class="form-control-modern @error('sale_price') is-invalid @enderror" 
                                                           step="0.01" min="0" value="{{ old('sale_price') }}">
                                                    <span class="input-group-text">ر.س</span>
                                                </div>
                                                @error('sale_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted mt-1">اتركه فارغاً إذا لم يكن هناك عرض</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">سعر التكلفة</label>
                                                <div class="input-group">
                                                    <input type="number" name="cost_price" class="form-control-modern @error('cost_price') is-invalid @enderror" 
                                                           step="0.01" min="0" value="{{ old('cost_price') }}">
                                                    <span class="input-group-text">ر.س</span>
                                                </div>
                                                @error('cost_price')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">حد الإنذار بالمخزون المنخفض</label>
                                                <input type="number" name="low_stock_threshold" class="form-control-modern @error('low_stock_threshold') is-invalid @enderror" 
                                                       min="1" value="{{ old('low_stock_threshold', 5) }}">
                                                @error('low_stock_threshold')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted mt-1">سيتم تنبيهك عندما يصل المخزون لهذا الحد</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6 mb-4">
                                            <div class="form-group-modern">
                                                <label class="form-label-modern">الوزن (كجم)</label>
                                                <div class="input-group">
                                                    <input type="number" name="weight" class="form-control-modern @error('weight') is-invalid @enderror" 
                                                           step="0.01" min="0" value="{{ old('weight') }}">
                                                    <span class="input-group-text">كجم</span>
                                                </div>
                                                @error('weight')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الجانب الأيمن -->
                        <div class="col-lg-4">
                            <!-- الإعدادات -->
                            <div class="card-modern mb-4">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-cog me-2"></i> إعدادات المنتج</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_active" 
                                                   id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                المنتج نشط
                                            </label>
                                        </div>
                                        <small class="text-muted">سيظهر المنتج في المتجر إذا كان نشطاً</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_featured" 
                                                   id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                منتج مميز
                                            </label>
                                        </div>
                                        <small class="text-muted">سيظهر في القسم المميز</small>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="is_prescription_required" 
                                                   id="is_prescription_required" value="1" {{ old('is_prescription_required') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_prescription_required">
                                                يحتاج وصفة طبية
                                            </label>
                                        </div>
                                        <small class="text-muted">يتطلب وصفة طبية للشراء</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- صور المنتج -->
                            <div class="card-modern mb-4">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-images me-2"></i> صور المنتج</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="form-group-modern mb-4">
                                        <label class="form-label-modern">رفع الصور</label>
                                        <div class="file-upload-modern">
                                            <input type="file" name="images[]" class="form-control-modern" 
                                                   accept="image/*" multiple id="imageUpload">
                                            <div class="file-upload-preview mt-3" id="imagePreviewContainer">
                                                <!-- ستظهر معاينة الصور هنا -->
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">يمكنك رفع أكثر من صورة، أول صورة ستكون الأساسية</small>
                                    </div>
                                    
                                    <div class="alert alert-info border-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span>الصيغ المدعومة: JPG, PNG, GIF, WebP</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- معلومات إضافية -->
                            <div class="card-modern mb-4">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات إضافية</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="form-group-modern mb-4">
                                        <label class="form-label-modern">الأبعاد</label>
                                        <input type="text" name="dimensions" class="form-control-modern @error('dimensions') is-invalid @enderror" 
                                               value="{{ old('dimensions') }}" placeholder="طول × عرض × ارتفاع">
                                        @error('dimensions')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group-modern mb-4">
                                        <label class="form-label-modern">الشركة المصنعة</label>
                                        <input type="text" name="manufacturer" class="form-control-modern @error('manufacturer') is-invalid @enderror" 
                                               value="{{ old('manufacturer') }}">
                                        @error('manufacturer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">فئة الضريبة</label>
                                        <select name="tax_class" class="form-select-modern @error('tax_class') is-invalid @enderror">
                                            <option value="standard" {{ old('tax_class') == 'standard' ? 'selected' : '' }}>قياسي</option>
                                            <option value="reduced" {{ old('tax_class') == 'reduced' ? 'selected' : '' }}>مخفض</option>
                                            <option value="zero" {{ old('tax_class') == 'zero' ? 'selected' : '' }}>صفر</option>
                                            <option value="exempt" {{ old('tax_class') == 'exempt' ? 'selected' : '' }}>معفى</option>
                                        </select>
                                        @error('tax_class')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer-modern">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary-modern btn-modern">
                            <i class="fas fa-times me-2"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern" id="submitBtn">
                            <i class="fas fa-save me-2"></i> حفظ المنتج
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .file-upload-modern {
        position: relative;
    }
    
    .file-upload-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
    }
    
    .preview-item {
        position: relative;
        border-radius: var(--radius-sm);
        overflow: hidden;
        height: 120px;
        background: var(--light);
    }
    
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .remove-preview {
        position: absolute;
        top: 5px;
        left: 5px;
        width: 25px;
        height: 25px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
    }
    
    .remove-preview:hover {
        background: var(--danger);
        transform: scale(1.1);
    }
    
    .form-control-modern.is-invalid {
        border-color: var(--danger);
    }
    
    .form-control-modern.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }
    
    .invalid-feedback {
        color: var(--danger);
        font-size: 0.875rem;
        margin-top: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
    // توليد SKU تلقائي
    function generateSKU() {
        const prefix = 'MED';
        const random = Math.random().toString(36).substring(2, 8).toUpperCase();
        const date = new Date().getTime().toString().substring(8, 12);
        const sku = `${prefix}-${random}-${date}`;
        document.querySelector('input[name="sku"]').value = sku;
    }
    
    // معاينة الصور
    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('imagePreviewContainer');
        previewContainer.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                    <button type="button" class="remove-preview" onclick="removeImage(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                previewContainer.appendChild(previewItem);
            }
            
            reader.readAsDataURL(file);
        });
    });
    
    // إزالة صورة من المعاينة
    function removeImage(index) {
        const input = document.getElementById('imageUpload');
        const files = Array.from(input.files);
        files.splice(index, 1);
        
        // تحديث ملفات الـ input
        const dataTransfer = new DataTransfer();
        files.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
        
        // تحديث المعاينة
        const event = new Event('change');
        input.dispatchEvent(event);
    }
    
    // توليد SKU عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.querySelector('input[name="sku"]').value) {
            generateSKU();
        }
    });
    
    // التحقق من النموذج قبل الإرسال
    document.getElementById('productForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';
        
        // التحقق الأساسي
        const name = document.querySelector('input[name="name"]').value;
        const sku = document.querySelector('input[name="sku"]').value;
        const category = document.querySelector('select[name="category_id"]').value;
        const price = document.querySelector('input[name="regular_price"]').value;
        const quantity = document.querySelector('input[name="quantity"]').value;
        
        if (!name || !sku || !category || !price || !quantity) {
            Swal.fire({
                icon: 'warning',
                title: 'حقول مطلوبة',
                text: 'يرجى ملء جميع الحقول المطلوبة (المميزة بـ *)',
                confirmButtonColor: '#1b7158'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-2"></i> حفظ المنتج';
            e.preventDefault();
        }
    });
</script>
@endpush