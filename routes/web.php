<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\AddressController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// الصفحة الرئيسية
Route::get('/', function () {
    return view('welcome');
});

// تسجيل الدخول
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// تسجيل الخروج
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// التسجيل (اختياري)
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// مجموعة مسارات لوحة التحكم (تحتاج لتسجيل الدخول)
Route::middleware(['auth'])->group(function () {
    
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // مسارات المنتجات
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
            
            // المسارات الإضافية
            Route::get('/trashed', [ProductController::class, 'trashed'])->name('trashed');
            Route::post('/{product}/restore', [ProductController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('force-delete');
            
            // AJAX Routes
            Route::post('/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('toggle-featured');
            Route::post('/{product}/update-stock', [ProductController::class, 'updateStock'])->name('update-stock');
            
            // العروض
            Route::post('/{product}/offers', [ProductController::class, 'createOffer'])->name('offers.store');
            Route::post('/offers/{offer}/toggle-status', [ProductController::class, 'toggleOfferStatus'])->name('offers.toggle-status');
            Route::delete('/offers/{offer}', [ProductController::class, 'deleteOffer'])->name('offers.destroy');
            
            // الاستيراد والتصدير
            Route::get('/export', [ProductController::class, 'export'])->name('export');
            Route::post('/import', [ProductController::class, 'import'])->name('import');
            
            // البحث
            Route::get('/search', [ProductController::class, 'search'])->name('search');
            Route::get('/{id}/info', [ProductController::class, 'getProductInfo'])->name('info');
        });
        
        // مسارات التصنيفات
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
            
            // AJAX Routes
            Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/update-order', [CategoryController::class, 'updateOrder'])->name('update-order');
        });

        // مسارات الطلبات
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/create', [OrderController::class, 'create'])->name('create');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::get('/{order}/edit', [OrderController::class, 'edit'])->name('edit');
            Route::put('/{order}', [OrderController::class, 'update'])->name('update');
            Route::delete('/{order}', [OrderController::class, 'destroy'])->name('destroy');
            
            // مسارات إضافية
            Route::post('/{order}/update-status', [OrderController::class, 'updateStatus'])->name('update-status');
            Route::post('/{order}/add-note', [OrderController::class, 'addNote'])->name('add-note');
            Route::get('/{order}/print', [OrderController::class, 'printInvoice'])->name('print');
            Route::get('/export', [OrderController::class, 'export'])->name('export');
            
            // AJAX Routes
            Route::get('/customer/{customerId}/addresses', [OrderController::class, 'getCustomerAddresses'])->name('customer.addresses');
            Route::get('/product/{productId}/details', [OrderController::class, 'getProductDetails'])->name('product.details');
            Route::get('/statistics', [OrderController::class, 'getStatistics'])->name('statistics');

            Route::get('/customer/{customerId}/addresses', [OrderController::class, 'getCustomerAddresses'])
         ->name('customer.addresses');
        });

        // مسارات العملاء
        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
            Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
            Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
            Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
            
            // مسارات إضافية
            Route::post('/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{customer}/add-note', [CustomerController::class, 'addNote'])->name('add-note');
            Route::get('/{customer}/orders', [CustomerController::class, 'orders'])->name('orders');
            // إزالة هذا السطر لأنه يسبب تعارضاً
            // Route::get('/{customer}/addresses', [CustomerController::class, 'addresses'])->name('addresses');
            Route::get('/{customer}/reviews', [CustomerController::class, 'reviews'])->name('reviews');
            Route::get('/export', [CustomerController::class, 'export'])->name('export');
            Route::get('/statistics', [CustomerController::class, 'getStatistics'])->name('statistics');
            
            // **هذا هو المكان الصحيح لروتات العناوين - داخل مجموعة customers**
            Route::prefix('{customer}/addresses')->name('addresses.')->group(function () {
                Route::get('/', [AddressController::class, 'index'])->name('index');
                Route::get('/create', [AddressController::class, 'create'])->name('create');
                Route::post('/', [AddressController::class, 'store'])->name('store');
                Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
                Route::put('/{address}', [AddressController::class, 'update'])->name('update');
                Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
                Route::post('/{address}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
            });
        });

        // Home redirect
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
    });
});

// إذا حاول الدخول إلى لوحة التحكم دون تسجيل دخول
Route::get('/admin', function () {
    return redirect()->route('login');
});

// الصفحة الرئيسية بعد التسجيل
Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->type == 'admin' || auth()->user()->type == 'employee') {
            return redirect()->route('admin.dashboard');
        }
        return redirect('/');
    }
    return redirect()->route('login');
})->name('home');