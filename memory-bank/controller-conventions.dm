# Controller Development Conventions

## Overview
This document outlines the standardized conventions for controller development in the SNG POS application.

## 1. Controller Data Format

### Standard Format
All controllers must follow this exact format for passing data to views:

```php
public function index()
{
    $data['title'] = 'Page Title';
    $data['items'] = Model::all();
    $data['otherData'] = 'value';
    return view('admin.module.index', $data)->with('menu', 'menu_name');
}
```

### Example Implementation
```php
// SettingsController
public function index()
{
    $data['title'] = 'General Settings';
    return view('admin.settings.index', $data)->with('menu', 'settings');
}

// CurrencyController
public function index()
{
    $data['title'] = 'Currencies';
    $data['currencies'] = Currency::all();
    $data['currentCurrency'] = Option::get('app_currency', '$');
    return view('admin.currency.index', $data)->with('menu', 'currency');
}
```

### Key Requirements
- ✅ Use `$data` array for ALL view variables
- ✅ Always include `$data['title']` for page title
- ✅ Use `->with('menu', 'menu_name')` for sidebar active state
- ✅ Follow consistent naming conventions
- ✅ Pass `$data` as single parameter to view

## 2. Validation Rules

### Request Files Only
All validation rules MUST be moved to Request files, NOT in controllers.

### File Structure
```
app/Http/Requests/Admin/
├── UpdateSettingsRequest.php
├── StoreCurrencyRequest.php
├── SetCurrencyRequest.php
└── [ModuleName]Request.php
```

### Request File Template
```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ExampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Always true for admin requests
    }

    public function rules(): array
    {
        return [
            'field' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'field.required' => 'This field is required.',
            'field.max' => 'This field cannot exceed 255 characters.',
            'email.unique' => 'This email is already taken.',
            'image.mimes' => 'Image must be: jpeg, png, jpg, gif.',
        ];
    }
}
```

### Controller Usage
```php
use App\Http\Requests\Admin\ExampleRequest;

class ExampleController extends Controller
{
    public function store(ExampleRequest $request)
    {
        // Validation is automatically handled
        // Access validated data with $request->field
        
        Model::create([
            'field' => $request->field,
            'email' => $request->email,
        ]);
        
        notyf()->success('Item created successfully');
        return redirect()->back();
    }
}
```

## 3. SweetAlert Implementation

### Delete Confirmations
Use SweetAlert2 instead of browser `confirm()` for all delete operations.

### HTML Button Structure
```html
<button type="button" class="btn btn-sm btn-outline-danger delete-item" 
        data-id="{{ $item->id }}" 
        data-name="{{ $item->name }}"
        data-extra="{{ $item->extra_field }}">
    <i class="fa fa-trash"></i>
</button>
```

### JavaScript Implementation
```javascript
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-item').forEach(function(button) {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-name');

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete "${itemName}"? This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('admin.items.destroy', '') }}/${itemId}`;
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Add DELETE method
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
```

## 4. Controller Structure

### Standard Controller Template
```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreItemRequest;
use App\Http\Requests\Admin\UpdateItemRequest;
use App\Models\Item;

class ItemController extends Controller
{
    public function index()
    {
        $data['title'] = 'Items';
        $data['items'] = Item::all();
        return view('admin.items.index', $data)->with('menu', 'items');
    }

    public function create()
    {
        $data['title'] = 'Add New Item';
        return view('admin.items.create', $data)->with('menu', 'items');
    }

    public function store(StoreItemRequest $request)
    {
        Item::create($request->validated());
        
        notyf()->success('Item created successfully');
        return redirect()->route('admin.items.index');
    }

    public function show($id)
    {
        $data['title'] = 'Item Details';
        $data['item'] = Item::findOrFail($id);
        return view('admin.items.show', $data)->with('menu', 'items');
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Item';
        $data['item'] = Item::findOrFail($id);
        return view('admin.items.edit', $data)->with('menu', 'items');
    }

    public function update(UpdateItemRequest $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->update($request->validated());
        
        notyf()->success('Item updated successfully');
        return redirect()->route('admin.items.index');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $item->delete();
        
        notyf()->success('Item deleted successfully');
        return redirect()->back();
    }
}
```

## 5. Notification Standards

### Success Messages
```php
notyf()->success('Operation completed successfully');
```

### Error Messages
```php
notyf()->error('Operation failed');
```

### Warning Messages
```php
notyf()->warning('Please check your input');
```

## 6. File Organization

### Controllers
- Keep controllers clean and focused
- Use Request files for all validation
- Follow consistent naming conventions
- Use proper HTTP status codes

### Requests
- One request file per form/operation
- Include custom error messages
- Use appropriate validation rules
- Set authorize() to true for admin requests

### Views
- Use SweetAlert for confirmations
- Follow consistent HTML structure
- Include proper error handling
- Use data attributes for JavaScript interactions
- Include external JavaScript files using @push('js')

### JavaScript Files
- Create separate JS files for each module in `public/admin/partial/js/`
- Use existing global functions from `global.js`
- Follow consistent naming conventions
- Use jQuery for DOM manipulation
- Implement proper error handling

## 6. JavaScript File Conventions

### File Structure
Create separate JavaScript files for each module:
```
public/admin/partial/js/
├── global.js (global utilities)
├── currency.js (currency module)
├── products.js (products module)
├── category.js (category module)
└── [module].js
```

### JavaScript File Template
```javascript
"use strict";

/**
 * Module Name JavaScript
 * This file contains all JavaScript functionality for [module] management
 */

$(document).ready(function() {
    initializeModuleModule();
});

/**
 * Initialize module
 */
function initializeModuleModule() {
    setupDeleteHandlers();
    setupFormValidation();
}

/**
 * Setup delete handlers using global SweetAlert function
 */
function setupDeleteHandlers() {
    $(document).on('click', '.delete-item', function() {
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');
        
        deleteItem(itemId, itemName);
    });
}

/**
 * Delete item with confirmation
 */
function deleteItem(itemId, itemName) {
    const title = 'Are you sure?';
    const text = `You want to delete "${itemName}"? This action cannot be undone!`;
    
    showConfirmDialog(
        title,
        text,
        'Yes, delete it!',
        'Cancel',
        function() {
            submitDeleteForm(itemId);
        },
        'warning'
    );
}

/**
 * Submit delete form
 */
function submitDeleteForm(itemId) {
    const form = $('<form>', {
        'method': 'POST',
        'action': `/admin/items/${itemId}`
    });
    
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': $('meta[name="csrf-token"]').attr('content')
    }));
    
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_method',
        'value': 'DELETE'
    }));
    
    $('body').append(form);
    form.submit();
}
```

### Including JavaScript Files in Views
```blade
@endsection

@push('js')
    <script type="text/javascript" src="{{asset('admin/partial/js/module.js')}}"></script>
@endpush

@if($errors->any())
    @push('js')
        <script>
            $(document).ready(function() {
                $('#moduleModal').modal('show');
            });
        </script>
    @endpush
@endif
```

### Global JavaScript Functions
Use existing global functions from `global.js`:

**Confirmation Dialog:**
```javascript
showConfirmDialog(title, text, confirmText, cancelText, onConfirm, icon);
```

**Notifications:**
```javascript
showToastr('success', 'Message');
showToastr('error', 'Error message');
showToastr('warning', 'Warning message');
```

**Loading Dialog:**
```javascript
showLoadingDialog('Processing...', 'Please wait...');
hideLoadingDialog();
```

## 7. Best Practices

### Do's
- ✅ Always use `$data` array format
- ✅ Move validation to Request files
- ✅ Use SweetAlert for confirmations
- ✅ Include proper error messages
- ✅ Use consistent naming conventions
- ✅ Follow RESTful conventions
- ✅ Create separate JS files for each module
- ✅ Use global functions from global.js
- ✅ Include JS files using @push('js')

### Don'ts
- ❌ Don't validate in controllers
- ❌ Don't use browser confirm()
- ❌ Don't pass variables directly to view
- ❌ Don't skip error handling
- ❌ Don't hardcode values
- ❌ Don't ignore naming conventions
- ❌ Don't write inline JavaScript in Blade files
- ❌ Don't duplicate JavaScript functionality

## 8. Examples

### Complete Working Examples
See the following files for complete implementations:
- `app/Http/Controllers/Admin/SettingsController.php`
- `app/Http/Controllers/Admin/CurrencyController.php`
- `app/Http/Requests/Admin/UpdateSettingsRequest.php`
- `app/Http/Requests/Admin/StoreCurrencyRequest.php`
- `resources/views/admin/currency/index.blade.php`
- `public/admin/partial/js/currency.js`
- `public/admin/partial/js/global.js`

These files demonstrate all the conventions outlined in this document.
