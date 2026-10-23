<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Packstub\SessionReplay\Events\ReplayReported;
use Packstub\SessionReplay\Facades\SessionReplay;
use Packstub\SessionReplay\Models\ReplayMarker;
use Packstub\SessionReplay\Models\ReplaySession;

class SessionReplayServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Support watches; customers never do.
        Gate::define('viewSessionReplay', fn (User $user, ?ReplaySession $session = null): bool => $user->is_support);

        // Record customers, not the support team looking at their replays.
        SessionReplay::recordWhen(fn (?User $user): bool => ! $user?->is_support);

        // A server error's marker carries the report id that Laravel also put on the log line;
        // "Open in logs" in the player opens Log Viewer searched for it.
        SessionReplay::logUrlUsing(fn (ReplayMarker $marker): ?string => isset($marker->payload['report'])
            ? url('/log-viewer?query='.$marker->payload['report'])
            : null);

        // "Report a problem" (SessionReplay.send() in resources/js/app.js): tell support, with a link.
        // A real app would send a Slack or mail notification here.
        Event::listen(function (ReplayReported $event): void {
            Log::warning('Problem reported: '.$event->marker->label, [
                'user' => $event->session->user_id,
                'replay' => $event->session->url(),
            ]);
        });
    }
}
