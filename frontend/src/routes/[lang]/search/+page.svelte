<script lang="ts">
	/* --- INIT --- */
	// Components
	import Main from '$component/Main.svelte';
	import VideoData from '$component/VideoData.svelte';
	import SearchSection from '$component/SearchSection.svelte';

	// LoadData
	import type { PageData } from './$types';
	export let data: PageData;
</script>

<svelte:head>
	<meta property="og:url" content="https://svelte-kit-vid-slide.vercel.app/" />
	<meta property="og:title" content="Search" />

	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/search-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/search.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/search-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/search.svg" media="(prefers-color-scheme:light)" />

	<title>Search</title>
</svelte:head>

<Main css={'gap-6'}>
	<SearchSection page={data.pathname} />

	<VideoData page={data.pathname} />
</Main>
