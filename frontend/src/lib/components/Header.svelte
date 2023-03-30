<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// CSS-Framework/Library
	import { toastStore } from '@skeletonlabs/skeleton';
	import type { ToastSettings } from '@skeletonlabs/skeleton';

	/* Notifications */
	const ts: ToastSettings = {
		message: 'Logged in!',
		background: 'variant-ghost-success'
	};

	const ti: ToastSettings = {
		message: 'You are already logged in!',
		background: 'variant-ghost-primary'
	};

	const tw: ToastSettings = {
		message: 'Something went wrong!',
		background: 'variant-ghost-warning'
	};

	const te: ToastSettings = {
		message: "Couldn't log in!",
		background: 'variant-ghost-error'
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
			// $modalStore.push(su);
			modalStore.trigger(su);
		} else {
			toastStore.trigger(ti);
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
