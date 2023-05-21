<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';

	// JS-Framework/Library
	import { onMount } from 'svelte';

	// CSS-Framework/Library
	import { toastStore } from '@skeletonlabs/skeleton';

	// Components
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte

	// Stores
	import { config } from '$store/config';
	import { themeState } from '$store/config';

	// Scripts
	import styleCfg from '$script/styleStorage';

	/* --- LOGIC --- */
	export let variant = 'large';

	onMount(async () => {
		$config = await styleCfg.load();
		$themeState = $config.theme;
	});
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<select
	class="select outlined text-md rounded-lg {variant === 'large'
		? 'w-full'
		: 'w-[2rem]'} variant-ringed cursor-pointer"
	name="theme"
	bind:value={$themeState}
	on:change={() => {
		toastStore.trigger(popups.configSaved_success);
		$config.theme = $themeState;
		styleCfg.save($config);
	}}
>
	<option disabled selected>{$translation.ThemeSelect.theme()}</option>
	<option value="dark">{$translation.ThemeSelect.theme_dark()}</option>
	<option value="light">{$translation.ThemeSelect.theme_light()}</option>
</select>
