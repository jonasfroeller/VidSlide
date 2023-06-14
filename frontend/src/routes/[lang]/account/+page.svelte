<script lang="ts">
	/* --- INIT --- */
	// Translation
	import { locale } from '$translation/i18n-svelte';

	// JS-Framework/Library
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';

	// Stores
	import { user } from '$store/account';

	/* --- LOGIC --- */
	$: redirectURL = `/${$locale}/${`${
		$user?.data?.USER_USERNAME != undefined || $user?.data?.USER_USERNAME != null ? 'account/' : ''
	}${$user?.data?.USER_USERNAME ?? 'home'}`}`;

	if (browser) {
		goto(redirectURL);
	}
</script>

<div class="flex justify-center items-center">
	<h1>
		redirecting to {redirectURL}...
	</h1>
</div>
