<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';
	import { locale } from '$translation/i18n-svelte';

	// Components
	import Avatar from '$component/Avatar.svelte';

	// Stores
	import { loginState, user_comments_liked, user_comments_disliked } from '$store/account';

	// CSS
	import { CSS_Styles } from '$script/styles';

	// Props
	export let comment_id;
	export let comment_username;
	export let comment_date_time_posted;
	export let comment_text;
	export let comment_avatar = null;
	export let comment_likes = 0;
	export let comment_dislikes = 0;
	export let comment_replies = 0;

	/* --- LOGIC --- */
</script>

<div class="flex flex-col">
	<div class="flex items-center p-2 pl-0 gap-2">
		<a href="/{$locale}/account/{comment_username}" class="transition">
			{#if comment_avatar != null}
				<Avatar {comment_avatar} {comment_username} />
			{:else}
				<Avatar {comment_username} />
			{/if}
		</a>
		<p>
			<a
				href="/{$locale}/account/{comment_username}"
				class="unstyled hover:underline {CSS_Styles.SMALL_ELEMENT.FONT_PRIMARY}"
				>{comment_username}</a
			>
			|
			<span class={CSS_Styles.SMALL_ELEMENT.FONT_TERTIARY}
				>{$translation.CommentPost.dateTime(new Date(comment_date_time_posted))}</span
			>
		</p>
	</div>
	<div
		class="{CSS_Styles.SMALL_ELEMENT
			.FONT_SECONDARY} p-2 rounded-md bg-surface-200 dark:bg-surface-600 max-h-36"
	>
		{comment_text}
	</div>
	<div class="flex justify-between mt-2">
		<div class="flex gap-1">
			<div class="flex gap-1">
				<button
					type="button"
					disabled={$loginState ? false : true}
					class="btn btn-sm variant-ringed-secondary {CSS_Styles.SMALL_ELEMENT.FONT_PRIMARY}"
				>
					{#if $user_comments_liked?.includes(comment_id)}
						<iconify-icon icon="material-symbols:thumb-up-rounded" />
					{:else}
						<iconify-icon icon="material-symbols:thumb-up-outline-rounded" />
					{/if}
					<span class="text-center {CSS_Styles.SMALL_ELEMENT.FONT_TERTIARY}">{comment_likes}</span>
				</button>
			</div>
			<div class="flex gap-1">
				<button
					type="button"
					disabled={$loginState ? false : true}
					class="btn btn-sm variant-ringed-secondary {CSS_Styles.SMALL_ELEMENT.FONT_PRIMARY}"
				>
					{#if $user_comments_disliked?.includes(comment_id)}
						<iconify-icon icon="material-symbols:thumb-up-rounded" />
					{:else}
						<iconify-icon icon="material-symbols:thumb-down-outline-rounded" />
					{/if}
					<span class="text-center {CSS_Styles.SMALL_ELEMENT.FONT_TERTIARY}"
						>{comment_dislikes}</span
					>
				</button>
			</div>
		</div>
		<div class="flex gap-1">
			{#if $loginState}
				<button
					type="button"
					disabled={$loginState ? false : true}
					class="btn btn-sm variant-ringed-secondary"
				>
					{$translation.CommentPost.reply()}
				</button>
			{/if}
			<button type="button" class="btn btn-sm variant-ringed-secondary">
				{$translation.CommentPost.replies({ replies: comment_replies })}
			</button>
		</div>
	</div>
</div>
