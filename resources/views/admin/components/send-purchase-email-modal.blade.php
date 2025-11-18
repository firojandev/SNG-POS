<!-- Send Purchase Email Modal -->
<div class="modal fade" id="sendPurchaseEmailModal" tabindex="-1" aria-labelledby="sendPurchaseEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendPurchaseEmailModalLabel">
                    <i class="fa fa-envelope me-2"></i>Send Purchase Invoice via Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendPurchaseEmailForm">
                @csrf
                <input type="hidden" id="purchase_uuid" name="purchase_uuid" value="{{ $purchase->uuid }}">

                <div class="modal-body">
                    <!-- Alert Container -->
                    <div id="purchaseEmailAlertContainer"></div>

                    <!-- Recipient Email -->
                    <div class="mb-3">
                        <label for="supplier_email" class="form-label">
                            Recipient Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="supplier_email" name="supplier_email"
                               value="{{ $purchase->supplier->email ?? '' }}"
                               placeholder="supplier@example.com" required>
                        <small class="form-text text-muted">
                            @if($purchase->supplier && $purchase->supplier->name)
                                Supplier: {{ $purchase->supplier->name }}
                            @else
                                Enter recipient email address
                            @endif
                        </small>
                    </div>

                    <!-- Email Subject -->
                    <div class="mb-3">
                        <label for="purchase_email_subject" class="form-label">
                            Subject <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="purchase_email_subject" name="email_subject"
                               value="Purchase Order {{ $purchase->invoice_number }} from {{ get_option('app_name') }}"
                               required>
                        <small class="form-text text-muted">Email subject line</small>
                    </div>

                    <!-- Email Body -->
                    <div class="mb-3">
                        <label for="purchase_email_body" class="form-label">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control summernote" id="purchase_email_body" name="email_body" rows="10" required><p>Dear <strong>{{ $purchase->supplier->name ?? 'Supplier' }}</strong>,</p>

<p>Please find attached purchase order <strong>{{ $purchase->invoice_number }}</strong> dated <strong>{{ \Carbon\Carbon::parse($purchase->date ?? $purchase->created_at)->format(get_option('date_format', 'Y-m-d')) }}</strong>.</p>

<p><strong>Purchase Order Details:</strong></p>
<ul>
    <li>PO Number: <strong>{{ $purchase->invoice_number }}</strong></li>
    <li>Total Amount: <strong>{{ get_option('app_currency', '$') }}{{ number_format($purchase->total_amount, 2) }}</strong></li>
    <li>Paid Amount: <strong>{{ get_option('app_currency', '$') }}{{ number_format($purchase->paid_amount, 2) }}</strong></li>
    <li>Due Amount: <strong>{{ get_option('app_currency', '$') }}{{ number_format($purchase->due_amount, 2) }}</strong></li>
</ul>

<p>If you have any questions about this purchase order, please contact us.</p>

<p>Thank you for your service!</p>

<p>Best regards,<br>
<strong>{{ get_option('app_name') }}</strong></p></textarea>
                        <small class="form-text text-muted">Email message body (supports rich text formatting)</small>
                    </div>

                    <!-- Purchase Attachment Info -->
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fa fa-paperclip me-2"></i>
                        <div>
                            <strong>Attachment:</strong> Purchase Order {{ $purchase->invoice_number }}.pdf will be attached automatically
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="sendPurchaseEmailBtn">
                        <i class="fa fa-paper-plane me-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
