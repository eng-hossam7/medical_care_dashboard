@extends('layouts.admin')

@section('title', 'إدارة العملاء')
@section('breadcrumb')
    <li class="breadcrumb-item active">العملاء</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-0">العملاء</h2>
        <p class="text-muted mb-0">إدارة وعرض جميع عملاء المتجر</p>
    </div>
    <div class="col-md-6 text-start">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary-modern btn-modern">
            <i class="fas fa-plus me-2"></i> إضافة عميل جديد
        </a>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-grid">
            <div class="stat-card slide-in-right" style="animation-delay: 0.1s">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">إجمالي العملاء</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up trend-up"></i>
                    <span class="trend-up">{{ $stats['new_month'] }} جديد</span>
                </div>
            </div>
            
            <div class="stat-card slide-in-right" style="animation-delay: 0.2s">
                <div class="stat-icon success">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value">{{ $stats['with_orders'] }}</div>
                <div class="stat-label">عملاء قاموا بالشراء</div>
                <div class="stat-trend">
                    <span>{{ round(($stats['with_orders'] / max($stats['total'], 1)) * 100) }}%</span>
                </div>
            </div>
            
            <div class="stat-card slide-in-right" style="animation-delay: 0.3s">
                <div class="stat-icon warning">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-value">{{ $stats['new_today'] }}</div>
                <div class="stat-label">عملاء جدد اليوم</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up trend-up"></i>
                    <span class="trend-up">+{{ $stats['new_today'] }}</span>
                </div>
            </div>
            
            <div class="stat-card slide-in-right" style="animation-delay: 0.4s">
                <div class="stat-icon danger">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['total_revenue']) }}</div>
                <div class="stat-label">إجمالي الإيرادات (ر.س)</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up trend-up"></i>
                    <span class="trend-up">{{ number_format($stats['average_order_value']) }} متوسط</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- فلترة البحث -->
<div class="card-modern mb-4">
    <div class="card-body-modern">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">بحث</label>
                    <input type="text" name="search" class="form-control-modern" 
                           placeholder="الاسم، البريد، الهاتف..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">الجنس</label>
                    <select name="gender" class="form-select-modern">
                        <option value="">جميع العملاء</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">من تاريخ</label>
                    <input type="date" name="date_from" class="form-control-modern" value="{{ request('date_from') }}">
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control-modern" value="{{ request('date_to') }}">
                </div>
            </div>
            
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-filter me-2"></i> تطبيق الفلتر
                    </button>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary-modern btn-modern">
                        <i class="fas fa-redo me-2"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول العملاء -->
<div class="card-modern">
    <div class="card-header-modern d-flex justify-content-between align-items-center">
        <h5>
            <i class="fas fa-users me-2"></i> قائمة العملاء
            <span class="badge-modern badge-primary-modern ms-2">{{ $customers->total() }}</span>
        </h5>
        
        <div class="d-flex gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-primary-modern btn-modern btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-1"></i> ترتيب حسب
                </button>
                <ul class="dropdown-menu dropdown-menu-custom">
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}">الأحدث</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'first_name', 'direction' => 'asc']) }}">الاسم (أ-ي)</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'total_orders', 'direction' => 'desc']) }}">عدد الطلبات</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'total_spent', 'direction' => 'desc']) }}">إجمالي المشتريات</a></li>
                </ul>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-outline-primary-modern btn-modern btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i> تصدير
                </button>
                <ul class="dropdown-menu dropdown-menu-custom">
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ route('admin.customers.export') }}"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="#"><i class="fas fa-file-pdf me-2"></i> PDF</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="card-body-modern">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th>العميل</th>
                            <th>معلومات الاتصال</th>
                            <th>الطلبات</th>
                            <th>إجمالي المشتريات</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr class="slide-in-right">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        {{ substr($customer->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $customer->full_name ?? $customer->user->name }}</strong>
                                        @if($customer->date_of_birth)
                                            <div class="text-muted small">
                                                <i class="fas fa-birthday-cake me-1"></i> 
                                                {{ \Carbon\Carbon::parse($customer->date_of_birth)->age }} سنة
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span><i class="fas fa-envelope me-2"></i> {{ $customer->user->email }}</span>
                                    <span class="text-muted small mt-1">
                                        <i class="fas fa-phone me-2"></i> {{ $customer->user->phone ?? 'لا يوجد' }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge-modern badge-primary-modern">{{ $customer->total_orders_count }}</span>
                                    @if($customer->total_orders_count > 0)
                                        <small class="text-muted mt-1">آخر طلب: {{ $customer->orders->first()->created_at->diffForHumans() ?? 'N/A' }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($customer->total_spent_amount > 0)
                                    <strong class="text-success">{{ number_format($customer->total_spent_amount) }} ر.س</strong>
                                    <div class="text-muted small">متوسط: {{ number_format($customer->total_spent_amount / max($customer->total_orders_count, 1)) }} ر.س</div>
                                @else
                                    <span class="text-muted">لم يشتري بعد</span>
                                @endif
                            </td>
                            <td>
                                <button onclick="toggleCustomerStatus('{{ route('admin.customers.toggle-status', $customer) }}', this)" 
                                        class="btn btn-sm {{ $customer->user->status == 'active' ? 'btn-success' : 'btn-secondary' }}"
                                        style="min-width: 80px;">
                                    {!! $customer->user->status == 'active' ? '<i class="fas fa-check me-1"></i> نشط' : '<i class="fas fa-times me-1"></i> موقوف' !!}
                                </button>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $customer->created_at->format('Y-m-d') }}</span>
                                    <span class="text-muted small">{{ $customer->created_at->diffForHumans() }}</span>
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
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.customers.show', $customer) }}">
                                                <i class="fas fa-eye me-2"></i> عرض التفاصيل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.customers.edit', $customer) }}">
                                                <i class="fas fa-edit me-2"></i> تعديل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.customers.orders', $customer) }}">
                                                <i class="fas fa-shopping-cart me-2"></i> طلبات العميل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.customers.addresses.index', $customer) }}">
                                                <i class="fas fa-map-marker-alt me-2"></i> العناوين
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form id="delete-form-{{ $customer->id }}" 
                                                  action="{{ route('admin.customers.destroy', $customer) }}" 
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item dropdown-item-custom text-danger" 
                                                        onclick="confirmDelete(event, 'delete-form-{{ $customer->id }}')">
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
            @if($customers->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    عرض {{ $customers->firstItem() }} إلى {{ $customers->lastItem() }} من إجمالي {{ $customers->total() }} عميل
                </div>
                <nav>
                    <ul class="pagination-modern">
                        {{ $customers->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </ul>
                </nav>
            </div>
            @endif
            
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-users fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">لا توجد عملاء</h4>
                <p class="text-muted mb-4">لم يسجل أي عميل في المتجر بعد.</p>
                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary-modern btn-modern">
                    <i class="fas fa-plus me-2"></i> إضافة عميل جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar {
        width: 40px;
        height: 40px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--radius);
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: var(--transition);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 20px;
    }
    
    .stat-icon.primary { background: rgba(27, 113, 88, 0.1); color: #1b7158; }
    .stat-icon.success { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .stat-icon.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .stat-icon.danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
    
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 14px;
    }
    
    .stat-trend {
        margin-top: 10px;
        font-size: 12px;
    }
    
    .trend-up { color: var(--success); }
    .trend-down { color: var(--danger); }
</style>
@endpush

@push('scripts')
<script>
    // تبديل حالة العميل
    function toggleCustomerStatus(url, element) {
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
                element.innerHTML = data.status == 'active' ? 
                    '<i class="fas fa-check me-1"></i> نشط' : 
                    '<i class="fas fa-times me-1"></i> موقوف';
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
</script>
@endpush