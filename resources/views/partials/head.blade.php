<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<script>
	(() => {
		window.FluxFlowTheme = {
			toggle() {
				window.Flux.dark = ! window.Flux.dark;
			},
			set(value) {
				window.Flux.appearance = value === 'system' ? 'system' : value === 'dark' ? 'dark' : 'light';
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
