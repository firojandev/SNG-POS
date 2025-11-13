<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sale Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            margin-bottom: 20px;
        }
        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .logo-section {
            display: table-cell;
            width: 30%;
            vertical-align: top;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .store-info {
            display: table-cell;
            width: 70%;
            text-align: right;
            vertical-align: top;
        }
        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .store-details {
            font-size: 11px;
            line-height: 1.6;
            color: #666;
        }
        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            padding: 10px 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            color: #2c3e50;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .billing-section {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
        }
        .billing-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
            color: #2c3e50;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f8f9fa;
            padding: 8px;
            font-size: 11px;
            border: 1px solid #dee2e6;
        }
        .items-table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-section {
            margin-top: 20px;
        }
        .summary-table {
            width: 350px;
            margin-left: auto;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 6px 10px;
            font-size: 11px;
        }
        .summary-table .label-col {
            text-align: right;
            font-weight: bold;
            width: 60%;
        }
        .summary-table .value-col {
            text-align: right;
            width: 40%;
        }
        .summary-row {
            border-bottom: 1px solid #dee2e6;
        }
        .total-row {
            font-size: 13px;
            font-weight: bold;
            background-color: #f8f9fa;
            border-top: 2px solid #333;
        }
        .note-section {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #2c3e50;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="logo-section">
                @if(get_option('app_logo'))
                    @php
                        $logoPath = public_path('storage/' . get_option('app_logo'));
                        if(file_exists($logoPath)) {
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoExtension = pathinfo($logoPath, PATHINFO_EXTENSION);
                            $logoMimeType = $logoExtension === 'png' ? 'image/png' : ($logoExtension === 'jpg' || $logoExtension === 'jpeg' ? 'image/jpeg' : 'image/' . $logoExtension);
                        }
                    @endphp
                    @if(isset($logoData))
                        <img src="data:{{ $logoMimeType }};base64,{{ $logoData }}" alt="Logo" class="logo">
                    @endif
                @endif
            </div>
            <div class="store-info">
                <div class="store-name">{{ $invoice->store->name ?? get_option('app_name', 'SNG POS') }}</div>
                <div class="store-details">
                    @if(isset($invoice->store))
                        @if($invoice->store->address)
                            {{ $invoice->store->address }}<br>
                        @endif
                        @if($invoice->store->phone_number)
                            Phone: {{ $invoice->store->phone_number }}<br>
                        @endif
                        @if($invoice->store->email)
                            Email: {{ $invoice->store->email }}
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="invoice-title">Sale INVOICE</div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <td class="info-label">Invoice No:</td>
                            <td>{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Invoice Date:</td>
                            <td>
                                @if($invoice->date)
                                    {{ \Carbon\Carbon::parse($invoice->date)->format(get_option('date_format', 'd/m/Y')) }}
                                @else
                                    {{ $invoice->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="info-label">Status:</td>
                            <td style="text-transform: uppercase; font-weight: bold;">{{ $invoice->status }}</td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <div class="billing-section">
                        <div class="billing-title">BILLING TO:</div>
                        <strong>{{ $invoice->customer->name }}</strong><br>
                        @if($invoice->customer->phone)
                            Phone: {{ $invoice->customer->phone }}<br>
                        @endif
                        @if($invoice->customer->email)
                            Email: {{ $invoice->customer->email }}<br>
                        @endif
                        @if($invoice->customer->address)
                            {{ $invoice->customer->address }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="30%">Product Name</th>
                <th width="12%">SKU</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="13%" class="text-center">Unit Price</th>
                <th width="12%" class="text-center">VAT</th>
                <th width="18%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $item->product->name }}
                    @if($item->product->category)
                        <br><small style="color: #666;">({{ $item->product->category->name }})</small>
                    @endif
                </td>
                <td>{{ $item->product->sku }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->formatted_unit_price }}</td>
                <td class="text-center">
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

    <div class="summary-section">
        <table class="summary-table">
            <tr class="summary-row">
                <td class="label-col">Unit Total:</td>
                <td class="value-col">{{ $invoice->formatted_unit_total }}</td>
            </tr>
            <tr class="summary-row">
                <td class="label-col">Total VAT:</td>
                <td class="value-col">{{ $invoice->formatted_total_vat }}</td>
            </tr>
            <tr class="summary-row">
                <td class="label-col">Total Amount:</td>
                <td class="value-col">{{ $invoice->formatted_total_amount }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr class="summary-row">
                <td class="label-col">Discount:</td>
                <td class="value-col" style="color: red;">- {{ $invoice->formatted_discount_amount }}</td>
            </tr>
            @endif
            <tr class="summary-row">
                <td class="label-col">Payable Amount:</td>
                <td class="value-col">{{ $invoice->formatted_payable_amount }}</td>
            </tr>
            <tr class="summary-row">
                <td class="label-col">Paid Amount:</td>
                <td class="value-col" style="color: green;">{{ $invoice->formatted_paid_amount }}</td>
            </tr>
            <tr class="total-row">
                <td class="label-col">Due Amount:</td>
                <td class="value-col" style="color: {{ $invoice->due_amount > 0 ? 'red' : 'green' }};">
                    {{ $invoice->formatted_due_amount }}
                </td>
            </tr>
        </table>
    </div>

    @if($invoice->note)
    <div class="note-section">
        <strong>Note:</strong><br>
        {{ $invoice->note }}
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y h:i A') }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
