@extends('layouts.admin')

@section('title', 'إدارة الطلبات')
@section('breadcrumb')
    <li class="breadcrumb-item active">الطلبات</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-0">الطلبات</h2>
        <p class="text-muted mb-0">إدارة وعرض جميع طلبات المتجر</p>
    </div>
    <div class="col-md-6 text-start">
        <a href="{{ route('admin.orders.create') }}" class="btn btn-primary-modern btn-modern">
            <i class="fas fa-plus me-2"></i> إنشاء طلب جديد
        </a>
    </div>
</div>

<!-- إحصائيات سريعة -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-grid">
            @foreach($stats as $key => $stat)
                @php
                    $config = [
                        'total' => ['icon' => 'fas fa-shopping-cart', 'color' => 'primary', 'label' => 'إجمالي الطلبات'],
                        'pending' => ['icon' => 'fas fa-clock', 'color' => 'warning', 'label' => 'طلبات معلقة'],
                        'processing' => ['icon' => 'fas fa-cogs', 'color' => 'info', 'label' => 'قيد المعالجة'],
                        'shipped' => ['icon' => 'fas fa-shipping-fast', 'color' => 'info', 'label' => 'تم الشحن'],
                        'delivered' => ['icon' => 'fas fa-check-circle', 'color' => 'success', 'label' => 'تم التوصيل'],
                        'cancelled' => ['icon' => 'fas fa-times-circle', 'color' => 'danger', 'label' => 'ملغاة'],
                        'today' => ['icon' => 'fas fa-calendar-day', 'color' => 'primary', 'label' => 'طلبات اليوم'],
                        'week' => ['icon' => 'fas fa-calendar-week', 'color' => 'success', 'label' => 'طلبات الأسبوع'],
                        'month' => ['icon' => 'fas fa-calendar-alt', 'color' => 'info', 'label' => 'طلبات الشهر']
                    ][$key] ?? ['icon' => 'fas fa-chart-bar', 'color' => 'secondary', 'label' => $key];
                @endphp
                <div class="stat-card slide-in-right" style="animation-delay: {{ $loop->iteration * 0.1 }}s">
                    <div class="stat-icon {{ $config['color'] }}">
                        <i class="{{ $config['icon'] }}"></i>
                    </div>
                    <div class="stat-value">{{ $stat }}</div>
                    <div class="stat-label">{{ $config['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- فلترة البحث -->
<div class="card-modern mb-4">
    <div class="card-body-modern">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">بحث</label>
                    <input type="text" name="search" class="form-control-modern" 
                           placeholder="رقم الطلب، اسم العميل..." value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="form-group-modern">
                    <label class="form-label-modern">الحالة</label>
                    <select name="status" class="form-select-modern">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم التوصيل</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغى</option>
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
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary-modern btn-modern">
                        <i class="fas fa-redo me-2"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول الطلبات -->
<div class="card-modern">
    <div class="card-header-modern d-flex justify-content-between align-items-center">
        <h5>
            <i class="fas fa-shopping-cart me-2"></i> قائمة الطلبات
            <span class="badge-modern badge-primary-modern ms-2">{{ $orders->total() }}</span>
        </h5>
        
        <div class="d-flex gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-primary-modern btn-modern btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-sort me-1"></i> ترتيب حسب
                </button>
                <ul class="dropdown-menu dropdown-menu-custom">
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}">الأحدث</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'total', 'direction' => 'desc']) }}">المبلغ (عالي-منخفض)</a></li>
                    <li><a class="dropdown-item dropdown-item-custom" href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'direction' => 'asc']) }}">الحالة</a></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="card-body-modern">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>العميل</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="slide-in-right">
                            <td>
                                <strong>{{ $order->order_number }}</strong>
                                <div class="text-muted small">{{ $order->items_count }} منتج</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        {{ substr($order->user->name ?? 'زائر', 0, 1) }}
                                    </div>
                                    <div>
                                        <strong>{{ $order->user->name ?? 'زائر' }}</strong>
                                        <div class="text-muted small">{{ $order->user->email ?? 'لا يوجد بريد' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong class="text-primary">{{ number_format($order->total) }} ر.س</strong>
                                    <div class="text-muted small">
                                        <span>فرعي: {{ number_format($order->subtotal) }}</span>
                                        <span class="mx-1">•</span>
                                        <span>شحن: {{ number_format($order->shipping_cost) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'processing' => 'primary',
                                        'shipped' => 'info',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        'refunded' => 'secondary'
                                    ];
                                @endphp
                                <span class="badge-modern badge-{{ $statusColors[$order->status] ?? 'secondary' }}-modern">
                                    {{ $order->status_arabic }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $order->created_at->format('Y-m-d') }}</span>
                                    <span class="text-muted small">{{ $order->created_at->format('H:i') }}</span>
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
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.orders.show', $order) }}">
                                                <i class="fas fa-eye me-2"></i> عرض التفاصيل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.orders.edit', $order) }}">
                                                <i class="fas fa-edit me-2"></i> تعديل
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.orders.print', $order) }}" target="_blank">
                                                <i class="fas fa-print me-2"></i> طباعة الفاتورة
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form id="delete-form-{{ $order->id }}" 
                                                  action="{{ route('admin.orders.destroy', $order) }}" 
                                                  method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item dropdown-item-custom text-danger" 
                                                        onclick="confirmDelete(event, 'delete-form-{{ $order->id }}')">
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
            @if($orders->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    عرض {{ $orders->firstItem() }} إلى {{ $orders->lastItem() }} من إجمالي {{ $orders->total() }} طلب
                </div>
                <nav>
                    <ul class="pagination-modern">
                        {{ $orders->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </ul>
                </nav>
            </div>
            @endif
            
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">لا توجد طلبات</h4>
                <p class="text-muted mb-4">لم يتم تقديم أي طلبات بعد.</p>
                <a href="{{ route('admin.orders.create') }}" class="btn btn-primary-modern btn-modern">
                    <i class="fas fa-plus me-2"></i> إنشاء طلب جديد
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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    .stat-icon.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .stat-icon.info { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
    .stat-icon.success { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .stat-icon.danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .stat-icon.secondary { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
    
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
</style>
@endpush