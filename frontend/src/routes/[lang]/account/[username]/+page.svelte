<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Components
	import Main from '$component/Main.svelte';
	import UserData from '$component/UserData.svelte';
	import SearchSection from '$component/SearchSection.svelte';

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	// LoadData
	import type { PageData } from './$types';
	export let data: PageData;
</script>

<svelte:head>
	<meta property="og:url" content="https://svelte-kit-vid-slide.vercel.app/account" />
	<meta property="og:title" content="Account" />

	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/account-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/account.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/account-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/account.svg" media="(prefers-color-scheme:light)" />

	<title>Account</title>
</svelte:head>

<Main css={'flex-col'}>
	<UserData page={data.pathname} />
	<SearchSection display_variant={'horizontal'} />
</Main>
