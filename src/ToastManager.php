<?php

namespace sndpbag\LaravelToast;

use Illuminate\Session\Store;

class ToastManager
{
    protected $session;
    protected $toasts = [];

    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Create a success toast
     */
    public function success($message, $title = null, array $options = [])
    {
        return $this->addToast('success', $message, $title, $options);
    }

    /**
     * Create an error toast
     */
    public function error($message, $title = null, array $options = [])
    {
        return $this->addToast('error', $message, $title, $options);
    }

    /**
     * Create a warning toast
     */
    public function warning($message, $title = null, array $options = [])
    {
        return $this->addToast('warning', $message, $title, $options);
    }

    /**
     * Create an info toast
     */
    public function info($message, $title = null, array $options = [])
    {
        return $this->addToast('info', $message, $title, $options);
    }

    /**
     * Create a loading toast
     */
    public function loading($message, $title = null, array $options = [])
    {
        $options['duration'] = 0; // Loading toasts don't auto-dismiss
        $options['showCloseButton'] = false; // No close button by default
        $options['showProgressBar'] = false; // No progress bar
        return $this->addToast('loading', $message, $title, $options);
    }

    /**
     * Create a custom toast with custom styling
     */
    public function custom($message, $title = null, array $options = [])
    {
        return $this->addToast('custom', $message, $title, $options);
    }

    /**
     * Create a promise toast (loading -> success/error)
     * Returns the toast ID for updating
     */
    public function promise($loadingMessage = 'Loading...', $title = null)
    {
        $toastId = uniqid('toast_promise_');
        
        $toast = [
            'id' => $toastId,
            'type' => 'loading',
            'title' => $title,
            'message' => $loadingMessage,
            'duration' => 0,
            'position' => config('toast.position', 'top-right'),
            'showCloseButton' => false,
            'showProgressBar' => false,
            'pauseOnHover' => false,
            'icon' => $this->getDefaultIcon('loading'),
            'preventDuplicates' => false,
            'showIcon' => true,
            'isPromise' => true,
        ];

        $toasts = $this->session->get('toasts', []);
        $toasts[] = $toast;
        $this->session->flash('toasts', $toasts);

        return $toastId;
    }

    /**
     * Update a promise toast to success
     */
    public function promiseSuccess($toastId, $message, $title = null)
    {
        $this->session->flash('toast_update', [
            'id' => $toastId,
            'type' => 'success',
            'title' => $title,
            'message' => $message,
            'duration' => config('toast.duration', 3000),
            'showCloseButton' => true,
            'showProgressBar' => true,
        ]);
        return $this;
    }

    /**
     * Update a promise toast to error
     */
    public function promiseError($toastId, $message, $title = null)
    {
        $this->session->flash('toast_update', [
            'id' => $toastId,
            'type' => 'error',
            'title' => $title,
            'message' => $message,
            'duration' => config('toast.duration', 3000),
            'showCloseButton' => true,
            'showProgressBar' => true,
        ]);
        return $this;
    }

    /**
     * Add a toast notification
     */
    protected function addToast($type, $message, $title = null, array $options = [])
    {
        $toast = array_merge([
            'id' => uniqid('toast_'),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'duration' => config('toast.duration', 3000),
            'position' => config('toast.position', 'top-right'),
            'showCloseButton' => config('toast.show_close_button', true),
            'showProgressBar' => config('toast.show_progress_bar', true),
            'pauseOnHover' => config('toast.pause_on_hover', true),
            'icon' => $this->getDefaultIcon($type),
            'preventDuplicates' => config('toast.prevent_duplicates', true),
            'showIcon' => true,
            'clickable' => false,
            'url' => null,
            'actions' => [],
            'animation' => [
                'enter' => config('toast.animation.enter', 'slideInRight'),
                'exit' => config('toast.animation.exit', 'slideOutRight'),
            ],
        ], $options);

        $toasts = $this->session->get('toasts', []);
        
        // Prevent duplicates if enabled
        if ($toast['preventDuplicates']) {
            $exists = collect($toasts)->contains(function ($item) use ($toast) {
                return $item['message'] === $toast['message'] && $item['type'] === $toast['type'];
            });
            
            if ($exists) {
                return $this;
            }
        }

        $toasts[] = $toast;
        $this->session->flash('toasts', $toasts);

        return $this;
    }

    /**
     * Add action buttons to the last toast
     */
    public function withActions(array $actions)
    {
        $toasts = $this->session->get('toasts', []);
        if (!empty($toasts)) {
            $lastIndex = count($toasts) - 1;
            $toasts[$lastIndex]['actions'] = $actions;
            $this->session->flash('toasts', $toasts);
        }
        return $this;
    }

    /**
     * Make the last toast clickable
     */
    public function clickable($url)
    {
        $toasts = $this->session->get('toasts', []);
        if (!empty($toasts)) {
            $lastIndex = count($toasts) - 1;
            $toasts[$lastIndex]['clickable'] = true;
            $toasts[$lastIndex]['url'] = $url;
            $this->session->flash('toasts', $toasts);
        }
        return $this;
    }

    /**
     * Set custom icon for the last toast
     */
    public function withIcon($icon)
    {
        $toasts = $this->session->get('toasts', []);
        if (!empty($toasts)) {
            $lastIndex = count($toasts) - 1;
            $toasts[$lastIndex]['icon'] = $icon;
            $this->session->flash('toasts', $toasts);
        }
        return $this;
    }

    /**
     * Get default icon based on type
     */
    protected function getDefaultIcon($type)
    {
        $icons = [
            'success' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            'error' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
            'warning' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
            'info' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
            'loading' => '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>',
            'custom' => '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>',
        ];

        return $icons[$type] ?? $icons['info'];
    }

    /**
     * Get all toasts
     */
    public function all()
    {
        return $this->session->get('toasts', []);
    }

    /**
     * Clear all toasts
     */
    public function clear()
    {
        $this->session->forget('toasts');
        return $this;
    }
}