<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'is_active',
        'configuration'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'configuration' => 'array'
    ];

    // العلاقات
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // دالة التحويل للعربية
    public function getNameArabicAttribute()
    {
        $names = [
            'credit_card' => 'بطاقة ائتمان',
            'paypal' => 'باي بال',
            'bank_transfer' => 'تحويل بنكي',
            'cash_on_delivery' => 'الدفع عند الاستلام'
        ];
        
        return $names[$this->name] ?? $this->name;
    }
}
