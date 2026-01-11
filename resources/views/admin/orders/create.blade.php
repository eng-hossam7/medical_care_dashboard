@extends('layouts.admin')

@section('title', 'إنشاء طلب جديد')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">الطلبات</a></li>
    <li class="breadcrumb-item active">إنشاء طلب جديد</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card-modern">
            <div class="card-header-modern">
                <h5><i class="fas fa-plus me-2"></i> إنشاء طلب جديد</h5>
            </div>
            
            <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
                @csrf
                <div class="card-body-modern">
                    <!-- رسائل الخطأ -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle me-2"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <!-- تحذير إذا لم تكن عناوين متاحة -->
                    <div class="alert alert-info mb-4 d-none" id="addressWarning">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="addressMessage"></span>
                    </div>
                    
                    <div class="row">
                        <!-- اختيار العميل -->
                        <div class="col-lg-4 mb-4">
                            <div class="card-modern">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i> اختيار العميل</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="form-group-modern mb-4">
                                        <label class="form-label-modern">العملاء <span class="text-danger">*</span></label>
                                        <select name="user_id" id="userSelect" class="form-select-modern" required>
                                            <option value="">اختر عميل</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->user_id }}" 
                                                        data-email="{{ $customer->user->email }}"
                                                        data-phone="{{ $customer->user->phone }}">
                                                    {{ $customer->user->name }} - {{ $customer->user->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div id="customerInfo" class="d-none">
                                        <div class="border rounded p-3 mb-3">
                                            <h6>معلومات العميل</h6>
                                            <div class="mb-2">
                                                <strong>البريد الإلكتروني:</strong>
                                                <span id="customerEmail" class="text-muted"></span>
                                            </div>
                                            <div class="mb-2">
                                                <strong>الهاتف:</strong>
                                                <span id="customerPhone" class="text-muted"></span>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label-modern">عنوان الشحن <span class="text-danger">*</span></label>
                                            <select name="shipping_address_id" id="shippingAddress" class="form-select-modern" required disabled>
                                                <option value="">اختر عنوان الشحن</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label-modern">عنوان الفاتورة <span class="text-danger">*</span></label>
                                            <select name="billing_address_id" id="billingAddress" class="form-select-modern" required disabled>
                                                <option value="">اختر عنوان الفاتورة</option>
                                            </select>
                                        </div>
                                        
                                        <!-- زر لإضافة عنوان جديد -->
                                        <div class="mb-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary w-100" 
                                                    onclick="openNewAddressModal()">
                                                <i class="fas fa-plus me-1"></i> إضافة عنوان جديد
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ملخص الطلب -->
                            <div class="card-modern mt-4">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-receipt me-2"></i> ملخص الطلب</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="text-center mb-3">
                                        <div class="h4 text-primary" id="previewTotal">0.00 ر.س</div>
                                        <div class="text-muted small">الإجمالي النهائي</div>
                                    </div>
                                    <div class="small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>المنتجات:</span>
                                            <span id="previewSubtotal">0.00 ر.س</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>الشحن:</span>
                                            <span id="previewShipping">0.00 ر.س</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>الضريبة (15%):</span>
                                            <span id="previewTax">0.00 ر.س</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- إضافة المنتجات -->
                        <div class="col-lg-8">
                            <div class="card-modern mb-4">
                                <div class="card-header-modern bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0"><i class="fas fa-boxes me-2"></i> إضافة المنتجات</h6>
                                    <span class="badge-modern badge-primary-modern" id="productsCount">0 منتج</span>
                                </div>
                                <div class="card-body-modern">
                                    <div class="mb-4">
                                        <div class="input-group">
                                            <select id="productSelect" class="form-select-modern">
                                                <option value="">اختر منتج لإضافته</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}"
                                                            data-name="{{ $product->name }}"
                                                            data-price="{{ $product->final_price }}"
                                                            data-stock="{{ $product->quantity }}"
                                                            data-sku="{{ $product->sku }}">
                                                        {{ $product->name }} - {{ number_format($product->final_price, 2) }} ر.س (المخزون: {{ $product->quantity }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-primary-modern btn-modern" onclick="addProduct()">
                                                <i class="fas fa-plus"></i> إضافة
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-modern" id="productsTable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40%">المنتج</th>
                                                    <th style="width: 15%">السعر</th>
                                                    <th style="width: 20%">الكمية</th>
                                                    <th style="width: 15%">الإجمالي</th>
                                                    <th style="width: 10%">إجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- سيتم إضافة المنتجات هنا -->
                                                <tr id="noProducts" class="text-center">
                                                    <td colspan="5" class="py-4">
                                                        <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                                        <p class="text-muted mb-0">لم تقم بإضافة أي منتجات بعد</p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>المجموع الفرعي:</strong></td>
                                                    <td><span id="subtotal">0.00</span> ر.س</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>تكلفة الشحن:</strong></td>
                                                    <td>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" name="shipping_cost" 
                                                                   class="form-control form-control-sm" 
                                                                   value="0" min="0" step="0.01" 
                                                                   style="width: 100px;" 
                                                                   onchange="calculateTotal()">
                                                            <span class="input-group-text">ر.س</span>
                                                        </div>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-end"><strong>الضريبة (15%):</strong></td>
                                                    <td><span id="tax">0.00</span> ر.س</td>
                                                    <td></td>
                                                </tr>
                                                <tr class="table-active">
                                                    <td colspan="3" class="text-end"><strong>الإجمالي النهائي:</strong></td>
                                                    <td class="text-success fw-bold"><span id="total">0.00</span> ر.س</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ملاحظات -->
                            <div class="card-modern">
                                <div class="card-header-modern bg-light">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i> ملاحظات إضافية</h6>
                                </div>
                                <div class="card-body-modern">
                                    <div class="form-group-modern">
                                        <textarea name="notes" class="form-control-modern" rows="4" placeholder="أي ملاحظات إضافية للطلب..."></textarea>
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
                        <button type="submit" class="btn btn-primary-modern btn-modern" id="submitBtn">
                            <i class="fas fa-save me-2"></i> إنشاء الطلب
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- مودال لإضافة عنوان جديد -->
<div class="modal fade" id="newAddressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إضافة عنوان جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newAddressForm">
                    <div class="mb-3">
                        <label class="form-label">نوع العنوان <span class="text-danger">*</span></label>
                        <select name="address_type" class="form-select" required>
                            <option value="shipping">عنوان الشحن</option>
                            <option value="billing">عنوان الفاتورة</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الأول <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الأخير <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الهاتف <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان الرئيسي <span class="text-danger">*</span></label>
                        <input type="text" name="address_line_1" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">العنوان الثانوي</label>
                        <input type="text" name="address_line_2" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المدينة <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المنطقة</label>
                            <input type="text" name="state" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الرمز البريدي</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">البلد <span class="text-danger">*</span></label>
                            <input type="text" name="country" class="form-control" value="السعودية" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1">
                            <label class="form-check-label">تعيين كعنوان افتراضي</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-primary" onclick="saveNewAddress()">حفظ العنوان</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let products = [];
    let productCounter = 0;
    let currentUserId = null;
    
    // عند اختيار عميل
    document.getElementById('userSelect').addEventListener('change', function() {
        const customerInfo = document.getElementById('customerInfo');
        const selectedOption = this.options[this.selectedIndex];
        currentUserId = this.value;
        
        if (this.value) {
            customerInfo.classList.remove('d-none');
            document.getElementById('customerEmail').textContent = selectedOption.dataset.email;
            document.getElementById('customerPhone').textContent = selectedOption.dataset.phone;
            
            // تحميل عناوين العميل
            loadCustomerAddresses(this.value);
        } else {
            customerInfo.classList.add('d-none');
            document.getElementById('shippingAddress').disabled = true;
            document.getElementById('billingAddress').disabled = true;
        }
    });
    
    // تحميل عناوين العميل - إصدار محسّن
    // تحميل عناوين العميل
    function loadCustomerAddresses(userId) {
        console.log('جاري تحميل عناوين العميل:', userId);
        
        const shippingSelect = document.getElementById('shippingAddress');
        const billingSelect = document.getElementById('billingAddress');
        
        // مسار بديل للاختبار
        const url = `/admin/orders/customer/${userId}/addresses`;
        console.log('URL الكامل:', url);
        
        // عرض مؤشر التحميل
        shippingSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        billingSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        
        fetch(url)
            .then(response => {
                console.log('حالة الرد:', response.status, response.statusText);
                console.log('رأسيات الرد:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`خطأ HTTP: ${response.status} ${response.statusText}`);
                }
                
                // تحقق من نوع المحتوى
                const contentType = response.headers.get('content-type');
                console.log('نوع المحتوى:', contentType);
                
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('الرد ليس JSON');
                }
                
                return response.json();
            })
            .then(data => {
                console.log('البيانات المستلمة:', data);
                
                shippingSelect.innerHTML = '<option value="">اختر عنوان الشحن</option>';
                billingSelect.innerHTML = '<option value="">اختر عنوان الفاتورة</option>';
                
                if (data.success && data.addresses && data.addresses.length > 0) {
                    data.addresses.forEach(address => {
                        const optionText = `${address.first_name} ${address.last_name} - ${address.address_line_1}, ${address.city}`;
                        
                        shippingSelect.innerHTML += `<option value="${address.id}">${optionText}</option>`;
                        billingSelect.innerHTML += `<option value="${address.id}">${optionText}</option>`;
                    });
                    
                    shippingSelect.disabled = false;
                    billingSelect.disabled = false;
                    
                    console.log(`تم تحميل ${data.addresses.length} عنوان بنجاح`);
                } else {
                    shippingSelect.innerHTML = '<option value="">لا توجد عناوين</option>';
                    billingSelect.innerHTML = '<option value="">لا توجد عناوين</option>';
                    console.log('لا توجد عناوين أو بيانات غير صحيحة');
                }
            })
            .catch(error => {
                console.error('خطأ في التحميل:', error);
                
                // عرض معلومات أكثر عن الخطأ
                shippingSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
                billingSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
                
                // إظهار رسالة توضيحية
                alert('تعذر تحميل العناوين. الرجاء:\n1. التحقق من اتصال الإنترنت\n2. تحديث الصفحة\n3. التحقق من Console للمزيد من المعلومات');
            });
    }
    
    // فتح مودال إضافة عنوان جديد
    function openNewAddressModal() {
        if (!currentUserId) {
            alert('الرجاء اختيار عميل أولاً');
            return;
        }
        $('#newAddressModal').modal('show');
    }
    
    // حفظ عنوان جديد
    function saveNewAddress() {
        const form = document.getElementById('newAddressForm');
        const formData = new FormData(form);
        
        // إضافة user_id
        formData.append('user_id', currentUserId);
        
        fetch('/admin/addresses', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تمت إضافة العنوان بنجاح');
                $('#newAddressModal').modal('hide');
                form.reset();
                
                // إعادة تحميل العناوين
                loadCustomerAddresses(currentUserId);
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في إضافة العنوان');
        });
    }
    
    // إضافة منتج
    function addProduct() {
        const productSelect = document.getElementById('productSelect');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        
        if (!selectedOption.value) {
            alert('يرجى اختيار منتج');
            return;
        }
        
        const productId = selectedOption.value;
        const productName = selectedOption.dataset.name;
        const productPrice = parseFloat(selectedOption.dataset.price);
        const productStock = parseInt(selectedOption.dataset.stock);
        const productSku = selectedOption.dataset.sku;
        
        // التحقق من المخزون
        if (productStock <= 0) {
            alert('هذا المنتج غير متوفر في المخزون');
            return;
        }
        
        // التحقق من وجود المنتج مسبقاً
        const existingProduct = products.find(p => p.id == productId);
        if (existingProduct) {
            // التحقق من أن الكمية لا تتجاوز المخزون
            if (existingProduct.quantity >= productStock) {
                alert('لا يمكن إضافة المزيد، تم الوصول إلى الحد الأقصى للمخزون');
                return;
            }
            
            // زيادة الكمية
            existingProduct.quantity += 1;
            updateProductRow(existingProduct);
        } else {
            // إضافة منتج جديد
            const product = {
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1,
                stock: productStock,
                sku: productSku,
                counter: productCounter++
            };
            
            products.push(product);
            addProductRow(product);
            
            // إخفاء رسالة "لا توجد منتجات"
            document.getElementById('noProducts').style.display = 'none';
        }
        
        calculateTotal();
        updateProductsCount();
        productSelect.selectedIndex = 0;
    }
    
    // إضافة صف منتج
    function addProductRow(product) {
        const tbody = document.querySelector('#productsTable tbody');
        const row = document.createElement('tr');
        row.id = `product-${product.counter}`;
        row.innerHTML = `
            <td>
                <input type="hidden" name="items[${product.counter}][product_id]" value="${product.id}">
                <input type="hidden" name="items[${product.counter}][product_name]" value="${product.name}">
                <div class="d-flex align-items-center">
                    <div style="min-width: 60px;" class="me-2">
                        <i class="fas fa-box fa-2x text-muted"></i>
                    </div>
                    <div>
                        <strong class="d-block">${product.name}</strong>
                        <small class="text-muted">${product.sku}</small>
                    </div>
                </div>
            </td>
            <td>
                <input type="hidden" name="items[${product.counter}][unit_price]" value="${product.price}">
                <span class="fw-bold">${product.price.toFixed(2)}</span> ر.س
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="input-group input-group-sm" style="width: 120px;">
                        <button type="button" class="btn btn-outline-secondary" 
                                onclick="updateQuantity(${product.counter}, -1)">-</button>
                        <input type="number" name="items[${product.counter}][quantity]" 
                               class="form-control text-center" 
                               value="${product.quantity}" min="1" max="${product.stock}"
                               onchange="updateProductQuantity(${product.counter}, this.value)">
                        <button type="button" class="btn btn-outline-secondary" 
                                onclick="updateQuantity(${product.counter}, 1)">+</button>
                    </div>
                    <div class="ms-2">
                        <small class="text-muted d-block">المخزون: ${product.stock}</small>
                    </div>
                </div>
            </td>
            <td>
                <input type="hidden" name="items[${product.counter}][total_price]" 
                       value="${product.price * product.quantity}">
                <span class="text-primary fw-bold" id="total-${product.counter}">
                    ${(product.price * product.quantity).toFixed(2)}
                </span> ر.س
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="removeProduct(${product.counter})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }
    
    // تحديث عدد المنتجات
    function updateProductsCount() {
        const totalItems = products.reduce((sum, product) => sum + product.quantity, 0);
        document.getElementById('productsCount').textContent = `${totalItems} منتج`;
    }
    
    // تحديث صف المنتج
    function updateProductRow(product) {
        const row = document.getElementById(`product-${product.counter}`);
        if (row) {
            const quantityInput = row.querySelector(`input[name="items[${product.counter}][quantity]"]`);
            const totalPriceInput = row.querySelector(`input[name="items[${product.counter}][total_price]"]`);
            const totalSpan = document.getElementById(`total-${product.counter}`);
            
            quantityInput.value = product.quantity;
            quantityInput.max = product.stock;
            totalPriceInput.value = product.price * product.quantity;
            totalSpan.textContent = (product.price * product.quantity).toFixed(2);
        }
    }
    
    // تحديث الكمية
    function updateQuantity(counter, change) {
        const product = products.find(p => p.counter == counter);
        if (product) {
            const newQuantity = product.quantity + change;
            if (newQuantity >= 1 && newQuantity <= product.stock) {
                product.quantity = newQuantity;
                updateProductRow(product);
                calculateTotal();
                updateProductsCount();
            }
        }
    }
    
    // تحديث الكمية من الـ input
    function updateProductQuantity(counter, value) {
        const product = products.find(p => p.counter == counter);
        if (product) {
            const newQuantity = parseInt(value);
            if (newQuantity >= 1 && newQuantity <= product.stock) {
                product.quantity = newQuantity;
                updateProductRow(product);
                calculateTotal();
                updateProductsCount();
            } else {
                // إعادة تعيين القيمة
                const row = document.getElementById(`product-${counter}`);
                const input = row.querySelector(`input[name="items[${product.counter}][quantity]"]`);
                input.value = product.quantity;
                
                if (newQuantity > product.stock) {
                    alert(`الحد الأقصى للمخزون هو ${product.stock}`);
                }
            }
        }
    }
    
    // إزالة منتج
    function removeProduct(counter) {
        products = products.filter(p => p.counter != counter);
        const row = document.getElementById(`product-${counter}`);
        if (row) row.remove();
        
        // إذا لم يعد هناك منتجات، أظهر الرسالة
        if (products.length === 0) {
            document.getElementById('noProducts').style.display = '';
        }
        
        calculateTotal();
        updateProductsCount();
    }
    
    // حساب الإجماليات
    function calculateTotal() {
        let subtotal = 0;
        
        products.forEach(product => {
            subtotal += product.price * product.quantity;
        });
        
        const shippingCost = parseFloat(document.querySelector('input[name="shipping_cost"]').value) || 0;
        const tax = subtotal * 0.15; // 15% ضريبة
        const total = subtotal + shippingCost + tax;
        
        // تحديث الجدول
        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('tax').textContent = tax.toFixed(2);
        document.getElementById('total').textContent = total.toFixed(2);
        
        // تحديث الملخص
        document.getElementById('previewSubtotal').textContent = subtotal.toFixed(2) + ' ر.س';
        document.getElementById('previewShipping').textContent = shippingCost.toFixed(2) + ' ر.س';
        document.getElementById('previewTax').textContent = tax.toFixed(2) + ' ر.س';
        document.getElementById('previewTotal').textContent = total.toFixed(2) + ' ر.س';
    }
    
    // التحقق من النموذج قبل الإرسال
    document.getElementById('orderForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        
        // التحقق من وجود منتجات
        if (products.length === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'منتجات مطلوبة',
                text: 'يرجى إضافة منتجات إلى الطلب',
                confirmButtonColor: '#1b7158'
            });
            return;
        }
        
        // التحقق من اختيار عميل
        if (!document.getElementById('userSelect').value) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'عميل مطلوب',
                text: 'يرجى اختيار عميل',
                confirmButtonColor: '#1b7158'
            });
            return;
        }
        
        // التحقق من عناوين الشحن والفاتورة
        const shippingAddress = document.getElementById('shippingAddress');
        const billingAddress = document.getElementById('billingAddress');
        
        if (!shippingAddress.value || shippingAddress.disabled) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'عنوان الشحن مطلوب',
                text: 'يرجى اختيار عنوان الشحن',
                confirmButtonColor: '#1b7158'
            });
            return;
        }
        
        if (!billingAddress.value || billingAddress.disabled) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'عنوان الفاتورة مطلوب',
                text: 'يرجى اختيار عنوان الفاتورة',
                confirmButtonColor: '#1b7158'
            });
            return;
        }
        
        // تغيير حالة الزر
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري إنشاء الطلب...';
    });
    
    // تحديث الإجمالي عند تغيير تكلفة الشحن
    document.querySelector('input[name="shipping_cost"]').addEventListener('input', calculateTotal);
    
    // تحديث عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
        updateProductsCount();
    });
</script>
@endpush