<?php

if (! function_exists('money')) {
    /** Cents as dollars: 4250 → "$42.50". */
    function money(int $cents): string
    {
        return '$'.number_format($cents / 100, 2);
    }
}
