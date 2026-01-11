<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Customer $customer)
    {
        // الحصول على عناوين المستخدم المرتبط بالعميل
        $addresses = $customer->user->addresses()->latest()->paginate(12);
        
        return view('admin.customers.addresses.addresses', compact('customer', 'addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Customer $customer)
    {
        return view('admin.customers.addresses.create', compact('customer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'address_type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'is_default' => 'boolean',
        ]);

        // إذا كان العنوان الجديد هو الافتراضي، نزيل الافتراضي من العناوين الأخرى
        if ($request->has('is_default') && $request->is_default) {
            $customer->user->addresses()->update(['is_default' => false]);
        }

        // إنشاء العنوان مرتبطاً بالمستخدم
        $address = $customer->user->addresses()->create($validated);

        return redirect()->route('admin.customers.addresses.index', $customer)
            ->with('success', 'تم إضافة العنوان بنجاح.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer, Address $address)
    {
        // للتأكد أن العنوان يخص مستخدم العميل
        if ($address->user_id !== $customer->user_id) {
            abort(404);
        }

        return view('admin.customers.addresses.edit', compact('customer', 'address'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer, Address $address)
    {
        // للتأكد أن العنوان يخص مستخدم العميل
        if ($address->user_id !== $customer->user_id) {
            abort(404);
        }

        $validated = $request->validate([
            'address_type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'is_default' => 'boolean',
        ]);

        // إذا كان العنوان الجديد هو الافتراضي، نزيل الافتراضي من العناوين الأخرى
        if ($request->has('is_default') && $request->is_default) {
            $customer->user->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return redirect()->route('admin.customers.addresses.index', $customer)
            ->with('success', 'تم تحديث العنوان بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer, Address $address)
    {
        // للتأكد أن العنوان يخص مستخدم العميل
        if ($address->user_id !== $customer->user_id) {
            abort(404);
        }

        // إذا كان العنوان المحذوف هو الافتراضي، نجعل العنوان الأول افتراضياً
        if ($address->is_default) {
            $newDefault = $customer->user->addresses()
                ->where('id', '!=', $address->id)
                ->first();
                
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        $address->delete();

        return redirect()->route('admin.customers.addresses.index', $customer)
            ->with('success', 'تم حذف العنوان بنجاح.');
    }

    /**
     * تغيير العنوان الافتراضي
     */
    public function setDefault(Customer $customer, Address $address)
    {
        // للتأكد أن العنوان يخص مستخدم العميل
        if ($address->user_id !== $customer->user_id) {
            abort(404);
        }

        DB::transaction(function () use ($customer, $address) {
            $customer->user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return redirect()->route('admin.customers.addresses', $customer)
            ->with('success', 'تم تعيين العنوان كافتراضي.');
    }
}