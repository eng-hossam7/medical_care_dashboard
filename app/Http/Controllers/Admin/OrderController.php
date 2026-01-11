<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Address;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request)
    {
        // Start query
        $query = Order::with(['user', 'shippingAddress', 'billingAddress', 'items'])
            ->withCount('items')
            ->withSum('items as items_total', 'total_price');

        // Search filters
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Date filters
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'today' => Order::whereDate('created_at', today())->count(),
            'week' => Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month' => Order::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $customers = Customer::with('user')->get();
        $products = Product::active()->inStock()->get();
        
        return view('admin.orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'shipping_address_id' => 'required|exists:addresses,id',
                'billing_address_id' => 'required|exists:addresses,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'shipping_cost' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
            ]);

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(6)) . '-' . date('Ymd');

            // Calculate totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product->hasEnoughStock($item['quantity'])) {
                    throw new \Exception("المنتج {$product->name} لا يحتوي على مخزون كافي.");
                }

                $unitPrice = $product->final_price;
                $totalPrice = $unitPrice * $item['quantity'];
                
                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ];

                $subtotal += $totalPrice;
                
                // Reduce stock
                $product->decreaseStock($item['quantity']);
            }

            $shippingCost = $request->shipping_cost ?? 0;
            $tax = ($subtotal * 0.15); // 15% tax - adjust as needed
            $total = $subtotal + $shippingCost + $tax;

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $request->user_id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $total,
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->billing_address_id,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($itemsData as $item) {
                $order->items()->create($item);
            }

            // Create initial payment record
            $order->payments()->create([
                'payment_method_id' => 1, // Default payment method
                'transaction_id' => 'MANUAL-' . $order->id,
                'amount' => $total,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'تم إنشاء الطلب بنجاح. رقم الطلب: ' . $orderNumber);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load([
            'user', 
            'shippingAddress', 
            'billingAddress', 
            'items.product', 
            'payments.paymentMethod'
        ]);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'لا يمكن تعديل الطلب إلا إذا كان في حالة "قيد الانتظار".');
        }

        $order->load(['items.product']);
        $customers = Customer::with('user')->get();
        $products = Product::active()->inStock()->get();
        
        return view('admin.orders.edit', compact('order', 'customers', 'products'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'لا يمكن تعديل الطلب إلا إذا كان في حالة "قيد الانتظار".');
        }

        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
                'shipping_cost' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'cancelled_reason' => 'nullable|string|required_if:status,cancelled',
            ]);

            // If status changed to cancelled, handle stock return
            if ($order->status !== 'cancelled' && $request->status === 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increaseStock($item->quantity);
                    }
                }
            }

            // If status changed to delivered
            if ($order->status !== 'delivered' && $request->status === 'delivered') {
                $validated['delivered_at'] = now();
            }

            $order->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.orders.show', $order)
                ->with('success', 'تم تحديث الطلب بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الطلب: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'لا يمكن حذف الطلب إلا إذا كان في حالة "قيد الانتظار".');
        }

        try {
            DB::beginTransaction();

            // Return stock for all items
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increaseStock($item->quantity);
                }
            }

            // Delete related records
            $order->items()->delete();
            $order->payments()->delete();
            $order->reviews()->delete();
            
            // Delete order
            $order->delete();

            DB::commit();

            return redirect()
                ->route('admin.orders.index')
                ->with('success', 'تم حذف الطلب بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.orders.show', $order)
                ->with('error', 'حدث خطأ أثناء حذف الطلب: ' . $e->getMessage());
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'cancelled_reason' => 'nullable|string|required_if:status,cancelled',
        ]);

        try {
            DB::beginTransaction();

            // Handle stock based on status change
            if ($order->status !== 'cancelled' && $validated['status'] === 'cancelled') {
                // Return stock
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increaseStock($item->quantity);
                    }
                }
            }

            // If cancelling an order that was shipped/delivered
            if (in_array($order->status, ['shipped', 'delivered']) && $validated['status'] === 'cancelled') {
                // You might want to handle refunds here
            }

            // If delivering order
            if ($validated['status'] === 'delivered') {
                $validated['delivered_at'] = now();
            }

            $order->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب بنجاح.',
                'status' => $order->status,
                'status_arabic' => $order->status_arabic
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add note to order.
     */
    public function addNote(Request $request, Order $order)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $order->update([
            'notes' => $order->notes ? $order->notes . "\n\n[" . now()->format('Y-m-d H:i') . "]: " . $validated['note'] 
                                   : "[" . now()->format('Y-m-d H:i') . "]: " . $validated['note']
        ]);

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'تمت إضافة الملاحظة بنجاح.');
    }

    /**
     * Print invoice.
     */
    public function printInvoice(Order $order)
    {
        $order->load(['user', 'shippingAddress', 'billingAddress', 'items.product', 'payments.paymentMethod']);
        
        return view('admin.orders.print', compact('order'));
    }

    /**
     * Export orders to Excel.
     */
    public function export(Request $request)
    {
        // You can implement Excel export using Laravel Excel package
        // For now, return JSON response
        $orders = Order::with(['user', 'items'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'سيتم تنزيل ملف Excel قريباً.'
        ]);
    }

    /**
     * Get customer addresses for order creation.
     */
    /**
 * الحصول على عناوين العميل
 */
    public function getCustomerAddresses($userId)
    {
        try {
            $user = User::with('addresses')->findOrFail($userId);
            
            return response()->json([
                'success' => true,
                'addresses' => $user->addresses->map(function($address) {
                    return [
                        'id' => $address->id,
                        'text' => $address->first_name . ' ' . $address->last_name . ' - ' . 
                                $address->address_line_1 . ', ' . $address->city,
                        'full_address' => $address->full_address,
                        'phone' => $address->phone
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب العناوين: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get product details for order creation.
     */
    public function getProductDetails($productId)
    {
        $product = Product::with('category')->findOrFail($productId);
        
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->final_price,
                'stock' => $product->quantity,
                'category' => $product->category->name ?? null
            ]
        ]);
    }

    /**
     * Get order statistics for dashboard.
     */
    public function getStatistics()
    {
        $today = now()->format('Y-m-d');
        $weekStart = now()->startOfWeek()->format('Y-m-d');
        $monthStart = now()->startOfMonth()->format('Y-m-d');

        $stats = [
            'today' => [
                'orders' => Order::whereDate('created_at', $today)->count(),
                'revenue' => Order::whereDate('created_at', $today)->sum('total'),
            ],
            'week' => [
                'orders' => Order::whereDate('created_at', '>=', $weekStart)->count(),
                'revenue' => Order::whereDate('created_at', '>=', $weekStart)->sum('total'),
            ],
            'month' => [
                'orders' => Order::whereDate('created_at', '>=', $monthStart)->count(),
                'revenue' => Order::whereDate('created_at', '>=', $monthStart)->sum('total'),
            ],
            'total' => [
                'orders' => Order::count(),
                'revenue' => Order::sum('total'),
                'customers' => Customer::count(),
            ],
            'status' => [
                'pending' => Order::where('status', 'pending')->count(),
                'processing' => Order::where('status', 'processing')->count(),
                'shipped' => Order::where('status', 'shipped')->count(),
                'delivered' => Order::where('status', 'delivered')->count(),
                'cancelled' => Order::where('status', 'cancelled')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}