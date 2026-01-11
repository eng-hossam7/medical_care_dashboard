<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'short_description',
        'description',
        'regular_price',
        'sale_price',
        'cost_price',
        'quantity',
        'low_stock_threshold',
        'weight',
        'dimensions',
        'manufacturer',
        'is_active',
        'is_featured',
        'is_prescription_required',
        'category_id',
        'tax_class'
    ];

    // العلاقات
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function offers()
    {
        return $this->hasMany(ProductOffer::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Accessors (المحصولين)

    /**
     * الحصول على الصورة الأساسية
     */
    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first() 
               ?? $this->images()->first();
    }

    /**
     * الحصول على السعر النهائي (بعد الخصم)
     */
    public function getFinalPriceAttribute()
    {
        // إذا كان هناك سعر بيع
        if ($this->sale_price && $this->sale_price > 0) {
            return $this->sale_price;
        }
        
        // التحقق من وجود عروض فعالة
        $activeOffer = $this->offers()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
            
        if ($activeOffer) {
            if ($activeOffer->offer_type == 'percentage') {
                // خصم نسبة مئوية
                $discount = $this->regular_price * ($activeOffer->discount_value / 100);
                return $this->regular_price - $discount;
            } else {
                // خصم مبلغ ثابت
                return max(0, $this->regular_price - $activeOffer->discount_value);
            }
        }
        
        // إذا لم يكن هناك خصومات
        return $this->regular_price;
    }

    /**
     * التحقق إذا كان المنتج في العرض
     */
    public function getHasOfferAttribute()
    {
        return $this->offers()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
    }

    /**
     * التحقق إذا كان المنتج متوفر
     */
    public function getIsInStockAttribute()
    {
        return $this->quantity > 0;
    }

    /**
     * التحقق إذا كان المخزون منخفض
     */
    public function getIsLowStockAttribute()
    {
        return $this->quantity <= $this->low_stock_threshold && $this->quantity > 0;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price && $this->sale_price > 0 && $this->regular_price > 0) {
            $discount = (($this->regular_price - $this->sale_price) / $this->regular_price) * 100;
            return round($discount, 2);
        }
        return 0;
    }

    /**
     * الحصول على صورة المنتج الأساسية (URL)
     */
    public function getImageUrlAttribute()
    {
        if ($this->primaryImage) {
            return asset('storage/' . $this->primaryImage->image_url);
        }
        return asset('images/default-product.png'); // صورة افتراضية
    }

    /**
     * الحصول على التصنيف الكامل (مع الأباء)
     */
    public function getFullCategoryAttribute()
    {
        if (!$this->category) {
            return 'بدون تصنيف';
        }

        $category = $this->category;
        $categories = [];
        
        while ($category) {
            $categories[] = $category->name;
            $category = $category->parent;
        }
        
        return implode(' > ', array_reverse($categories));
    }

    /**
     * الحصول على عدد المنتجات المباعة
     */
    public function getTotalSoldAttribute()
    {
        return $this->orderItems()->sum('quantity');
    }

    /**
     * الحصول على إجمالي الإيرادات
     */
    public function getTotalRevenueAttribute()
    {
        return $this->orderItems()->sum('total_price');
    }

    /**
     * الحصول على متوسط التقييم
     */
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    /**
     * الحصول على عدد التقييمات
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    /**
     * التحقق إذا كان المنتج جديد (أقل من 7 أيام)
     */
    public function getIsNewAttribute()
    {
        return $this->created_at->greaterThan(now()->subDays(7));
    }

    /**
     * الحصول على الهامش الربحي
     */
    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price || $this->cost_price <= 0) {
            return 0;
        }

        $sellingPrice = $this->final_price;
        $profit = $sellingPrice - $this->cost_price;
        
        return ($profit / $this->cost_price) * 100;
    }

    // Scopes (نطاقات الاستعلام)

    /**
     * نطاق للمنتجات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * نطاق للمنتجات المميزة
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * نطاق للمنتجات التي تحتاج وصفة طبية
     */
    public function scopePrescriptionRequired($query)
    {
        return $query->where('is_prescription_required', true);
    }

    /**
     * نطاق للمنتجات المتوفرة بالمخزون
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * نطاق للمنتجات منخفضة المخزون
     */
    public function scopeLowStock($query)
    {
        return $query->where('quantity', '<=', \DB::raw('low_stock_threshold'))
                     ->where('quantity', '>', 0);
    }

    /**
     * نطاق للمنتجات التي نفذت من المخزون
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * نطاق للمنتجات في نطاق سعري
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('regular_price', [$min, $max]);
    }

    /**
     * نطاق للمنتجات في تصنيف معين
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Methods (الدوال العامة)

    /**
     * زيادة المخزون
     */
    public function increaseStock($quantity)
    {
        $this->increment('quantity', $quantity);
        return $this;
    }

    /**
     * تقليل المخزون
     */
    public function decreaseStock($quantity)
    {
        $newQuantity = max(0, $this->quantity - $quantity);
        $this->update(['quantity' => $newQuantity]);
        return $this;
    }

    /**
     * تعيين المخزون
     */
    public function setStock($quantity)
    {
        $this->update(['quantity' => max(0, $quantity)]);
        return $this;
    }

    /**
     * التحقق إذا كان المخزون كافي
     */
    public function hasEnoughStock($quantity)
    {
        return $this->quantity >= $quantity;
    }

    /**
     * إضافة صورة للمنتج
     */
    public function addImage($imageUrl, $isPrimary = false, $altText = null)
    {
        // إذا كانت الصورة أساسية، نقوم بإلغاء أي صورة أساسية أخرى
        if ($isPrimary) {
            $this->images()->update(['is_primary' => false]);
        }

        return $this->images()->create([
            'image_url' => $imageUrl,
            'alt_text' => $altText ?? $this->name,
            'is_primary' => $isPrimary,
            'order' => $this->images()->count()
        ]);
    }

    /**
     * تحديث سعر المنتج
     */
    public function updatePrice($regularPrice, $salePrice = null)
    {
        $this->update([
            'regular_price' => $regularPrice,
            'sale_price' => $salePrice
        ]);
        
        return $this;
    }

    /**
     * الحصول على العرض النشط الحالي
     */
    public function getActiveOffer()
    {
        return $this->offers()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * إنشاء عرض جديد للمنتج
     */
    public function createOffer($data)
    {
        return $this->offers()->create([
            'offer_type' => $data['offer_type'] ?? 'percentage',
            'discount_value' => $data['discount_value'],
            'min_quantity' => $data['min_quantity'] ?? 1,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'is_active' => $data['is_active'] ?? true
        ]);
    }

    /**
     * الحصول على المراجعات المعتمدة
     */
    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * الحصول على صورة المنتج المصغرة
     */
    public function getThumbnailUrl($size = 'small')
    {
        $sizes = [
            'small' => '150x150',
            'medium' => '300x300',
            'large' => '600x600'
        ];

        if ($this->primaryImage) {
            // هنا يمكنك إضافة منطق لتوليد صور مصغرة
            // حالياً نعيد الصورة الأصلية
            return asset('storage/' . $this->primaryImage->image_url);
        }

        return asset('images/default-product-' . $size . '.png');
    }

    /**
     * التحقق من صلاحية المنتج للطلب
     */
    public function isAvailableForOrder($quantity = 1)
    {
        return $this->is_active && $this->hasEnoughStock($quantity);
    }

    /**
     * توليد رابط المنتج
     */
    public function getUrlAttribute()
    {
        return route('products.show', $this->slug);
    }

    /**
     * الحصول على إحصائيات المنتج
     */
    public function getStats()
    {
        return [
            'total_sold' => $this->total_sold,
            'total_revenue' => $this->total_revenue,
            'average_rating' => $this->average_rating,
            'reviews_count' => $this->reviews_count,
            'profit_margin' => $this->profit_margin,
            'wishlist_count' => $this->wishlists()->count(),
        ];
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
     * التبديل بين الحالة المميزة/غير المميزة
     */
    public function toggleFeatured()
    {
        $this->update(['is_featured' => !$this->is_featured]);
        return $this;
    }
}