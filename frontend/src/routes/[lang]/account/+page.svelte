<script lang="ts">
	/* --- INIT --- */
	// Translation
	import { locale } from '$translation/i18n-svelte'; // current locale

	// JS-Framework/Library
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';

	// Stores
	import { user } from '$store/account';

	/* --- LOGIC --- */
	if (browser) {
		goto(`/${$locale}/account/${$user?.USER_USERNAME ?? ''}`);
	}
</script>

<div class="flex justify-center items-center">
	<h1>redirecting to /{$locale}/account/{$user?.USER_USERNAME ?? ''}...</h1>
</div>
