<script>
	/* INIT */
	// JS-Framework/Library
	import { page } from '$app/stores';

	// Translation
	import translation from '$translation/i18n-svelte';
	import { locale } from '$translation/i18n-svelte';

	/* LOGIC */
</script>

<section class="flex flex-col items-center">
	<div class="mb-4 text-center">
		<h1>{$page.status} {$page.error.message}</h1>
		<p><b>{$translation.Error.query()}:</b> "{$page.url.pathname}"</p>
	</div>

	<a class="btn variant-ghost" href={`/${$locale}/home`}> {$translation.Error.back()} </a>
</section>
