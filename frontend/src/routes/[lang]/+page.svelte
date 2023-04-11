<script lang="ts">
	/* --- INIT --- */
	// Translation
	import { locale } from '$translation/i18n-svelte'; // current locale

	// JS-Framework/Library
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';

	/* --- LOGIC --- */
	if (browser) {
		goto(`/${$locale}/home`);
	}
</script>

<div class="flex justify-center items-center">
	<h1>redirecting to /{$locale}/home...</h1>
</div>
