
@php
    $modalId = $modalId ?? 'paymentFromCustomerModal';
    $formId = $formId ?? 'paymentFromCustomerForm';
@endphp

<!-- Payment from Customer Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">Receive Payment from Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="{{ $formId }}">
                <div class="modal-body">
                    <input type="hidden" id="invoice_uuid" name="invoice_uuid">
                    <input type="hidden" id="customer_id" name="customer_id">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Invoice Number</label>
                        <input type="text" class="form-control" id="invoice_number_display" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Due Amount</label>
                        <input type="text" class="form-control text-danger fw-bold" id="due_amount_display" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <input type="number"
                               step="0.01"
                               min="0.01"
                               class="form-control"
                               id="payment_amount"
                               name="amount"
                               placeholder="Enter payment amount"
                               required
                               aria-describedby="amountError">
                        <div class="invalid-feedback" id="amountError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control"
                               id="payment_date"
                               name="payment_date"
                               required
                               aria-describedby="payment_dateError">
                        <div class="invalid-feedback" id="payment_dateError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="payment_note" class="form-label">Note</label>
                        <textarea class="form-control"
                                  id="payment_note"
                                  name="note"
                                  rows="3"
                                  placeholder="Enter payment note (optional)"
                                  aria-describedby="noteError"></textarea>
                        <div class="invalid-feedback" id="noteError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="paymentFromCustomerSaveBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="paymentFromCustomerSaveSpinner" role="status" aria-hidden="true"></span>
                        Receive Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
