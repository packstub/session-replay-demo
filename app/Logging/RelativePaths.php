<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\NormalizerFormatter;

/**
 * Writes file paths in stack traces relative to the app (app/Payments/PaymentGateway.php),
 * not as absolute paths from the machine the demo runs on.
 */
class RelativePaths
{
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $formatter = $handler->getFormatter();

            if ($formatter instanceof NormalizerFormatter) {
                $formatter->setBasePath(base_path());
            }
        }
    }
}
