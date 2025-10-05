# JavaScript File Conventions

## Overview
This document outlines the conventions for organizing and writing JavaScript code in the SNG POS application.

## File Organization

### Directory Structure
```
public/admin/partial/js/
├── global.js          # Global utilities and configurations
├── currency.js        # Currency management functionality
├── products.js        # Products management functionality
├── category.js        # Category management functionality
├── tax.js            # Tax management functionality
├── unit.js           # Unit management functionality
└── [module].js       # Module-specific functionality
```

### Naming Convention
- Use lowercase with hyphens for file names: `module-name.js`
- Match the module/controller name: `currency.js` for CurrencyController
- Keep names descriptive and consistent

## JavaScript File Template

### Standard Structure
```javascript
"use strict";

/**
 * Module Name JavaScript
 * This file contains all JavaScript functionality for [module] management
 */

$(document).ready(function() {
    initializeModuleName();
});

/**
 * Initialize module
 */
function initializeModuleName() {
    setupDeleteHandlers();
    setupFormValidation();
    setupEventHandlers();
}

/**
 * Setup delete handlers
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
        'action': `/admin/module/${itemId}`
    });
    
    // Add CSRF token
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': $('meta[name="csrf-token"]').attr('content')
    }));
    
    // Add DELETE method
    form.append($('<input>', {
        'type': 'hidden',
        'name': '_method',
        'value': 'DELETE'
    }));
    
    $('body').append(form);
    form.submit();
}

/**
 * Setup form validation
 */
function setupFormValidation() {
    $('#moduleForm').on('submit', function(e) {
        // Add validation logic here
        return true;
    });
}
```

## Global Functions Usage

### Available Global Functions (from global.js)

#### Confirmation Dialog
```javascript
showConfirmDialog(title, text, confirmText, cancelText, onConfirm, icon);

// Example
showConfirmDialog(
    'Delete Item',
    'Are you sure you want to delete this item?',
    'Yes, delete it!',
    'Cancel',
    function() {
        // Delete logic here
    },
    'warning'
);
```

#### Notifications
```javascript
// Success notification
showToastr('success', 'Operation completed successfully');

// Error notification
showToastr('error', 'Operation failed');

// Warning notification
showToastr('warning', 'Please check your input');

// Info notification
showToastr('info', 'Information message');
```

#### Loading Dialogs
```javascript
// Show loading
showLoadingDialog('Processing...', 'Please wait while we process your request.');

// Hide loading
hideLoadingDialog();
```

## Including JavaScript in Views

### Standard Method (Recommended)
```blade
@endsection

@push('js')
    <script type="text/javascript" src="{{asset('admin/partial/js/module.js')}}"></script>
@endpush
```

### Error Handling in JavaScript Files
Instead of conditional scripts in Blade, handle errors in the JavaScript file:

**In JavaScript file:**
```javascript
/**
 * Handle validation errors - show modal if there are errors
 */
function handleValidationErrors() {
    // Check if there are validation errors by looking for error elements
    if ($('.alert-danger').length > 0 || $('.is-invalid').length > 0) {
        $('#moduleModal').modal('show');
    }
}

function initializeModule() {
    setupHandlers();
    handleValidationErrors(); // Call this in initialization
}
```

**In Blade view (clean approach):**
```blade
@endsection

@push('js')
    <script type="text/javascript" src="{{asset('admin/partial/js/module.js')}}"></script>
@endpush
```

## Best Practices

### Do's ✅
- Use `"use strict";` at the top of every file
- Use jQuery for DOM manipulation
- Use global functions from `global.js`
- Document functions with JSDoc comments
- Use consistent naming conventions
- Handle errors gracefully
- Use event delegation for dynamic elements
- Keep functions focused and single-purpose

### Don'ts ❌
- Don't write inline JavaScript in Blade files
- Don't duplicate functionality across files
- Don't ignore error handling
- Don't use vanilla JavaScript when jQuery is available
- Don't hardcode URLs (use Laravel route helpers when possible)
- Don't forget CSRF tokens in AJAX requests
- Don't create global variables unnecessarily

## Common Patterns

### Delete Confirmation Pattern
```javascript
function setupDeleteHandlers() {
    $(document).on('click', '.delete-item', function() {
        const itemId = $(this).data('id');
        const itemName = $(this).data('name');
        
        showConfirmDialog(
            'Are you sure?',
            `You want to delete "${itemName}"? This action cannot be undone!`,
            'Yes, delete it!',
            'Cancel',
            function() {
                submitDeleteForm(itemId);
            },
            'warning'
        );
    });
}
```

### Form Validation Pattern
```javascript
function setupFormValidation() {
    $('#moduleForm').on('submit', function(e) {
        const requiredFields = ['field1', 'field2'];
        let isValid = true;
        
        requiredFields.forEach(function(field) {
            const value = $(`#${field}`).val().trim();
            if (!value) {
                isValid = false;
                $(`#${field}`).addClass('is-invalid');
            } else {
                $(`#${field}`).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showToastr('error', 'Please fill in all required fields.');
            return false;
        }
        
        return true;
    });
}
```

### AJAX Request Pattern
```javascript
function performAjaxRequest(url, data, successCallback) {
    $.ajax({
        url: url,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            showLoadingDialog();
        },
        success: function(response) {
            hideLoadingDialog();
            if (typeof successCallback === 'function') {
                successCallback(response);
            }
        },
        error: function(xhr, status, error) {
            hideLoadingDialog();
            showToastr('error', 'An error occurred. Please try again.');
        }
    });
}
```

### Error Handling Pattern
```javascript
/**
 * Handle validation errors - show modal if there are errors
 */
function handleValidationErrors() {
    // Check for Laravel validation errors
    if ($('.alert-danger').length > 0 || $('.is-invalid').length > 0) {
        $('#moduleModal').modal('show');
    }
    
    // Alternative: Check for specific error messages
    if ($('.invalid-feedback').length > 0) {
        $('#moduleModal').modal('show');
    }
    
    // You can also check for custom error indicators
    if ($('[data-has-errors="true"]').length > 0) {
        $('#moduleModal').modal('show');
    }
}
```

## Example Implementation

See `public/admin/partial/js/currency.js` for a complete implementation following these conventions.

## Integration with Laravel

### CSRF Token
Always include CSRF token in forms and AJAX requests:
```javascript
'_token': $('meta[name="csrf-token"]').attr('content')
```

### Route Generation
When possible, use Laravel route helpers in Blade and pass to JavaScript:
```blade
<script>
    window.routes = {
        delete: '{{ route("admin.module.destroy", ":id") }}'
    };
</script>
```

Then in JavaScript:
```javascript
const deleteUrl = window.routes.delete.replace(':id', itemId);
```

This approach maintains clean separation between backend routing and frontend JavaScript.
