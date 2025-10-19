<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Toast Position
    |--------------------------------------------------------------------------
    |
    | Available positions: 'top-left', 'top-center', 'top-right',
    | 'bottom-left', 'bottom-center', 'bottom-right'
    |
    */
    'position' => env('TOAST_POSITION', 'top-right'),

    /*
    |--------------------------------------------------------------------------
    | Toast Duration
    |--------------------------------------------------------------------------
    |
    | Duration in milliseconds. Set to 0 for persistent toasts.
    |
    */
    'duration' => env('TOAST_DURATION', 3000),

    /*
    |--------------------------------------------------------------------------
    | Show Close Button
    |--------------------------------------------------------------------------
    |
    | Show or hide the close button on toasts.
    |
    */
    'show_close_button' => env('TOAST_SHOW_CLOSE_BUTTON', true),

    /*
    |--------------------------------------------------------------------------
    | Show Progress Bar
    |--------------------------------------------------------------------------
    |
    | Show a progress bar indicating remaining time.
    |
    */
    'show_progress_bar' => env('TOAST_SHOW_PROGRESS_BAR', true),

    /*
    |--------------------------------------------------------------------------
    | Pause on Hover
    |--------------------------------------------------------------------------
    |
    | Pause the auto-dismiss timer when hovering over the toast.
    |
    */
    'pause_on_hover' => env('TOAST_PAUSE_ON_HOVER', true),

    /*
    |--------------------------------------------------------------------------
    | Prevent Duplicates
    |--------------------------------------------------------------------------
    |
    | Prevent showing duplicate toasts with the same message.
    |
    */
    'prevent_duplicates' => env('TOAST_PREVENT_DUPLICATES', true),

    /*
    |--------------------------------------------------------------------------
    | Animation
    |--------------------------------------------------------------------------
    |
    | Entry and exit animations for toasts.
    | Available: slideInRight, slideInLeft, slideInTop, slideInBottom,
    | fadeIn, bounceIn, zoomIn
    |
    */
    'animation' => [
        'enter' => env('TOAST_ANIMATION_ENTER', 'slideInRight'),
        'exit' => env('TOAST_ANIMATION_EXIT', 'slideOutRight'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | Theme mode: 'light', 'dark', or 'auto' (follows system preference)
    |
    */
    'theme' => env('TOAST_THEME', 'light'),

    /*
    |--------------------------------------------------------------------------
    | Max Toasts
    |--------------------------------------------------------------------------
    |
    | Maximum number of toasts to display at once.
    |
    */
    'max_toasts' => env('TOAST_MAX_TOASTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Stack Direction
    |--------------------------------------------------------------------------
    |
    | Direction to stack multiple toasts: 'up' or 'down'
    |
    */
    'stack_direction' => env('TOAST_STACK_DIRECTION', 'down'),

    /*
    |--------------------------------------------------------------------------
    | Accessibility
    |--------------------------------------------------------------------------
    |
    | Accessibility settings for screen readers.
    |
    */
    'accessibility' => [
        'role' => 'alert', // or 'status'
        'aria_live' => 'polite', // or 'assertive'
    ],

];