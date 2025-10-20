<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Invoice - {{ $purchase->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .invoice-info table {
            width: 100%;
        }
        .invoice-info td {
            padding: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .items-table tfoot td {
            font-weight: bold;
            background-color: #f5f5f5;
            border-top: 2px solid #333;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-table {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
        }
        .summary-table td {
            padding: 5px;
        }
        .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PURCHASE INVOICE</h1>
        <p>{{ get_option('app_name', 'SNG POS') }}</p>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td width="50%">
                    <strong>Invoice Number:</strong> {{ $purchase->invoice_number }}<br>
                    <strong>Date:</strong> {{ $purchase->created_at->format('M d, Y') }}<br>
                    <strong>Time:</strong> {{ $purchase->created_at->format('h:i A') }}
                </td>
                <td width="50%">
                    <strong>Supplier:</strong> {{ $purchase->supplier->name }}<br>
                    <strong>Phone:</strong> {{ $purchase->supplier->phone }}<br>
                    @if($purchase->supplier->email)
                    <strong>Email:</strong> {{ $purchase->supplier->email }}<br>
                    @endif
                    @if($purchase->supplier->address)
                    <strong>Address:</strong> {{ $purchase->supplier->address }}
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="35%">Product</th>
                <th width="12%">SKU</th>
                <th width="12%" class="text-right">Unit Price</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="12%" class="text-right">Tax</th>
                <th width="14%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    {{ $item->product->name }}
                    @if($item->product->category)
                        <br><small style="color: #666;">({{ $item->product->category->name }})</small>
                    @endif
                </td>
                <td>{{ $item->product->sku }}</td>
                <td class="text-right">{{ $item->formatted_unit_price }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">
                    @if($item->tax_amount > 0)
                        {{ $item->formatted_tax_amount }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">{{ $item->formatted_unit_total }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Grand Total:</td>
                <td class="text-right">{{ $purchase->formatted_total_amount }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="summary-table">
        <tr>
            <td><strong>Total Amount:</strong></td>
            <td class="text-right">{{ $purchase->formatted_total_amount }}</td>
        </tr>
        <tr>
            <td><strong>Paid Amount:</strong></td>
            <td class="text-right" style="color: green;">{{ $purchase->formatted_paid_amount }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Due Amount:</strong></td>
            <td class="text-right" style="color: {{ $purchase->due_amount > 0 ? 'red' : 'green' }};">
                {{ $purchase->formatted_due_amount }}
            </td>
        </tr>
    </table>

    @if($purchase->note)
    <div style="margin-top: 30px; padding: 10px; background-color: #f5f5f5; border-left: 4px solid #2c3e50;">
        <strong>Note:</strong><br>
        {{ $purchase->note }}
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
