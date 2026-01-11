<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - الطبي | لوحة التحكم</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-rtl@5.0.0-beta1/dist/css/bootstrap-rtl.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-dark: #1d4b82;
            --primary: #1b7158;
            --primary-light: #4a9e8a;
            --secondary: #bcbcbc;
            --secondary-light: #e0e0e0;
            --secondary-dark: #8a8a8a;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #2c3e50;
            --white: #ffffff;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 15px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', 'Segoe UI', 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            color: #2d3748;
            line-height: 1.6;
        }
        
        /* Sidebar - تصميم جانبي جديد */
        .sidebar-wrapper {
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            width: 260px;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: var(--transition);
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .brand-text h2 {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
        }
        
        .brand-text span {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
        }
        
        .nav-container {
            padding: 20px 0;
        }
        
        .nav-item {
            margin: 5px 15px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            color: rgba(255, 255, 255, 0.85);
            border-radius: var(--radius-sm);
            transition: var(--transition);
            text-decoration: none;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transform: translateX(-5px);
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            position: relative;
        }
        
        .nav-link.active::before {
            content: '';
            position: absolute;
            right: -15px;
            top: 0;
            bottom: 0;
            width: 4px;
            background: white;
            border-radius: 2px;
        }
        
        .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-wrapper {
            margin-right: 260px;
            min-height: 100vh;
            transition: var(--transition);
        }
        
        /* Header - تصميم رأس الصفحة الجديد */
        .main-header {
            background: var(--white);
            padding: 0 30px;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .toggle-sidebar {
            background: none;
            border: none;
            color: var(--primary-dark);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 8px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            display: none;
        }
        
        .toggle-sidebar:hover {
            background: var(--secondary-light);
        }
        
        .page-title h1 {
            color: var(--primary-dark);
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
        }
        
        .breadcrumb-custom {
            background: none;
            padding: 0;
            margin: 5px 0 0;
        }
        
        .breadcrumb-custom .breadcrumb-item {
            color: var(--secondary-dark);
            font-size: 0.9rem;
        }
        
        .breadcrumb-custom .breadcrumb-item.active {
            color: var(--primary);
            font-weight: 500;
        }
        
        .breadcrumb-custom .breadcrumb-item + .breadcrumb-item::before {
            color: var(--secondary);
        }
        
        /* User Dropdown */
        .user-dropdown .dropdown-toggle {
            background: var(--light);
            border: none;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            color: var(--primary-dark);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }
        
        .user-dropdown .dropdown-toggle:hover {
            background: var(--secondary-light);
            transform: translateY(-1px);
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .dropdown-menu-custom {
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: 10px;
            min-width: 200px;
            margin-top: 10px !important;
        }
        
        .dropdown-item-custom {
            padding: 10px 15px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            color: var(--dark);
        }
        
        .dropdown-item-custom:hover {
            background: var(--light);
            color: var(--primary);
        }
        
        /* Content Area */
        .content-wrapper {
            padding: 30px;
        }
        
        /* Cards - تصميم بطاقات جديد */
        .card-modern {
            background: var(--white);
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .card-modern:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        
        .card-header-modern {
            background: linear-gradient(135deg, var(--white) 0%, #f8fafc 100%);
            border-bottom: 2px solid var(--secondary-light);
            padding: 20px 25px;
            border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
        }
        
        .card-header-modern h5 {
            color: var(--primary-dark);
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card-body-modern {
            padding: 25px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--white) 0%, #f8fafc 100%);
            border-radius: var(--radius-md);
            padding: 25px;
            border: 1px solid var(--secondary-light);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary) 0%, var(--primary-light) 100%);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .stat-icon.primary { 
            background: rgba(29, 123, 130, 0.1); 
            color: var(--primary);
        }
        
        .stat-icon.success { 
            background: rgba(40, 167, 69, 0.1); 
            color: var(--success);
        }
        
        .stat-icon.warning { 
            background: rgba(255, 193, 7, 0.1); 
            color: var(--warning);
        }
        
        .stat-icon.danger { 
            background: rgba(220, 53, 69, 0.1); 
            color: var(--danger);
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 5px;
            line-height: 1;
        }
        
        .stat-label {
            color: var(--secondary-dark);
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .stat-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .trend-up { color: var(--success); }
        .trend-down { color: var(--danger); }
        
        /* Forms - تحسين حقول الإدخال */
        .form-group-modern {
            margin-bottom: 25px;
        }
        
        .form-label-modern {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .form-control-modern {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--secondary-light);
            border-radius: var(--radius-sm);
            background: var(--white);
            color: var(--dark);
            font-size: 1rem;
            transition: var(--transition);
            font-family: inherit;
        }
        
        .form-control-modern:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(29, 123, 130, 0.1);
        }
        
        .form-control-modern::placeholder {
            color: var(--secondary);
        }
        
        .form-select-modern {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--secondary-light);
            border-radius: var(--radius-sm);
            background: var(--white) url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%231d4b82' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e") no-repeat left 16px center;
            background-size: 16px 12px;
            color: var(--dark);
            font-size: 1rem;
            transition: var(--transition);
            appearance: none;
        }
        
        .form-select-modern:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(29, 123, 130, 0.1);
        }
        
        /* Buttons - تحسين الأزرار */
        .btn-modern {
            padding: 10px 24px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
        }
        
        .btn-modern:active {
            transform: translateY(1px);
        }
        
        .btn-primary-modern {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
        }
        
        .btn-primary-modern:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(27, 113, 88, 0.2);
        }
        
        .btn-secondary-modern {
            background: var(--secondary-light);
            color: var(--dark);
        }
        
        .btn-secondary-modern:hover {
            background: var(--secondary);
            color: var(--dark);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary-modern {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-outline-primary-modern:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        /* Tables - تحسين الجداول */
        .table-modern {
            background: var(--white);
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }
        
        .table-modern thead {
            background: linear-gradient(135deg, var(--white) 0%, #f8fafc 100%);
        }
        
        .table-modern th {
            border: none;
            padding: 18px 20px;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--secondary-light);
        }
        
        .table-modern td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--secondary-light);
            vertical-align: middle;
        }
        
        .table-modern tbody tr {
            transition: var(--transition);
        }
        
        .table-modern tbody tr:hover {
            background: rgba(29, 123, 130, 0.03);
        }
        
        /* Badges - تحسين البادجات */
        .badge-modern {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .badge-primary-modern {
            background: rgba(29, 123, 130, 0.1);
            color: var(--primary);
        }
        
        .badge-success-modern {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .badge-warning-modern {
            background: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .badge-danger-modern {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        /* Images - تحسين عرض الصور */
        .image-container {
            position: relative;
            border-radius: var(--radius-md);
            overflow: hidden;
            background: var(--light);
        }
        
        .image-preview-modern {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: var(--transition);
        }
        
        .image-preview-modern:hover {
            transform: scale(1.05);
        }
        
        .image-actions-modern {
            position: absolute;
            bottom: 0;
            right: 0;
            left: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            padding: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
            opacity: 0;
            transition: var(--transition);
        }
        
        .image-container:hover .image-actions-modern {
            opacity: 1;
        }
        
        /* Charts - تنسيق الرسوم البيانية */
        .chart-container {
            position: relative;
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            height: 100%;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-title {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        /* Alerts - تحسين التنبيهات */
        .alert-modern {
            border: none;
            border-radius: var(--radius-md);
            padding: 18px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: var(--shadow-sm);
        }
        
        .alert-success-modern {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border-right: 4px solid var(--success);
        }
        
        .alert-danger-modern {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border-right: 4px solid var(--danger);
        }
        
        /* Pagination - تحسين الترقيم */
        .pagination-modern {
            display: flex;
            gap: 8px;
        }
        
        .page-link-modern {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--secondary-light);
            border-radius: var(--radius-sm);
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .page-link-modern:hover {
            background: var(--light);
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .page-link-modern.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar-wrapper {
                transform: translateX(100%);
                width: 280px;
            }
            
            .sidebar-wrapper.active {
                transform: translateX(0);
            }
            
            .main-wrapper {
                margin-right: 0;
            }
            
            .toggle-sidebar {
                display: block;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px 15px;
            }
            
            .main-header {
                padding: 0 20px;
            }
            
            .page-title h1 {
                font-size: 1.4rem;
            }
            
            .card-body-modern {
                padding: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .content-wrapper {
                padding: 15px;
            }
            
            .main-header {
                padding: 0 15px;
                height: 60px;
            }
            
            .user-dropdown .dropdown-toggle span {
                display: none;
            }
            
            .table-modern {
                font-size: 0.9rem;
            }
            
            .table-modern th,
            .table-modern td {
                padding: 12px 10px;
            }
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .slide-in-right {
            animation: slideInRight 0.4s ease-out;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--secondary-light);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar-wrapper" id="sidebar">
        <div class="sidebar-header">
            <div class="brand">
                <div class="brand-icon">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div class="brand-text">
                    <h2>متجر</h2>
                    <span>الرعاية الطبية  </span>
                </div>
            </div>
        </div>
        
        <div class="nav-container">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                    <span>لوحة التحكم</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-pills"></i></span>
                    <span>المنتجات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-list"></i></span>
                    <span>التصنيفات</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.orders.index') }}" 
                    class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span>الطلبات</span>
                    <span class="badge-modern badge-warning-modern ms-auto" id="pendingOrdersCount">0</span>
                </a>
        </div>
            
            <div class="nav-item">
                <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                    <span class="nav-icon"><i class="fas fa-users"></i></span>
                    <span>العملاء</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                    <span>التقارير</span>
                </a>
            </div>
            
            <div class="nav-item mt-4">
                <a href="#" class="nav-link">
                    <span class="nav-icon"><i class="fas fa-cog"></i></span>
                    <span>الإعدادات</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-wrapper" id="mainContent">
        <!-- Header -->
        <header class="main-header">
            <div class="header-left">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="page-title">
                    <h1>@yield('title')</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-custom">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i> الرئيسية</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>
            
            <div class="header-right">
                <div class="user-dropdown dropdown">
                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span>{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-custom">
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="#">
                                <i class="fas fa-user me-2"></i> الملف الشخصي
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="#">
                                <i class="fas fa-cog me-2"></i> الإعدادات
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-item-custom w-100 text-start">
                                    <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <main class="content-wrapper">
            <!-- Alerts -->
            @if(session('success'))
                <div class="alert-modern alert-success-modern fade-in-up">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <div class="flex-grow-1">{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert-modern alert-danger-modern fade-in-up">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                    <div class="flex-grow-1">{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert-modern alert-danger-modern fade-in-up">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                    <div class="flex-grow-1">
                        <strong>يوجد أخطاء في البيانات:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            <!-- Page Content -->
            <div class="fade-in-up">
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Toggle Sidebar
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            
            if (window.innerWidth <= 992 && 
                !sidebar.contains(event.target) && 
                !toggleBtn.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
        
        // SweetAlert for delete confirmation
        function confirmDelete(event, formId) {
            event.preventDefault();
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من التراجع عن هذا الإجراء!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1b7158',
                cancelButtonColor: '#bcbcbc',
                confirmButtonText: 'نعم، احذف!',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
        
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-modern');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
        
        // Form validation enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري المعالجة...';
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>