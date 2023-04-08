<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	// Components
	import CommentPost from '$component/CommentPost.svelte';

	// Stores
	import { loginState, user_comments_liked, user_comments_disliked } from '$store/account';

	// Props
	export let comment_id;
	export let comment_username;
	export let comment_avatar = null;
	export let date_time_posted;
	export let text;
	export let likes = 0;
	export let dislikes = 0;
	export let replies = 0;
</script>

<div class="comment comment-body flex flex-col">
	<div class="comment-header flex items-center ml-[-10px] mb-[-5px]">
		{#if comment_avatar != null}
			<Avatar class="scale-50" src={comment_avatar} />
		{:else}
			<Avatar class="scale-50" initials={comment_username?.charAt(0)} />
		{/if}
		<p>
			<span class="comment-author">{comment_username}</span> |
			<span class="comment-datetime text-md text-primary-700 dark:text-primary-500"
				>{date_time_posted}</span
			>
		</p>
	</div>
	<div
		class="comment-text w-full h-14 text-md text-primary-700 dark:text-primary-500 p-2 rounded-md bg-surface-200 dark:bg-surface-600 mb-1"
	>
		{text}
	</div>
	<div class="comment-actions flex justify-between">
		<div class="flex">
			<div class="flex flex-col gap-1 scale-[80%]">
				<button type="button" class="btn-icon variant-ringed">
					{#if $user_comments_liked?.includes(comment_id)}
						<iconify-icon icon="material-symbols:thumb-up-rounded" />
					{:else}
						<iconify-icon class="scale-125" icon="material-symbols:thumb-up-outline-rounded" />
					{/if}
				</button>
				<span class="text-xs text-center text-primary-700 dark:text-primary-500">{likes}</span>
			</div>
			<div class="flex flex-col gap-1 scale-[80%]">
				<button type="button" class="btn-icon variant-ringed">
					{#if $user_comments_disliked?.includes(comment_id)}
						<iconify-icon icon="material-symbols:thumb-up-rounded" />
					{:else}
						<iconify-icon class="scale-125" icon="material-symbols:thumb-down-outline-rounded" />
					{/if}
				</button>
				<span class="text-xs text-center text-primary-700 dark:text-primary-500">{dislikes}</span>
			</div>
		</div>
		<div class="flex">
			{#if $loginState}
				<div class="flex flex-col">
					<button type="button" class="btn variant-ringed scale-[80%] hover:scale-[80%]">
						{$translation.CommentPost.reply()}
					</button>
				</div>
			{/if}
			<div class="flex flex-col gap-1">
				<button type="button" class="btn variant-ringed scale-[80%] hover:scale-[80%]">
					{$translation.CommentPost.replies(replies)}
				</button>
			</div>
		</div>
	</div>
</div>
