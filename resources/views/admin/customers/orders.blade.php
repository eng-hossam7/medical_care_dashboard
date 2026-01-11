@extends('layouts.admin')

@section('title', 'طلبات العميل: ' . ($customer->full_name ?? $customer->user->name))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">العملاء</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer) }}">{{ Str::limit($customer->full_name ?? $customer->user->name, 20) }}</a></li>
    <li class="breadcrumb-item active">طلبات العميل</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-0">طلبات العميل</h2>
        <p class="text-muted mb-0">جميع طلبات {{ $customer->full_name ?? $customer->user->name }}</p>
    </div>
    <div class="col-md-6 text-start">
        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-secondary-modern btn-modern">
            <i class="fas fa-arrow-right me-2"></i> العودة للعميل
        </a>
    </div>
</div>

<!-- إحصائيات الطلبات -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-grid">
            @php
                $stats = [
                    'total' => $customer->orders()->count(),
                    'pending' => $customer->orders()->where('status', 'pending')->count(),
                    'processing' => $customer->orders()->where('status', 'processing')->count(),
                    'shipped' => $customer->orders()->where('status', 'shipped')->count(),
                    'delivered' => $customer->orders()->where('status', 'delivered')->count(),
                    'cancelled' => $customer->orders()->where('status', 'cancelled')->count(),
                    'total_spent' => $customer->orders()->where('status', '!=', 'cancelled')->sum('total'),
                    'average_order' => $customer->orders()->where('status', '!=', 'cancelled')->avg('total'),
                ];
            @endphp
            
            @foreach(['total', 'pending', 'processing', 'delivered', 'cancelled'] as $key)
                @php
                    $config = [
                        'total' => ['icon' => 'fas fa-shopping-cart', 'color' => 'primary', 'label' => 'إجمالي الطلبات'],
                        'pending' => ['icon' => 'fas fa-clock', 'color' => 'warning', 'label' => 'معلقة'],
                        'processing' => ['icon' => 'fas fa-cogs', 'color' => 'info', 'label' => 'قيد المعالجة'],
                        'delivered' => ['icon' => 'fas fa-check-circle', 'color' => 'success', 'label' => 'تم التوصيل'],
                        'cancelled' => ['icon' => 'fas fa-times-circle', 'color' => 'danger', 'label' => 'ملغاة'],
                    ][$key];
                @endphp
                <div class="stat-card slide-in-right" style="animation-delay: {{ $loop->iteration * 0.1 }}s">
                    <div class="stat-icon {{ $config['color'] }}">
                        <i class="{{ $config['icon'] }}"></i>
                    </div>
                    <div class="stat-value">{{ $stats[$key] }}</div>
                    <div class="stat-label">{{ $config['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- جدول الطلبات -->
<div class="card-modern">
    <div class="card-header-modern d-flex justify-content-between align-items-center">
        <h5>
            <i class="fas fa-shopping-cart me-2"></i> طلبات العميل
            <span class="badge-modern badge-primary-modern ms-2">{{ $orders->total() }}</span>
        </h5>
        
        <div class="text-muted">
            إجمالي المشتريات: <strong class="text-success">{{ number_format($stats['total_spent']) }} ر.س</strong>
        </div>
    </div>
    
    <div class="card-body-modern">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern table-hover">
                    <thead>
                        <tr>
                            <th>رقم الطلب</th>
                            <th>التاريخ</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="slide-in-right">
                            <td>
                                <strong>{{ $order->order_number }}</strong>
                                <div class="text-muted small">{{ $order->items->count() }} منتج</div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $order->created_at->format('Y-m-d') }}</span>
                                    <span class="text-muted small">{{ $order->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <strong class="text-primary">{{ number_format($order->total) }} ر.س</strong>
                                    <div class="text-muted small">
                                        فرعي: {{ number_format($order->subtotal) }}
                                        <span class="mx-1">•</span>
                                        شحن: {{ number_format($order->shipping_cost) }}
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
                                        'cancelled' => 'danger'
                                    ];
                                @endphp
                                <span class="badge-modern badge-{{ $statusColors[$order->status] ?? 'secondary' }}-modern">
                                    {{ $order->status_arabic }}
                                </span>
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
                        {{ $orders->links('vendor.pagination.bootstrap-4') }}
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
                <p class="text-muted mb-4">لم يقم هذا العميل بأي طلبات بعد.</p>
                <a href="{{ route('admin.orders.create', ['user_id' => $customer->user_id]) }}" class="btn btn-primary-modern btn-modern">
                    <i class="fas fa-plus me-2"></i> إنشاء طلب جديد لهذا العميل
                </a>
            </div>
        @endif
    </div>
</div>
@endsection