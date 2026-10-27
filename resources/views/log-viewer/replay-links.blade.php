<style>
    .replay-link { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.6rem; border-radius: 0.375rem;
        background: #4f46e5; color: #fff; font-size: 0.75rem; font-weight: 600; white-space: nowrap; text-decoration: none; }
    .replay-link:hover { background: #4338ca; }
    .replay-link svg { width: 0.7rem; height: 0.7rem; }
</style>
<script>
    // Log Viewer shows column values as text: the Replay column's link becomes a button that
    // opens the replay at the moment the line was logged.
    (() => {
        const prefix = @json(url('/session-replay').'/');
        const root = document.getElementById('log-viewer');
        const linkify = () => {
            root.querySelectorAll('td span').forEach((span) => {
                const url = span.textContent.trim();
                if (!url.startsWith(prefix)) {
                    return;
                }
                const link = document.createElement('a');
                link.href = url;
                link.className = 'replay-link';
                link.innerHTML = '<svg viewBox="0 0 12 12" aria-hidden="true"><path d="M3 1.5v9l7.5-4.5z" fill="currentColor"/></svg>';
                link.append(@json(__('Watch replay')));
                // The row toggles open on click; the button only opens the replay.
                link.addEventListener('click', (event) => event.stopPropagation());
                span.replaceChildren(link);
            });
        };
        new MutationObserver(linkify).observe(root, { childList: true, subtree: true });
    })();
</script>
