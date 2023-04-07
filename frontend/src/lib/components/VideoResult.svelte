<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { loginState } from '$store/config';
	import { user, user_following } from '$store/account';

	// Components
	import { Avatar } from '@skeletonlabs/skeleton';

	// JS-Framework/Library
	import { browser } from '$app/environment';

	// Props
	export let publisher;
	export let publisher_followers;
	export let publisher_following = false;
	export let video;
	export let video_title;
	export let video_tags = [];
	export let video_views;
	export let video_likes;

	/* --- LOGIC --- */
	let video_name = video.split('_');
	let video_id = 'video_' + video_name[video_name.length - 1].replace(/.mp4/i, '');

	$: play_button_state = false;
	$: sound_button_state = false;

	function playVideo() {
		if (browser) {
			const video = document.getElementById(video_id);
			video.play();
		}
	}

	function pauseVideo() {
		if (browser) {
			const video = document.getElementById(video_id);
			video.pause();
		}
	}

	function muteVideo() {
		if (browser) {
			const video = document.getElementById(video_id);
			video.muted = !video.muted;
		}
	}
</script>

{#if publisher == $user?.username}
	<div class="video-result flex flex-col items-center relative">
		<p class="text-center">{video_title}</p>
		<div class="absolute right-0 flex gap-2">
			<iconify-icon icon="ic:round-edit" />
			<iconify-icon icon="mdi:delete" />
		</div>
		<div class="aspect-9-16 relative border border-gray-500 rounded-md mb-2">
			<!-- 1920/6 -->
			<div class="absolute w-full flex justify-between p-2">
				{#if play_button_state}
					<button
						on:click={() => {
							play_button_state = !play_button_state;
							pauseVideo();
						}}
					>
						<iconify-icon icon="material-symbols:pause-rounded" />
					</button>
				{:else}
					<button
						on:click={() => {
							play_button_state = !play_button_state;
							playVideo();
						}}
					>
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
					</button>
				{/if}

				{#if sound_button_state}
					<button
						on:click={() => {
							sound_button_state = !sound_button_state;
							muteVideo();
						}}
					>
						<iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" />
					</button>
				{:else}
					<button
						on:click={() => {
							sound_button_state = !sound_button_state;
							muteVideo();
						}}
					>
						<iconify-icon icon="heroicons-solid:volume-off" />
					</button>
				{/if}
			</div>
			<div class="absolute bottom-0 w-full flex justify-between dark:bg-surface-800 bg-surface-100">
				<div class="p-2 text-xs">
					{$translation.VideoResult.views(video_views)}
				</div>
				<div class="p-2 text-xs">
					{$translation.VideoResult.likes(video_likes)}
				</div>
			</div>
		</div>
		<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
			{#each video_tags as tag}
				<span class="chip variant-ringed truncate">#{tag}</span>
			{:else}
				<span class="truncate text-xs text-primary-700 dark:text-primary-500"
					>{$translation.VideoResult.no_tags()}</span
				>
			{/each}
		</div>
	</div>
{:else}
	<div class="video-result flex flex-col items-center">
		<div class="divide-y">
			<div class="flex items-center">
				<a class="unstyled" href="/">
					<Avatar class="scale-75" initials={publisher.charAt(0)} />
				</a>
				<div class="flex flex-col">
					<div class="text-xs truncate">{publisher}</div>
					<div class="text-xs text-primary-700 dark:text-primary-500">
						<a class="unstyled truncate" href="/"
							>{$translation.VideoResult.follower(publisher_followers)}</a
						>
					</div>
				</div>
				<button
					type="button"
					class="ml-2 btn btn-icon variant-ringed hover:variant-filled scale-75"
				>
					{#if publisher_following}
						<iconify-icon class="cursor-pointer" icon="ic:outline-minus" />
					{:else}
						<iconify-icon class="cursor-pointer" icon="ic:outline-plus" />
					{/if}
				</button>
			</div>
			<p class="text-center truncate">{video_title}</p>
		</div>
		<div class="aspect-9-16 relative border border-gray-500 rounded-md mb-2 overflow-hidden">
			<!-- 1920/6 -->
			<!-- svelte-ignore a11y-media-has-caption -->
			<video
				id={video_id}
				class="video absolute inset-0 w-full h-full"
				title={video}
				aria-label={video}
				controls
				muted
			>
				<source src="http://localhost:8196/media/video/{video}" type="video/mp4" />
				Your browser does not support the video tag.
			</video>
			<div class="absolute w-full flex justify-between p-2">
				{#if play_button_state}
					<button
						on:click={() => {
							play_button_state = !play_button_state;
							pauseVideo();
						}}
					>
						<iconify-icon icon="material-symbols:pause-rounded" />
					</button>
				{:else}
					<button
						on:click={() => {
							play_button_state = !play_button_state;
							playVideo();
						}}
					>
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
					</button>
				{/if}

				{#if sound_button_state}
					<button
						on:click={() => {
							sound_button_state = !sound_button_state;
							muteVideo();
						}}
					>
						<iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" />
					</button>
				{:else}
					<button
						on:click={() => {
							sound_button_state = !sound_button_state;
							muteVideo();
						}}
					>
						<iconify-icon icon="heroicons-solid:volume-off" />
					</button>
				{/if}
			</div>
			<div class="absolute bottom-0 w-full flex justify-between dark:bg-surface-800 bg-surface-100">
				<div class="p-2 text-xs">
					{$translation.VideoResult.views(video_views)}
				</div>
				<div class="p-2 text-xs">
					{$translation.VideoResult.likes(video_likes)}
				</div>
			</div>
		</div>
		<div class="video-info-tags flex flex-wrap gap-1 max-w-[180px]">
			{#each video_tags as tag}
				<span class="chip variant-ringed truncate">#{tag}</span>
			{:else}
				<span class="truncate text-xs text-primary-700 dark:text-primary-500"
					>{$translation.VideoResult.no_tags()}</span
				>
			{/each}
		</div>
	</div>
{/if}

<style>
	.video::-webkit-media-controls,
	.video::-webkit-media-controls-enclosure,
	.video::-webkit-media-controls-panel {
		display: none !important;
	}

	.aspect-9-16 {
		min-height: 320px;
		aspect-ratio: 9/16;
	}
</style>
