# Helper Functions Documentation

## Overview
The application uses a config-based helper system for accessing application settings. Settings are stored in the database `options` table and loaded into the `general_settings` config at runtime.

## Main Helper Function

### get_option($option_key)
Primary function to retrieve application settings from config.

**Usage:**
```php
{{get_option('app_currency')}}
{{get_option('app_name')}}
{{get_option('date_format')}}
```

**Parameters:**
- `$option_key` (string): The setting key to retrieve

**Returns:**
- Mixed: The setting value or empty string if not found

**Example in Blade:**
```blade
<h1>{{get_option('app_name')}}</h1>
<p>Currency: {{get_option('app_currency')}}</p>
<p>Phone: {{get_option('app_phone')}}</p>
```

## Available Settings Keys

### Core Application Settings
- `app_name` - Application name (default: 'SNG POS')
- `app_address` - Business address
- `app_phone` - Business phone number
- `date_format` - Date display format (default: 'Y-m-d')
- `app_logo` - Logo file path
- `app_favicon` - Favicon file path
- `app_currency` - Currency symbol (default: '$')

### Additional Settings
- `timezone` - Application timezone (default: 'UTC')
- `language` - Application language (default: 'en')
- `items_per_page` - Pagination limit (default: 10)
- `tax_calculation` - Tax calculation method (default: 'exclusive')
- `decimal_places` - Number of decimal places (default: 2)
- `currency_position` - Currency symbol position (default: 'before')

## Legacy Helper Functions
For backward compatibility, these functions are still available:

- `setting($key, $default)` - Legacy function, uses get_option internally
- `app_name()` - Get application name
- `app_currency()` - Get currency symbol
- `app_logo()` - Get logo URL with asset() wrapper
- `app_favicon()` - Get favicon URL with asset() wrapper
- `format_date($date)` - Format date according to app setting

## File Structure

### Helper File
- `app/Helpers/helper.php` - Contains all helper functions

### Config File
- `config/general_settings.php` - Default config values

### Service Provider
- `app/Providers/AppServiceProvider.php` - Dynamically loads all database settings into config

### Database
- `options` table - Stores key-value pairs
- `Option` model - Provides get/set methods

## How It Works

1. **Dynamic Loading**: AppServiceProvider checks database connection
2. **All Settings Fetch**: Loads ALL options from database using `Option::all()->pluck('value', 'key')`
3. **Config Set**: Sets entire general_settings config array dynamically
4. **Helper Access**: get_option() retrieves from the loaded config
5. **Graceful Fallback**: If database unavailable, silently continues without error

## AppServiceProvider Implementation

```php
try {
    $connection = DB::connection()->getPdo();
    if ($connection) {
        $allOptions = [];
        $allOptions['general_settings'] = Option::all()->pluck('value', 'key')->toArray();
        config($allOptions);
    }
} catch (\Exception $e) {
    //
}
```

This approach is more dynamic because:
- Loads ALL settings from database in one query
- No need to specify individual keys
- Automatically includes any new settings added to database
- More efficient than multiple individual queries

## Usage Examples

### In Controllers
```php
$appName = get_option('app_name');
$currency = get_option('app_currency');
```

### In Blade Templates
```blade
<title>{{get_option('app_name')}}</title>
<span>{{get_option('app_currency')}}100.00</span>
```

### In Models/Services
```php
$dateFormat = get_option('date_format');
$formattedDate = date($dateFormat, strtotime($date));
```

## Management

Settings can be managed through:
- **Admin Panel**: `/admin/settings` - General settings form
- **Currency Page**: `/admin/currency` - Currency management
- **Database**: Direct manipulation of `options` table
- **Seeder**: `DefaultSettingsSeeder` for initial setup

## Notes

- Settings are cached in config for performance
- Database connection errors are handled gracefully
- Config caching is supported (no database calls during cache)
- All functions return empty string if setting not found
- Legacy functions maintained for backward compatibility

## Development Conventions

### Controller Data Format
All controllers must follow this format for passing data to views:

```php
public function index()
{
    $data['title'] = 'Page Title';
    $data['items'] = Model::all();
    $data['otherData'] = 'value';
    return view('admin.module.index', $data)->with('menu', 'menu_name');
}
```

**Key Points:**
- Use `$data` array to pass all variables to view
- Always include `$data['title']` for page title
- Use `->with('menu', 'menu_name')` for sidebar active state
- Follow consistent naming conventions

### Validation Rules
All validation rules must be moved to Request files, not in controllers:

**File Structure:**
- `app/Http/Requests/Admin/UpdateSettingsRequest.php`
- `app/Http/Requests/Admin/StoreCurrencyRequest.php`
- `app/Http/Requests/Admin/SetCurrencyRequest.php`

**Request File Template:**
```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ExampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'field' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'field.required' => 'Custom error message.',
        ];
    }
}
```

**Controller Usage:**
```php
use App\Http\Requests\Admin\ExampleRequest;

public function store(ExampleRequest $request)
{
    // Validation is automatically handled
    // Access validated data with $request->field
}
```

### SweetAlert Implementation
For delete confirmations, use SweetAlert2 instead of browser confirm():

**HTML Button:**
```html
<button type="button" class="btn btn-sm btn-outline-danger delete-item" 
        data-id="{{ $item->id }}" 
        data-name="{{ $item->name }}">
    <i class="fa fa-trash"></i>
</button>
```

**JavaScript Implementation:**
```javascript
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
                // Create and submit form for DELETE request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/items/${itemId}`;
                
                // Add CSRF token and DELETE method
                // ... form submission code
            }
        });
    });
});
```

### File Organization
- **Controllers**: Clean, use Request files for validation
- **Requests**: All validation rules and custom messages
- **Views**: Use SweetAlert for confirmations
- **Data Passing**: Always use `$data` array format
