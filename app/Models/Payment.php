<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'transaction_id',
        'amount',
        'status',
        'payment_details',
        'paid_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'paid_at' => 'datetime'
    ];

    // العلاقات
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // دالة التحويل للعربية
    public function getStatusArabicAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'refunded' => 'تم الاسترجاع'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
}
