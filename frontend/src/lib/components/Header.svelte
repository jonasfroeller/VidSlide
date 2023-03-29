<script lang="ts">
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Skeleton
	import { toastStore } from '@skeletonlabs/skeleton';
	import type { ToastSettings } from '@skeletonlabs/skeleton';

	// Notifications
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

	// Form
	import { modalStore } from '@skeletonlabs/skeleton';
	import type { ModalSettings } from '@skeletonlabs/skeleton';

	const su: ModalSettings = {
		type: 'component',
		component: 'signupModalComponent'
	};

	function openLoginModal() {
		if (!loggedIn) {
			// $modalStore.push(su);
			modalStore.trigger(su);
		} else {
			toastStore.trigger(ti);
		}
	}

	function throwLoginError() {
		toastStore.trigger(te);
	}

	export let loggedIn = false;

	$: showPrivateData = loggedIn;
</script>

<header class="flex justify-end gap-2 text-lg">
	<button type="button" class="btn variant-ringed" on:click={() => openLoginModal()}>
		{#if !showPrivateData}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:login-variant" />
			<span>Login</span>
		{:else}
			<iconify-icon class="cursor-pointer flex items-center" icon="mdi:logout-variant" />
			<span>Logout</span>
		{/if}
	</button>
</header>
