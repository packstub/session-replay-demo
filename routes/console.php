<?php

use Illuminate\Support\Facades\Schedule;

// Deletes recordings past retention.days (30) and the stylesheets nothing points at any more.
Schedule::command('session-replay:prune')->daily();
