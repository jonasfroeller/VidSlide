<script context="module" lang="ts">
	const videos = new Set();
</script>

<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations
	import { locale } from '$translation/i18n-svelte';

	// Stores
	import {
		loginState,
		user,
		user_subscribed,
		user_videos_liked,
		user_videos_disliked
	} from '$store/account';

	// CSS-Framework/Library
	import { clipboard } from '@skeletonlabs/skeleton';
	import { toastStore } from '@skeletonlabs/skeleton';
	import { popup } from '@skeletonlabs/skeleton';
	import type { PopupSettings } from '@skeletonlabs/skeleton';

	// CSS
	import { CSS_Styles } from '$script/styles';

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// Components
	import Avatar from '$component/Avatar.svelte';
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte

	// Props
	export let publisher;
	export let publisher_avatar = null;
	export let publisher_followers = [];
	export let video;
	export let video_id;
	export let video_views = 0;
	export let video_likes = 0;
	export let video_dislikes = 0;

	export let display_variant = 'default';

	export let video_tags = []; // display_variant small
	export let video_title = ''; // display_variant small

	export let video_comments = 0; // display_variant large/default

	$: publisher_following = $user_subscribed?.includes(publisher);

	/* --- LOGIC --- */
	let video_path = 'http://localhost:8196/media/video/uploaded/';
	$: video_element_id = 'vid_' + video_id;

	$: play_button_state = true;
	$: sound_button_state = false;

	function playVideo() {
		if (browser) {
			videoElement.play();
		}
	}

	function pauseVideo() {
		if (browser) {
			videoElement.pause();
		}
	}

	function muteVideo() {
		if (browser) {
			videoElement.muted = !videoElement.muted;
		}
	}

	onMount(() => {
		videos.add(videoElement);
		return () => videos.delete(videoElement);
	});

	function stopOthers() {
		videos.forEach((video) => {
			if (video !== videoElement) video.pause();
		});
	}

	let videoElement;

	let view_followers: PopupSettings = {
		event: 'click',
		target: 'view_followers'
	};
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

{#if display_variant == 'default'}
	<div id="video-section" class="flex gap-4">
		<div id="video" class="flex flex-col gap-2">
			<div id="video-publisher" class="flex justify-between items-center w-[360px]">
				<!-- 1080/3 -->
				<div id="video-info" class="flex items-center gap-2">
					<a href="/{$locale}/account/{publisher}" class="transition">
						{#if publisher_avatar != null}
							<Avatar
								size="medium"
								comment_avatar={publisher_avatar}
								comment_username={publisher}
							/>
						{:else}
							<Avatar size="medium" comment_username={publisher} />
						{/if}
					</a>
					<div id="video-publisher-info" class="flex flex-col">
						<a href="/{$locale}/account/{publisher}" class="unstyled hover:underline text-lg"
							>{publisher} | {video_id}</a
						>
						<div id="subscriber" class="text-md text-primary-700 dark:text-primary-500">
							<button use:popup={view_followers}
								>{$translation.VideoSection.follower(publisher_followers?.length ?? 0)}</button
							>
							<div
								data-popup="view_followers"
								class="z-[999] rounded-md bg-surface-500 p-2 overflow-auto max-h-96"
							>
								{#each publisher_followers as follower, i}
									<div class="flex gap-2 items-center h-full divide-x">
										<p>{i + 1}</p>
										<div class="flex items-center p-2 gap-2">
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
												class="unstyled hover:underline text-lg"
											>
												{follower?.USER_USERNAME}
											</a>
										</div>
									</div>
								{:else}
									{$translation.VideoSection.follower(publisher_followers?.length ?? 0)}
								{/each}
							</div>
						</div>
					</div>
				</div>
				<button
					id="video-action"
					type="button"
					class="btn variant-ringed hover:variant-filled h-1/2"
				>
					{$translation.VideoSection.subscribe(publisher_following ? 0 : 1)}
				</button>
			</div>
			<div class="aspect-9-16 relative border border-gray-500 rounded-md">
				<!-- 1920/3 -->
				<!-- svelte-ignore a11y-media-has-caption -->
				<video
					id={video_element_id}
					class="video absolute inset-0 w-full h-full"
					title={video}
					aria-label={video}
					bind:this={videoElement}
					bind:paused={play_button_state}
					on:play={stopOthers}
					on:click={() => {
						play_button_state = !play_button_state;
						if (play_button_state) {
							playVideo();
						} else {
							pauseVideo();
						}
					}}
					controls
					muted
				>
					<source src="{video_path}{video}" type="video/mp4" />
					Your browser does not support the video tag.
				</video>
				<div
					class="absolute w-full flex justify-between p-2 bg-primary-50/70 dark:bg-primary-900/60"
				>
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
					class="absolute bottom-0 w-full p-2 text-xs text-primary-900 dark:text-primary-500 select-none bg-primary-50/70 dark:bg-primary-900/60"
				>
					{$translation.VideoSection.views(video_views)}
				</div>
			</div>
		</div>
		<div id="actions" class="flex justify-end flex-col gap-2 h-[710px]">
			<div class="flex flex-col gap-1">
				<button type="button" class="btn-icon variant-ringed">
					{#if $user_videos_liked?.includes(video_id)}
						<iconify-icon icon="material-symbols:thumb-up-rounded" />
					{:else}
						<iconify-icon class="scale-125" icon="material-symbols:thumb-up-outline-rounded" />
					{/if}
				</button>
				<span class="text-xs text-center text-primary-700 dark:text-primary-500 select-none"
					>{video_likes}</span
				>
			</div>
			<div class="flex flex-col gap-1">
				<button type="button" class="btn-icon variant-ringed">
					{#if $user_videos_disliked?.includes(video_id)}
						<iconify-icon icon="material-symbols:thumb-up-rounded" />
					{:else}
						<iconify-icon class="scale-125" icon="material-symbols:thumb-down-outline-rounded" />
					{/if}
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
					use:clipboard={`${video_path}${video}`}
					on:click={() => toastStore.trigger(popups.copiedURL_toClipboard_success)}
				>
					<iconify-icon class="scale-150" icon="mdi:share" />
				</button>
			</div>
		</div>
	</div>
{:else if display_variant == 'small'}
	{#if publisher == user?.data?.USER_USERNAME}
		<div class="video-result flex flex-col items-center gap-2 relative">
			<p class="text-center">{video_title}</p>
			<div class="absolute right-0 flex gap-2">
				<iconify-icon icon="ic:round-edit" />
				<iconify-icon icon="mdi:delete" />
			</div>
			<div
				class="aspect-9-16-small relative border border-gray-500 rounded-md mb-2 bg-primary-50/70 dark:bg-primary-900/60"
			>
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
				<div
					class="absolute bottom-0 w-full flex justify-between dark:bg-surface-800 bg-surface-100"
				>
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
		<div class="video-result flex flex-col items-center gap-2">
			<div class="divide-y">
				<div class="flex items-center gap-1 mb-1">
					<a class="unstyled" href="/">
						<Avatar comment_username={publisher} />
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
			<div
				class="aspect-9-16-small relative border border-gray-500 rounded-md mb-2 overflow-hidden"
			>
				<!-- 1920/6 -->
				<!-- svelte-ignore a11y-media-has-caption -->
				<video
					id={video_element_id}
					class="video absolute inset-0 w-full h-full"
					title={video}
					aria-label={video}
					bind:this={videoElement}
					bind:paused={play_button_state}
					on:play={stopOthers}
					on:click={() => {
						play_button_state = !play_button_state;
						if (play_button_state) {
							playVideo();
						} else {
							pauseVideo();
						}
					}}
					controls
					muted
				>
					<source src="{video_path}{video}" type="video/mp4" />
					Your browser does not support the video tag.
				</video>
				<div
					class="absolute w-full flex justify-between items-center p-2 bg-primary-50/70 dark:bg-primary-900/60"
				>
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
					class="absolute bottom-0 w-full flex justify-between dark:bg-surface-800 bg-surface-100"
				>
					<div class="p-2 text-xs">
						{$translation.VideoResult.views(video_views)}
					</div>
					<div class="p-2 text-xs">
						<span class="text-success-400">{video_likes}</span> /
						<span class="text-error-400">{video_dislikes}</span>
					</div>
				</div>
			</div>
			<div class="video-info-tags flex flex-wrap gap-1 max-w-[180px]">
				{#each video_tags as tag}
					<span class="chip variant-ringed truncate">#{tag?.HASHTAG_NAME}</span>
				{:else}
					<span class="truncate text-xs text-primary-700 dark:text-primary-500"
						>{$translation.VideoResult.no_tags()}</span
					>
				{/each}
			</div>
		</div>
	{/if}
{/if}

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

	.aspect-9-16-small {
		height: 320px;
		aspect-ratio: 9/16;
	}
</style>
