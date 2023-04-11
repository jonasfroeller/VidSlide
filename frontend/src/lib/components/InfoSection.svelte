<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { loginState, user } from '$store/account';

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';
	import { ProgressBar } from '@skeletonlabs/skeleton';

	/* -- Confirmation Modal -- */
	const confirm: ModalSettings = {
		type: 'confirm',
		// Data
		title: 'Sign Out?',
		body: 'Would you like to sign out?',
		response: (r: boolean) => (r ? SignOut() : console.log('declined log out'))
	};

	/* Form */
	import { modalStore } from '@skeletonlabs/skeleton';
	import type { ModalSettings } from '@skeletonlabs/skeleton';

	const su: ModalSettings = {
		type: 'component',
		component: 'signupModalComponent'
	};

	// Components
	import CommentPost from '$component/CommentPost.svelte';

	// Props
	export let video_title;
	export let video_description;
	export let video_date_time_posted;
	export let video_tags = [];
	export let video_comments = []; // TODO: replies

	/* --- LOGIC --- */
	export const openLoginModal = () => {
		if (!$loginState) {
			modalStore.trigger(su);
		} else {
			modalStore.trigger(confirm);
		}
	};

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
				{#if $loginState}
					<div id="write-comment" class="flex items-center gap-2 justify-center">
						{#if $user?.USER_PROFILEPICTURE != undefined}
							<Avatar class="scale-50" src={$user?.USER_PROFILEPICTURE} />
						{:else}
							<Avatar class="scale-75" initials={$user?.USER_USERNAME?.charAt(0) ?? '??'} />
						{/if}
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
				{:else}
					<button
						class="underline hover:no-underline dark:text-tertiary-300 text-tertiary-500"
						on:click={() => openLoginModal()}>log in</button
					> to write a comment
				{/if}
				<hr />
				<div class="flex flex-col gap-6">
					{#each video_comments as comment}
						<CommentPost
							comment_id={comment?.COMMENT_ID}
							comment_username={comment?.USER_USERNAME}
							comment_date_time_posted={comment?.COMMENT_DATETIMEPOSTED}
							comment_text={comment?.COMMENT_MESSAGE}
							comment_avatar={comment?.USER_PROFILEPICTURE}
							comment_likes={comment?.COMMENT_LIKES?.length}
							comment_dislikes={comment?.COMMENT_DISLIKES?.length}
							comment_replies={0}
						/>
						<!-- TODO: replies -->
					{:else}
						be the first to comment on this post
					{/each}
				</div>
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
	<div id="video-info-tags" class="flex flex-wrap gap-1">
		{#each video_tags as tag}
			<span class="chip variant-ringed truncate">#{tag?.HASHTAG_NAME}</span>
		{:else}
			<span class="truncate text-xs text-primary-700 dark:text-primary-500"
				>{$translation.VideoResult.no_tags()}</span
			>
		{/each}
	</div>
</article>
