<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'shipping_address_id' => 'required|exists:addresses,id',
            'billing_address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => 'العميل مطلوب',
            'status.required' => 'حالة الطلب مطلوبة',
            'shipping_address_id.required' => 'عنوان الشحن مطلوب',
            'billing_address_id.required' => 'عنوان الفاتورة مطلوب',
            'items.required' => 'يجب إضافة منتجات للطلب',
        ];
    }
}