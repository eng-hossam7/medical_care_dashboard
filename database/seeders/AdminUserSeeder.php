<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
            'phone' => '0500000000',
            'type' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        // يمكنك إضافة المزيد من المستخدمين إذا أردت
        User::create([
            'name' => 'موظف',
            'email' => 'employee@gmail.com',
            'password' => Hash::make('pass123'),
            'phone' => '0511111111',
            'type' => 'employee',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        
        User::create([
            'name' => 'عميل',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password123'),
            'phone' => '0522222222',
            'type' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}