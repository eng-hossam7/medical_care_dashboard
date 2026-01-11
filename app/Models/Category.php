<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'meta_title',
        'meta_description',
        'is_active',
        'order'
    ];

    protected $appends = ['parent_name', 'image_url', 'full_path', 'active_products_count'];

    // العلاقات
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Accessors (المحصولين)
    
    /**
     * الحصول على اسم التصنيف الرئيسي
     */
    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->name : 'رئيسي';
    }

    /**
     * الحصول على رابط الصورة
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-category.png');
    }

    /**
     * الحصول على المسار الكامل للتصنيف
     */
    public function getFullPathAttribute()
    {
        $path = [];
        $category = $this;
        
        while ($category) {
            $path[] = $category->name;
            $category = $category->parent;
        }
        
        return implode(' > ', array_reverse($path));
    }

    /**
     * الحصول على عدد المنتجات النشطة
     */
    public function getActiveProductsCountAttribute()
    {
        return $this->products()->where('is_active', true)->count();
    }

    // Scopes (نطاقات الاستعلام)

    /**
     * نطاق للتصنيفات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق للتصنيفات الرئيسية فقط
     */
    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * نطاق للتصنيفات مع التصنيفات الفرعية
     */
    public function scopeWithChildren($query)
    {
        return $query->with(['children' => function($q) {
            $q->orderBy('order');
        }]);
    }

    /**
     * نطاق للتصنيفات مع عدد المنتجات
     */
    public function scopeWithProductsCount($query)
    {
        return $query->withCount(['products as products_count' => function($q) {
            $q->where('is_active', true);
        }]);
    }

    // Methods (الدوال العامة)

    /**
     * التحقق إذا كان التصنيف له أبناء
     */
    public function hasChildren()
    {
        return $this->children()->count() > 0;
    }

    /**
     * التحقق إذا كان التصنيف له منتجات
     */
    public function hasProducts()
    {
        return $this->products()->count() > 0;
    }

    /**
     * الحصول على جميع الأبناء بشكل متكرر
     */
    public function getAllChildren()
    {
        $children = collect();
        
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }
        
        return $children;
    }

    /**
     * الحصول على جميع المنتجات بما فيها منتجات التصنيفات الفرعية
     */
    public function getAllProducts()
    {
        $categoryIds = [$this->id];
        
        // إضافة جميع التصنيفات الفرعية
        $children = $this->getAllChildren();
        foreach ($children as $child) {
            $categoryIds[] = $child->id;
        }
        
        return Product::whereIn('category_id', $categoryIds)->get();
    }

    /**
     * التبديل بين الحالة النشطة/المعطلة
     */
    public function toggleStatus()
    {
        $this->update(['is_active' => !$this->is_active]);
        return $this;
    }

    /**
     * تحديث الترتيب
     */
    public function updateOrder($order)
    {
        $this->update(['order' => $order]);
        return $this;
    }

    /**
     * تحديث التصنيف الرئيسي
     */
    public function updateParent($parentId)
    {
        // التحقق من عدم إنشاء دورة (تصنيف يكون أباً لنفسه)
        if ($parentId == $this->id) {
            throw new \Exception('لا يمكن أن يكون التصنيف أباً لنفسه.');
        }
        
        // التحقق من عدم إنشاء دورة في التصنيفات الفرعية
        $childrenIds = $this->getAllChildren()->pluck('id')->toArray();
        if (in_array($parentId, $childrenIds)) {
            throw new \Exception('لا يمكن نقل التصنيف إلى أحد تصنيفاته الفرعية.');
        }
        
        $this->update(['parent_id' => $parentId]);
        return $this;
    }

    /**
     * نسخ التصنيف مع منتجاته
     */
    public function duplicate($newName = null)
    {
        $newCategory = $this->replicate();
        $newCategory->name = $newName ?: $this->name . ' (نسخة)';
        $newCategory->slug = $this->slug . '-' . time();
        $newCategory->push();
        
        // نسخ التصنيفات الفرعية
        foreach ($this->children as $child) {
            $newChild = $child->duplicate();
            $newChild->parent_id = $newCategory->id;
            $newChild->save();
        }
        
        return $newCategory;
    }

    /**
     * دمج التصنيف مع تصنيف آخر
     */
    public function mergeInto(Category $targetCategory)
    {
        // نقل جميع المنتجات إلى التصنيف الهدف
        $this->products()->update(['category_id' => $targetCategory->id]);
        
        // نقل جميع التصنيفات الفرعية إلى التصنيف الهدف
        $this->children()->update(['parent_id' => $targetCategory->id]);
        
        // حذف التصنيف
        $this->delete();
        
        return $targetCategory;
    }

    /**
     * التحقق من صحة التصنيف (للـ SEO)
     */
    public function isValidForSeo()
    {
        $errors = [];
        
        // التحقق من وجود الاسم
        if (empty($this->name)) {
            $errors[] = 'اسم التصنيف مطلوب';
        }
        
        // التحقق من طول الـ Meta Title
        if ($this->meta_title && strlen($this->meta_title) > 60) {
            $errors[] = 'عنوان SEO طويل جداً (الحد الأقصى 60 حرفاً)';
        }
        
        // التحقق من طول الـ Meta Description
        if ($this->meta_description && strlen($this->meta_description) > 160) {
            $errors[] = 'وصف SEO طويل جداً (الحد الأقصى 160 حرفاً)';
        }
        
        return [
            'is_valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * توليد الـ Meta Tags تلقائياً
     */
    public function generateMetaTags()
    {
        if (empty($this->meta_title)) {
            $this->meta_title = $this->name . ' - متجر المستلزمات الطبية';
        }
        
        if (empty($this->meta_description)) {
            $this->meta_description = 'تصفح ' . $this->name . ' في متجر المستلزمات الطبية. ' 
                                    . ($this->description ?: 'منتجات طبية عالية الجودة بأسعار مناسبة.');
        }
        
        return $this;
    }

    /**
     * الحصول على إحصائيات التصنيف
     */
    public function getStats()
    {
        $allProducts = $this->getAllProducts();
        
        return [
            'total_products' => $allProducts->count(),
            'active_products' => $allProducts->where('is_active', true)->count(),
            'featured_products' => $allProducts->where('is_featured', true)->count(),
            'low_stock_products' => $allProducts->filter(function($product) {
                return $product->quantity <= $product->low_stock_threshold && $product->quantity > 0;
            })->count(),
            'out_of_stock_products' => $allProducts->where('quantity', 0)->count(),
            'total_revenue' => $allProducts->sum(function($product) {
                return $product->orderItems->sum('total_price');
            }),
            'average_price' => $allProducts->avg('regular_price'),
        ];
    }

    /**
     * الحصول على التصنيفات المتشابهة
     */
    public function getSimilarCategories($limit = 5)
    {
        return self::where('id', '!=', $this->id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->name . '%')
                      ->orWhere('description', 'like', '%' . $this->name . '%');
            })
            ->orWhere('parent_id', $this->parent_id)
            ->limit($limit)
            ->get();
    }

    /**
     * التحقق من توافق التصنيف مع التطبيق
     */
    public function isCompatibleWithApp()
    {
        // التحقق من عدم وجود أحرف غير مسموح بها في الـ Slug
        if (!preg_match('/^[a-z0-9\-]+$/', $this->slug)) {
            return false;
        }
        
        // التحقق من أن الـ Slug فريد
        $exists = self::where('slug', $this->slug)
            ->where('id', '!=', $this->id)
            ->exists();
            
        return !$exists;
    }

    /**
     * تنقية وتهيئة بيانات التصنيف
     */
    public function sanitize()
    {
        // تنقية الاسم
        $this->name = trim(strip_tags($this->name));
        
        // تنقية الوصف
        if ($this->description) {
            $this->description = trim(strip_tags($this->description, '<p><br><strong><em><ul><ol><li>'));
        }
        
        // تنقية الـ Meta Tags
        if ($this->meta_title) {
            $this->meta_title = trim(strip_tags($this->meta_title));
        }
        
        if ($this->meta_description) {
            $this->meta_description = trim(strip_tags($this->meta_description));
        }
        
        return $this;
    }
}