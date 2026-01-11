@extends('layouts.admin')

@section('title', 'لوحة التحكم')
@section('breadcrumb')
    <li class="breadcrumb-item active">لوحة التحكم</li>
@endsection

@section('content')
<div class="row mb-4">
    <!-- إحصائيات سريعة -->
    <div class="col-12">
        <div class="stats-grid">
            <!-- Total Products -->
            <div class="stat-card slide-in-right" style="animation-delay: 0.1s">
                <div class="stat-icon primary">
                    <i class="fas fa-pills"></i>
                </div>
                <div class="stat-value">{{ $stats['total_products'] }}</div>
                <div class="stat-label">إجمالي المنتجات</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up trend-up"></i>
                    <span class="trend-up">12% زيادة</span>
                </div>
            </div>
            
            <!-- Total Orders -->
            <div class="stat-card slide-in-right" style="animation-delay: 0.2s">
                <div class="stat-icon success">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value">{{ $stats['total_orders'] }}</div>
                <div class="stat-label">إجمالي الطلبات</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up trend-up"></i>
                    <span class="trend-up">8% زيادة</span>
                </div>
            </div>
            
            <!-- Total Customers -->
            <div class="stat-card slide-in-right" style="animation-delay: 0.3s">
                <div class="stat-icon warning">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $stats['total_customers'] }}</div>
                <div class="stat-label">إجمالي العملاء</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up trend-up"></i>
                    <span class="trend-up">5% زيادة</span>
                </div>
            </div>
            
            <!-- Total Revenue -->
            <div class="stat-card slide-in-right" style="animation-delay: 0.4s">
                <div class="stat-icon danger">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-value">{{ number_format($stats['total_revenue'] ?? 0) }}</div>
                <div class="stat-label">إجمالي الإيرادات (ر.س)</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up trend-up"></i>
                    <span class="trend-up">15% زيادة</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Chart 1: Sales Overview -->
    <div class="col-lg-8 mb-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fas fa-chart-line me-2"></i> نظرة عامة على المبيعات</h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Categories -->
    <div class="col-lg-4 mb-4">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fas fa-chart-pie me-2"></i> توزيع المنتجات حسب التصنيف</h5>
            </div>
            <div class="card-body-modern">
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-6 mb-4">
        <div class="card-modern">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-history me-2"></i> أحدث الطلبات</h5>
                <a href="#" class="btn btn-outline-primary-modern btn-modern btn-sm">
                    <i class="fas fa-eye me-1"></i> عرض الكل
                </a>
            </div>
            <div class="card-body-modern">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr class="slide-in-right">
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                    <div class="text-muted small">{{ $order->created_at->format('H:i') }}</div>
                                </td>
                                <td>{{ $order->user->name ?? 'زائر' }}</td>
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
                                            'cancelled' => 'danger',
                                            'refunded' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge-modern badge-{{ $statusColors[$order->status] ?? 'secondary' }}-modern">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد طلبات حديثة</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Products -->
    <div class="col-lg-6 mb-4">
        <div class="card-modern">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-boxes me-2"></i> أحدث المنتجات</h5>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary-modern btn-modern btn-sm">
                    <i class="fas fa-plus me-1"></i> إضافة جديد
                </a>
            </div>
            <div class="card-body-modern">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>السعر</th>
                                <th>المخزون</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProducts as $product)
                            <tr class="slide-in-right">
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($product->images->first())
                                            <img src="{{ asset('storage/' . $product->images->first()->image_url) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="rounded me-3" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-box text-secondary"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ Str::limit($product->name, 25) }}</strong>
                                            <div class="text-muted small">{{ $product->category ? $product->category->name : 'بدون تصنيف' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($product->sale_price)
                                        <span class="text-danger"><strong>{{ number_format($product->sale_price) }} ر.س</strong></span>
                                        <div class="text-muted small text-decoration-line-through">{{ number_format($product->regular_price) }} ر.س</div>
                                    @else
                                        <span class="text-dark">{{ number_format($product->regular_price) }} ر.س</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->quantity == 0)
                                        <span class="badge-modern badge-danger-modern">نفذ</span>
                                    @elseif($product->quantity <= $product->low_stock_threshold)
                                        <span class="badge-modern badge-warning-modern">{{ $product->quantity }}</span>
                                    @else
                                        <span class="badge-modern badge-success-modern">{{ $product->quantity }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge-modern badge-success-modern">نشط</span>
                                    @else
                                        <span class="badge-modern badge-secondary-modern">معطل</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد منتجات</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary-modern btn-modern">
                                        <i class="fas fa-plus me-2"></i> إضافة منتج جديد
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fas fa-chart-bar me-2"></i> إحصائيات سريعة</h5>
            </div>
            <div class="card-body-modern">
                <div class="row">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <div class="stat-value text-primary mb-2">{{ $stats['active_products'] }}</div>
                            <div class="stat-label">منتجات نشطة</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <div class="stat-value text-warning mb-2">{{ $stats['low_stock_products'] }}</div>
                            <div class="stat-label">منخفضة المخزون</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <div class="stat-value text-danger mb-2">{{ $stats['out_of_stock_products'] }}</div>
                            <div class="stat-label">نفذ من المخزون</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="text-center">
                            <div class="stat-value text-success mb-2">{{ $stats['pending_orders'] }}</div>
                            <div class="stat-label">طلبات معلقة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو'],
        datasets: [{
            label: 'المبيعات',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000],
            borderColor: '#1b7158',
            backgroundColor: 'rgba(27, 113, 88, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#1b7158',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top',
                labels: {
                    font: {
                        family: 'Cairo',
                        size: 14
                    },
                    color: '#1d4b82'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(29, 75, 130, 0.9)',
                titleFont: {
                    family: 'Cairo',
                    size: 14
                },
                bodyFont: {
                    family: 'Cairo',
                    size: 13
                },
                padding: 12,
                displayColors: false
            }
        },
        scales: {
            x: {
                grid: {
                    color: 'rgba(188, 188, 188, 0.1)'
                },
                ticks: {
                    font: {
                        family: 'Cairo',
                        size: 12
                    },
                    color: '#1d4b82'
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(188, 188, 188, 0.1)'
                },
                ticks: {
                    font: {
                        family: 'Cairo',
                        size: 12
                    },
                    color: '#1d4b82',
                    callback: function(value) {
                        return value.toLocaleString('ar-SA') + ' ر.س';
                    }
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: ['أجهزة طبية', 'مستلزمات', 'أدوية', 'معدات'],
        datasets: [{
            data: [35, 25, 20, 20],
            backgroundColor: [
                '#1b7158',
                '#1d4b82',
                '#4a9e8a',
                '#bcbcbc'
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
                        size: 13
                    },
                    color: '#1d4b82',
                    padding: 20,
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                backgroundColor: 'rgba(29, 75, 130, 0.9)',
                titleFont: {
                    family: 'Cairo',
                    size: 14
                },
                bodyFont: {
                    family: 'Cairo',
                    size: 13
                },
                padding: 12,
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed + '%';
                    }
                }
            }
        }
    }
});
</script>
@endpush