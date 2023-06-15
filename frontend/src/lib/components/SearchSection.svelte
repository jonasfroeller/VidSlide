<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';

	// Components
	import VideoData from '$component/VideoData.svelte';

	// CSS-Framework/Library
	import { RadioGroup, RadioItem } from '@skeletonlabs/skeleton';

	// Scripts
	import { searchedVideos } from '$store/searchedVideos';

	// Props
	export let display_variant = 'vertical';

	/* --- LOGIC --- */
	let subject: string = 'username';
	let sort: string = 'date';
	export let search: string = '';
</script>

<div
	id="search-slot"
	class="max-h-[44rem] {display_variant === 'vertical'
		? 'max-w-md'
		: 'max-w-full'} overflow-y-auto rounded-lg"
>
	<div
		id="search-section"
		class="flex {display_variant == 'vertical'
			? 'flex-col'
			: 'flex-col xl:flex-row'} items-center justify-between gap-4 pt-4 pb-4 sticky top-0 dark:bg-surface-900 bg-surface-50 z-50 p-2"
	>
		<div
			id="search-bar"
			class="input-group {display_variant == 'vertical'
				? 'items-center'
				: 'items-start'} input-group-divider grid-cols-[auto_1fr_auto] h-fit w-max"
		>
			<div class="h-full">
				<iconify-icon class="cursor-pointer" icon="material-symbols:search-rounded" />
			</div>
			<input
				class="p-2 input rounded-none"
				type="search"
				placeholder="{$translation.SearchSection.search()}..."
				bind:value={search}
				on:change={searchedVideos.set([])}
			/>
			<button class="variant-soft-primary h-full border-l border-surface-400-500-token"
				>{$translation.SearchSection.search()}</button
			>
		</div>
		<div
			id="search-config"
			class="flex {display_variant == 'vertical' ? 'flex-col' : 'flex-col lg:flex-row'} gap-4"
		>
			<div class="w-full">
				{$translation.SearchSection.search_option.subject()}
				<hr />
				<RadioGroup>
					<RadioItem
						on:change={searchedVideos.set([])}
						bind:group={subject}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'username'}
						>{$translation.SearchSection.search_option.username()}
					</RadioItem>
					<RadioItem
						on:change={searchedVideos.set([])}
						bind:group={subject}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'tag'}>{$translation.SearchSection.search_option.category()}</RadioItem
					>
					<RadioItem
						on:change={searchedVideos.set([])}
						bind:group={subject}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'title'}>{$translation.SearchSection.search_option.title()}</RadioItem
					>
				</RadioGroup>
			</div>
			<div class="w-full">
				{$translation.SearchSection.search_option.sort_by()}
				<hr />
				<RadioGroup>
					<RadioItem
						on:change={searchedVideos.set([])}
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'date'}
						>{$translation.SearchSection.search_option.date()}
					</RadioItem>
					<RadioItem
						on:change={searchedVideos.set([])}
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'views'}>{$translation.SearchSection.search_option.views()}</RadioItem
					>
					<RadioItem
						on:change={searchedVideos.set([])}
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'likes'}>{$translation.SearchSection.search_option.likes()}</RadioItem
					>
					<RadioItem
						on:change={searchedVideos.set([])}
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'dislikes'}>{$translation.SearchSection.search_option.dislikes()}</RadioItem
					>
				</RadioGroup>
			</div>
		</div>
	</div>
	{#key subject}
		{#key sort}
			{#key search}
				<VideoData
					page={'search'}
					isResultVideo={true}
					searchSubject={subject}
					sortBy={sort}
					searchedText={search}
				/>
			{/key}
		{/key}
	{/key}
</div>
