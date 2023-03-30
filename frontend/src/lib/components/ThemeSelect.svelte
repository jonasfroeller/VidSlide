<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';

	// JS-Framework/Library
	import { onMount } from 'svelte';

	// CSS-Framework/Library
	import { toastStore } from '@skeletonlabs/skeleton';
	import type { ToastSettings } from '@skeletonlabs/skeleton';

	/* Toast */
	const ts: ToastSettings = {
		message: 'Config saved!',
		// Provide any utility or variant background style:
		background: 'variant-ghost-success'
	};

	// Stores
	import { config } from '$store/config';
	import { themeState } from '$store/config';

	// Scripts
	import styleCfg from '$script/styleStorage';

	/* --- LOGIC --- */
	export let variant = 'large';

	onMount(async () => {
		$config = await styleCfg.load();
		// @ts-ignore
		$themeState = $config.theme;
	});
</script>

<select
	class="select outlined text-md rounded-lg {variant === 'large'
		? 'w-full'
		: 'w-[6rem]'} variant-ringed cursor-pointer"
	bind:value={$themeState}
	on:change={() => {
		// @ts-ignore
		$config.theme = $themeState;
		styleCfg.save($config);
		toastStore.trigger(ts);
	}}
>
	<option disabled selected>{$translation.ThemeSelect.theme()}</option>
	<option value="dark">dark</option>
	<option value="light">light</option>
</select>
