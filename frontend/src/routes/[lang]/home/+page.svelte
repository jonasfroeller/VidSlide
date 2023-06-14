<script lang="ts">
	/* --- INIT --- */
	// Components
	import Main from '$component/Main.svelte';
	import VideoData from '$component/VideoData.svelte';

	// Data Load Props
	import type { PageData } from './$types';
	export let data: PageData;

	/* LOGIC */
</script>

<svelte:head>
	<meta property="og:url" content="https://svelte-kit-vid-slide.vercel.app/home" />
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

<Main css={'gap-6 justify-center flex-wrap'} key={data.pathname}>
	<VideoData page={data.pathname} />
</Main>
