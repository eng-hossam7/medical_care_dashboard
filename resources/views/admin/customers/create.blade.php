@extends('layouts.admin')

@section('title', 'إضافة عميل جديد')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">العملاء</a></li>
    <li class="breadcrumb-item active">إضافة جديد</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fas fa-plus me-2"></i> إضافة عميل جديد</h5>
            </div>
            
            <form action="{{ route('admin.customers.store') }}" method="POST" id="customerForm">
                @csrf
                <div class="card-body-modern">
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
                        <!-- المعلومات الأساسية -->
                        <div class="col-12 mb-4">
                            <h6 class="mb-3"><i class="fas fa-user me-2"></i> المعلومات الأساسية</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">الاسم الكامل <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control-modern" 
                                               value="{{ old('name') }}" required autofocus>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">البريد الإلكتروني <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control-modern" 
                                               value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">كلمة المرور <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="password" class="form-control-modern" 
                                                   id="password" required>
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="togglePassword('password')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="generatePassword()">
                                                <i class="fas fa-key"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted mt-1">يجب أن تكون كلمة المرور 6 أحرف على الأقل</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" class="form-control-modern" 
                                                   id="password_confirmation" required>
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="togglePassword('password_confirmation')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">رقم الهاتف <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control-modern" 
                                               value="{{ old('phone') }}" required>
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
                                               value="{{ old('first_name') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">الاسم الأخير</label>
                                        <input type="text" name="last_name" class="form-control-modern" 
                                               value="{{ old('last_name') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">تاريخ الميلاد</label>
                                        <input type="date" name="date_of_birth" class="form-control-modern" 
                                               value="{{ old('date_of_birth') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">الجنس</label>
                                        <select name="gender" class="form-select-modern">
                                            <option value="">اختر الجنس</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ملاحظات -->
                        <div class="col-12 mb-3">
                            <div class="form-group-modern">
                                <label class="form-label-modern">ملاحظات</label>
                                <textarea name="notes" class="form-control-modern" rows="4">{{ old('notes') }}</textarea>
                                <small class="text-muted mt-1">يمكنك إضافة أي ملاحظات خاصة بالعميل</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer-modern">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary-modern btn-modern">
                            <i class="fas fa-times me-2"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary-modern btn-modern" id="submitBtn">
                            <i class="fas fa-save me-2"></i> حفظ العميل
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
    // توليد كلمة مرور عشوائية
    function generatePassword() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        let password = '';
        for (let i = 0; i < 12; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        document.getElementById('password').value = password;
        document.getElementById('password_confirmation').value = password;
    }
    
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
        const submitBtn = document.getElementById('submitBtn');
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        // التحقق من تطابق كلمات المرور
        if (password !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'كلمات المرور غير متطابقة'
            });
            return;
        }
        
        // التحقق من طول كلمة المرور
        if (password.length < 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
            });
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';
    });
</script>
@endpush