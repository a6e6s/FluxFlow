<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<script>
	(() => {
		const storageKey = 'flux.appearance';
		const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
		const validAppearances = ['light', 'dark', 'system'];

		const getAppearance = () => window.localStorage.getItem(storageKey) || 'system';
		const isDarkAppearance = (appearance) =>
			appearance === 'dark' || (appearance === 'system' && colorSchemeQuery.matches);
		const applyAppearance = (appearance) => {
			const normalizedAppearance = validAppearances.includes(appearance) ? appearance : 'system';

			if (normalizedAppearance === 'system') {
				window.localStorage.removeItem(storageKey);
			} else {
				window.localStorage.setItem(storageKey, normalizedAppearance);
			}

			if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
				window.Flux.applyAppearance(normalizedAppearance);
			} else {
				document.documentElement.classList.toggle('dark', isDarkAppearance(normalizedAppearance));
			}

			window.dispatchEvent(new CustomEvent('flux-theme-changed', {
				detail: {
					appearance: normalizedAppearance,
					dark: isDarkAppearance(normalizedAppearance),
				},
			}));
		};

		window.FluxFlowTheme = {
			getAppearance,
			isDark() {
				return isDarkAppearance(getAppearance());
			},
			set(value) {
				applyAppearance(value);
			},
			toggle() {
				applyAppearance(this.isDark() ? 'light' : 'dark');
			},
		};
	})();
</script>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=cairo:400,500,600,700" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
