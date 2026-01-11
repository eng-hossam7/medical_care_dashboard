<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'offer_type',
        'discount_value',
        'min_quantity',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'discount_value' => 'decimal:2'
    ];

    // العلاقات
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // دالة التحويل للعربية
    public function getOfferTypeArabicAttribute()
    {
        $types = [
            'percentage' => 'نسبة مئوية',
            'fixed' => 'مبلغ ثابت'
        ];
        
        return $types[$this->offer_type] ?? $this->offer_type;
    }

    // دالة للتحقق إذا كان العرض ساري
    public function getIsValidAttribute()
    {
        $now = now();
        return $this->is_active && 
               $now->gte($this->start_date) && 
               $now->lte($this->end_date);
    }
}
