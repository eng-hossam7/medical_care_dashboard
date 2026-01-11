@extends('layouts.admin')

@section('title', 'إضافة تصنيف جديد')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">التصنيفات</a></li>
    <li class="breadcrumb-item active">إضافة جديد</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i> إضافة تصنيف جديد</h5>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="categoryForm">
                @csrf
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle me-2"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name') }}" required autofocus>
                            <div class="form-text">الاسم الذي سيظهر للمستخدمين</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">التصنيف الرئيسي</label>
                            <select name="parent_id" class="form-select">
                                <option value="">تصنيف رئيسي (بدون أب)</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">اختر تصنيفاً رئيسياً إذا كان هذا تصنيفاً فرعياً</div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            <div class="form-text">وصف مختصر للتصنيف</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">صورة التصنيف</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text">الصيغ المدعومة: JPG, PNG, GIF, WebP. الحد الأقصى: 2MB</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">معاينة الصورة</label>
                            <div id="imagePreview" class="border rounded p-2 text-center bg-light" style="height: 100px;">
                                <i class="fas fa-image fa-2x text-muted"></i>
                                <p class="small text-muted mb-0">لم يتم اختيار صورة</p>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-check-circle me-1"></i> التصنيف نشط
                                </label>
                            </div>
                            <div class="form-text">سيظهر التصنيف للمستخدمين إذا كان نشطاً</div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-times me-2"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary-custom btn-custom" id="submitBtn">
                            <i class="fas fa-save me-2"></i> حفظ التصنيف
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // معاينة الصورة
    document.querySelector('input[name="image"]').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        
        if (file) {
            // التحقق من حجم الصورة
            if (file.size > 2 * 1024 * 1024) { // 2MB
                alert('حجم الصورة كبير جداً. الحد الأقصى هو 2MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="معاينة الصورة" 
                         class="img-fluid rounded" 
                         style="max-height: 90px; object-fit: cover;">
                `;
            }
            
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = `
                <i class="fas fa-image fa-2x text-muted"></i>
                <p class="small text-muted mb-0">لم يتم اختيار صورة</p>
            `;
        }
    });
    
    // التحقق من النموذج قبل الإرسال
    document.getElementById('categoryForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';
    });
</script>
@endpush