<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { loginState, user } from '$store/account';

	// Components
	import Popups from '$component/Popups.svelte';

	/* --- LOGIC --- */
	// CSS-Framework/Library
	import { modalStore } from '@skeletonlabs/skeleton';
	let popups; // popups in Popups.svelte

	export const openLoginModal = () => {
		if (!$loginState) {
			modalStore.trigger(popups.signInUpForm);
		} else {
			modalStore.trigger(popups.confirmLogOut);
		}
	};
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<header class="flex justify-end gap-2 text-lg">
	<button type="button" class="btn variant-ringed" name="login-btn" on:click={openLoginModal}>
		{#if !$loginState}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:login-variant" />
			<span>{$translation.Header.logIn()}</span>
		{:else}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:logout-variant" />
			<span>{$translation.Header.logOut($user?.data?.USER_USERNAME ?? '')}</span>
		{/if}
	</button>
</header>
