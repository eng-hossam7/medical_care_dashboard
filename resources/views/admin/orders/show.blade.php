@extends('layouts.admin')

@section('title', 'تفاصيل الطلب: ' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">الطلبات</a></li>
    <li class="breadcrumb-item active">تفاصيل الطلب</li>
@endsection

@section('content')
<div class="row">
    <!-- معلومات الطلب -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i> الطلب #{{ $order->order_number }}
                        <small class="text-muted">بتاريخ {{ $order->created_at->format('Y-m-d H:i') }}</small>
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-1"></i> تعديل
                        </a>
                        <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-print me-1"></i> طباعة
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- معلومات العميل -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user me-2"></i> معلومات العميل</h6>
                        <div class="border rounded p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-lg me-3">
                                    {{ substr($order->user->name ?? 'زائر', 0, 1) }}
                                </div>
                                <div>
                                    <strong>{{ $order->user->name ?? 'زائر' }}</strong>
                                    <div class="text-muted small">{{ $order->user->email ?? 'لا يوجد بريد' }}</div>
                                    <div class="text-muted small">{{ $order->user->phone ?? 'لا يوجد هاتف' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle me-2"></i> معلومات الطلب</h6>
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">رقم الطلب:</span>
                                <span class="fw-bold">{{ $order->order_number }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">تاريخ الطلب:</span>
                                <span class="fw-bold">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">آخر تحديث:</span>
                                <span class="fw-bold">{{ $order->updated_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- حالة الطلب -->
                <div class="mb-4">
                    <h6 class="mb-3"><i class="fas fa-flag me-2"></i> حالة الطلب</h6>
                    <div class="d-flex align-items-center">
                        @php
                            $statusSteps = [
                                'pending' => ['icon' => 'fas fa-clock', 'label' => 'معلق', 'color' => 'warning'],
                                'processing' => ['icon' => 'fas fa-cogs', 'label' => 'قيد المعالجة', 'color' => 'primary'],
                                'shipped' => ['icon' => 'fas fa-shipping-fast', 'label' => 'تم الشحن', 'color' => 'info'],
                                'delivered' => ['icon' => 'fas fa-check-circle', 'label' => 'تم التوصيل', 'color' => 'success'],
                                'cancelled' => ['icon' => 'fas fa-times-circle', 'label' => 'ملغى', 'color' => 'danger']
                            ];
                        @endphp
                        
                        <div class="d-flex w-100">
                            @foreach($statusSteps as $statusKey => $step)
                                <div class="text-center flex-fill">
                                    <div class="status-step {{ $order->status == $statusKey ? 'active' : '' }} 
                                          {{ array_search($order->status, array_keys($statusSteps)) >= array_search($statusKey, array_keys($statusSteps)) ? 'completed' : '' }}">
                                        <div class="step-icon {{ $step['color'] }}">
                                            <i class="{{ $step['icon'] }}"></i>
                                        </div>
                                        <div class="step-label mt-2">{{ $step['label'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- منتجات الطلب -->
                <div class="mb-4">
                    <h6 class="mb-3"><i class="fas fa-boxes me-2"></i> المنتجات</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->product->images->first())
                                                <img src="{{ asset('storage/' . $item->product->images->first()->image_url) }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     class="rounded me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $item->product_name }}</strong>
                                                <div class="text-muted small">{{ $item->product->sku ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item->unit_price) }} ر.س</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-primary fw-bold">{{ number_format($item->total_price) }} ر.س</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>المجموع الفرعي:</strong></td>
                                    <td class="text-primary fw-bold">{{ number_format($order->subtotal) }} ر.س</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>تكلفة الشحن:</strong></td>
                                    <td class="text-primary fw-bold">{{ number_format($order->shipping_cost) }} ر.س</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>الضريبة:</strong></td>
                                    <td class="text-primary fw-bold">{{ number_format($order->tax) }} ر.س</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>الإجمالي النهائي:</strong></td>
                                    <td class="text-success fw-bold h5">{{ number_format($order->total) }} ر.س</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <!-- الملاحظات -->
                @if($order->notes)
                <div class="mb-4">
                    <h6 class="mb-3"><i class="fas fa-sticky-note me-2"></i> ملاحظات الطلب</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($order->notes)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- تاريخ الطلب -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i> تاريخ الطلب</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-icon bg-primary">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>تم إنشاء الطلب</h6>
                            <p class="text-muted small">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($order->status != 'pending')
                    <div class="timeline-item">
                        <div class="timeline-icon bg-info">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>تم تغيير الحالة إلى {{ $order->status_arabic }}</h6>
                            <p class="text-muted small">{{ $order->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($order->delivered_at)
                    <div class="timeline-item">
                        <div class="timeline-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>تم توصيل الطلب</h6>
                            <p class="text-muted small">{{ $order->delivered_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- الجانب الأيمن -->
    <div class="col-lg-4">
        <!-- ملخص الطلب -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-receipt me-2"></i> ملخص الطلب</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">رقم الطلب:</span>
                        <span class="fw-bold">{{ $order->order_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">تاريخ الطلب:</span>
                        <span class="fw-bold">{{ $order->created_at->format('Y-m-d') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">وقت الطلب:</span>
                        <span class="fw-bold">{{ $order->created_at->format('H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">طريقة الدفع:</span>
                        <span class="fw-bold">{{ $order->payments->first()->paymentMethod->display_name ?? 'غير محدد' }}</span>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>المجموع الفرعي:</span>
                        <span>{{ number_format($order->subtotal) }} ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>تكلفة الشحن:</span>
                        <span>{{ number_format($order->shipping_cost) }} ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>الضريبة:</span>
                        <span>{{ number_format($order->tax) }} ر.س</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>الإجمالي:</span>
                        <span class="text-success">{{ number_format($order->total) }} ر.س</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- عناوين الشحن والفواتير -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> العناوين</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="small text-muted mb-2">عنوان الشحن</h6>
                    <div class="border rounded p-3">
                        @if($order->shippingAddress)
                            <strong>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</strong>
                            <p class="mb-1 small">{{ $order->shippingAddress->address_line_1 }}</p>
                            @if($order->shippingAddress->address_line_2)
                                <p class="mb-1 small">{{ $order->shippingAddress->address_line_2 }}</p>
                            @endif
                            <p class="mb-1 small">
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}
                            </p>
                            <p class="mb-0 small">
                                {{ $order->shippingAddress->country }} - {{ $order->shippingAddress->postal_code }}
                            </p>
                            <p class="mb-0 small">
                                <i class="fas fa-phone me-1"></i> {{ $order->shippingAddress->phone }}
                            </p>
                        @else
                            <p class="text-muted text-center py-2">لا يوجد عنوان شحن</p>
                        @endif
                    </div>
                </div>
                
                <div>
                    <h6 class="small text-muted mb-2">عنوان الفاتورة</h6>
                    <div class="border rounded p-3">
                        @if($order->billingAddress)
                            <strong>{{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}</strong>
                            <p class="mb-1 small">{{ $order->billingAddress->address_line_1 }}</p>
                            @if($order->billingAddress->address_line_2)
                                <p class="mb-1 small">{{ $order->billingAddress->address_line_2 }}</p>
                            @endif
                            <p class="mb-1 small">
                                {{ $order->billingAddress->city }}, {{ $order->billingAddress->state }}
                            </p>
                            <p class="mb-0 small">
                                {{ $order->billingAddress->country }} - {{ $order->billingAddress->postal_code }}
                            </p>
                            <p class="mb-0 small">
                                <i class="fas fa-phone me-1"></i> {{ $order->billingAddress->phone }}
                            </p>
                        @else
                            <p class="text-muted text-center py-2">لا يوجد عنوان فاتورة</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- إجراءات سريعة -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i> إجراءات سريعة</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-edit me-2"></i> تعديل الطلب
                    </a>
                    
                    <!-- تحديث الحالة -->
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-custom dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-sync-alt me-2"></i> تحديث الحالة
                        </button>
                        <ul class="dropdown-menu">
                            @foreach(['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                                @if($order->status != $status)
                                    <li>
                                        <form action="{!! route('admin.orders.update-status', $order) !!}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-arrow-right me-2"></i> تغيير إلى {{ trans('orders.status.' . $status) }}
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    
                    <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="btn btn-outline-info btn-custom">
                        <i class="fas fa-print me-2"></i> طباعة الفاتورة
                    </a>
                    
                    @if($order->status == 'pending')
                    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-grid">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger-custom btn-custom" onclick="confirmDelete(event)">
                            <i class="fas fa-trash me-2"></i> حذف الطلب
                        </button>
                    </form>
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
        width: 60px;
        height: 60px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 24px;
    }
    
    .status-step {
        position: relative;
        padding: 10px 0;
    }
    
    .status-step.active .step-icon {
        transform: scale(1.2);
        box-shadow: 0 0 0 5px rgba(var(--primary-rgb), 0.2);
    }
    
    .status-step.completed .step-icon {
        background: var(--success);
        color: white;
    }
    
    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 20px;
        transition: var(--transition);
    }
    
    .step-icon.warning { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .step-icon.primary { background: rgba(27, 113, 88, 0.1); color: #1b7158; }
    .step-icon.info { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
    .step-icon.success { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .step-icon.danger { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
    
    .step-label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
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
<script>
    function confirmDelete(e) {
        e.preventDefault();
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                e.target.closest('form').submit();
            }
        });
    }
</script>
@endpush