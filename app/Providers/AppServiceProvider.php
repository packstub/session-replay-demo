<?php

namespace App\Providers;

use App\Http\Middleware\LinkReplaysInLogViewer;
use App\Logging\ReplayLaravelLog;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\Facades\LogViewer;
use Opcodes\LogViewer\Http\Middleware\AuthorizeLogViewer;
use Packstub\AccountSwitcher\AppContext;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Before Log Viewer boots and registers its routes with this list; its defaults when its
        // config is not merged yet (ours then wins the merge).
        $middleware = config('log-viewer.middleware', ['web', AuthorizeLogViewer::class]);
        config(['log-viewer.middleware' => [...$middleware, LinkReplaysInLogViewer::class]]);
    }

    public function boot(): void
    {
        LogViewer::auth(fn ($request): bool => (bool) $request->user()?->is_support);
        ReplayLaravelLog::register();

        // A developer login lands where the password sign-in does.
        $this->app->make(AppContext::class)
            ->redirectTo(fn ($user): string => $user->is_support ? url('/session-replay') : route('shop'));
    }
}
