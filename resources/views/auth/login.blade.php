<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - متجر المستلزمات الطبية</title>
    
    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.0.0-beta1/dist/css/bootstrap-rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
        }
        
        body {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 700;
        }
        
        .login-header p {
            opacity: 0.9;
            margin: 10px 0 0;
        }
        
        .login-body {
            padding: 40px;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 20px;
            border: 2px solid #e8e8e8;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 2px solid #e8e8e8;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #2980b9 100%);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .divider span {
            padding: 0 15px;
            color: #6c757d;
            font-size: 14px;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .login-footer {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #e8e8e8;
        }
        
        .login-footer a {
            color: var(--secondary-color);
            text-decoration: none;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-body {
                padding: 30px 20px;
            }
            
            .login-header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <h2><i class="fas fa-heartbeat"></i> متجر المستلزمات الطبية</h2>
            <p>سجل الدخول للوصول إلى لوحة التحكم</p>
        </div>
        
        <!-- Body -->
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- البريد الإلكتروني -->
                <div class="mb-4">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               placeholder="أدخل بريدك الإلكتروني">
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- كلمة المرور -->
                <div class="mb-4">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required
                               placeholder="أدخل كلمة المرور">
                        <button type="button" class="input-group-text" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <!-- تذكرني -->
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            تذكرني
                        </label>
                    </div>
                </div>
                
                <!-- زر تسجيل الدخول -->
                <div class="mb-4">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i> تسجيل الدخول
                    </button>
                </div>
                
                <!-- روابط إضافية -->
                <div class="text-center">
                    <a href="#" class="text-decoration-none">نسيت كلمة المرور؟</a>
                </div>
            </form>
        </div>
        
        <!-- Footer -->
        <div class="login-footer">
            <p class="mb-0">
                © {{ date('Y') }} متجر المستلزمات الطبية. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // إظهار/إخفاء كلمة المرور
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // إظهار رسائل الخطأ
        document.addEventListener('DOMContentLoaded', function() {
            // إخفاء التنبيهات تلقائياً بعد 5 ثوان
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>