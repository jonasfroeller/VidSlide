<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { loginState } from '$store/config';
	import { user, user_following } from '$store/account';

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';
	import { clipboard } from '@skeletonlabs/skeleton';

	// JS-Framework/Library
	import { browser } from '$app/environment';

	// Props
	export let publisher;
	export let publisher_followers;
	export let publisher_following = false;
	export let video;
	export let video_views;
	export let video_likes;
	export let video_dislikes;
	export let video_comments;

	$: following = publisher_following;

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

	/* Form */
	import { toastStore } from '@skeletonlabs/skeleton';
	import type { ToastSettings } from '@skeletonlabs/skeleton';

	/* Notifications */
	const ts: ToastSettings = {
		message: 'Successfully copied URL to clipboard!',
		background: 'variant-ghost-success'
	};
</script>

<div id="video-section" class="flex gap-4">
	<div id="video" class="flex flex-col gap-2">
		<div id="video-publisher" class="flex justify-between items-center w-[360px]">
			<!-- 1080/3 -->
			<div id="video-info" class="flex items-center gap-2">
				<a class="unstyled" href="/">
					<Avatar initials={publisher.charAt(0)} />
				</a>
				<div id="video-publisher-info" class="flex flex-col">
					<div id="username" class="text-lg">{publisher}</div>
					<div id="subscriber" class="text-md text-primary-700 dark:text-primary-500">
						<a class="unstyled" href="/"
							>{$translation.VideoSection.follower(publisher_followers)}</a
						>
					</div>
				</div>
			</div>
			<button id="video-action" type="button" class="btn variant-ringed hover:variant-filled h-1/2">
				{$translation.VideoSection.subscribe(following ? 1 : 0)}
			</button>
		</div>
		<div class="aspect-9-16 relative border border-gray-500 rounded-md">
			<!-- 1920/3 -->
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
			<div
				id="video-player-info-bottom"
				class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500 select-none"
			>
				{$translation.VideoSection.views(video_views)}
			</div>
		</div>
	</div>
	<div id="actions" class="flex justify-end flex-col gap-2 h-[710px]">
		<div class="flex flex-col gap-1">
			<button type="button" class="btn-icon variant-ringed">
				<iconify-icon class="scale-125" icon="material-symbols:thumb-up-outline-rounded" />
				<!-- <iconify-icon icon="material-symbols:thumb-up-rounded" /> -->
			</button>
			<span class="text-xs text-center text-primary-700 dark:text-primary-500 select-none"
				>{video_likes}</span
			>
		</div>
		<div class="flex flex-col gap-1">
			<button type="button" class="btn-icon variant-ringed">
				<iconify-icon class="scale-125" icon="material-symbols:thumb-down-outline-rounded" />
				<!-- <iconify-icon icon="material-symbols:thumb-up-rounded" /> -->
			</button>
			<span class="text-xs text-center text-primary-700 dark:text-primary-500 select-none"
				>{video_dislikes}</span
			>
		</div>
		<div class="flex flex-col gap-1">
			<button type="button" class="btn-icon variant-ringed">
				<iconify-icon class="scale-125" icon="fa:commenting-o" />
			</button>
			<span class="text-xs text-center text-primary-700 dark:text-primary-500 select-none"
				>{video_comments}</span
			>
		</div>
		<div class="flex flex-col gap-1">
			<button
				type="button"
				class="btn-icon variant-ringed"
				use:clipboard={`http://localhost:8196/media/video/${video}`}
				on:click={() => toastStore.trigger(ts)}
			>
				<iconify-icon class="scale-150" icon="mdi:share" />
			</button>
		</div>
	</div>
</div>

<style>
	.video::-webkit-media-controls,
	.video::-webkit-media-controls-enclosure,
	.video::-webkit-media-controls-panel {
		display: none !important;
	}

	.aspect-9-16 {
		height: 640px;
		aspect-ratio: 9/16;
	}
</style>
