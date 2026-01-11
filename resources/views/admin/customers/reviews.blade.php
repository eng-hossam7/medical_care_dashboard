@extends('layouts.admin')

@section('title', 'تقييمات العميل: ' . ($customer->full_name ?? $customer->user->name))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">العملاء</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer) }}">{{ Str::limit($customer->full_name ?? $customer->user->name, 20) }}</a></li>
    <li class="breadcrumb-item active">تقييمات العميل</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-0">تقييمات العميل</h2>
        <p class="text-muted mb-0">جميع تقييمات {{ $customer->full_name ?? $customer->user->name }}</p>
    </div>
    <div class="col-md-6 text-start">
        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-secondary-modern btn-modern">
            <i class="fas fa-arrow-right me-2"></i> العودة للعميل
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card-modern">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h5>
                    <i class="fas fa-star me-2"></i> تقييمات العميل
                    <span class="badge-modern badge-primary-modern ms-2">{{ $reviews->total() }}</span>
                </h5>
            </div>
            
            <div class="card-body-modern">
                @if($reviews->count() > 0)
                    @foreach($reviews as $review)
                    <div class="border-bottom pb-4 mb-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                @if($review->product->images->first())
                                    <img src="{{ asset('storage/' . $review->product->images->first()->image_url) }}" 
                                         alt="{{ $review->product->name }}" 
                                         class="rounded me-3" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $review->product->name }}</h6>
                                    <div class="text-muted small">{{ $review->product->sku }}</div>
                                </div>
                            </div>
                            <div class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="mb-2">{{ $review->title }}</h6>
                            <p class="mb-3">{{ $review->comment }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="far fa-calendar me-1"></i> {{ $review->created_at->format('Y-m-d H:i') }}
                                </div>
                                <div>
                                    @if($review->is_approved)
                                        <span class="badge-modern badge-success-modern">معتمد</span>
                                    @else
                                        <span class="badge-modern badge-warning-modern">قيد المراجعة</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <!-- Pagination -->
                    @if($reviews->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            عرض {{ $reviews->firstItem() }} إلى {{ $reviews->lastItem() }} من إجمالي {{ $reviews->total() }} تقييم
                        </div>
                        <nav>
                            <ul class="pagination-modern">
                                {{ $reviews->links('vendor.pagination.bootstrap-4') }}
                            </ul>
                        </nav>
                    </div>
                    @endif
                    
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-star fa-4x text-muted"></i>
                        </div>
                        <h4 class="text-muted mb-3">لا توجد تقييمات</h4>
                        <p class="text-muted mb-4">لم يقم هذا العميل بتقييم أي منتج بعد.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- إحصائيات التقييمات -->
        <div class="card-modern mb-4">
            <div class="card-header-modern">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i> إحصائيات التقييمات</h6>
            </div>
            <div class="card-body-modern">
                <div class="text-center mb-4">
                    @php
                        $averageRating = $reviews->avg('rating');
                        $totalReviews = $reviews->count();
                        $approvedReviews = $reviews->where('is_approved', true)->count();
                    @endphp
                    <div class="h1 mb-2">{{ number_format($averageRating, 1) }}</div>
                    <div class="text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($averageRating))
                                <i class="fas fa-star"></i>
                            @elseif($i - 0.5 <= $averageRating)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="text-muted">متوسط التقييمات</div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">إجمالي التقييمات:</span>
                        <span>{{ $totalReviews }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">المعتمدة:</span>
                        <span>{{ $approvedReviews }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">قيد المراجعة:</span>
                        <span>{{ $totalReviews - $approvedReviews }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- توزيع التقييمات -->
        <div class="card-modern">
            <div class="card-header-modern">
                <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i> توزيع التقييمات</h6>
            </div>
            <div class="card-body-modern">
                <canvas id="ratingsChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // توزيع التقييمات
    const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
    const ratingsChart = new Chart(ratingsCtx, {
        type: 'pie',
        data: {
            labels: ['5 نجوم', '4 نجوم', '3 نجوم', '2 نجوم', '1 نجمة'],
            datasets: [{
                data: [
                    {{ $reviews->where('rating', 5)->count() }},
                    {{ $reviews->where('rating', 4)->count() }},
                    {{ $reviews->where('rating', 3)->count() }},
                    {{ $reviews->where('rating', 2)->count() }},
                    {{ $reviews->where('rating', 1)->count() }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#20c997',
                    '#ffc107',
                    '#fd7e14',
                    '#dc3545'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
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
</script>
@endpush