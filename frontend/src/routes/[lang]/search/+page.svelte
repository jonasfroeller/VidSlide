<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';
	import { RadioGroup, RadioItem } from '@skeletonlabs/skeleton';

	// Components
	import FetchVideo from '$component/FetchVideo.svelte';

	// LoadData
	import type { PageData } from './$types';
	export let data: PageData;

	/* --- LOGIC --- */
	let subject: string = 'username';
	let sort: string = 'date';
	let search: string = '';
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
	<div id="search-section" class="flex flex-col gap-4">
		<div id="search-bar" class="mt-2 input-group input-group-divider grid-cols-[auto_1fr_auto]">
			<div class="input-group-shim">
				<iconify-icon class="cursor-pointer" icon="material-symbols:search-rounded" />
			</div>
			<input
				class="p-2 rounded-none outline-none"
				type="search"
				placeholder="{$translation.global.search()}..."
				bind:value={search}
			/>
			<button class="variant-soft-secondary">{$translation.global.search()}</button>
		</div>
		<div id="search-config" class="flex flex-col gap-2">
			Subject:
			<hr />
			<RadioGroup>
				<RadioItem
					bind:group={subject}
					name="justify"
					active={'variant-ghost-tertiary'}
					value={'username'}>Username</RadioItem
				>
				<RadioItem
					bind:group={subject}
					name="justify"
					active={'variant-ghost-tertiary'}
					value={'category'}>Category</RadioItem
				>
				<RadioItem
					bind:group={subject}
					name="justify"
					active={'variant-ghost-tertiary'}
					value={'title'}>Title</RadioItem
				>
			</RadioGroup>
			Sort By:
			<hr />
			<RadioGroup>
				<RadioItem bind:group={sort} name="justify" active={'variant-ghost-tertiary'} value={'date'}
					>Date</RadioItem
				>
				<RadioItem
					bind:group={sort}
					name="justify"
					active={'variant-ghost-tertiary'}
					value={'views'}>Views</RadioItem
				>
				<RadioItem
					bind:group={sort}
					name="justify"
					active={'variant-ghost-tertiary'}
					value={'likes'}>Likes</RadioItem
				>
				<RadioItem
					bind:group={sort}
					name="justify"
					active={'variant-ghost-tertiary'}
					value={'dislikes'}>Dislikes</RadioItem
				>
			</RadioGroup>
		</div>
		<FetchVideo
			page={data.pathname}
			isResultVideo={true}
			searchSubject={subject}
			sortBy={sort}
			{search}
		/>
	</div>

	<FetchVideo page={data.pathname} />
</section>
