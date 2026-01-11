@extends('layouts.admin')

@section('title', 'تعديل الطلب: ' . $order->order_number)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">الطلبات</a></li>
    <li class="breadcrumb-item active">تعديل الطلب</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card-modern">
            <div class="card-header-modern">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i> تعديل الطلب
                        <small class="text-muted">#{{ $order->order_number }}</small>
                    </h5>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i> عرض
                    </a>
                </div>
            </div>
            
            <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body-modern">
                    @if($order->status !== 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            يمكن تعديل الطلب فقط إذا كان في حالة "قيد الانتظار"
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-group-modern">
                                <label class="form-label-modern">حالة الطلب <span class="text-danger">*</span></label>
                                <select name="status" class="form-select-modern" required>
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>معلق</option>
                                    <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                                    <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                                    <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>تم التوصيل</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                                    <option value="refunded" {{ old('status', $order->status) == 'refunded' ? 'selected' : '' }}>تم الاسترجاع</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="form-group-modern">
                                <label class="form-label-modern">تكلفة الشحن</label>
                                <div class="input-group">
                                    <input type="number" name="shipping_cost" class="form-control-modern" 
                                           step="0.01" min="0" value="{{ old('shipping_cost', $order->shipping_cost) }}">
                                    <span class="input-group-text">ر.س</span>
                                </div>
                            </div>
                        </div>
                        
                        @if($order->status == 'cancelled' || old('status') == 'cancelled')
                        <div class="col-12 mb-4">
                            <div class="form-group-modern">
                                <label class="form-label-modern">سبب الإلغاء <span class="text-danger">*</span></label>
                                <textarea name="cancelled_reason" class="form-control-modern" rows="3" required>{{ old('cancelled_reason', $order->cancelled_reason) }}</textarea>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-12 mb-4">
                            <div class="form-group-modern">
                                <label class="form-label-modern">ملاحظات إضافية</label>
                                <textarea name="notes" class="form-control-modern" rows="4">{{ old('notes', $order->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ملخص الطلب -->
                    <div class="card-modern mb-4">
                        <div class="card-header-modern bg-light">
                            <h6 class="mb-0"><i class="fas fa-receipt me-2"></i> ملخص الطلب الحالي</h6>
                        </div>
                        <div class="card-body-modern">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">رقم الطلب:</span>
                                            <span class="fw-bold">{{ $order->order_number }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">العميل:</span>
                                            <span class="fw-bold">{{ $order->user->name ?? 'زائر' }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">تاريخ الطلب:</span>
                                            <span class="fw-bold">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
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
                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <span>الإجمالي:</span>
                                            <span class="text-success">{{ number_format($order->total) }} ر.س</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer-modern">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary-modern btn-modern">
                            <i class="fas fa-times me-2"></i> إلغاء
                        </a>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary-modern btn-modern">
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