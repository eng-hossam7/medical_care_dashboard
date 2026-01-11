<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * عرض قائمة التصنيفات
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderBy('order')
            ->get();
            
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * عرض نموذج إنشاء تصنيف جديد
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * تخزين تصنيف جديد
     */
    public function store(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'meta_title' => 'nullable|string|max:60',
                'meta_description' => 'nullable|string|max:160',
                'is_active' => 'nullable|boolean',
            ]);

            // إعداد البيانات
            $categoryData = [
                'name' => $validated['name'],
                'slug' => $this->generateSlug($validated['name']),
                'parent_id' => $validated['parent_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'is_active' => $request->has('is_active') ? 1 : 0,
                'order' => Category::max('order') + 1,
            ];

            // معالجة الصورة
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('categories', $imageName, 'public');
                $categoryData['image'] = $imagePath;
            }

            // إنشاء التصنيف
            $category = Category::create($categoryData);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'تم إنشاء التصنيف "' . $category->name . '" بنجاح.');

        } catch (\Exception $e) {
            // إرجاع إلى الصفحة مع عرض الخطأ
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء التصنيف: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل تصنيف محدد
     */
    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products' => function($query) {
            $query->limit(10);
        }]);
        
        return view('admin.categories.show', compact('category'));
    }

    /**
     * عرض نموذج تعديل تصنيف
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();
            
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * تحديث التصنيف
     */
    public function update(Request $request, Category $category)
    {
        try {
            // التحقق من صحة البيانات
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'meta_title' => 'nullable|string|max:60',
                'meta_description' => 'nullable|string|max:160',
                'is_active' => 'nullable|boolean',
                'order' => 'nullable|integer',
            ]);

            // تحديث البيانات
            $updateData = [
                'name' => $validated['name'],
                'parent_id' => $validated['parent_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ];

            // إذا تغير الاسم، نحدث الـ Slug
            if ($category->name != $validated['name']) {
                $updateData['slug'] = $this->generateSlug($validated['name']);
            }

            // تحديث الترتيب إذا تم إرساله
            if (isset($validated['order'])) {
                $updateData['order'] = $validated['order'];
            }

            // معالجة الصورة
            if ($request->hasFile('image')) {
                // حذف الصورة القديمة إذا كانت موجودة
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('categories', $imageName, 'public');
                $updateData['image'] = $imagePath;
            } elseif ($request->has('remove_image')) {
                // حذف الصورة إذا طلب المستخدم ذلك
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                    $updateData['image'] = null;
                }
            }

            // تحديث التصنيف
            $category->update($updateData);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'تم تحديث التصنيف "' . $category->name . '" بنجاح.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث التصنيف: ' . $e->getMessage());
        }
    }

    /**
     * حذف التصنيف
     */
    public function destroy(Category $category)
    {
        try {
            $categoryName = $category->name;
            
            // التحقق إذا كان التصنيف يحتوي على منتجات
            if ($category->products()->count() > 0) {
                return redirect()
                    ->route('admin.categories.index')
                    ->with('error', 'لا يمكن حذف التصنيف "' . $categoryName . '" لأنه يحتوي على منتجات.');
            }
            
            // نقل التصنيفات الفرعية إلى التصنيف الرئيسي
            $category->children()->update(['parent_id' => $category->parent_id]);
            
            // حذف الصورة إذا كانت موجودة
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            
            // حذف التصنيف
            $category->delete();
            
            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'تم حذف التصنيف "' . $categoryName . '" بنجاح.');
                
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'حدث خطأ أثناء حذف التصنيف: ' . $e->getMessage());
        }
    }

    /**
     * تبديل حالة التصنيف
     */
    public function toggleStatus(Category $category)
    {
        try {
            $category->update(['is_active' => !$category->is_active]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة التصنيف بنجاح.',
                'is_active' => $category->is_active
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث ترتيب التصنيفات
     */
    public function updateOrder(Request $request)
    {
        try {
            $categories = $request->input('categories');
            
            foreach ($categories as $item) {
                Category::where('id', $item['id'])->update([
                    'parent_id' => $item['parent_id'] ?? null,
                    'order' => $item['order']
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'تم تحديث الترتيب بنجاح.']);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * توليد Slug فريد للتصنيف
     */
    private function generateSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;
        
        // التحقق من أن الـ Slug فريد
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        
        return $slug;
    }
}