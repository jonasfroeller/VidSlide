<script lang="ts">
	// @ts-nocheck
	/* --- INIT --- */
	// Stores
	import { langState } from '$store/config';
	import { config } from '$store/config';

	// Scripts
	import styleCfg from '$script/styleStorage';

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';
	import { page } from '$app/stores';

	// CSS-Framework/Library
	import { toastStore } from '@skeletonlabs/skeleton';

	// Components
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte

	// Translation
	import translation from '$translation/i18n-svelte';
	import { setLocale, locale } from '$translation/i18n-svelte';
	import { locales } from '$translation/i18n-util';
	import { loadLocaleAsync } from '$translation/i18n-util.async';
	import { replaceLocaleInUrl } from '$main/utils';

	/* --- LOGIC --- */
	export let variant = 'large';

	/**
	 * the way the language gets changed in the url
	 * @param { import('$translation/i18n-types').Locales } newLocale
	 * @param { boolean } updateHistoryState
	 * @return { Promise<void> }
	 */
	const switchLocale = async (newLocale, updateHistoryState = true) => {
		// save to locale storage
		$config = await styleCfg.load();
		// @ts-ignore
		$config.language = newLocale;
		await styleCfg.save($config);

		if (!newLocale || $locale === newLocale) return;
		// load new dictionary from server
		await loadLocaleAsync(newLocale);
		// select locale
		setLocale(newLocale);
		// update `lang` attribute
		// @ts-ignore
		document.querySelector('html').setAttribute('lang', newLocale);
		if (updateHistoryState) {
			// update url to reflect locale changes
			history.pushState({ locale: newLocale }, '', replaceLocaleInUrl(location, newLocale));
		}
	};
	// update locale when navigating via browser back/forward buttons
	/** @param { PopStateEvent } event */
	const handlePopStateEvent = async ({ state }) => switchLocale(state.locale, false);
	// update locale when page store changes
	$: if (browser) {
		const lang =
			/** @type { import('$translation/i18n-types').Locales } page.params.lang */ $page.params.lang;
		switchLocale(lang, false);
		history.replaceState(
			{ ...history.state, locale: lang },
			'',
			replaceLocaleInUrl(location, lang)
		);
	}

	onMount(async () => {
		$config = await styleCfg.load();
		$langState = $config.language;
	});
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<svelte:window on:popstate={handlePopStateEvent} />

<select
	class="select text-md rounded-lg {variant === 'large'
		? 'w-full'
		: 'w-[2rem]'} max-w-full variant-ringed cursor-pointer"
	bind:value={$langState}
	on:change={() => {
		toastStore.trigger(popups.configSaved_success);
		$config.language = $langState;
		styleCfg.save($config);
		switchLocale($langState);
	}}
>
	<option disabled selected>{$translation.LangSelect.lang()}</option>
	{#each locales as l}
		<option value={l}>
			{l}
		</option>
	{/each}
</select>
