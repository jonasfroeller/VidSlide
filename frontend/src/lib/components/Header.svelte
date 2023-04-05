<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// CSS-Framework/Library
	/* -- Confirmation Modal -- */
	const confirm: ModalSettings = {
		type: 'confirm',
		// Data
		title: 'Sign Out?',
		body: 'Would you like to sign out?',
		response: (r: boolean) => console.log('response:', r)
	};

	/* Form */
	import { modalStore } from '@skeletonlabs/skeleton';
	import type { ModalSettings } from '@skeletonlabs/skeleton';

	const su: ModalSettings = {
		type: 'component',
		component: 'signupModalComponent'
	};

	/* --- LOGIC --- */
	function openLoginModal() {
		if (!li) {
			modalStore.trigger(su);
		} else {
			modalStore.trigger(confirm);
		}
	}

	export let loggedIn = false;
	$: li = loggedIn;
</script>

<header class="flex justify-end gap-2 text-lg">
	<button type="button" class="btn variant-ringed" on:click={() => openLoginModal()}>
		{#if !li}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:login-variant" />
			<span>{$translation.Header.logIn()}</span>
		{:else}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:logout-variant" />
			<span>{$translation.Header.logOut()}</span>
		{/if}
	</button>
</header>
