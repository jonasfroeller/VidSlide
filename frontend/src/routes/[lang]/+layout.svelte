<script lang="ts">
	/* --- INIT --- */
	// Translation
	import { setLocale } from '$translation/i18n-svelte';
	import type { PageData } from './$types';
	export let data: PageData;
	setLocale(data.locale);

	// Styling
	import '$main/app.css'; // Global CSS
	import '$main/theme.postcss'; // Skeleton Theme

	// CSS-Framework/Library
	import '@skeletonlabs/skeleton/styles/all.css';
	import { Toast } from '@skeletonlabs/skeleton';

	// Icons
	import 'iconify-icon';

	// Components
	import Header from '$component/Header.svelte';
	import Sidebar from '$component/Sidebar.svelte';
	import { AppShell } from '@skeletonlabs/skeleton';

	// Stores
	import { loginState } from '$store/account';

	// JS-Framework/Library
	import { onMount } from 'svelte';

	/* --- LOGIC --- */

	/* Modals */
	import { Modal, modalStore } from '@skeletonlabs/skeleton';
	import type { ModalComponent } from '@skeletonlabs/skeleton';

	/* -- Signup Form -- */
	import signupComponent from '$component/SignInUp.svelte';

	const modalComponentRegistry: Record<string, ModalComponent> = {
		signupModalComponent: {
			// component
			ref: signupComponent,
			// props
			props: {},
			// default slot
			slot: "<p>SignUp Form couldn't load!</p>"
		}
	};

	/* -- Confirmation Modal -- */
	const confirm: ModalSettings = {
		type: 'confirm',
		// Data
		title: 'You are not logged in!',
		body: 'Would you like to create an Account or log in an existing account?',
		response: (r: boolean) => (r ? openLoginModal() : console.log('declined opening form'))
	};

	let openLoginModal;
	function checkIfLoggedIn() {
		if (!$loginState) {
			modalStore.trigger(confirm);
		}
	}

	/* --- LOGGING --- */
	onMount(async () => {
		console.log(new Date().toLocaleString());
		console.log(
			"\r\n       _                         ______              _ _           \r\n      | |                       |  ____|            | | |          \r\n      | | ___  _ __   __ _ ___  | |__ _ __ ___   ___| | | ___ _ __ \r\n  _   | |/ _ \\| '_ \\ / _` / __| |  __| '__/ _ \\ / _ \\ | |/ _ \\ '__|\r\n | |__| | (_) | | | | (_| \\__ \\ | |  | | | (_) |  __/ | |  __/ |   \r\n  \\____/ \\___/|_| |_|\\__,_|___/ |_|  |_|  \\___/ \\___|_|_|\\___|_|   \r\n                                                                   \r\n                                                                   \r\n"
		);
		checkIfLoggedIn();
	});

	/* --- CONFIG --- */
	$: sidebarVariant = 'small';
</script>

<AppShell>
	<svelte:fragment slot="sidebarLeft">
		<Sidebar variant={sidebarVariant} />
	</svelte:fragment>
	<!-- Router Slot -->
	<section class="flex h-full w-full">
		<div class="w-[2px] h-full bg-primary-500 relative">
			<button
				on:click={() => {
					sidebarVariant === 'large' ? (sidebarVariant = 'small') : (sidebarVariant = 'large');
				}}
				type="button"
				class="btn-icon variant-glass-tertiary variant-ringed-tertiary ml-[-1.3rem] fixed top-2/4 translate-y-[-50%] text-2xl"
			>
				{#if sidebarVariant === 'large'}
					<iconify-icon class="cursor-pointer flex items-center" icon="mdi:keyboard-arrow-left" />
				{:else}
					<iconify-icon class="cursor-pointer flex items-center" icon="mdi:keyboard-arrow-right" />
				{/if}
			</button>
		</div>
		<!-- divider -->
		<div class="p-8 w-full">
			<Header bind:openLoginModal />
			<slot />
		</div>
	</section>
	<!-- ---- / ---- -->
	<Toast position={'br'} buttonDismiss={'btn-icon'} padding={'p-2 pl-4'} />
	<Modal components={modalComponentRegistry} />
</AppShell>
