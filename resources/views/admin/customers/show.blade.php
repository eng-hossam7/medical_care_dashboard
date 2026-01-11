@extends('layouts.admin')

@section('title', $customer->full_name ?? $customer->user->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">العملاء</a></li>
    <li class="breadcrumb-item active">تفاصيل العميل</li>
@endsection

@section('content')
<div class="row">
    <!-- معلومات العميل -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <!-- الصورة والمعلومات الأساسية -->
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <div class="avatar-lg mx-auto mb-3">
                                {{ substr($customer->user->name, 0, 1) }}
                            </div>
                            <h4>{{ $customer->full_name ?? $customer->user->name }}</h4>
                            <p class="text-muted">عميل منذ {{ $customer->created_at->diffForHumans() }}</p>
                            
                            <div class="d-grid gap-2 mt-4">
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary-custom btn-custom">
                                    <i class="fas fa-edit me-2"></i> تعديل
                                </a>
                                
                                <button onclick="toggleCustomerStatus('{{ route('admin.customers.toggle-status', $customer) }}', this)" 
                                        class="btn btn-custom {{ $customer->user->status == 'active' ? 'btn-success' : 'btn-secondary' }}">
                                    {!! $customer->user->status == 'active' ? '<i class="fas fa-check me-2"></i> نشط' : '<i class="fas fa-times me-2"></i> موقوف' !!}
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- التفاصيل -->
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-2">معلومات العميل</h5>
                                <div class="text-muted">
                                    <span class="me-3">
                                        <i class="fas fa-user me-1"></i> {{ $customer->full_name ?? $customer->user->name }}
                                    </span>
                                    @if($customer->gender)
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-venus-mars me-1"></i> {{ $customer->gender_arabic }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.customers.edit', $customer) }}">
                                            <i class="fas fa-edit me-2"></i> تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.customers.orders', $customer) }}">
                                            <i class="fas fa-shopping-cart me-2"></i> طلبات العميل
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.customers.addresses.index', $customer) }}">
                                            <i class="fas fa-map-marker-alt me-2"></i> العناوين
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form id="delete-form" action="{{ route('admin.customers.destroy', $customer) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="dropdown-item text-danger" onclick="confirmDelete(event, 'delete-form')">
                                                <i class="fas fa-trash me-2"></i> حذف
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- معلومات الاتصال -->
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-address-card me-2"></i> معلومات الاتصال</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex">
                                        <i class="fas fa-envelope text-primary me-2 mt-1"></i>
                                        <div>
                                            <strong>البريد الإلكتروني</strong>
                                            <div class="text-muted">{{ $customer->user->email }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex">
                                        <i class="fas fa-phone text-primary me-2 mt-1"></i>
                                        <div>
                                            <strong>رقم الهاتف</strong>
                                            <div class="text-muted">{{ $customer->user->phone ?? 'غير محدد' }}</div>
                                        </div>
                                    </div>
                                </div>
                                @if($customer->date_of_birth)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex">
                                        <i class="fas fa-birthday-cake text-primary me-2 mt-1"></i>
                                        <div>
                                            <strong>تاريخ الميلاد</strong>
                                            <div class="text-muted">
                                                {{ $customer->date_of_birth->format('Y-m-d') }}
                                                ({{ \Carbon\Carbon::parse($customer->date_of_birth)->age }} سنة)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex">
                                        <i class="fas fa-calendar text-primary me-2 mt-1"></i>
                                        <div>
                                            <strong>تاريخ التسجيل</strong>
                                            <div class="text-muted">{{ $customer->created_at->format('Y-m-d H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الملاحظات -->
                        @if($customer->notes)
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-sticky-note me-2"></i> ملاحظات</h6>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($customer->notes)) !!}
                            </div>
                        </div>
                        @endif
                        
                        <!-- إضافة ملاحظة -->
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-plus-circle me-2"></i> إضافة ملاحظة</h6>
                            <form action="{{ route('admin.customers.add-note', $customer) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea name="note" class="form-control" rows="3" placeholder="اكتب ملاحظة جديدة..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary-custom btn-custom">
                                    <i class="fas fa-save me-2"></i> حفظ الملاحظة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- إحصائيات العميل -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> إحصائيات العميل</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon primary mb-2">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-number">{{ $customerStats['total_orders'] }}</div>
                        <div class="stat-label">الطلبات</div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon success mb-2">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-number">{{ number_format($customerStats['total_spent']) }}</div>
                        <div class="stat-label">إجمالي المشتريات (ر.س)</div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon warning mb-2">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-number">{{ $customerStats['reviews_count'] }}</div>
                        <div class="stat-label">التقييمات</div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon info mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="stat-number">{{ $customerStats['addresses_count'] }}</div>
                        <div class="stat-label">العناوين</div>
                    </div>
                </div>
                
                <!-- رسوم بيانية مصغرة -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-center mb-3">نسبة الطلبات</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-success">مكتملة: {{ $customerStats['completed_orders'] }}</span>
                                    <span class="text-warning">معلقة: {{ $customerStats['pending_orders'] }}</span>
                                </div>
                                @php
                                    $totalOrders = $customerStats['total_orders'];
                                    $completedPercent = $totalOrders > 0 ? ($customerStats['completed_orders'] / $totalOrders) * 100 : 0;
                                    $pendingPercent = $totalOrders > 0 ? ($customerStats['pending_orders'] / $totalOrders) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" style="width: {{ $completedPercent }}%"></div>
                                    <div class="progress-bar bg-warning" style="width: {{ $pendingPercent }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="text-center mb-3">متوسط قيمة الطلب</h6>
                                <div class="text-center">
                                    <div class="h2 text-primary">{{ number_format($customerStats['average_order_value']) }}</div>
                                    <div class="text-muted">ريال سعودي</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- أحدث الطلبات -->
        @if($recentOrders->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i> أحدث الطلبات</h5>
                <a href="{{ route('admin.customers.orders', $customer) }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
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
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    <div class="text-muted small">{{ $order->items_count }} منتج</div>
                                </td>
                                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <strong class="text-primary">{{ number_format($order->total) }} ر.س</strong>
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
                                    <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                                        {{ $order->status_arabic }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
        
        <!-- المنتجات المفضلة -->
        @if($favoriteProducts->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-heart me-2"></i> المنتجات المفضلة</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($favoriteProducts as $product)
                        <div class="col-md-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center me-2" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-box text-secondary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ Str::limit($product->name, 20) }}</h6>
                                        <small class="text-muted">{{ $product->sku }}</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-primary">
                                        {{ $product->total_quantity }} وحدة
                                    </span>
                                    <span class="text-success">
                                        {{ number_format($product->total_spent) }} ر.س
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- الجانب الأيمن -->
    <div class="col-lg-4">
        <!-- معلومات سريعة -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات سريعة</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الاسم الكامل:</span>
                        <span class="fw-bold">{{ $customer->full_name ?? $customer->user->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">البريد الإلكتروني:</span>
                        <span class="fw-bold">{{ $customer->user->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">رقم الهاتف:</span>
                        <span class="fw-bold">{{ $customer->user->phone ?? 'غير محدد' }}</span>
                    </div>
                    @if($customer->date_of_birth)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">العمر:</span>
                        <span class="fw-bold">{{ \Carbon\Carbon::parse($customer->date_of_birth)->age }} سنة</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">الجنس:</span>
                        <span class="fw-bold">{{ $customer->gender_arabic ?? 'غير محدد' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">نقاط الولاء:</span>
                        <span class="fw-bold">{{ $customer->loyalty_points ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- إحصائيات الطلبات -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i> تحليل الطلبات</h5>
            </div>
            <div class="card-body">
                <canvas id="ordersChart" height="200"></canvas>
                <div class="text-center mt-3">
                    <small class="text-muted">
                        إجمالي الطلبات: {{ $customerStats['total_orders'] }}
                    </small>
                </div>
            </div>
        </div>
        
        <!-- إجراءات سريعة -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> إجراءات سريعة</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-edit me-2"></i> تعديل العميل
                    </a>
                    <a href="{{ route('admin.customers.orders', $customer) }}" class="btn btn-outline-primary btn-custom">
                        <i class="fas fa-shopping-cart me-2"></i> عرض الطلبات
                    </a>
                <a href="{{ route('admin.customers.addresses.index', $customer) }}" class="btn btn-outline-info btn-custom">
                        <i class="fas fa-map-marker-alt me-2"></i> العناوين
                    </a>
                    <a href="{{ route('admin.customers.reviews', $customer) }}" class="btn btn-outline-warning btn-custom">
                        <i class="fas fa-star me-2"></i> التقييمات
                    </a>
                    <button onclick="toggleCustomerStatus('{{ route('admin.customers.toggle-status', $customer) }}', this)" 
                            class="btn btn-custom {{ $customer->user->status == 'active' ? 'btn-success' : 'btn-secondary' }}">
                        {{ $customer->user->status == 'active' ? '<i class="fas fa-check me-2"></i> تعطيل' : '<i class="fas fa-times me-2"></i> تفعيل' }}
                    </button>
                </div>
            </div>
        </div>
        
        <!-- معلومات التسجيل -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i> تاريخ العميل</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon bg-primary">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>انضم كعميل</h6>
                            <p class="text-muted small">{{ $customer->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($customerStats['last_order_date'])
                    <div class="timeline-item">
                        <div class="timeline-icon bg-success">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>آخر طلب</h6>
                            <p class="text-muted small">{{ $customerStats['last_order_date']->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($customerStats['reviews_count'] > 0)
                    <div class="timeline-item">
                        <div class="timeline-icon bg-warning">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>أضاف تقييمات</h6>
                            <p class="text-muted small">{{ $customerStats['reviews_count'] }} تقييم</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 100px;
        height: 100px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 40px;
        margin: 0 auto;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 20px;
    }
    
    .stat-icon.primary { background: rgba(27, 113, 88, 0.1); color: #1b7158; }
    .stat-icon.success { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .stat-icon.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .stat-icon.info { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
    
    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 14px;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-icon {
        position: absolute;
        left: -30px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
    }
    
    .timeline-content {
        padding-bottom: 10px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // مخطط الطلبات
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersChart = new Chart(ordersCtx, {
        type: 'doughnut',
        data: {
            labels: ['مكتملة', 'معلقة', 'ملغاة'],
            datasets: [{
                data: [
                    {{ $customerStats['completed_orders'] }},
                    {{ $customerStats['pending_orders'] }},
                    {{ $customerStats['total_orders'] - $customerStats['completed_orders'] - $customerStats['pending_orders'] }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Cairo',
                            size: 12
                        }
                    }
                }
            }
        }
    });
    
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
                    '<i class="fas fa-check me-2"></i> تعطيل' : 
                    '<i class="fas fa-times me-2"></i> تفعيل';
                
                // تحديث كافة الأزرار
                document.querySelectorAll('.btn-custom').forEach(btn => {
                    if (btn.innerHTML.includes('تعطيل') || btn.innerHTML.includes('تفعيل')) {
                        btn.classList.toggle('btn-success');
                        btn.classList.toggle('btn-secondary');
                        btn.innerHTML = data.status == 'active' ? 
                            '<i class="fas fa-check me-2"></i> تعطيل';
                            '<i class="fas fa-times me-2"></i> تفعيل';
                    }
                });
                
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