@extends('layouts.admin')

@section('title', 'تعديل التصنيف: ' . $category->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">التصنيفات</a></li>
    <li class="breadcrumb-item active">تعديل التصنيف</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i> تعديل التصنيف</h5>
                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> عرض
                    </a>
                </div>
            </div>
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم التصنيف <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $category->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">التصنيف الرئيسي</label>
                            <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                <option value="">تصنيف رئيسي</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" 
                                            {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}
                                            {{ $parent->id == $category->id ? 'disabled' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">صورة التصنيف</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if($category->image)
                                <div class="mt-2">
                                    <strong>الصورة الحالية:</strong>
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $category->image) }}" 
                                             alt="{{ $category->name }}" 
                                             class="img-fluid rounded" 
                                             style="max-height: 100px;">
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ترتيب العرض</label>
                            <input type="number" name="order" class="form-control" 
                                   value="{{ old('order', $category->order) }}" min="0">
                            <div class="form-text">يتم العرض حسب الترتيب تصاعدياً</div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                       {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">التصنيف نشط</label>
                            </div>
                        </div>
                        
                        <!-- SEO Section -->
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-header bg-transparent">
                                    <h6 class="mb-0"><i class="fas fa-search me-2"></i> تحسين محركات البحث (SEO)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">عنوان الصفحة (Meta Title)</label>
                                        <input type="text" name="meta_title" class="form-control" 
                                               value="{{ old('meta_title', $category->meta_title) }}" maxlength="60">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">وصف الصفحة (Meta Description)</label>
                                        <textarea name="meta_description" class="form-control" rows="2" 
                                                  maxlength="160">{{ old('meta_description', $category->meta_description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-custom">
                            <i class="fas fa-times me-2"></i> إلغاء
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-outline-primary btn-custom">
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