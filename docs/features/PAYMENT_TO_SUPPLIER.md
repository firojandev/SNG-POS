# Payment to Supplier Feature Documentation

## Table of Contents
1. [Overview](#overview)
2. [Database Schema](#database-schema)
3. [File Structure](#file-structure)
4. [Business Logic](#business-logic)
5. [Backend Implementation](#backend-implementation)
6. [Frontend Implementation](#frontend-implementation)
7. [API Endpoints](#api-endpoints)
8. [Usage Guide](#usage-guide)
9. [Testing](#testing)
10. [Future Enhancements](#future-enhancements)

---

## Overview

The **Payment to Supplier** feature manages payments made to suppliers, tracking payment amounts, dates, and automatically updating supplier balances. It includes full CRUD operations with server-side pagination, real-time balance validation, and soft delete functionality.

### Key Features
- ✅ Full CRUD operations (Create, Read, Update, Delete)
- ✅ Automatic supplier balance management
- ✅ Real-time balance validation
- ✅ Server-side pagination for large datasets
- ✅ Advanced search and filtering
- ✅ Soft delete with balance restoration
- ✅ Multi-store support with global scopes
- ✅ Responsive DataTable with sorting
- ✅ Date picker integration
- ✅ Currency formatting
- ✅ Loading indicators and spinners

---

## Database Schema

### Table: `payment_to_suppliers`

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | NO | AUTO_INCREMENT | Primary key |
| `store_id` | BIGINT UNSIGNED | NO | - | Foreign key to stores table |
| `supplier_id` | BIGINT UNSIGNED | NO | - | Foreign key to suppliers table |
| `purchase_id` | BIGINT | YES | NULL | Optional reference to purchase |
| `amount` | DECIMAL(15,2) | NO | - | Payment amount |
| `payment_date` | DATE | NO | - | Date of payment |
| `note` | TEXT | YES | NULL | Optional note/description |
| `created_at` | TIMESTAMP | YES | NULL | Record creation timestamp |
| `updated_at` | TIMESTAMP | YES | NULL | Record update timestamp |
| `deleted_at` | TIMESTAMP | YES | NULL | Soft delete timestamp |

### Indexes
- Primary key on `id`
- Foreign key on `store_id` references `stores(id)`
- Foreign key on `supplier_id` references `suppliers(id)`
- Index on `deleted_at` for soft delete queries

### Migrations
```
2025_10_21_174000_create_payment_to_suppliers_table.php
2025_10_25_160624_add_purchase_id_to_payment_to_suppliers_table.php
2025_10_25_165311_add_deleted_at_to_payment_to_suppliers_table.php
```

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       └── PaymentToSupplierController.php
│   ├── Requests/
│   │   ├── PaymentToSupplierStoreRequest.php
│   │   └── PaymentToSupplierUpdateRequest.php
│   └── Resources/
│       └── PaymentToSupplierResource.php
├── Models/
│   └── PaymentToSupplier.php
│
database/
└── migrations/
    ├── 2025_10_21_174000_create_payment_to_suppliers_table.php
    ├── 2025_10_25_160624_add_purchase_id_to_payment_to_suppliers_table.php
    └── 2025_10_25_165311_add_deleted_at_to_payment_to_suppliers_table.php

public/
└── admin/
    └── partial/
        └── js/
            └── payment-to-supplier.js

resources/
└── views/
    └── admin/
        ├── PaymentToSupplier/
        │   └── index.blade.php
        └── Common/
            └── aside.blade.php (menu link added)

routes/
└── admin.php (routes registered)
```

---

## Business Logic

### 1. Supplier Balance Management

The system automatically manages supplier balances based on payment operations:

#### **Create Payment**
```
Supplier Balance = Current Balance - Payment Amount
```

#### **Update Payment**
- **Same Supplier:**
  ```
  Supplier Balance = Current Balance + Old Amount - New Amount
  ```

- **Different Supplier:**
  ```
  Old Supplier Balance = Current Balance + Old Amount
  New Supplier Balance = Current Balance - New Amount
  ```

#### **Delete Payment**
```
Supplier Balance = Current Balance + Payment Amount
```

### 2. Validation Rules

#### **Create Payment**
- Supplier must exist
- Amount must be > 0.01
- **Amount cannot exceed supplier's current balance**
- Payment date is required
- Note is optional

#### **Update Payment**
- Same as create, but:
- If supplier unchanged, old payment amount is added back to available balance for validation

### 3. Multi-Store Isolation

All queries are automatically scoped to the logged-in user's store via Global Scope:
```php
$builder->where('payment_to_suppliers.store_id', Auth::user()->store_id);
```

---

## Backend Implementation

### Model: `PaymentToSupplier.php`

**Location:** `app/Models/PaymentToSupplier.php`

#### Key Features:
- Uses `SoftDeletes` trait
- Auto-sets `store_id` on creation
- Global scope for store filtering
- Relationships: `supplier()`, `purchase()`
- Casts for `amount`, `payment_date`, timestamps

```php
class PaymentToSupplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'store_id', 'supplier_id', 'purchase_id',
        'amount', 'payment_date', 'note'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];
}
```

### Controller: `PaymentToSupplierController.php`

**Location:** `app/Http/Controllers/Admin/PaymentToSupplierController.php`

#### Methods:

| Method | Route | Description |
|--------|-------|-------------|
| `index()` | GET /admin/payment-to-supplier | Display list view |
| `getData()` | GET /admin/payment-to-supplier/get-data | Server-side pagination data |
| `getSupplierBalance()` | GET /admin/payment-to-supplier/get-supplier-balance | Get supplier balance |
| `store()` | POST /admin/payment-to-supplier | Create new payment |
| `edit()` | GET /admin/payment-to-supplier/{id}/edit | Get payment data |
| `update()` | PUT /admin/payment-to-supplier/{id} | Update payment |
| `destroy()` | DELETE /admin/payment-to-supplier/{id} | Soft delete payment |

#### Server-Side Pagination Features:
- Handles search across all columns
- Supports column sorting (including joined tables)
- Efficient pagination with `skip()` and `take()`
- Returns DataTables-compatible JSON response
- Default sorting: Latest payment_date first

### Form Requests

#### `PaymentToSupplierStoreRequest.php`
- Validates supplier exists
- Validates amount > 0.01
- Custom validator: Checks amount ≤ supplier balance
- Date format normalization

#### `PaymentToSupplierUpdateRequest.php`
- Same as store request
- Calculates available balance (adds back old amount if same supplier)
- Validates new amount against available balance

### Resource: `PaymentToSupplierResource.php`

Transforms payment data for API responses:
```php
return [
    'id' => $this->id,
    'supplier_id' => $this->supplier_id,
    'supplier' => ['id' => ..., 'name' => ...],
    'amount' => (float) $this->amount,
    'payment_date' => $this->payment_date->format(...),
    'note' => $this->note,
];
```

---

## Frontend Implementation

### View: `index.blade.php`

**Location:** `resources/views/admin/PaymentToSupplier/index.blade.php`

#### Structure:
```html
- Breadcrumb navigation
- Theme card with "Add" button
- DataTable for listing payments
- Modal for Create/Edit form
  ├── Supplier dropdown (Select2)
  ├── Supplier balance display (read-only)
  ├── Amount input with validation
  ├── Payment date picker
  └── Note textarea
```

#### Form Fields:
1. **Supplier** (required) - Select2 dropdown
2. **Supplier Balance** - Read-only, shown after supplier selection
3. **Amount** (required) - Number input with real-time validation
4. **Payment Date** (required) - jQuery UI datepicker
5. **Note** (optional) - Textarea

### JavaScript: `payment-to-supplier.js`

**Location:** `public/admin/partial/js/payment-to-supplier.js`

#### Key Functions:

| Function | Purpose |
|----------|---------|
| `initializeDataTable()` | Initialize server-side DataTable |
| `reloadDataTable()` | Refresh table after CRUD operations |
| `fetchSupplierBalance()` | Get supplier balance via AJAX |
| `validateAmount()` | Real-time amount validation |
| `openCreateModal()` | Open modal for creating payment |
| `openEditModal(id)` | Load and open edit modal |
| `openDeleteModal(id)` | Confirm and delete payment |
| `handleFormSubmit()` | Process form submission |
| `resetForm()` | Clear form and reset state |

#### DataTable Configuration:
```javascript
{
    serverSide: true,
    order: [[2, 'desc']], // Sort by payment_date (latest first)
    pageLength: 10,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    // ... columns, ajax, etc.
}
```

#### Real-Time Validation:
- Triggers on amount input
- Compares against current supplier balance
- Shows/hides error messages
- Prevents form submission if invalid

---

## API Endpoints

### Base URL: `/admin/payment-to-supplier`

### 1. Get Payments List (Paginated)
```
GET /admin/payment-to-supplier
```
**Response:** HTML view

### 2. Get Payments Data (AJAX)
```
GET /admin/payment-to-supplier/get-data

Query Parameters:
- draw: int (request counter)
- start: int (offset)
- length: int (records per page)
- search[value]: string (search term)
- order[0][column]: int (column index)
- order[0][dir]: string (asc|desc)

Response:
{
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 50,
    "data": [...]
}
```

### 3. Get Supplier Balance
```
GET /admin/payment-to-supplier/get-supplier-balance

Query Parameters:
- supplier_id: int (required)
- payment_id: int (optional, for edit mode)

Response:
{
    "success": true,
    "balance": 5000.00,
    "formatted_balance": "$5,000.00"
}
```

### 4. Create Payment
```
POST /admin/payment-to-supplier

Body (FormData):
- supplier_id: int (required)
- amount: decimal (required)
- payment_date: date (required)
- note: string (optional)

Response:
{
    "success": true,
    "message": "Payment created successfully",
    "data": {...}
}
```

### 5. Edit Payment
```
GET /admin/payment-to-supplier/{id}/edit

Response:
{
    "success": true,
    "data": {
        "id": 1,
        "supplier_id": 5,
        "amount": 1000.00,
        "payment_date": "2025-10-25",
        "note": "..."
    }
}
```

### 6. Update Payment
```
PUT /admin/payment-to-supplier/{id}

Body (FormData):
- supplier_id: int (required)
- amount: decimal (required)
- payment_date: date (required)
- note: string (optional)
- _method: PUT

Response:
{
    "success": true,
    "message": "Payment updated successfully",
    "data": {...}
}
```

### 7. Delete Payment
```
DELETE /admin/payment-to-supplier/{id}

Body:
- _method: DELETE

Response:
{
    "success": true,
    "message": "Payment deleted successfully"
}
```

---

## Usage Guide

### Creating a Payment

1. Navigate to **Manage Payment → Payment to Supplier**
2. Click **"Add"** button
3. Select a supplier from dropdown
4. System displays supplier's current balance
5. Enter payment amount (must be ≤ balance)
6. Select payment date
7. Add optional note
8. Click **"Save"**

**Result:** Payment is created, supplier balance is reduced

### Editing a Payment

1. Click **"Edit"** button on any payment row
2. Modify fields as needed
3. System recalculates available balance
4. Click **"Update"**

**Result:**
- If supplier changed: Old supplier gets refund, new supplier is charged
- If same supplier: Balance is adjusted by the difference

### Deleting a Payment

1. Click **"Delete"** button on any payment row
2. Confirm deletion in popup
3. Payment is soft deleted

**Result:** Supplier balance is increased (refund)

### Searching Payments

Use the search box to find payments by:
- Supplier name
- Amount
- Payment date
- Note

### Sorting Payments

Click column headers to sort by:
- Supplier name
- Amount
- Payment date
- Note (if enabled)

**Default:** Sorted by payment date (latest first)

---

## Testing

### Manual Testing Checklist

#### Create Payment
- [ ] Can create payment with valid data
- [ ] Cannot create payment exceeding supplier balance
- [ ] Supplier balance decreases correctly
- [ ] Validation errors display properly
- [ ] Date picker works correctly
- [ ] Select2 loads suppliers

#### Update Payment
- [ ] Can update payment with same supplier
- [ ] Can change supplier on payment
- [ ] Balance adjusts correctly for same supplier
- [ ] Old supplier gets refund when changing supplier
- [ ] Cannot exceed new supplier's balance
- [ ] Validation works in edit mode

#### Delete Payment
- [ ] Soft delete works correctly
- [ ] Supplier balance increases (refund)
- [ ] Deleted payments don't appear in list
- [ ] Confirmation dialog appears

#### Pagination
- [ ] Table loads with 10 records by default
- [ ] Can change page size (10/25/50/100/All)
- [ ] Navigation buttons work
- [ ] Page info displays correctly

#### Search
- [ ] Can search by supplier name
- [ ] Can search by amount
- [ ] Can search by date
- [ ] Can search by note
- [ ] Search results update in real-time

#### Sorting
- [ ] Can sort by each column
- [ ] Default sort is payment_date desc
- [ ] Sort direction toggles correctly
- [ ] Sort indicator shows current column

#### Multi-Store
- [ ] Users only see their store's payments
- [ ] Cannot access other stores' data
- [ ] Store ID auto-set on creation

---

## Future Enhancements

### Planned Features
1. **Bulk Payments** - Pay multiple suppliers at once
2. **Payment Methods** - Track payment method (cash, check, transfer)
3. **Payment Receipt** - Generate PDF receipts
4. **Payment Schedule** - Scheduled/recurring payments
5. **Partial Payments** - Link payments to specific purchases
6. **Payment Analytics** - Reports and charts
7. **Export** - Export payments to CSV/Excel
8. **Payment Approval** - Multi-level approval workflow
9. **Payment Notifications** - Email/SMS notifications
10. **Payment History** - Audit trail of changes

### Technical Improvements
1. Add unit tests for balance calculations
2. Add integration tests for CRUD operations
3. Implement caching for supplier balances
4. Add queue jobs for bulk operations
5. Implement event listeners for payment activities
6. Add payment number/reference generation
7. Optimize queries with eager loading
8. Add Redis cache for frequent queries

---

## Troubleshooting

### Common Issues

#### Issue: "Failed to fetch payments"
**Solution:** Check Laravel logs, verify database connection, ensure migrations are run

#### Issue: "Amount exceeds supplier balance"
**Solution:** Verify supplier balance in database, check if other payments are reducing balance

#### Issue: "Supplier balance not displaying"
**Solution:** Check browser console for JavaScript errors, verify AJAX endpoint is accessible

#### Issue: Ambiguous column error
**Solution:** Ensure all column references in queries use fully qualified table names

---

## Developer Notes

### Important Considerations
1. Always use transactions when updating balances
2. Qualify column names with table names to avoid ambiguity
3. Test balance calculations thoroughly
4. Use server-side validation in addition to client-side
5. Handle edge cases (deleted suppliers, negative balances)
6. Maintain audit trail for compliance

### Code Standards
- Follow PSR-12 coding standards
- Use type hints for all method parameters
- Document complex business logic
- Write meaningful commit messages
- Use database transactions for critical operations

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2025-10-25 | Initial release with full CRUD |
| 1.1.0 | 2025-10-25 | Added supplier balance management |
| 1.2.0 | 2025-10-25 | Implemented server-side pagination |
| 1.3.0 | 2025-10-25 | Added soft delete functionality |
| 1.4.0 | 2025-10-25 | Fixed default sorting to latest first |

---

## References

- [Laravel Documentation](https://laravel.com/docs)
- [DataTables Documentation](https://datatables.net/)
- [jQuery UI Datepicker](https://jqueryui.com/datepicker/)
- [Select2 Documentation](https://select2.org/)

---

**Last Updated:** October 25, 2025
**Maintained By:** Development Team
**Contact:** tech@yourcompany.com
