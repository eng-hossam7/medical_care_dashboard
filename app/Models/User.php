<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'type',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // العلاقات
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Order::class);
    }

    // دوال تحويل ENUM للعربية
    public function getTypeArabicAttribute()
    {
        $types = [
            'admin' => 'مدير',
            'employee' => 'موظف',
            'customer' => 'عميل'
        ];
        
        return $types[$this->type] ?? $this->type;
    }

    public function getStatusArabicAttribute()
    {
        $statuses = [
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'موقوف'
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
}
