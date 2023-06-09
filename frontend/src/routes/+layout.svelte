<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';
	import { setLocale } from '$translation/i18n-svelte';
	import type { PageData } from './[lang]/$types';
	export let data: PageData;
	setLocale(data.locale);

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// CSS-Framework/Library
	import '@skeletonlabs/skeleton/styles/all.css';
	import { AppShell } from '@skeletonlabs/skeleton';
	import { Toast } from '@skeletonlabs/skeleton';
	import { Modal, modalStore } from '@skeletonlabs/skeleton';
	import type { ModalComponent } from '@skeletonlabs/skeleton';
	import { storePopup } from '@skeletonlabs/skeleton';
	import { computePosition, autoUpdate, flip, shift, offset, arrow } from '@floating-ui/dom';
	storePopup.set({ computePosition, autoUpdate, flip, shift, offset, arrow });

	// Styling
	import '$main/app.css'; // Global CSS
	import '$main/theme.postcss'; // Skeleton Theme

	// Icons
	import 'iconify-icon';

	// Components
	import Header from '$component/Header.svelte';
	import Sidebar from '$component/Sidebar.svelte';
	import Popups from '$component/Popups.svelte';
	import signupComponent from '$component/SignInUp.svelte';

	// Stores
	import { loginState, user, jwt } from '$store/account';

	// Scripts
	import accountCfg from '$script/accountStorage';

	/* --- LOGIC --- */
	let popups; // popups in Popups.svelte
	let openLoginModal; // passed by Header

	const modalComponentRegistry: Record<string, ModalComponent> = {
		signupModalComponent: {
			// component
			ref: signupComponent,
			// props
			props: {},
			// default slot
			slot: '<em>Error</em>'
		}
	};

	// Responsiveness for Sidebar
	function handleResize() {
		if (window.innerWidth <= 750) {
			sidebarVariant = 'small';

			const element = document.querySelector('#resize-sidebar');
			element.classList.add('hidden');
		} else {
			const element = document.querySelector('#resize-sidebar');
			element.classList.remove('hidden');
		}
	}

	if (browser) {
		handleResize();
		window.addEventListener('resize', handleResize);
	}

	/* --- LOGGING --- */
	onMount(async () => {
		console.log(new Date().toLocaleString());
		console.log(
			"\r\n       _                         ______              _ _           \r\n      | |                       |  ____|            | | |          \r\n      | | ___  _ __   __ _ ___  | |__ _ __ ___   ___| | | ___ _ __ \r\n  _   | |/ _ \\| '_ \\ / _` / __| |  __| '__/ _ \\ / _ \\ | |/ _ \\ '__|\r\n | |__| | (_) | | | | (_| \\__ \\ | |  | | | (_) |  __/ | |  __/ |   \r\n  \\____/ \\___/|_| |_|\\__,_|___/ |_|  |_|  \\___/ \\___|_|_|\\___|_|   \r\n                                                                   \r\n                                                                   \r\n"
		);

		const cfg = await accountCfg.load();

		if (cfg) {
			$loginState = cfg.loginState;
			$user = cfg.user;
			$jwt = cfg.jwt;
		}

		checkIfLoggedIn();
	});

	function checkIfLoggedIn() {
		if (!$loginState) {
			modalStore.trigger(popups.confirmLogIn);
		}
	}

	/* --- CONFIG --- */
	$: sidebarVariant = 'small';
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

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
				id="resize-sidebar"
				type="button"
				class="btn-icon variant-glass-tertiary variant-ringed-tertiary ml-[-1.3rem] fixed top-2/4 translate-y-[-50%] text-2xl z-[999]"
			>
				<iconify-icon
					class="cursor-pointer flex items-center"
					icon={sidebarVariant === 'large' ? 'mdi:keyboard-arrow-left' : 'mdi:keyboard-arrow-right'}
				/>
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
