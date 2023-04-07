<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { user, user_following } from '$store/account';

	// Components
	import CommentPost from '$component/CommentPost.svelte';
	import { Avatar } from '@skeletonlabs/skeleton';

	// Props
	export let video_title;
	export let video_description = '';
	export let video_date_time_posted;
	export let video_tags = [];
	export let video_comments = []; // TODO: replies

	/* --- LOGIC --- */
	$: selectedBox = true; // true if comments are selected
</script>

<article id="video-info-section" class="flex flex-col gap-2">
	<div id="video-info-actions">
		<div class="btn-group variant-ringed">
			<button
				class={selectedBox ? 'variant-ghost' : ''}
				on:click={() => {
					selectedBox = true;
				}}>{$translation.InfoSection.comments()}</button
			>
			<button
				class={!selectedBox ? 'variant-ghost' : ''}
				on:click={() => {
					selectedBox = false;
				}}>{$translation.InfoSection.description()}</button
			>
		</div>
	</div>
	<div
		id="video-info-content"
		class="border border-gray-500 rounded-md p-4 w-[360px] h-[640px] overflow-auto relative"
	>
		{#if selectedBox}
			<div id="comments">
				<h3>{$translation.InfoSection.comments_amount(video_comments?.length)}</h3>
				<div id="write-comment" class="flex items-center gap-2 justify-center">
					<Avatar class="scale-75" initials={$user?.username?.charAt(0) ?? 'UN'} />
					<textarea
						class="textarea p-2 w-fit resize-none"
						rows="1"
						maxlength="200"
						placeholder="I think..."
					/>
					<button type="button" class="btn-icon variant-soft-secondary h-fit">
						<iconify-icon class="cursor-pointer scale-150" icon="material-symbols:send" />
					</button>
				</div>
				<hr />
				<CommentPost
					comment_username={'peterson'}
					comment_avatar={null}
					date_time_posted={'5 days ago'}
					text={'hello world!!!'}
					likes={2}
					dislikes={3}
				/>
			</div>
		{:else}
			<div id="info">
				<h3>{video_title}</h3>
				<p class="text-md">
					{video_description}
				</p>
			</div>
		{/if}
	</div>
	<hr />
	<p class="text-xs text-primary-700 dark:text-primary-500 text-ellipsis">
		{$translation.InfoSection.posted_on()}
		<span id="date-posted"> {video_date_time_posted} </span>
		<span id="time-posted"> <!-- TODO: insert date and time --> </span>
	</p>
	<div id="video-info-tags">
		{#each video_tags as tag}
			<span class="chip variant-ringed truncate">#{tag}</span>
		{:else}
			<span class="truncate text-xs text-primary-700 dark:text-primary-500"
				>{$translation.VideoResult.no_tags()}</span
			>
		{/each}
	</div>
</article>
