<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
        'read_at' => 'datetime'
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // دالة التحويل للعربية
    public function getTypeArabicAttribute()
    {
        $types = [
            'order_status' => 'حالة الطلب',
            'payment' => 'دفع',
            'promotion' => 'ترويج',
            'system' => 'نظام'
        ];
        
        return $types[$this->type] ?? $this->type;
    }
}
