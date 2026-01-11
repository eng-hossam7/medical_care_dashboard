<div class="category-item" data-id="{{ $category->id }}">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="category-handle me-3">
                <i class="fas fa-arrows-alt"></i>
            </span>
            
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" 
                     alt="{{ $category->name }}" 
                     class="rounded me-3" 
                     style="width: 40px; height: 40px; object-fit: cover;">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                     style="width: 40px; height: 40px;">
                    <i class="fas fa-folder text-secondary"></i>
                </div>
            @endif
            
            <div>
                <h6 class="mb-0">{{ $category->name }}</h6>
                <small class="text-muted">{{ $category->slug }}</small>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-info category-badge">
                {{ $category->products_count }} منتج
            </span>
            
            @if($category->is_active)
                <span class="badge bg-success category-badge">نشط</span>
            @else
                <span class="badge bg-secondary category-badge">معطل</span>
            @endif
            
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.categories.show', $category) }}">
                            <i class="fas fa-eye me-2"></i> عرض
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.categories.edit', $category) }}">
                            <i class="fas fa-edit me-2"></i> تعديل
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}">
                            <i class="fas fa-plus me-2"></i> إضافة فرعي
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button onclick="toggleCategoryStatus('{{ route('admin.categories.toggle-status', $category) }}', this)" 
                                class="dropdown-item">
                            {{ $category->is_active ? '<i class="fas fa-times me-2"></i> تعطيل' : '<i class="fas fa-check me-2"></i> تفعيل' }}
                        </button>
                    </li>
                    <li>
                        <form id="delete-form-{{ $category->id }}" 
                              action="{{ route('admin.categories.destroy', $category) }}" 
                              method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="dropdown-item text-danger" 
                                    onclick="confirmDelete(event, 'delete-form-{{ $category->id }}')">
                                <i class="fas fa-trash me-2"></i> حذف
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    @if($category->children->count() > 0)
        <div class="category-children">
            @foreach($category->children as $child)
                @include('admin.categories.partials.category-item', ['category' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>