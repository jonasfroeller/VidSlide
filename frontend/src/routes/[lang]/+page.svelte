<script lang="ts">
	/* --- INIT --- */
	// Translation
	import { locale } from '$translation/i18n-svelte'; // current language

	// JS-Framework/Library
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';

	/* --- LOGIC --- */
	if (browser) {
		goto(`/${$locale}/home`);
	}
</script>

<div class="flex justify-center items-center w-full h-full">
	<h1>redirecting to /{$locale}/home...</h1>
</div>
