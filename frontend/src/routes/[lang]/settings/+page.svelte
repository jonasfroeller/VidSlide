<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';

	// Stores
	import { loginState } from '$store/account';

	// Components
	import Main from '$component/Main.svelte';
	import LangSelect from '$component/LangSelect.svelte';
	import ThemeSelect from '$component/ThemeSelect.svelte';
	import UserData from '$component/UserData.svelte';

	// Data Load Props
	import type { PageData } from './$types';
	export let data: PageData;

	/* LOGIC */
</script>

<svelte:head>
	<meta property="og:url" content="https://svelte-kit-vid-slide.vercel.app/settings" />
	<meta property="og:title" content="Settings" />

	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/settings-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/settings.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/settings-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/settings.svg" media="(prefers-color-scheme:light)" />

	<title>Settings</title>
</svelte:head>

<Main css={'flex-col divide-y'} key={data.pathname}>
	<h3>{$translation.pages.settings.site_section.title()}</h3>
	<div class="flex flex-col md:flex-row gap-4 pt-4 pb-4">
		<ThemeSelect />
		<LangSelect />
	</div>
	{#if $loginState}
		<UserData page={data.pathname} />
	{/if}
</Main>
