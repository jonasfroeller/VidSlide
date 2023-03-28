<script lang="ts">
	// Translation
	import { locale } from '$translation/i18n-svelte'; // current language

	// Svelte
	import { browser } from '$app/environment';
	import { goto } from '$app/navigation';
	/* if (browser) {
		goto(`/${$locale}/home`);
	} */

	import Api from '$api/api';
	// import { onMount } from 'svelte';

	/* onMount(async () => {
		Api.get();
	}); */

	async function getData() {
		return await Api.get();
	}
</script>

<div class="flex justify-center items-center w-full h-full">
	<h1>redirecting to /{$locale}/home...</h1>
</div>

testing path

{#await getData()}
	<p>loading...</p>
{:then data}
	{#each data as item}
		<p>{item.variable}</p>
	{/each}
{:catch error}
	<em><p>couldn't load data from api ({error})</p></em>
{/await}
<!-- TODO: replace with login page -->
