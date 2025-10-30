<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Invoice - {{ $invoice->invoice_number }}</title>
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
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        .status-active {
            background-color: #28a745;
            color: white;
        }
        .status-returned {
            background-color: #ffc107;
            color: #333;
        }
        .status-cancelled {
            background-color: #6c757d;
            color: white;
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
        <h1>SALES INVOICE</h1>
        <p>{{ get_option('app_name', 'SNG POS') }}</p>
        @if($invoice->status === 'active')
            <span class="status-badge status-active">ACTIVE</span>
        @elseif($invoice->status === 'returned')
            <span class="status-badge status-returned">RETURNED</span>
        @else
            <span class="status-badge status-cancelled">CANCELLED</span>
        @endif
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td width="50%">
                    <strong>Invoice Number:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Invoice Date:</strong>
                    @if($invoice->date)
                        {{ \Carbon\Carbon::parse($invoice->date)->format(get_option('date_format', 'Y-m-d')) }}
                    @else
                        {{ $invoice->created_at->format('M d, Y') }}
                    @endif
                    <br>
                    <strong>Created At:</strong> {{ $invoice->created_at->format('M d, Y h:i A') }}
                </td>
                <td width="50%">
                    <strong>Customer:</strong> {{ $invoice->customer->name }}<br>
                    <strong>Phone:</strong> {{ $invoice->customer->phone }}<br>
                    @if($invoice->customer->email)
                    <strong>Email:</strong> {{ $invoice->customer->email }}<br>
                    @endif
                    @if($invoice->customer->address)
                    <strong>Address:</strong> {{ $invoice->customer->address }}
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
                <th width="12%" class="text-right">VAT</th>
                <th width="14%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
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
                    @if($item->vat_amount > 0)
                        {{ $item->formatted_vat_amount }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">{{ $item->formatted_unit_total }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td><strong>Unit Total:</strong></td>
            <td class="text-right">{{ $invoice->formatted_unit_total }}</td>
        </tr>
        <tr>
            <td><strong>Total VAT:</strong></td>
            <td class="text-right">{{ $invoice->formatted_total_vat }}</td>
        </tr>
        <tr>
            <td><strong>Total Amount:</strong></td>
            <td class="text-right">{{ $invoice->formatted_total_amount }}</td>
        </tr>
        @if($invoice->discount > 0)
        <tr>
            <td><strong>Discount:</strong></td>
            <td class="text-right" style="color: red;">- {{ $invoice->formatted_discount }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>Payable Amount:</strong></td>
            <td class="text-right">{{ $invoice->formatted_payable_amount }}</td>
        </tr>
        <tr>
            <td><strong>Paid Amount:</strong></td>
            <td class="text-right" style="color: green;">{{ $invoice->formatted_paid_amount }}</td>
        </tr>
        <tr class="total-row">
            <td><strong>Due Amount:</strong></td>
            <td class="text-right" style="color: {{ $invoice->due_amount > 0 ? 'red' : 'green' }};">
                {{ $invoice->formatted_due_amount }}
            </td>
        </tr>
    </table>

    @if($invoice->note)
    <div style="margin-top: 30px; padding: 10px; background-color: #f5f5f5; border-left: 4px solid #2c3e50;">
        <strong>Note:</strong><br>
        {{ $invoice->note }}
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('M d, Y h:i A') }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
