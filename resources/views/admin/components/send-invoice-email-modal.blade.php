<!-- Send Invoice Email Modal -->
<div class="modal fade" id="sendInvoiceEmailModal" tabindex="-1" aria-labelledby="sendInvoiceEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendInvoiceEmailModalLabel">
                    <i class="fa fa-envelope me-2"></i>Send Invoice via Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sendInvoiceEmailForm">
                @csrf
                <input type="hidden" id="invoice_uuid" name="invoice_uuid" value="{{ $invoice->uuid }}">

                <div class="modal-body">
                    <!-- Alert Container -->
                    <div id="emailAlertContainer"></div>

                    <!-- Recipient Email -->
                    <div class="mb-3">
                        <label for="customer_email" class="form-label">
                            Recipient Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email"
                               value="{{ $invoice->customer->email ?? '' }}"
                               placeholder="customer@example.com" required>
                        <small class="form-text text-muted">
                            @if($invoice->customer && $invoice->customer->name)
                                Customer: {{ $invoice->customer->name }}
                            @else
                                Enter recipient email address
                            @endif
                        </small>
                    </div>

                    <!-- Email Subject -->
                        <div class="mb-3">
                            <label for="email_subject" class="form-label">
                                Subject <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="email_subject" name="email_subject"
                                   value="Invoice {{ $invoice->invoice_number }} from {{ get_option('app_name') }}"
                                   required>
                            <small class="form-text text-muted">Email subject line</small>
                        </div>

                    <!-- Email Body -->
                    <div class="mb-3">
                        <label for="email_body" class="form-label">
                            Message <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control summernote" id="email_body" name="email_body" rows="10" required><p>Dear <strong>{{ $invoice->customer->name ?? 'Customer' }}</strong>,</p>

<p>Please find attached invoice <strong>{{ $invoice->invoice_number }}</strong> dated <strong>{{ \Carbon\Carbon::parse($invoice->date ?? $invoice->created_at)->format(get_option('date_format', 'Y-m-d')) }}</strong>.</p>

<p><strong>Invoice Details:</strong></p>
<ul>
    <li>Invoice Number: <strong>{{ $invoice->invoice_number }}</strong></li>
    <li>Total Amount: <strong>{{ get_option('app_currency', '$') }}{{ number_format($invoice->payable_amount, 2) }}</strong></li>
    <li>Paid Amount: <strong>{{ get_option('app_currency', '$') }}{{ number_format($invoice->paid_amount, 2) }}</strong></li>
    <li>Due Amount: <strong>{{ get_option('app_currency', '$') }}{{ number_format($invoice->due_amount, 2) }}</strong></li>
</ul>

<p>If you have any questions about this invoice, please contact us.</p>

<p>Thank you for your business!</p>

<p>Best regards,<br>
<strong>{{ get_option('app_name') }}</strong></p></textarea>
                        <small class="form-text text-muted">Email message body (supports rich text formatting)</small>
                    </div>

                    <!-- Invoice Attachment Info -->
                    <div class="alert alert-success d-flex align-items-center">
                        <i class="fa fa-paperclip me-2"></i>
                        <div>
                            <strong>Attachment:</strong> Invoice {{ $invoice->invoice_number }}.pdf will be attached automatically
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="sendEmailBtn">
                        <i class="fa fa-paper-plane me-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
