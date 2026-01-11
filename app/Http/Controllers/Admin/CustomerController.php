<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * عرض قائمة العملاء
     */
    public function index(Request $request)
    {
        // بدء الاستعلام
        // $query = Customer::with(['user', 'orders'])
        //     ->withCount(['orders as total_orders_count'])
        //     ->withSum(['orders as total_spent_amount' => function($q) {
        //         $q->where('status', '!=', 'cancelled');
        //     }], 'total');

        $query = Customer::with(['user', 'orders'])
            ->withCount(['orders as total_orders_count'])
            ->withSum(['orders as total_spent_amount' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'total');
         // البحث
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%")
                            ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
                });
        }

        // تصفية حسب الجنس
        if ($request->has('gender') && $request->gender != '') {
            $query->where('gender', $request->gender);
        }

        // تصفية حسب تاريخ التسجيل
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ترتيب النتائج
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // التقسيم
        $perPage = $request->get('per_page', 15);
        $customers = $query->paginate($perPage);

        // إحصائيات
        $stats = [
            'total' => Customer::count(),
            'male' => Customer::where('gender', 'male')->count(),
            'female' => Customer::where('gender', 'female')->count(),
            'new_today' => Customer::whereDate('created_at', today())->count(),
            'new_week' => Customer::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'new_month' => Customer::whereMonth('created_at', now()->month)->count(),
            'with_orders' => Customer::has('orders')->count(),
            'without_orders' => Customer::doesntHave('orders')->count(),
            'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            'average_order_value' => Order::where('status', '!=', 'cancelled')->avg('total'),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * عرض تفاصيل عميل
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'user', 
            'orders' => function($q) {
                $q->orderBy('created_at', 'desc')->take(10);
            },
            'user.addresses',
            'user.reviews'
        ]);

        // إحصائيات العميل
        $customerStats = [
            'total_orders' => $customer->orders()->count(),
            'total_spent' => $customer->orders()->where('status', '!=', 'cancelled')->sum('total'),
            'pending_orders' => $customer->orders()->where('status', 'pending')->count(),
            'completed_orders' => $customer->orders()->whereIn('status', ['delivered', 'shipped'])->count(),
            'average_order_value' => $customer->orders()->where('status', '!=', 'cancelled')->avg('total'),
            'last_order_date' => $customer->orders()->latest()->first()->created_at ?? null,
            'reviews_count' => $customer->user->reviews()->count(),
            'addresses_count' => $customer->user->addresses()->count(),
        ];

        // أحدث الطلبات
        $recentOrders = $customer->orders()
            ->with(['items'])
            ->latest()
            ->take(5)
            ->get();

        // منتجات مفضلة (بناءً على الطلبات)
        $favoriteProducts = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.user_id', $customer->user_id)
            ->select('products.id', 'products.name', 'products.sku', 
                     DB::raw('SUM(order_items.quantity) as total_quantity'),
                     DB::raw('SUM(order_items.total_price) as total_spent'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_quantity', 'desc')
            ->take(5)
            ->get();

        return view('admin.customers.show', compact('customer', 'customerStats', 'recentOrders', 'favoriteProducts'));
    }

    /**
     * عرض نموذج إنشاء عميل جديد
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * تخزين عميل جديد
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'password' => 'required|string|min:6',
                'first_name' => 'nullable|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female',
                'notes' => 'nullable|string',
            ]);

            // إنشاء المستخدم
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'type' => 'customer',
                'status' => 'active',
            ]);

            // إنشاء العميل
            $customer = Customer::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'loyalty_points' => 0,
                'total_orders' => 0,
                'total_spent' => 0,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.customers.show', $customer)
                ->with('success', 'تم إنشاء العميل بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء العميل: ' . $e->getMessage());
        }
    }


    /**
 * عرض عناوين العميل
 */
    public function addresses(Customer $customer)
    {
        $addresses = $customer->user->addresses()->latest()->paginate(12);
        
        return view('admin.customers.addresses.addresses', compact('customer', 'addresses'));
    }
    /**
     * عرض نموذج تعديل عميل
     */
    public function edit(Customer $customer)
    {
        $customer->load('user');
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * تحديث بيانات العميل
     */
    public function update(Request $request, Customer $customer)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $customer->user_id,
                'phone' => 'required|string|max:20',
                'first_name' => 'nullable|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female',
                'notes' => 'nullable|string',
                'password' => 'nullable|string|min:6',
            ]);

            // تحديث بيانات المستخدم
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];

            // إذا تم إرسال كلمة مرور جديدة
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $customer->user->update($userData);

            // تحديث بيانات العميل
            $customer->update([
                'first_name' => $validated['first_name'] ?? null,
                'last_name' => $validated['last_name'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.customers.show', $customer)
                ->with('success', 'تم تحديث بيانات العميل بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث العميل: ' . $e->getMessage());
        }
    }

    /**
     * حذف عميل
     */
    public function destroy(Customer $customer)
    {
        try {
            DB::beginTransaction();

            // التحقق من وجود طلبات مرتبطة
            if ($customer->orders()->count() > 0) {
                return redirect()
                    ->route('admin.customers.show', $customer)
                    ->with('error', 'لا يمكن حذف العميل لأنه لديه طلبات مرتبطة.');
            }

            // حذف العميل ثم المستخدم
            $customer->delete();
            $customer->user->delete();

            DB::commit();

            return redirect()
                ->route('admin.customers.index')
                ->with('success', 'تم حذف العميل بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.customers.show', $customer)
                ->with('error', 'حدث خطأ أثناء حذف العميل: ' . $e->getMessage());
        }
    }

    /**
     * تبديل حالة العميل (نشط/موقوف)
     */
    public function toggleStatus(Customer $customer)
    {
        try {
            $newStatus = $customer->user->status == 'active' ? 'suspended' : 'active';
            $customer->user->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة العميل بنجاح.',
                'status' => $newStatus,
                'status_arabic' => $newStatus == 'active' ? 'نشط' : 'موقوف'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة ملاحظة للعميل
     */
    public function addNote(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $currentNotes = $customer->notes ? $customer->notes . "\n\n" : '';
        $customer->update([
            'notes' => $currentNotes . "[" . now()->format('Y-m-d H:i') . "]: " . $validated['note']
        ]);

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'تمت إضافة الملاحظة بنجاح.');
    }

    /**
     * تصدير بيانات العملاء
     */
    public function export(Request $request)
    {
        // يمكن تطوير هذا لتصدير Excel
        $customers = Customer::with('user')->get();
        
        return response()->json([
            'success' => true,
            'data' => $customers,
            'message' => 'سيتم تنزيل ملف Excel قريباً.'
        ]);
    }

    /**
     * الحصول على إحصائيات العملاء للداشبورد
     */
    public function getStatistics()
    {
        $today = now()->format('Y-m-d');
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');

        $stats = [
            'today' => Customer::whereDate('created_at', $today)->count(),
            'week' => Customer::whereDate('created_at', '>=', $weekStart)->count(),
            'month' => Customer::whereDate('created_at', '>=', $monthStart)->count(),
            'total' => Customer::count(),
            'with_orders' => Customer::has('orders')->count(),
            'gender_distribution' => [
                'male' => Customer::where('gender', 'male')->count(),
                'female' => Customer::where('gender', 'female')->count(),
                'unknown' => Customer::whereNull('gender')->count(),
            ],
            'top_customers' => Customer::withSum(['orders as total_spent_amount' => function($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'total')
            ->orderBy('total_spent_amount', 'desc')
            ->take(5)
            ->get()
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * عرض طلبات العميل
     */
    public function orders(Customer $customer, Request $request)
    {
        $orders = $customer->orders()
            ->with(['items', 'shippingAddress', 'billingAddress'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.customers.orders', compact('customer', 'orders'));
    }

    /**
     * عرض عناوين العميل
     */
    // public function addresses(Customer $customer)
    // {
    //     $addresses = $customer->user->addresses()->get();
    //     return view('admin.customers.addresses', compact('customer', 'addresses'));
    // }

    /**
     * عرض تقييمات العميل
     */
    public function reviews(Customer $customer)
    {
        $reviews = $customer->user->reviews()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.customers.reviews', compact('customer', 'reviews'));
    }
}