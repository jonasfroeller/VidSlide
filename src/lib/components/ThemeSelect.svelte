<script lang="ts">
	// Svelte
	import { onMount } from 'svelte';

	// Skeleton
	import { toastStore } from '@skeletonlabs/skeleton';
	import type { ToastSettings } from '@skeletonlabs/skeleton';

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
	<option disabled selected>Color Theme</option>
	<option value="dark">dark</option>
	<option value="light">light</option>
</select>
