<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_type',
        'first_name',
        'last_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default'
    ];

    protected $casts = [
        'is_default' => 'boolean'
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingOrders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function billingOrders()
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }

    // دالة التحويل للعربية
    public function getAddressTypeArabicAttribute()
    {
        $types = [
            'shipping' => 'عنوان الشحن',
            'billing' => 'عنوان الفاتورة'
        ];
        
        return $types[$this->address_type] ?? $this->address_type;
    }

    // دالة للحصول على العنوان كامل
    public function getFullAddressAttribute()
    {
        $address = $this->address_line_1;
        
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        
        $address .= ', ' . $this->city . ', ' . $this->state;
        $address .= ', ' . $this->country . ', ' . $this->postal_code;
        
        return $address;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
