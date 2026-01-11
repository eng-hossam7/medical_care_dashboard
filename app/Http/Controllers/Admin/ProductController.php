<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // إضافة هذا السطر
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * عرض قائمة المنتجات
     */
    public function index(Request $request)
    {
        // البحث والتصفية
        $query = Product::with(['category'])
            ->withCount(['orderItems as total_sold' => function($q) {
                $q->select(\DB::raw('COALESCE(SUM(quantity), 0)'));
            }]);

        // البحث بالاسم أو SKU
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // التصفية بالتصنيف
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // التصفية بالحالة
        if ($request->has('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            }
        }

        // التصفية بالمخزون
        if ($request->has('stock')) {
            if ($request->stock == 'in_stock') {
                $query->where('quantity', '>', 0);
            } elseif ($request->stock == 'out_of_stock') {
                $query->where('quantity', '<=', 0);
            }
        }

        // الترتيب
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // التقسيم
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        // الحصول على التصنيفات للفلتر
        $categories = Category::where('is_active', true)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * عرض نموذج إنشاء منتج جديد
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * تخزين منتج جديد
     */
    public function store(Request $request)
    {
        try {
            // التحقق الأساسي من البيانات
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:products',
                'category_id' => 'required|exists:categories,id',
                'regular_price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
            ], [
                'name.required' => 'اسم المنتج مطلوب',
                'sku.required' => 'كود المنتج (SKU) مطلوب',
                'sku.unique' => 'كود المنتج موجود بالفعل',
                'category_id.required' => 'التصنيف مطلوب',
                'regular_price.required' => 'السعر العادي مطلوب',
                'quantity.required' => 'الكمية مطلوبة',
            ]);

            // إعداد بيانات المنتج
            $productData = [
                'name' => $validated['name'],
                'sku' => $validated['sku'],
                'category_id' => $validated['category_id'],
                'regular_price' => $validated['regular_price'],
                'quantity' => $validated['quantity'],
                'slug' => $this->generateSlug($validated['name']),
                'is_active' => $request->has('is_active') ? 1 : 0,
                'is_featured' => $request->has('is_featured') ? 1 : 0,
                'is_prescription_required' => $request->has('is_prescription_required') ? 1 : 0,
            ];

            // إضافة الحقول الاختيارية
            $optionalFields = [
                'short_description',
                'description',
                'sale_price',
                'cost_price',
                'low_stock_threshold',
                'weight',
                'dimensions',
                'manufacturer',
                'tax_class'
            ];

            foreach ($optionalFields as $field) {
                if ($request->has($field) && !empty($request->$field)) {
                    $productData[$field] = $request->$field;
                }
            }

            // تعيين قيمة افتراضية للمخزون المنخفض
            if (!isset($productData['low_stock_threshold']) || empty($productData['low_stock_threshold'])) {
                $productData['low_stock_threshold'] = 5;
            }

            // إنشاء المنتج
            $product = Product::create($productData);

            // معالجة الصور
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imageName = time() . '_' . $product->id . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('products/' . $product->id, $imageName, 'public');
                    
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $path,
                        'alt_text' => $product->name,
                        'is_primary' => $index === 0,
                        'order' => $index,
                    ]);
                }
            }

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'تم إنشاء المنتج "' . $product->name . '" بنجاح.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'يوجد أخطاء في البيانات المدخلة.');
                
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل منتج محدد
     */
    public function show(Product $product)
    {
        $product->load(['category', 'images', 'reviews']);
        return view('admin.products.show', compact('product'));
    }

    /**
     * عرض نموذج تعديل منتج
     */
    public function edit(Product $product)
    {
        $product->load(['images']);
        $categories = Category::where('is_active', true)->get();
        
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * تحديث المنتج
     */
    public function update(Request $request, Product $product)
    {
        try {
            // التحقق من صحة البيانات
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
                'category_id' => 'required|exists:categories,id',
                'regular_price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
            ]);

            // إعداد بيانات التحديث
            $updateData = [
                'name' => $validated['name'],
                'sku' => $validated['sku'],
                'category_id' => $validated['category_id'],
                'regular_price' => $validated['regular_price'],
                'quantity' => $validated['quantity'],
                'is_active' => $request->has('is_active') ? 1 : 0,
                'is_featured' => $request->has('is_featured') ? 1 : 0,
                'is_prescription_required' => $request->has('is_prescription_required') ? 1 : 0,
            ];

            // تحديث Slug إذا تغير الاسم
            if ($product->name != $validated['name']) {
                $updateData['slug'] = $this->generateSlug($validated['name']);
            }

            // إضافة الحقول الاختيارية
            $optionalFields = [
                'short_description',
                'description',
                'sale_price',
                'cost_price',
                'low_stock_threshold',
                'weight',
                'dimensions',
                'manufacturer',
                'tax_class'
            ];

            foreach ($optionalFields as $field) {
                $updateData[$field] = $request->$field;
            }

            // تحديث المنتج
            $product->update($updateData);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'تم تحديث المنتج "' . $product->name . '" بنجاح.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * حذف منتج
     */
    public function destroy(Product $product)
    {
        try {
            $productName = $product->name;
            $product->delete();
            
            return redirect()
                ->route('admin.products.index')
                ->with('success', 'تم حذف المنتج "' . $productName . '" بنجاح.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
        }
    }

    /**
     * توليد Slug فريد
     */
    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    /**
     * التبديل بين الحالة النشطة/المعطلة
     */
    public function toggleStatus(Request $request, Product $product)
    {
        try {
            $product->update(['is_active' => !$product->is_active]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة المنتج بنجاح.',
                'is_active' => $product->is_active
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * التبديل بين الحالة المميزة/غير المميزة
     */
    public function toggleFeatured(Request $request, Product $product)
    {
        try {
            $product->update(['is_featured' => !$product->is_featured]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة التمييز بنجاح.',
                'is_featured' => $product->is_featured
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}