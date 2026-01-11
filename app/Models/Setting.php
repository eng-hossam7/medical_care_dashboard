<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'options'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    // دالة التحويل للعربية
    public function getGroupArabicAttribute()
    {
        $groups = [
            'general' => 'عام',
            'payment' => 'دفع',
            'shipping' => 'شحن',
            'email' => 'بريد إلكتروني',
            'seo' => 'تحسين محركات البحث'
        ];
        
        return $groups[$this->group] ?? $this->group;
    }

    public function getTypeArabicAttribute()
    {
        $types = [
            'text' => 'نص',
            'number' => 'رقم',
            'boolean' => 'منطقي',
            'json' => 'جسون',
            'select' => 'قائمة منسدلة'
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    // دالة للحصول على قيمة الإعداد
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
