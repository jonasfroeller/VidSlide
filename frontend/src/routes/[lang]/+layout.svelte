<script lang="ts">
	// Set Language
	import type { PageData } from './$types';
	export let data: PageData;
	setLocale(data.locale);

	// Components
	import Header from '$component/Header.svelte';
	import { AppShell } from '@skeletonlabs/skeleton';

	// Icons
	import 'iconify-icon';

	// CSS-Framework/Library (SKELETON)
	// Custom Theme
	import '$main/theme.postcss';

	// Skeleton
	import '@skeletonlabs/skeleton/styles/all.css';
	import { Toast } from '@skeletonlabs/skeleton';

	// Modals
	import { Modal, modalStore } from '@skeletonlabs/skeleton';
	import type { ModalSettings, ModalComponent } from '@skeletonlabs/skeleton';

	// Confirm Modal
	const confirm: ModalSettings = {
		type: 'confirm',
		// Data
		title: 'Accept Cookies?',
		body: 'Cookies are used to save user sessions.',
		response: (r: boolean) => console.log('response:', r)
	};

	// modalStore.trigger(confirm);

	// Signup Form
	import signupComponent from '$component/SignInUp.svelte';

	const modalComponentRegistry: Record<string, ModalComponent> = {
		signupModalComponent: {
			// component
			ref: signupComponent,
			// props
			props: {},
			// default slot
			slot: "<p>SignUp Form couldn't load</p>"
		}
	};

	// Global Stylesheet
	import '$main/app.css';

	// Translation
	import { setLocale } from '$translation/i18n-svelte';

	// Svelte
	import { onMount } from 'svelte';
	import Sidebar from '$main/lib/components/Sidebar.svelte';

	onMount(async () => {
		console.log(new Date().toLocaleString());
		console.log(
			"\r\n       _                         ______              _ _           \r\n      | |                       |  ____|            | | |          \r\n      | | ___  _ __   __ _ ___  | |__ _ __ ___   ___| | | ___ _ __ \r\n  _   | |/ _ \\| '_ \\ / _` / __| |  __| '__/ _ \\ / _ \\ | |/ _ \\ '__|\r\n | |__| | (_) | | | | (_| \\__ \\ | |  | | | (_) |  __/ | |  __/ |   \r\n  \\____/ \\___/|_| |_|\\__,_|___/ |_|  |_|  \\___/ \\___|_|_|\\___|_|   \r\n                                                                   \r\n                                                                   \r\n"
		);
	});

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
		<div class="p-4 w-full">
			<Header loggedIn={false} />
			<slot />
		</div>
	</section>
	<!-- ---- / ---- -->
	<Toast position={'br'} buttonDismiss={'btn-icon'} padding={'p-2 pl-4'} />
	<Modal components={modalComponentRegistry} />
</AppShell>
