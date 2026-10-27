<?php

namespace App\Logging;

use Opcodes\LogViewer\Facades\LogViewer;
use Opcodes\LogViewer\Logs\LaravelLog;
use Opcodes\LogViewer\Logs\Log;
use Opcodes\LogViewer\Logs\LogType;

/**
 * Laravel log lines for Log Viewer, with a Replay column: the link Session Replay adds to the
 * context of every line logged during a recorded request (`session_replay_moment`, the replay
 * at that moment). Log Viewer shows column values as text; `log-viewer.replay-links` turns
 * the link into a button.
 */
class ReplayLaravelLog extends LaravelLog
{
    public const COLUMN = ['label' => 'Replay', 'data_path' => 'extra.session_replay'];

    public static array $columns = [
        ['label' => 'Severity', 'data_path' => 'level'],
        ['label' => 'Datetime', 'data_path' => 'datetime'],
        ['label' => 'Env', 'data_path' => 'extra.environment'],
        ['label' => 'Message', 'data_path' => 'message'],
        self::COLUMN,
    ];

    /**
     * A search across every log file (what "Open in logs" opens) shows the base Log class's
     * columns, so the Replay column goes there too; each line is still read by its file's class.
     */
    public static function register(): void
    {
        LogViewer::extend(LogType::LARAVEL, static::class);

        if (! in_array(self::COLUMN, Log::$columns, true)) {
            Log::$columns[] = self::COLUMN;
        }
    }

    protected function parseText(array &$matches = []): void
    {
        parent::parseText($matches);

        // One context is the array itself; several (context and Laravel's Context) are a list.
        $contexts = array_is_list($this->context) ? $this->context : [$this->context];

        foreach ($contexts as $context) {
            if (is_array($context) && is_string($context['session_replay_moment'] ?? null)) {
                $this->extra['session_replay'] = $context['session_replay_moment'];
            }
        }
    }
}
