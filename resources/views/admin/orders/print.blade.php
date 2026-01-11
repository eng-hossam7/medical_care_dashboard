<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة الطلب #{{ $order->order_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #1b7158 0%, #1d4b82 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .invoice-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 20px;
            opacity: 0.9;
        }
        
        .invoice-body {
            padding: 30px;
        }
        
        .section-title {
            color: #1b7158;
            border-bottom: 2px solid #1b7158;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 18px;
        }
        
        .company-info, .customer-info {
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 15px;
            background: #f9f9f9;
        }
        
        .info-card h3 {
            color: #1d4b82;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        table th {
            background: #f5f5f5;
            color: #1d4b82;
            padding: 12px 15px;
            text-align: right;
            border-bottom: 2px solid #1b7158;
        }
        
        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: left;
        }
        
        .totals {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .total-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            color: #1b7158;
        }
        
        .notes {
            background: #fff8e1;
            padding: 20px;
            border-radius: 6px;
            border-right: 4px solid #ffc107;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        
        @media print {
            body {
                padding: 0;
            }
            
            .invoice-container {
                border: none;
                box-shadow: none;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .print-controls {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 6px;
        }
        
        .btn-print {
            background: #1b7158;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            margin: 0 5px;
        }
        
        .btn-print:hover {
            background: #1d4b82;
        }
        
        .invoice-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="print-controls no-print">
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> طباعة الفاتورة
        </button>
        <button class="btn-print" onclick="window.close()">
            <i class="fas fa-times"></i> إغلاق
        </button>
    </div>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <h1>فاتورة</h1>
            <div class="invoice-number">رقم الطلب: {{ $order->order_number }}</div>
            <div class="invoice-status status-{{ $order->status }}">
                {{ $order->status_arabic }}
            </div>
        </div>
        
        <!-- Body -->
        <div class="invoice-body">
            <!-- Company Info -->
            <div class="company-info">
                <h2 class="section-title">معلومات المتجر</h2>
                <div class="info-grid">
                    <div class="info-card">
                        <h3>اسم المتجر</h3>
                        <p>متجر المستلزمات الطبية</p>
                    </div>
                    <div class="info-card">
                        <h3>العنوان</h3>
                        <p>الرياض، المملكة العربية السعودية</p>
                    </div>
                    <div class="info-card">
                        <h3>معلومات الاتصال</h3>
                        <p>الهاتف: +966 11 123 4567</p>
                        <p>البريد: info@medical-store.com</p>
                    </div>
                </div>
            </div>
            
            <!-- Order & Customer Info -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>معلومات الطلب</h3>
                    <p><strong>رقم الطلب:</strong> {{ $order->order_number }}</p>
                    <p><strong>تاريخ الطلب:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                    <p><strong>طريقة الدفع:</strong> {{ $order->payments->first()->paymentMethod->display_name ?? 'غير محدد' }}</p>
                </div>
                
                <div class="info-card">
                    <h3>معلومات العميل</h3>
                    <p><strong>الاسم:</strong> {{ $order->user->name ?? 'زائر' }}</p>
                    <p><strong>البريد الإلكتروني:</strong> {{ $order->user->email ?? 'غير محدد' }}</p>
                    <p><strong>الهاتف:</strong> {{ $order->user->phone ?? 'غير محدد' }}</p>
                </div>
                
                <div class="info-card">
                    <h3>عنوان الشحن</h3>
                    @if($order->shippingAddress)
                        <p>{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                        <p>{{ $order->shippingAddress->address_line_1 }}</p>
                        @if($order->shippingAddress->address_line_2)
                            <p>{{ $order->shippingAddress->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }}</p>
                        <p>{{ $order->shippingAddress->country }} - {{ $order->shippingAddress->postal_code }}</p>
                        <p>الهاتف: {{ $order->shippingAddress->phone }}</p>
                    @else
                        <p>لا يوجد عنوان شحن</p>
                    @endif
                </div>
            </div>
            
            <!-- Order Items -->
            <h2 class="section-title">تفاصيل المنتجات</h2>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th class="text-right">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product->sku)
                                <br><small>SKU: {{ $item->product->sku }}</small>
                            @endif
                        </td>
                        <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->total_price, 2) }} ر.س</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Totals -->
            <div class="totals">
                <div class="total-row">
                    <span>المجموع الفرعي:</span>
                    <span>{{ number_format($order->subtotal, 2) }} ر.س</span>
                </div>
                <div class="total-row">
                    <span>تكلفة الشحن:</span>
                    <span>{{ number_format($order->shipping_cost, 2) }} ر.س</span>
                </div>
                <div class="total-row">
                    <span>الضريبة:</span>
                    <span>{{ number_format($order->tax, 2) }} ر.س</span>
                </div>
                <div class="total-row">
                    <span>الإجمالي النهائي:</span>
                    <span>{{ number_format($order->total, 2) }} ر.س</span>
                </div>
            </div>
            
            <!-- Notes -->
            @if($order->notes)
            <div class="notes">
                <h3>ملاحظات الطلب:</h3>
                <p>{{ $order->notes }}</p>
            </div>
            @endif
            
            <!-- Footer -->
            <div class="footer">
                <p>شكراً لشرائك من متجر المستلزمات الطبية</p>
                <p>هذه الفاتورة تم إنشاؤها تلقائياً بتاريخ {{ now()->format('Y-m-d H:i') }}</p>
                <p>للاستفسارات: info@medical-store.com | +966 11 123 4567</p>
            </div>
        </div>
    </div>
    
    <script>
        // طباعة تلقائية عند فتح الصفحة
        window.onload = function() {
            // يمكن تفعيل الطباعة التلقائية إذا أردت
            // window.print();
        };
        
        // إعادة حساب الارتفاع للطباعة
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>