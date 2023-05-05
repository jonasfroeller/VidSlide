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
		goto(
			`/${$locale}/${`${
				$user.USER_USERNAME != undefined || $user.USER_USERNAME != null ? 'account/' : ''
			}${$user?.USER_USERNAME ?? 'home'}`}`
		);
	}
</script>

<div class="flex justify-center items-center">
	<h1>
		redirecting to {`/${$locale}/${`${
			$user.USER_USERNAME != undefined || $user.USER_USERNAME != null ? 'account/' : ''
		}${$user?.USER_USERNAME ?? 'home'}`}`}...
	</h1>
</div>
