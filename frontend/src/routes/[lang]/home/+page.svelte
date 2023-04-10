<script lang="ts">
	/* --- INIT --- */
	// Components
	import FetchVideo from '$component/FetchVideo.svelte';

	// LoadData
	import type { PageData } from './$types';
	export let data: PageData;
</script>

<svelte:head>
	<meta property="og:url" content="https://rabbidly.com/en/" />
	<meta property="og:title" content="Home" />

	<!-- %sveltekit.assets% and $images fails -->
	<!-- prefers-color-scheme and not $themestate because browser has different theme than website -->
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/home-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/home.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/home-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/home.svg" media="(prefers-color-scheme:light)" />

	<title>Home</title>
</svelte:head>

<section id="home-body" class="flex justify-center pt-2 gap-6 flex-wrap">
	<FetchVideo page={data.pathname} />
</section>
