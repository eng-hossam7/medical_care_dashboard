<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany; // تأكد من استيراد هذا
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasFactory;

    protected $fillable   = [
        'user_id',
        'first_name',
        'last_name',
        'gender',
        'loyalty_points',
        'total_orders',
        'total_spent',
        'notes'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // دالة تحويل الجنس للعربية
    public function getGenderArabicAttribute()
    {
        $genders = [
            'male' => 'ذكر',
            'female' => 'أنثى'
        ];
        
        return $this->gender ? $genders[$this->gender] ?? $this->gender : null;
    }
}
