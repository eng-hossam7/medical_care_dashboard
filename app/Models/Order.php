<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'discount',
        'tax',
        'shipping_cost',
        'total',
        'shipping_address_id',
        'billing_address_id',
        'notes',
        'cancelled_reason',
        'estimated_delivery_date',
        
        'delivered_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'estimated_delivery_date' => 'date',
        'delivered_at' => 'datetime'
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    // دالة التحويل للعربية
    public function getStatusArabicAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التوصيل',
            'cancelled' => 'ملغى',
            'refunded' => 'تم الاسترجاع'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    // دالة للتحقق من الحالة
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isProcessing()
    {
        return $this->status === 'processing';
    }

    public function isCompleted()
    {
        return in_array($this->status, ['shipped', 'delivered']);
    }

    public function isCancelled()
    {
        return in_array($this->status, ['cancelled', 'refunded']);
    }
}
