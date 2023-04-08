<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { loginState, user } from '$store/account';

	// CSS-Framework/Library
	/* -- Confirmation Modal -- */
	const confirm: ModalSettings = {
		type: 'confirm',
		// Data
		title: 'Sign Out?',
		body: 'Would you like to sign out?',
		response: (r: boolean) => (r ? SignOut() : console.log('declined log out'))
	};

	/* Form */
	import { modalStore, toastStore } from '@skeletonlabs/skeleton';
	import type { ModalSettings, ToastSettings } from '@skeletonlabs/skeleton';

	const su: ModalSettings = {
		type: 'component',
		component: 'signupModalComponent'
	};

	/* Notifications */
	const ts: ToastSettings = {
		message: 'You are now logged out!',
		background: 'variant-ghost-success'
	};

	const ti: ToastSettings = {
		message: 'Logging you out!',
		background: 'variant-ghost-primary'
	};

	/* --- LOGIC --- */
	export const openLoginModal = () => {
		if (!$loginState) {
			modalStore.trigger(su);
		} else {
			modalStore.trigger(confirm);
		}
	};

	function SignOut() {
		toastStore.trigger(ti);
		toastStore.trigger(ts);
		$loginState = false;
	}
</script>

<header class="flex justify-end gap-2 text-lg">
	<button type="button" class="btn variant-ringed" on:click={() => openLoginModal()}>
		{#if !$loginState}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:login-variant" />
			<span>{$user?.USER_USERNAME ?? ''}{$translation.Header.logIn()}</span>
		{:else}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:logout-variant" />
			<span>{$user?.USER_USERNAME ?? ''}{$translation.Header.logOut()}</span>
		{/if}
	</button>
</header>
