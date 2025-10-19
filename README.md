@if(session('toasts') || session('toast_update'))
<div id="toast-container" 
     data-position="{{ config('toast.position') }}" 
     data-theme="{{ config('toast.theme') }}"
     data-max-toasts="{{ config('toast.max_toasts') }}"
     data-stack-direction="{{ config('toast.stack_direction') }}"
     data-toast-update="{{ session('toast_update') ? json_encode(session('toast_update')) : '' }}"
     class="toast-container toast-{{ config('toast.position') }}">
</div>

<script>
(function() {
    const toasts = @json(session('toasts') ?? []);
    const toastUpdate = @json(session('toast_update') ?? null);
    
    if ((!toasts || toasts.length === 0) && !toastUpdate) return;

    const container = document.getElementById('toast-container');
    const theme = container.dataset.theme;
    const maxToasts = parseInt(container.dataset.maxToasts);
    const stackDirection = container.dataset.stackDirection;
    
    // Apply theme
    if (theme === 'auto') {
        const isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        container.classList.add(isDark ? 'toast-theme-dark' : 'toast-theme-light');
    } else {
        container.classList.add(`toast-theme-${theme}`);
    }

    let activeToasts = [];

    // Handle toast updates for promise toasts
    if (toastUpdate) {
        updateToast(toastUpdate);
    }

    function updateToast(updateData) {
        const existingToast = document.getElementById(updateData.id);
        if (!existingToast) return;

        // Update icon
        const iconEl = existingToast.querySelector('.toast-icon');
        const newIcon = getIconForType(updateData.type);
        if (iconEl) iconEl.innerHTML = newIcon;

        // Update title
        if (updateData.title) {
            const titleEl = existingToast.querySelector('.toast-title');
            if (titleEl) {
                titleEl.textContent = updateData.title;
            } else {
                const bodyEl = existingToast.querySelector('.toast-body');
                const newTitle = document.createElement('div');
                newTitle.className = 'toast-title';
                newTitle.textContent = updateData.title;
                bodyEl.insertBefore(newTitle, bodyEl.firstChild);
            }
        }

        // Update message
        const messageEl = existingToast.querySelector('.toast-message');
        if (messageEl) messageEl.textContent = updateData.message;

        // Update type class
        existingToast.className = existingToast.className.replace(/toast-\w+(?=\s|$)/, `toast-${updateData.type}`);

        // Add close button if not exists
        if (updateData.showCloseButton && !existingToast.querySelector('.toast-close')) {
            const closeBtn = document.createElement('button');
            closeBtn.className = 'toast-close';
            closeBtn.setAttribute('aria-label', 'Close');
            closeBtn.setAttribute('tabindex', '0');
            closeBtn.innerHTML = `<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>`;
            closeBtn.addEventListener('click', () => removeToast(existingToast));
            existingToast.querySelector('.toast-content').appendChild(closeBtn);
        }

        // Add progress bar
        if (updateData.showProgressBar && updateData.duration > 0) {
            const progressHTML = '<div class="toast-progress"><div class="toast-progress-bar"></div></div>';
            existingToast.insertAdjacentHTML('beforeend', progressHTML);
            
            const progressBar = existingToast.querySelector('.toast-progress-bar');
            progressBar.style.animationDuration = `${updateData.duration}ms`;
        }

        // Auto-dismiss after update
        if (updateData.duration > 0) {
            setTimeout(() => removeToast(existingToast), updateData.duration);
        }
    }

    function getIconForType(type) {
        const icons = {
            'success': '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
            'error': '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
        };
        return icons[type] || icons['success'];
    }

    function showToast(toast) {
        if (activeToasts.length >= maxToasts) {
            if (stackDirection === 'down') {
                removeToast(activeToasts[0]);
            } else {
                removeToast(activeToasts[activeToasts.length - 1]);
            }
        }

        const toastEl = document.createElement('div');
        toastEl.className = `toast toast-${toast.type} toast-animation-${toast.animation.enter}`;
        toastEl.id = toast.id;
        toastEl.setAttribute('role', '{{ config("toast.accessibility.role") }}');
        toastEl.setAttribute('aria-live', '{{ config("toast.accessibility.aria_live") }}');
        toastEl.setAttribute('aria-atomic', 'true');
        
        if (toast.clickable && toast.url) {
            toastEl.style.cursor = 'pointer';
            toastEl.addEventListener('click', function(e) {
                if (!e.target.closest('.toast-close') && !e.target.closest('.toast-action')) {
                    window.location.href = toast.url;
                }
            });
        }

        let html = '<div class="toast-content">';
        
        if (toast.showIcon && toast.icon) {
            html += `<div class="toast-icon">${toast.icon}</div>`;
        }
        
        html += '<div class="toast-body">';
        if (toast.title) {
            html += `<div class="toast-title">${toast.title}</div>`;
        }
        html += `<div class="toast-message">${toast.message}</div>`;
        
        if (toast.actions && toast.actions.length > 0) {
            html += '<div class="toast-actions">';
            toast.actions.forEach(action => {
                html += `<button class="toast-action" data-action="${action.action}" onclick="${action.callback}">${action.label}</button>`;
            });
            html += '</div>';
        }
        
        html += '</div>';
        
        if (toast.showCloseButton) {
            html += `<button class="toast-close" aria-label="Close" tabindex="0">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>`;
        }
        
        html += '</div>';
        
        if (toast.showProgressBar && toast.duration > 0) {
            html += '<div class="toast-progress"><div class="toast-progress-bar"></div></div>';
        }
        
        toastEl.innerHTML = html;
        container.appendChild(toastEl);
        activeToasts.push(toastEl);

        // Close button handler
        const closeBtn = toastEl.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => removeToast(toastEl));
            closeBtn.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    removeToast(toastEl);
                }
            });
        }

        // Auto-dismiss logic
        if (toast.duration > 0) {
            let timeoutId;
            let startTime = Date.now();
            let remainingTime = toast.duration;
            
            const progressBar = toastEl.querySelector('.toast-progress-bar');
            if (progressBar) {
                progressBar.style.animationDuration = `${toast.duration}ms`;
            }

            function startTimer() {
                timeoutId = setTimeout(() => {
                    removeToast(toastEl);
                }, remainingTime);
            }

            startTimer();

            if (toast.pauseOnHover) {
                toastEl.addEventListener('mouseenter', () => {
                    clearTimeout(timeoutId);
                    remainingTime -= (Date.now() - startTime);
                    if (progressBar) {
                        progressBar.style.animationPlayState = 'paused';
                    }
                });

                toastEl.addEventListener('mouseleave', () => {
                    startTime = Date.now();
                    startTimer();
                    if (progressBar) {
                        progressBar.style.animationPlayState = 'running';
                    }
                });
            }
        }

        // Trigger reflow for animation
        toastEl.offsetHeight;
    }

    function removeToast(toastEl) {
        if (!toastEl || !toastEl.parentNode) return;
        
        const exitAnim = toasts[0]?.animation?.exit || 'slideOutRight';
        toastEl.classList.remove(toastEl.classList[2]); // Remove enter animation
        toastEl.classList.add(`toast-animation-${exitAnim}`);
        
        setTimeout(() => {
            if (toastEl.parentNode) {
                toastEl.parentNode.removeChild(toastEl);
            }
            activeToasts = activeToasts.filter(t => t !== toastEl);
        }, 300);
    }

    // Show all toasts
    toasts.forEach((toast, index) => {
        setTimeout(() => showToast(toast), index * 150);
    });
})();
</script>

<style>
.toast-container {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 420px;
    padding: 16px;
}

.toast-top-right { top: 0; right: 0; }
.toast-top-left { top: 0; left: 0; }
.toast-top-center { top: 0; left: 50%; transform: translateX(-50%); }
.toast-bottom-right { bottom: 0; right: 0; }
.toast-bottom-left { bottom: 0; left: 0; }
.toast-bottom-center { bottom: 0; left: 50%; transform: translateX(-50%); }

.toast {
    pointer-events: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    min-width: 300px;
    max-width: 100%;
    position: relative;
}

.toast-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
}

.toast-icon {
    flex-shrink: 0;
    width: 24px;
    height: 24px;
}

.toast-body {
    flex: 1;
    min-width: 0;
}

.toast-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    color: #1a1a1a;
}

.toast-message {
    font-size: 14px;
    color: #4a4a4a;
    line-height: 1.5;
}

.toast-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.toast-action {
    padding: 4px 12px;
    font-size: 13px;
    font-weight: 500;
    border-radius: 4px;
    border: 1px solid currentColor;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s;
}

.toast-action:hover {
    background: rgba(0, 0, 0, 0.05);
}

.toast-close {
    position: absolute;
    top: 12px;
    right: 12px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    color: #666;
    transition: all 0.2s;
}

.toast-close:hover, .toast-close:focus {
    background: rgba(0, 0, 0, 0.1);
    outline: none;
}

.toast-progress {
    height: 4px;
    background: rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.toast-progress-bar {
    height: 100%;
    background: currentColor;
    animation: progress linear forwards;
}

@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}

/* Toast Types */
.toast-success { border-left: 4px solid #10b981; }
.toast-success .toast-icon { color: #10b981; }
.toast-success .toast-progress-bar { background: #10b981; }

.toast-error { border-left: 4px solid #ef4444; }
.toast-error .toast-icon { color: #ef4444; }
.toast-error .toast-progress-bar { background: #ef4444; }

.toast-warning { border-left: 4px solid #f59e0b; }
.toast-warning .toast-icon { color: #f59e0b; }
.toast-warning .toast-progress-bar { background: #f59e0b; }

.toast-info { border-left: 4px solid #3b82f6; }
.toast-info .toast-icon { color: #3b82f6; }
.toast-info .toast-progress-bar { background: #3b82f6; }

.toast-loading { border-left: 4px solid #6b7280; }
.toast-loading .toast-icon { color: #6b7280; }

.toast-custom { border-left: 4px solid #8b5cf6; }
.toast-custom .toast-icon { color: #8b5cf6; }
.toast-custom .toast-progress-bar { background: #8b5cf6; }

/* Spinner animation for loading */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Dark Theme */
.toast-theme-dark .toast {
    background: #1f2937;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
}

.toast-theme-dark .toast-title {
    color: #f9fafb;
}

.toast-theme-dark .toast-message {
    color: #d1d5db;
}

.toast-theme-dark .toast-close {
    color: #9ca3af;
}

.toast-theme-dark .toast-close:hover {
    background: rgba(255, 255, 255, 0.1);
}

/* Animations */
.toast-animation-slideInRight {
    animation: slideInRight 0.3s ease-out;
}

.toast-animation-slideOutRight {
    animation: slideOutRight 0.3s ease-in;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast-animation-slideInLeft {
    animation: slideInLeft 0.3s ease-out;
}

.toast-animation-slideOutLeft {
    animation: slideOutLeft 0.3s ease-in;
}

@keyframes slideInLeft {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutLeft {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(-100%);
        opacity: 0;
    }
}

.toast-animation-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

.toast-animation-fadeOut {
    animation: fadeOut 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}

/* Responsive */
@media (max-width: 640px) {
    .toast-container {
        max-width: 100%;
        left: 0 !important;
        right: 0 !important;
        transform: none !important;
        padding: 8px;
    }
    
    .toast {
        min-width: 100%;
    }
}
</style>
@endif