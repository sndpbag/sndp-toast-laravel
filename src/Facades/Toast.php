<?php

namespace sndpbag\LaravelToast\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \sndpbag\LaravelToast\ToastManager success(string $message, string $title = null, array $options = [])
 * @method static \sndpbag\LaravelToast\ToastManager error(string $message, string $title = null, array $options = [])
 * @method static \sndpbag\LaravelToast\ToastManager warning(string $message, string $title = null, array $options = [])
 * @method static \sndpbag\LaravelToast\ToastManager info(string $message, string $title = null, array $options = [])
 * @method static \sndpbag\LaravelToast\ToastManager loading(string $message, string $title = null, array $options = [])
 * @method static \sndpbag\LaravelToast\ToastManager custom(string $message, string $title = null, array $options = [])
 * @method static string promise(string $loadingMessage = 'Loading...', string $title = null)
 * @method static \sndpbag\LaravelToast\ToastManager promiseSuccess(string $toastId, string $message, string $title = null)
 * @method static \sndpbag\LaravelToast\ToastManager promiseError(string $toastId, string $message, string $title = null)
 * @method static \sndpbag\LaravelToast\ToastManager withActions(array $actions)
 * @method static \sndpbag\LaravelToast\ToastManager clickable(string $url)
 * @method static \sndpbag\LaravelToast\ToastManager withIcon(string $icon)
 * @method static array all()
 * @method static \sndpbag\LaravelToast\ToastManager clear()
 *
 * @see \sndpbag\LaravelToast\ToastManager
 */
class Toast extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'toast';
    }
}