{{--
    Syncs the light/dark scheme with Horizon's scheme-toggler on standalone pages.
    Horizon stores the choice in localStorage['horizonColorScheme'] ('system'|'light'|'dark')
    and applies it by toggling the media attribute of <style data-scheme="dark">.
    This mirrors that logic so /admin matches whatever was chosen on /horizon.
--}}
<script>
    (function () {
        var KEY = 'horizonColorScheme';
        var dark = document.querySelector('style[data-scheme="dark"]');
        if (!dark) { return; }

        var apply = function () {
            var scheme = localStorage.getItem(KEY) || 'system';
            var useDark = scheme === 'system'
                ? window.matchMedia('(prefers-color-scheme: dark)').matches
                : scheme === 'dark';
            dark.media = useDark ? '' : 'max-width: 1px';
        };

        apply();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', apply);
    })();
</script>