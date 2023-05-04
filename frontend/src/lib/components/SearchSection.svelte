<script lang="ts">
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Components
	import VideoData from '$component/VideoData.svelte';

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';
	import { RadioGroup, RadioItem } from '@skeletonlabs/skeleton';

	/* --- LOGIC --- */
	let subject: string = 'username';
	let sort: string = 'date';
	let search: string = '';

	export let display_variant = 'vertical';
</script>

<div>
	<div
		id="search-section"
		class="flex {display_variant == 'vertical'
			? 'flex-col'
			: 'flex-col xl:flex-row'} items-center justify-between gap-4 pt-4 pb-4"
	>
		<div
			id="search-bar"
			class="input-group {display_variant == 'vertical'
				? 'items-center'
				: 'items-start'} input-group-divider grid-cols-[auto_1fr_auto] h-fit w-max"
		>
			<div class="input-group-shim h-full">
				<iconify-icon class="cursor-pointer" icon="material-symbols:search-rounded" />
			</div>
			<input
				class="p-2 rounded-none outline-none"
				type="search"
				placeholder="{$translation.SearchSection.search()}..."
				bind:value={search}
			/>
			<button class="variant-soft-secondary h-full">{$translation.SearchSection.search()}</button>
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
						bind:group={subject}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'username'}>{$translation.SearchSection.search_option.username()}</RadioItem
					>
					<RadioItem
						bind:group={subject}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'category'}>{$translation.SearchSection.search_option.category()}</RadioItem
					>
					<RadioItem
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
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'date'}
						>{$translation.SearchSection.search_option.date()}
					</RadioItem>
					<RadioItem
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'views'}>{$translation.SearchSection.search_option.views()}</RadioItem
					>
					<RadioItem
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'likes'}>{$translation.SearchSection.search_option.likes()}</RadioItem
					>
					<RadioItem
						bind:group={sort}
						name="justify"
						active={'variant-ghost-tertiary'}
						value={'dislikes'}>{$translation.SearchSection.search_option.dislikes()}</RadioItem
					>
				</RadioGroup>
			</div>
		</div>
	</div>
	<div>
		{#key search}
			<VideoData page={'search'} isResultVideo={true} {subject} {sort} {search} />
		{/key}
	</div>
</div>
