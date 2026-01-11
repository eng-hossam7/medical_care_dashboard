@extends('layouts.admin')

@section('title', 'تعديل العنوان - ' . ($customer->full_name ?? $customer->user->name))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">العملاء</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer) }}">{{ Str::limit($customer->full_name ?? $customer->user->name, 20) }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.addresses.index', $customer) }}">عناوين العميل</a></li>
    <li class="breadcrumb-item active">تعديل العنوان</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header-modern">
                <h4 class="mb-0"><i class="fas fa-edit me-2"></i> تعديل العنوان</h4>
            </div>
            <div class="card-body-modern">
                <form action="{{ route('admin.customers.addresses.update', [$customer, $address]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="address_type" class="form-label">نوع العنوان <span class="text-danger">*</span></label>
                            <select name="address_type" id="address_type" class="form-select" required>
                                <option value="">اختر نوع العنوان</option>
                                <option value="shipping" {{ old('address_type', $address->address_type) == 'shipping' ? 'selected' : '' }}>عنوان الشحن</option>
                                <option value="billing" {{ old('address_type', $address->address_type) == 'billing' ? 'selected' : '' }}>عنوان الفاتورة</option>
                            </select>
                            @error('address_type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" name="is_default" id="is_default" value="1" 
                                    {{ old('is_default', $address->is_default) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">تعيين كعنوان افتراضي</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                value="{{ old('first_name', $address->first_name) }}" required>
                            @error('first_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                value="{{ old('last_name', $address->last_name) }}" required>
                            @error('last_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                            value="{{ old('phone', $address->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="address_line_1" class="form-label">العنوان (الخط الأول) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address_line_1" name="address_line_1" 
                            value="{{ old('address_line_1', $address->address_line_1) }}" required>
                        @error('address_line_1')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="address_line_2" class="form-label">العنوان (الخط الثاني)</label>
                        <input type="text" class="form-control" id="address_line_2" name="address_line_2" 
                            value="{{ old('address_line_2', $address->address_line_2) }}">
                        @error('address_line_2')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="city" class="form-label">المدينة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="city" name="city" 
                                value="{{ old('city', $address->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="state" class="form-label">المنطقة/المحافظة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="state" name="state" 
                                value="{{ old('state', $address->state) }}" required>
                            @error('state')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">الدولة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="country" name="country" 
                                value="{{ old('country', $address->country) }}" required>
                            @error('country')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="postal_code" class="form-label">الرمز البريدي <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                value="{{ old('postal_code', $address->postal_code) }}" required>
                            @error('postal_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.customers.addresses.index', $customer) }}" class="btn btn-outline-secondary-modern">
                            إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary-modern">
                            <i class="fas fa-save me-2"></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات العنوان</h5>
            </div>
            <div class="card-body-modern">
                <div class="mb-3">
                    <strong>نوع العنوان:</strong>
                    <p class="mb-0">{{ $address->address_type_arabic }}</p>
                </div>
                <div class="mb-3">
                    <strong>الحالة:</strong>
                    <p class="mb-0">
                        @if($address->is_default)
                            <span class="badge-modern badge-success-modern">افتراضي</span>
                        @else
                            <span class="badge-modern badge-secondary-modern">عادي</span>
                        @endif
                    </p>
                </div>
                <div class="mb-3">
                    <strong>تاريخ الإضافة:</strong>
                    <p class="mb-0">{{ $address->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <div class="mb-3">
                    <strong>تاريخ التعديل الأخير:</strong>
                    <p class="mb-0">{{ $address->updated_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
        
        <div class="card-modern mt-4">
            <div class="card-header-modern">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> ملاحظات هامة</h5>
            </div>
            <div class="card-body-modern">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fas fa-exclamation-circle text-warning me-2"></i> تغيير العنوان الافتراضي سيؤثر على الطلبات الجديدة</li>
                    <li class="mb-2"><i class="fas fa-exclamation-circle text-warning me-2"></i> تأكد من صحة المعلومات قبل الحفظ</li>
                    <li><i class="fas fa-exclamation-circle text-warning me-2"></i> التعديلات لن تؤثر على الطلبات السابقة</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection