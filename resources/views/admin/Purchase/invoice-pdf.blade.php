<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Invoice - {{ $purchase->invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'Noto Sans Bengali';
            font-style: normal;
            font-weight: 400;
            src: url('{{ public_path('fonts/NotoSansBengali-Regular.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Noto Sans Bengali';
            font-style: normal;
            font-weight: 700;
            src: url('{{ public_path('fonts/NotoSansBengali-Regular.ttf') }}') format('truetype');
        }
        @page {
            margin: 15px;
        }
        body {
            font-family: 'Noto Sans Bengali', 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
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
            width: 20%;
            vertical-align: middle;
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
        }
        .store-info {
            display: table-cell;
            width: 80%;
            text-align: center;
            vertical-align: middle;
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
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
            padding: 10px 0;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            color: #2c3e50;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .billing-section {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #dee2e6;
        }
        .billing-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 4px;
            color: #2c3e50;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f8f9fa;
            padding: 5px;
            font-size: 11px;
            border: 1px solid #dee2e6;
        }
        .items-table td {
            padding: 5px;
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
            width: 70%;
        }
        .summary-table .value-col {
            text-align: right;
            width: 30%;
        }
        .summary-row {
            border-bottom: 1px solid #dee2e6;
        }
        .total-row {
            font-size: 13px;
            font-weight: bold;
            background-color: #f8f9fa;
            border-top: 2px solid #333;
            font-family: 'Noto Sans Bengali', 'DejaVu Sans', sans-serif;
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
            <div class="store-info">
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
                        <img src="data:{{ $logoMimeType }};base64,{{ $logoData }}" alt="Logo" class="logo" height="80">
                    @endif
                @endif
                <div class="store-name">{{ $purchase->store->name ?? get_option('app_name', 'SNG POS') }}</div>
                <div class="store-details">
                    @if(isset($purchase->store))
                        @if($purchase->store->address)
                            {{ $purchase->store->address }}<br>
                        @endif
                        @if($purchase->store->phone_number)
                            Phone: {{ $purchase->store->phone_number }}<br>
                        @endif
                        @if($purchase->store->email)
                            Email: {{ $purchase->store->email }}
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="invoice-title">PURCHASE INVOICE</div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <td class="info-label">Invoice No:</td>
                            <td>{{ $purchase->invoice_number }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Invoice Date:</td>
                            <td>
                                @if($purchase->date)
                                    {{ \Carbon\Carbon::parse($purchase->date)->format(get_option('date_format', 'd/m/Y')) }}
                                @else
                                    {{ $purchase->created_at->format('d/m/Y') }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="50%">
                    <div class="billing-section">
                        <div class="billing-title">SUPPLIER:</div>
                        <strong>{{ $purchase->supplier->name }}</strong><br>
                        @if($purchase->supplier->phone)
                            Phone: {{ $purchase->supplier->phone }}<br>
                        @endif
                        @if($purchase->supplier->email)
                            Email: {{ $purchase->supplier->email }}<br>
                        @endif
                        @if($purchase->supplier->address)
                            {{ $purchase->supplier->address }}
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
                <th width="30%" style="text-align: left;">Product Name</th>
                <th width="12%">SKU</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Unit Price</th>
                <th width="13%" class="text-right">Tax</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $item->product->name }}
                    @if($item->product->category)
                        <small style="color: #666;">({{ $item->product->category->name }})</small>
                    @endif
                </td>
                <td>{{ $item->product->sku }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ $item->formatted_unit_price }}</td>
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
    </table>

    <div class="summary-section">
        <table class="summary-table">
            <tr class="summary-row">
                <td class="label-col">Total Amount:</td>
                <td class="value-col">{{ $purchase->formatted_total_amount }}</td>
            </tr>
            <tr class="summary-row">
                <td class="label-col">Paid Amount:</td>
                <td class="value-col" style="color: green;">{{ $purchase->formatted_paid_amount }}</td>
            </tr>
            <tr class="total-row">
                <td class="label-col">Due Amount:</td>
                <td class="value-col" style="color: {{ $purchase->due_amount > 0 ? 'red' : 'green' }}; font-family: 'Noto Sans Bengali', 'DejaVu Sans', sans-serif;">
                    {{ $purchase->formatted_due_amount }}
                </td>
            </tr>
        </table>
    </div>

    @if($purchase->note)
    <div class="note-section">
        <strong>Note:</strong><br>
        {{ $purchase->note }}
    </div>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('d/m/Y h:i A') }}</p>
        <p>Thank you for your business!</p>
    </div>
</body>
</html>
