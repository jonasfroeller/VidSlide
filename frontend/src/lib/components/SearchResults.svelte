<script>
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Components
	import VideoData from '$component/VideoData.svelte';

	let key;
</script>

{$translation.SearchSection.search_option.videos_found(key)}
<hr />
<div id="results" class="flex gap-4 p-4 flex-wrap justify-center">
	{#each { length: 1 } as result, i (key)}
		<p>{result}</p>
	{:else}
		<p>no results</p>
	{/each}
</div>
