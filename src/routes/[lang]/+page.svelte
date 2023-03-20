<script lang="ts">
	// Translation
	import { locale } from '$translation/i18n-svelte'; // current language

	// Svelte
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';
	if (browser) {
		goto(`/${$locale}/home`);
	}
</script>

<div class="flex justify-center items-center w-full h-full">
	<h1>redirecting to /{$locale}/home...</h1>
	<!-- TODO: replace with login page -->
</div>
