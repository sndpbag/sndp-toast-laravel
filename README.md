# Laravel Toast Notifications

<p align="center">
<img src="https://img.shields.io/packagist/v/sndpbag/laravel-toast.svg?style=flat-square" alt="Latest Version">
<img src="https://img.shields.io/packagist/dt/sndpbag/laravel-toast.svg?style=flat-square" alt="Total Downloads">
<img src="https://img.shields.io/packagist/l/sndpbag/laravel-toast.svg?style=flat-square" alt="License">
</p>

A beautiful, feature-rich, and highly customizable toast notification package for Laravel. Built with modern design principles and accessibility in mind.

## ✨ Features

- 🎨 **7 Toast Types**: Success, Error, Warning, Info, Loading, Promise, Custom
- 📍 **6 Positions**: Top/Bottom × Left/Center/Right
- ⚡ **Auto-dismiss** with customizable duration
- ⏸️ **Pause on Hover** 
- 📊 **Progress Bar** showing remaining time
- 🎭 **Multiple Animations**: Slide, Fade, Bounce, Zoom
- 🌓 **Dark/Light Theme** with auto-detection
- ♿ **Accessibility**: Full ARIA support & keyboard navigation
- 🔄 **Prevent Duplicates**
- 🎯 **Action Buttons** in toasts
- 🔗 **Clickable Toasts** with URL redirect
- ⏳ **Promise Support** for async operations
- 🎨 **Custom Icons** support
- 📱 **Responsive Design**
- 🔧 **Highly Configurable**
- 🚀 **Zero Dependencies** (Vanilla JS)

## 📦 Installation

Install the package via Composer:

```bash
composer require sndpbag/laravel-toast
```

### Publish Configuration & Assets

```bash
php artisan vendor:publish --provider="sndpbag\LaravelToast\ToastServiceProvider"
```

Or publish individually:

```bash
# Publish config only
php artisan vendor:publish --tag=toast-config

# Publish views only
php artisan vendor:publish --tag=toast-views

# Publish assets only
php artisan vendor:publish --tag=toast-assets
```

## 🚀 Quick Start

### 1. Add to Your Layout

Add this directive before closing `</body>` tag in your layout file:

```blade
<!DOCTYPE html>
<html>
<head>
    <title>My App</title>
</head>
<body>
    
    <!-- Your content -->
    
    @toastify
</body>
</html>
```

### 2. Use in Controllers

```php
use sndpbag\LaravelToast\Facades\Toast;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Create user...
        
        Toast::success('User created successfully!');
        
        return redirect()->route('users.index');
    }
}
```

## 📖 Usage Examples

### Basic Usage

```php
// Success toast
Toast::success('Operation completed successfully!');

// Error toast
Toast::error('Something went wrong!');

// Warning toast
Toast::warning('Please check your input!');

// Info toast
Toast::info('New updates available!');

// Loading toast (doesn't auto-dismiss)
Toast::loading('Processing your request...');

// Custom toast with custom styling
Toast::custom('Custom notification message', 'Custom Title', [
    'icon' => '<svg>...</svg>',
    'duration' => 5000
]);
```

### With Title

```php
Toast::success('User Created', 'John Doe has been added to the system');

Toast::error('Delete Failed', 'You do not have permission to delete this item');
```

### With Options

```php
Toast::success('Saved!', 'Your changes have been saved', [
    'duration' => 5000,
    'position' => 'top-center',
    'showProgressBar' => false,
]);
```

### With Action Buttons

```php
Toast::info('File deleted', 'The file has been moved to trash')
    ->withActions([
        [
            'label' => 'Undo',
            'action' => 'undo',
            'callback' => 'handleUndo()'
        ],
        [
            'label' => 'View',
            'action' => 'view',
            'callback' => 'viewTrash()'
        ]
    ]);
```

### Clickable Toast with URL

```php
Toast::info('New message received')
    ->clickable(route('messages.show', $message->id));
```

### Custom Icon

```php
Toast::success('Profile Updated')
    ->withIcon('<svg>...</svg>'); // Your custom SVG icon

// Or use emoji
Toast::success('Welcome!')
    ->withIcon('👋');
```

### Multiple Toasts

```php
Toast::success('First task completed');
Toast::info('Second task in progress');
Toast::warning('Third task needs attention');
```

### Loading Toast Example

```php
// Show loading toast while processing
Toast::loading('Processing payment...', 'Please wait');

// After process completes, redirect with success/error toast
if ($paymentSuccess) {
    Toast::success('Payment successful!');
} else {
    Toast::error('Payment failed!');
}

return redirect()->back();
```

### Chaining Methods

```php
Toast::success('Post Published', 'Your post is now live!')
    ->clickable(route('posts.show', $post->id))
    ->withActions([
        ['label' => 'View', 'action' => 'view', 'callback' => 'viewPost()'],
        ['label' => 'Share', 'action' => 'share', 'callback' => 'sharePost()']
    ]);
```

### Promise Toasts (For Async Operations)

Perfect for showing loading state and updating to success/error:

```php
// In your controller
public function uploadFile(Request $request)
{
    // Show loading toast
    $toastId = Toast::promise('Uploading file...', 'Please wait');
    
    try {
        // Perform the operation
        $file = $request->file('document');
        $file->store('uploads');
        
        // Update to success
        Toast::promiseSuccess($toastId, 'File uploaded successfully!', 'Done');
        
    } catch (\Exception $e) {
        // Update to error
        Toast::promiseError($toastId, 'Failed to upload file', 'Error');
    }
    
    return redirect()->back();
}
```

**AJAX Example:**

```javascript
// Make AJAX request and show promise toast
fetch('/api/upload', {
    method: 'POST',
    body: formData
})
.then(response => {
    // Success - page will show updated toast on redirect
    window.location.href = '/dashboard';
})
.catch(error => {
    // Error - page will show error toast on redirect
    window.location.href = '/dashboard';
});
```', 'action' => 'view', 'callback' => 'viewPost()'],
        ['label' => 'Share', 'action' => 'share', 'callback' => 'sharePost()']
    ]);
```

## ⚙️ Configuration

The `config/toast.php` file contains all configuration options:

```php
return [
    // Position: 'top-left', 'top-center', 'top-right', 
    //           'bottom-left', 'bottom-center', 'bottom-right'
    'position' => 'top-right',
    
    // Duration in milliseconds (0 = persistent)
    'duration' => 3000,
    
    // Show close button
    'show_close_button' => true,
    
    // Show progress bar
    'show_progress_bar' => true,
    
    // Pause on hover
    'pause_on_hover' => true,
    
    // Prevent duplicate toasts
    'prevent_duplicates' => true,
    
    // Animations
    'animation' => [
        'enter' => 'slideInRight',
        'exit' => 'slideOutRight',
    ],
    
    // Theme: 'light', 'dark', or 'auto'
    'theme' => 'light',
    
    // Maximum toasts to show at once
    'max_toasts' => 5,
    
    // Stack direction: 'up' or 'down'
    'stack_direction' => 'down',
    
    // Accessibility settings
    'accessibility' => [
        'role' => 'alert',
        'aria_live' => 'polite',
    ],
];
```

### Environment Variables

You can override config values using `.env`:

```env
TOAST_POSITION=top-right
TOAST_DURATION=3000
TOAST_THEME=dark
TOAST_SHOW_PROGRESS_BAR=true
TOAST_PAUSE_ON_HOVER=true
TOAST_PREVENT_DUPLICATES=true
```

## 🎨 Available Animations

### Entry Animations
- `slideInRight`
- `slideInLeft`
- `slideInTop`
- `slideInBottom`
- `fadeIn`
- `bounceIn`
- `zoomIn`

### Exit Animations
- `slideOutRight`
- `slideOutLeft`
- `slideOutTop`
- `slideOutBottom`
- `fadeOut`
- `bounceOut`
- `zoomOut`

## 🎯 Available Options

When creating a toast, you can pass these options:

```php
Toast::success('Message', 'Title', [
    'duration' => 3000,              // Auto-dismiss time (ms), 0 = persistent
    'position' => 'top-right',       // Toast position
    'showCloseButton' => true,       // Show/hide close button
    'showProgressBar' => true,       // Show/hide progress bar
    'pauseOnHover' => true,          // Pause auto-dismiss on hover
    'showIcon' => true,              // Show/hide icon
    'clickable' => false,            // Make toast clickable
    'url' => null,                   // URL for clickable toast
    'preventDuplicates' => true,     // Prevent duplicate toasts
    'icon' => '<svg>...</svg>',      // Custom icon HTML
    'animation' => [
        'enter' => 'slideInRight',   // Entry animation
        'exit' => 'slideOutRight',   // Exit animation
    ],
]);
```

## 🌐 Helper Function

You can also use the global `toast()` helper:

```php
// If you add this to your helpers file
function toast() {
    return app('toast');
}

// Then use it like:
toast()->success('Hello World!');
```

## 🧪 Testing

```bash
composer test
```

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒 Security

If you discover any security-related issues, please email your.email@example.com instead of using the issue tracker.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🙏 Credits

- **Your Name** - Creator
- All contributors

## 🌟 Show Your Support

If you find this package helpful, please consider giving it a ⭐ on GitHub!

---

Made with ❤️ for the Laravel community