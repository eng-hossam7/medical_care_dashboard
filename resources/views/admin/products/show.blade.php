@extends('layouts.admin')

@section('title', $product->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">المنتجات</a></li>
    <li class="breadcrumb-item active">تفاصيل المنتج</li>
@endsection

@section('content')
<div class="row">
    <!-- معلومات المنتج -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <!-- معرض الصور -->
                    <div class="col-md-5">
                        <div class="sticky-top" style="top: 20px;">
                            @if($product->images->count() > 0)
                                <!-- الصورة الرئيسية -->
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_url) }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-fluid rounded" id="mainImage"
                                         style="max-height: 400px; object-fit: contain;">
                                </div>
                                
                                <!-- معرض الصور المصغر -->
                                @if($product->images->count() > 1)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($product->images as $image)
                                        <img src="{{ asset('storage/' . $image->image_url) }}" 
                                             alt="{{ $image->alt_text }}" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                             onclick="document.getElementById('mainImage').src = this.src">
                                    @endforeach
                                </div>
                                @endif
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-image fa-5x mb-3"></i>
                                    <p>لا توجد صور للمنتج</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- معلومات المنتج -->
                    <div class="col-md-7">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h2 class="mb-1">{{ $product->name }}</h2>
                                <div class="text-muted">
                                    <span class="me-3">
                                        <i class="fas fa-barcode me-1"></i> {{ $product->sku }}
                                    </span>
                                    @if($product->category)
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-tag me-1"></i> {{ $product->category->name }}
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
                                        <a class="dropdown-item" href="{{ route('admin.products.edit', $product) }}">
                                            <i class="fas fa-edit me-2"></i> تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#stockModal">
                                            <i class="fas fa-boxes me-2"></i> تحديث المخزون
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#offerModal">
                                            <i class="fas fa-percentage me-2"></i> إضافة عرض
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form id="delete-form" action="{{ route('admin.products.destroy', $product) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="dropdown-item text-danger" 
                                                    onclick="confirmDelete(event, 'delete-form')">
                                                <i class="fas fa-trash me-2"></i> حذف
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- حالة المنتج -->
                        <div class="mb-4">
                            <div class="d-flex gap-2 mb-2">
                                @if($product->is_active)
                                    <span class="badge bg-success badge-custom">
                                        <i class="fas fa-check me-1"></i> نشط
                                    </span>
                                @else
                                    <span class="badge bg-secondary badge-custom">
                                        <i class="fas fa-times me-1"></i> معطل
                                    </span>
                                @endif
                                
                                @if($product->is_featured)
                                    <span class="badge bg-warning badge-custom">
                                        <i class="fas fa-star me-1"></i> مميز
                                    </span>
                                @endif
                                
                                @if($product->is_prescription_required)
                                    <span class="badge bg-info badge-custom">
                                        <i class="fas fa-prescription-bottle-alt me-1"></i> يحتاج وصفة
                                    </span>
                                @endif
                                
                                @if($product->quantity == 0)
                                    <span class="badge bg-danger badge-custom">
                                        <i class="fas fa-times-circle me-1"></i> نفذ من المخزون
                                    </span>
                                @elseif($product->quantity <= $product->low_stock_threshold)
                                    <span class="badge bg-warning badge-custom">
                                        <i class="fas fa-exclamation-triangle me-1"></i> مخزون منخفض
                                    </span>
                                @else
                                    <span class="badge bg-success badge-custom">
                                        <i class="fas fa-check-circle me-1"></i> متوفر
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- الأسعار -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-muted small mb-1">السعر العادي</div>
                                        <div class="h4 text-dark">{{ number_format($product->regular_price) }} <small>ر.س</small></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center bg-light">
                                        <div class="text-muted small mb-1">سعر البيع</div>
                                        @if($product->sale_price)
                                            <div class="h4 text-danger">{{ number_format($product->sale_price) }} <small>ر.س</small></div>
                                            <div class="text-muted small text-decoration-line-through">{{ number_format($product->regular_price) }} ر.س</div>
                                        @else
                                            <div class="h4 text-secondary">- - -</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="border rounded p-3 text-center">
                                        <div class="text-muted small mb-1">سعر التكلفة</div>
                                        @if($product->cost_price)
                                            <div class="h4 text-dark">{{ number_format($product->cost_price) }} <small>ر.س</small></div>
                                            @if($product->sale_price)
                                                @php
                                                    $profit = $product->sale_price - $product->cost_price;
                                                    $margin = $product->cost_price > 0 ? ($profit / $product->cost_price) * 100 : 0;
                                                @endphp
                                                <div class="text-success small">+{{ number_format($profit) }} ر.س ({{ round($margin, 1) }}%)</div>
                                            @endif
                                        @else
                                            <div class="h4 text-secondary">- - -</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- المخزون -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-boxes me-2"></i> المخزون</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">الكمية المتاحة:</span>
                                        <span class="fw-bold">{{ $product->quantity }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">حد الإنذار:</span>
                                        <span class="fw-bold">{{ $product->low_stock_threshold }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">الكمية المباعة:</span>
                                        <span class="fw-bold">{{ $stats['total_sold'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">عدد الطلبات:</span>
                                        <span class="fw-bold">{{ $stats['total_orders'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- الوصف -->
                        <div class="mb-4">
                            <h5 class="mb-3"><i class="fas fa-align-left me-2"></i> الوصف</h5>
                            @if($product->short_description)
                                <p class="text-muted mb-3">{{ $product->short_description }}</p>
                            @endif
                            
                            @if($product->description)
                                <div class="border rounded p-3 bg-light">
                                    {!! $product->description !!}
                                </div>
                            @else
                                <p class="text-muted text-center py-3">لا يوجد وصف تفصيلي</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- إحصائيات المنتج -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> إحصائيات المنتج</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon primary mb-2">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-number">{{ $stats['total_sold'] ?? 0 }}</div>
                        <div class="stat-label">الكمية المباعة</div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon success mb-2">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-number">{{ number_format($stats['total_revenue'] ?? 0) }}</div>
                        <div class="stat-label">الإيرادات (ر.س)</div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon warning mb-2">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-number">{{ $product->wishlists()->count() }}</div>
                        <div class="stat-label">في المفضلة</div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-3">
                        <div class="stat-icon danger mb-2">
                            <i class="fas fa-comment-alt"></i>
                        </div>
                        <div class="stat-number">{{ $product->reviews()->count() }}</div>
                        <div class="stat-label">التقييمات</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- آخر الطلبات -->
        @if($product->orderItems->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i> آخر الطلبات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>الكمية</th>
                                <th>المبلغ</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->orderItems->sortByDesc('created_at')->take(5) as $item)
                            <tr>
                                <td>
                                    <a href="#" class="text-primary">
                                        {{ $item->order->order_number }}
                                    </a>
                                </td>
                                <td>{{ $item->order->user->name ?? 'زائر' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->total_price) }} ر.س</td>
                                <td>{{ $item->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <!-- الجانب الأيمن -->
    <div class="col-lg-4">
        <!-- معلومات إضافية -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات إضافية</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">الوزن:</span>
                        <span class="fw-bold">{{ $product->weight ? $product->weight . ' كجم' : 'غير محدد' }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">الأبعاد:</span>
                        <span class="fw-bold">{{ $product->dimensions ?? 'غير محددة' }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">الشركة المصنعة:</span>
                        <span class="fw-bold">{{ $product->manufacturer ?? 'غير محددة' }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">فئة الضريبة:</span>
                        <span class="fw-bold">{{ $product->tax_class ?? 'غير محدد' }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">تاريخ الإنشاء:</span>
                        <span class="fw-bold">{{ $product->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">آخر تحديث:</span>
                        <span class="fw-bold">{{ $product->updated_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- العروض الحالية -->
        @if($product->offers->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-percentage me-2"></i> العروض الحالية</h5>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#offerModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @foreach($product->offers as $offer)
                    <div class="alert alert-{{ $offer->is_active && $offer->start_date <= now() && $offer->end_date >= now() ? 'success' : 'secondary' }} mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $offer->offer_type == 'percentage' ? $offer->discount_value . '%' : $offer->discount_value . ' ر.س' }}</strong>
                                @if($offer->min_quantity > 1)
                                    <div class="small">الحد الأدنى: {{ $offer->min_quantity }}</div>
                                @endif
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <button class="dropdown-item" onclick="toggleOfferStatus({{ $offer->id }})">
                                            {{ $offer->is_active ? 'تعطيل' : 'تفعيل' }}
                                        </button>
                                    </li>
                                    <li>
                                        <button class="dropdown-item text-danger" onclick="deleteOffer({{ $offer->id }})">
                                            <i class="fas fa-trash me-2"></i> حذف
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="small mt-2">
                            <i class="far fa-calendar me-1"></i> {{ $offer->start_date->format('Y-m-d') }} إلى {{ $offer->end_date->format('Y-m-d') }}
                        </div>
                        <div class="small">
                            @if($offer->start_date > now())
                                <span class="badge bg-info">قادم</span>
                            @elseif($offer->end_date < now())
                                <span class="badge bg-secondary">منتهي</span>
                            @else
                                <span class="badge bg-success">نشط</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- التقييمات -->
        @if($product->reviews->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-star me-2"></i> آخر التقييمات</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h1 mb-2">{{ number_format($stats['average_rating'] ?? 0, 1) }}</div>
                    <div class="text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($stats['average_rating'] ?? 0))
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= ($stats['average_rating'] ?? 0))
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="text-muted">من {{ $product->reviews()->count() }} تقييم</div>
                </div>
                
                @foreach($product->reviews()->latest()->take(3)->get() as $review)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <strong>{{ $review->user->name ?? 'مستخدم' }}</strong>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="mb-1">{{ $review->title }}</p>
                        <p class="small text-muted mb-0">{{ Str::limit($review->comment, 100) }}</p>
                        <div class="small text-muted mt-1">{{ $review->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
                
                @if($product->reviews->count() > 3)
                    <div class="text-center">
                        <a href="#" class="btn btn-sm btn-outline-primary">عرض جميع التقييمات</a>
                    </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- إجراءات سريعة -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i> إجراءات سريعة</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary-custom btn-custom">
                        <i class="fas fa-edit me-2"></i> تعديل المنتج
                    </a>
                    <button class="btn btn-outline-primary btn-custom" data-bs-toggle="modal" data-bs-target="#stockModal">
                        <i class="fas fa-boxes me-2"></i> تحديث المخزون
                    </button>
                    <button class="btn btn-outline-warning btn-custom" data-bs-toggle="modal" data-bs-target="#offerModal">
                        <i class="fas fa-percentage me-2"></i> إضافة عرض
                    </button>
                    <button onclick="toggleStatus('{{ route('admin.products.toggle-status', $product) }}', this)" 
                            class="btn btn-custom {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}">
                        {{ $product->is_active ? '<i class="fas fa-check me-2"></i> مفعل' : '<i class="fas fa-times me-2"></i> معطل' }}
                    </button>
                    <button onclick="toggleFeatured('{{ route('admin.products.toggle-featured', $product) }}', this)" 
                            class="btn btn-custom {{ $product->is_featured ? 'btn-warning' : 'btn-outline-warning' }}">
                        {{ $product->is_featured ? '<i class="fas fa-star me-2"></i> مميز' : '<i class="far fa-star me-2"></i> تمييز' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تحديث المخزون -->
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحديث المخزون</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockForm" method="POST" action="{{ route('admin.products.update-stock', $product) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المنتج</label>
                        <input type="text" class="form-control" value="{{ $product->name }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المخزون الحالي</label>
                        <input type="text" class="form-control" value="{{ $product->quantity }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع التحديث <span class="text-danger">*</span></label>
                        <select name="adjustment_type" class="form-select" required>
                            <option value="set">تعيين قيمة جديدة</option>
                            <option value="increment">زيادة</option>
                            <option value="decrement">نقصان</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكمية <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">السبب (اختياري)</label>
                        <textarea name="reason" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary-custom btn-custom">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal إضافة عرض -->
<div class="modal fade" id="offerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة عرض جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.products.offers.store', $product) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نوع العرض <span class="text-danger">*</span></label>
                        <select name="offer_type" class="form-select" required>
                            <option value="percentage">نسبة مئوية (%)</option>
                            <option value="fixed">مبلغ ثابت (ر.س)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">قيمة الخصم <span class="text-danger">*</span></label>
                        <input type="number" name="discount_value" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحد الأدنى للكمية</label>
                        <input type="number" name="min_quantity" class="form-control" min="1" value="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ البداية <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاريخ النهاية <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary-custom btn-custom">إضافة العرض</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // تحديث حالة العرض
    function toggleOfferStatus(offerId) {
        const url = "{{ route('admin.products.offers.toggle-status', ':id') }}".replace(':id', offerId);
        
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
                Swal.fire({
                    icon: 'success',
                    title: 'تم!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    }
    
    // حذف العرض
    function deleteOffer(offerId) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const url = "{{ route('admin.products.offers.destroy', ':id') }}".replace(':id', offerId);
                
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف!',
                            text: 'تم حذف العرض بنجاح.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                });
            }
        });
    }
</script>
@endpush