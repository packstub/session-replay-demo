<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds the script that turns Log Viewer's Replay column into "Watch replay" buttons to its
 * page, so the vendor view needs no published copy to keep up to date.
 */
class LinkReplaysInLogViewer
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $content = $response->getContent();

        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html') || ! is_string($content) || ! str_contains($content, '</body>')) {
            return $response;
        }

        return $response->setContent(str_replace('</body>', view('log-viewer.replay-links')->render().'</body>', $content));
    }
}
