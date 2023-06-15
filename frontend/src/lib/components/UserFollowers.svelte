<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';
	import { locale } from '$translation/i18n-svelte';

	// JS-Framework/Library
	import { popup } from '@skeletonlabs/skeleton';
	import type { PopupSettings } from '@skeletonlabs/skeleton';

	// Components
	import Avatar from '$component/Avatar.svelte';

	// Props
	export let publisher_followers = [];

	/* --- LOGIC --- */
	let view_followers: PopupSettings = {
		event: 'click',
		target: 'view_followers',
		placement: 'bottom'
	};

	let followers_display = publisher_followers?.length ?? 0;
</script>

<div id="subscriber" class="text-md text-primary-700 dark:text-primary-600">
	<button use:popup={view_followers}>
		{$translation.VideoSection.follower(followers_display)}
	</button>
	<div
		data-popup="view_followers"
		class="z-[999] rounded-md bg-surface-200-700-token border-primary-400-500-token p-2 overflow-auto max-h-96 border-2"
	>
		{#each publisher_followers as follower, i}
			<div
				class="flex gap-2 items-center h-full divide-x divide-primary-400 dark:divide-primary-500"
			>
				<p class="text-lg">{i + 1}</p>
				<div class="flex items-center p-2 gap-2 text-primary-700 dark:text-primary-400">
					<a href="/{$locale}/account/{follower?.USER_USERNAME}" class="transition">
						{#if follower?.USER_PROFILEPICTURE != null}
							<Avatar
								comment_username={follower?.USER_USERNAME}
								comment_avatar={follower?.USER_PROFILEPICTURE}
							/>
						{:else}
							<Avatar comment_username={follower?.USER_USERNAME} />
						{/if}
					</a>
					<a
						href="/{$locale}/account/{follower?.USER_USERNAME}"
						class="unstyled hover:underline text-lg text-primary-700 dark:text-primary-400"
					>
						{follower?.USER_USERNAME}
					</a>
				</div>
			</div>
		{:else}
			{$translation.VideoSection.follower(followers_display)}
		{/each}
	</div>
</div>
