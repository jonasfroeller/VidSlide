<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { user, user_following, user_social } from '$store/account';

	// Components
	import UserData from '$component/UserData.svelte';
	import VideoResult from '$component/VideoResult.svelte';

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	/* --- LOGIC --- */
	// TODO: fetch and parse data
</script>

<svelte:head>
	<meta property="og:url" content="https://rabbidly.com/en/" />
	<meta property="og:title" content="Account" />

	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/account-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/account.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/account-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/account.svg" media="(prefers-color-scheme:light)" />

	<title>Account</title>
</svelte:head>

<section id="account-body" class="flex flex-col pt-2">
	<UserData />
	<div id="videos">
		<div id="controlls" class="flex justify-between items-center gap-8 p-2">
			<div
				id="search-bar"
				class="mt-2 input-group input-group-divider grid-cols-[auto_1fr_auto] h-fit w-fit"
			>
				<div class="input-group-shim">
					<iconify-icon class="cursor-pointer" icon="material-symbols:search-rounded" />
				</div>
				<input
					class="p-2 rounded-none outline-none"
					type="search"
					placeholder="{$translation.global.search()}..."
				/>
				<button class="variant-soft-secondary">{$translation.global.search()}</button>
			</div>
			<div id="search-config" class="flex flex-col gap-2">
				<div id="sort-subject" class="flex items-center gap-2">
					{$translation.pages.account.subject()}
					<button type="button" class="btn variant-ringed"
						>{$translation.pages.account.category()}</button
					>
					<button type="button" class="btn variant-ringed"
						>{$translation.pages.account.username()}</button
					>
					<button type="button" class="btn variant-ringed"
						>{$translation.pages.account.title()}</button
					>
				</div>
				<div id="sort-by" class="flex items-center gap-2">
					{$translation.pages.account.sort_by()}
					<button type="button" class="btn variant-ringed"
						>{$translation.pages.account.date()}</button
					>
					<button type="button" class="btn variant-ringed"
						>{$translation.pages.account.views()}</button
					>
					<button type="button" class="btn variant-ringed"
						>{$translation.pages.account.likes()}</button
					>
				</div>
			</div>
		</div>
		{$translation.pages.account.videos_found(20)}
		<hr />
		<div id="results" class="flex gap-4 p-4 flex-wrap justify-center">
			<VideoResult
				publisher={'Mitch'}
				publisher_following={true}
				publisher_followers={'0'}
				video={'vid_2.MP4'}
				video_title={'another title'}
				video_tags={['love', 'fashion']}
				video_views={'223'}
				video_likes={'2222'}
			/>
			<VideoResult
				publisher={'Mitch'}
				publisher_following={true}
				publisher_followers={'0'}
				video={'vid_2.MP4'}
				video_title={'another title'}
				video_tags={['love', 'fashion']}
				video_views={'223'}
				video_likes={'2222'}
			/>
			<VideoResult
				publisher={'Mitch'}
				publisher_following={true}
				publisher_followers={'0'}
				video={'vid_2.MP4'}
				video_title={'another title'}
				video_tags={['love', 'fashion']}
				video_views={'223'}
				video_likes={'2222'}
			/>
		</div>
	</div>
</section>
