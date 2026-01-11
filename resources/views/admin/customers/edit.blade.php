@extends('layouts.admin')

@section('title', 'تعديل العميل: ' . ($customer->full_name ?? $customer->user->name))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">العملاء</a></li>
    <li class="breadcrumb-item active">تعديل العميل</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card-modern">
            <div class="card-header-modern">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i> تعديل العميل
                        <small class="text-muted">{{ $customer->full_name ?? $customer->user->name }}</small>
                    </h5>
                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> عرض
                    </a>
                </div>
            </div>
            
            <form action="{{ route('admin.customers.update', $customer) }}" method="POST" id="customerForm">
                @csrf
                @method('PUT')
                <div class="card-body-modern">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="row">
                        <!-- المعلومات الأساسية -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3"><i class="fas fa-user me-2"></i> المعلومات الأساسية</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">الاسم الكامل <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control-modern" 
                                               value="{{ old('name', $customer->user->name) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">البريد الإلكتروني <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control-modern" 
                                               value="{{ old('email', $customer->user->email) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">كلمة المرور الجديدة</label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control-modern" 
                                                   id="password">
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="togglePassword('password')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted mt-1">اتركه فارغاً إذا كنت لا تريد تغيير كلمة المرور</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">رقم الهاتف <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control-modern" 
                                               value="{{ old('phone', $customer->user->phone) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- المعلومات الشخصية -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3"><i class="fas fa-id-card me-2"></i> المعلومات الشخصية</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">الاسم الأول</label>
                                        <input type="text" name="first_name" class="form-control-modern" 
                                               value="{{ old('first_name', $customer->first_name) }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">الاسم الأخير</label>
                                        <input type="text" name="last_name" class="form-control-modern" 
                                               value="{{ old('last_name', $customer->last_name) }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">تاريخ الميلاد</label>
                                        <input type="date" name="date_of_birth" class="form-control-modern" 
                                               value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">الجنس</label>
                                        <select name="gender" class="form-select-modern">
                                            <option value="">اختر الجنس</option>
                                            <option value="male" {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                            <option value="female" {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ملاحظات -->
                        <div class="col-12 mb-3">
                            <div class="form-group-modern">
                                <label class="form-label-modern">ملاحظات</label>
                                <textarea name="notes" class="form-control-modern" rows="4">{{ old('notes', $customer->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer-modern">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary-modern btn-modern">
                            <i class="fas fa-times me-2"></i> إلغاء
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-primary-modern btn-modern">
                                <i class="fas fa-eye me-2"></i> معاينة
                            </a>
                            <button type="submit" class="btn btn-primary-modern btn-modern">
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
<script>
    // إظهار/إخفاء كلمة المرور
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.parentElement.querySelector('button');
        
        if (field.type === 'password') {
            field.type = 'text';
            button.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            field.type = 'password';
            button.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }
    
    // التحقق من صحة النموذج
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        
        // إذا تم إدخال كلمة مرور جديدة، التحقق من طولها
        if (password && password.length < 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
            });
            return;
        }
    });
</script>
@endpush