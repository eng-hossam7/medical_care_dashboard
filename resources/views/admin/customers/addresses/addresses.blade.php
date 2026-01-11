@extends('layouts.admin')

@section('title', 'عناوين العميل: ' . ($customer->full_name ?? $customer->user->name))
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">العملاء</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.customers.show', $customer) }}">{{ Str::limit($customer->full_name ?? $customer->user->name, 20) }}</a></li>
    <li class="breadcrumb-item active">عناوين العميل</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2 class="mb-0">عناوين العميل</h2>
        <p class="text-muted mb-0">جميع عناوين {{ $customer->full_name ?? $customer->user->name }}</p>
    </div>
    <div class="col-md-6 text-start">
        <a href="{{ route('admin.customers.addresses.create', $customer) }}" class="btn btn-primary-modern me-2">
            <i class="fas fa-plus me-2"></i> إضافة عنوان جديد
        </a>
        <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-secondary-modern btn-modern">
            <i class="fas fa-arrow-right me-2"></i> العودة للعميل
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success-modern alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="row">
    @forelse($addresses as $address)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card-modern h-100">
            <div class="card-header-modern d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i> {{ $address->address_type_arabic }}
                    @if($address->is_default)
                        <span class="badge-modern badge-success-modern ms-2">افتراضي</span>
                    @endif
                </h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary-modern dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-custom">
                        <li>
                            <a class="dropdown-item dropdown-item-custom" href="{{ route('admin.customers.addresses.edit', [$customer, $address]) }}">
                                <i class="fas fa-edit me-2"></i> تعديل
                            </a>
                        </li>
                        @if(!$address->is_default)
                        <li>
                            <form action="{{ route('admin.customers.addresses.set-default', [$customer, $address]) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item dropdown-item-custom">
                                    <i class="fas fa-star me-2"></i> تعيين كافتراضي
                                </button>
                            </form>
                        </li>
                        @endif
                        <li>
                            <form action="{{ route('admin.customers.addresses.destroy', [$customer, $address]) }}" method="POST" 
                                  class="d-inline" 
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا العنوان؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item dropdown-item-custom text-danger">
                                    <i class="fas fa-trash me-2"></i> حذف
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body-modern">
                <div class="mb-3">
                    <strong>{{ $address->full_name }}</strong>
                    <div class="text-muted small">{{ $address->phone }}</div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-1">{{ $address->address_line_1 }}</p>
                    @if($address->address_line_2)
                        <p class="mb-1">{{ $address->address_line_2 }}</p>
                    @endif
                    <p class="mb-1">
                        {{ $address->city }}, {{ $address->state }}
                    </p>
                    <p class="mb-0">
                        {{ $address->country }} - {{ $address->postal_code }}
                    </p>
                </div>
                
                <div class="text-muted small">
                    <i class="far fa-calendar me-1"></i> 
                    أضيف في {{ $address->created_at ? $address->created_at->format('Y-m-d') : 'غير محدد' }}
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card-modern">
            <div class="card-body-modern text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-map-marker-alt fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">لا توجد عناوين</h4>
                <p class="text-muted mb-4">لم يضف هذا العميل أي عناوين بعد.</p>
                <a href="{{ route('admin.customers.addresses.create', $customer) }}" class="btn btn-primary-modern">
                    <i class="fas fa-plus me-2"></i> إضافة عنوان جديد
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($addresses->hasPages())
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-center">
            {{ $addresses->links() }}
        </div>
    </div>
</div>
@endif
@endsection