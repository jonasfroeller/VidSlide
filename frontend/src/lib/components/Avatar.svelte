<script lang="ts">
	/* --- INIT --- */
	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	// Styles
	import { CSS_Styles } from '$script/styles';

	// Translation
	import { locale } from '$translation/i18n-svelte';

	// Props
	export let size = 'small';
	export let comment_avatar = null;
	export let comment_username;

	/* --- LOGIC --- */
	$: element_size =
		size === 'large'
			? CSS_Styles.LARGE_ELEMENT.AVATAR_SIZE
			: size === 'medium'
			? CSS_Styles.MEDIUM_ELEMENT.AVATAR_SIZE
			: CSS_Styles.SMALL_ELEMENT.AVATAR_SIZE;
</script>

{#if comment_avatar && comment_avatar != ''}
	<a class="unstyled" href="/{$locale}/account/{comment_username ?? ''}">
		<Avatar cursor="cursor-pointer" class={element_size} src={comment_avatar} />
	</a>
{:else}
	<a class="unstyled" href="/{$locale}/account/{comment_username ?? ''}">
		<Avatar
			cursor="cursor-pointer"
			class={element_size}
			initials={`${comment_username?.charAt(0) ?? '?'}${comment_username?.charAt(1) ?? '?'}`}
			background={'bg-primary-900'}
		/>
	</a>
{/if}
