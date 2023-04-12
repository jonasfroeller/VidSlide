<script lang="ts">
	/* --- INIT --- */
	// Components
	import VideoData from '$component/VideoData.svelte';
	import SearchSection from '$component/SearchSection.svelte';

	// LoadData
	import type { PageData } from './$types';
	export let data: PageData;
</script>

<svelte:head>
	<meta property="og:url" content="https://rabbidly.com/en/" />
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

<section id="search-body" class="flex justify-center pt-2 gap-6 flex-wrap">
	<SearchSection page={data.pathname} />

	<VideoData page={data.pathname} />
</section>
