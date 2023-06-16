<script context="module" lang="ts">
	const videos = new Set();
</script>

<script lang="ts">
	/* --- INIT --- */
	//#region Imports
	// Translation
	import translation from '$translation/i18n-svelte';
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
	import { modalStore } from '@skeletonlabs/skeleton';
	import { toastStore } from '@skeletonlabs/skeleton';

	// CSS
	import { CSS_Styles } from '$script/styles';

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// Backend API
	import Api from '$api/api';
	import { Route } from '$api/api-routes';

	// Components
	import UserFollowers from '$component/UserFollowers.svelte';
	import Avatar from '$component/Avatar.svelte';
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte
	//#endregion

	// Props
	export let publisher_id;
	export let publisher;
	export let publisher_avatar = null;
	export let publisher_followers = [];
	export let video;
	export let video_id;
	export let video_views = 0;
	export let video_likes = [];
	export let video_dislikes = [];

	export let display_variant = 'default';

	export let video_tags = []; // display_variant small
	export let video_title = ''; // display_variant small

	export let video_comments = 0; // display_variant large/default

	export let fetchNextVideo: Function;

	/* --- LOGIC --- */
	//#region Video Variables
	let videoElement;

	let video_path = 'http://localhost:8196/media/video/uploaded/';
	let video_element_id = 'vid_' + video_id;

	let play_button_state = true;
	let sound_button_state = false;
	//#endregion

	//#region Video Functions
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

	function stopOthers() {
		videos.forEach((video) => {
			if (video !== videoElement) video.pause();
		});
	}
	//#endregion

	onMount(() => {
		videos.add(videoElement);
		return () => videos.delete(videoElement);
	});

	async function action(action, attributes, type = 'POST') {
		const res = await Api.post(attributes, action, type);

		if (res?.success) {
			if (action == 'follow') {
				const id = attributes[0].attribute;
				const res = await Api.get(
					Route.REQUEST_METHOD.GET.medium.user.root,
					Route.REQUEST_METHOD.GET.medium.user.id.id(id)
				);

				const user = {
					VS_USER_ID: res?.data[0][0]?.VS_USER_ID,
					USER_USERNAME: res?.data[0][0]?.USER_USERNAME,
					USER_PROFILEPICTURE: res?.data[0][0]?.USER_PROFILEPICTURE ?? null
				};

				$user.subscribed.push(user);
				following = $user.subscribed?.filter((obj) => obj.VS_USER_ID === publisher_id)?.length > 0;

				if (
					!publisher_followers.some(function (element) {
						return element.VS_USER_ID === $user.data.VS_USER_ID;
					})
				) {
					publisher_followers.push($user.data);
				}
			} else if (action == 'feedback') {
				liked = true;
			}
		} else if (res?.success == false) {
			// TODO: popup error
		} else {
			if (action == 'follow') {
				const id = attributes[0].attribute;

				publisher_followers = publisher_followers.filter(
					(obj) => obj.VS_USER_ID !== $user.data.VS_USER_ID
				);
				$user.subscribed = $user.subscribed.filter((obj) => obj.VS_USER_ID !== id);
				following = $user.subscribed?.filter((obj) => obj.VS_USER_ID === publisher_id)?.length > 0;
			} else if (action == 'feedback') {
				liked = false;
			}
		}
	}

	let following = $user.subscribed?.filter((obj) => obj.VS_USER_ID === publisher_id)?.length > 0;
	let liked = $user.user_videos_liked?.filter((obj) => obj.VS_USER_ID === publisher_id)?.length > 0;
	let disliked =
		$user.user_videos_disliked?.filter((obj) => obj.VS_USER_ID === publisher_id)?.length > 0;
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

{#if display_variant == 'default'}
	<div id="video-section" class="flex gap-4">
		<div id="video" class="flex flex-col gap-2">
			<div id="video-publisher" class="flex justify-between items-center w-[360px]">
				<!-- 1080/3 -->
				<div id="video-info" class="flex items-center gap-2 text-primary-700 dark:text-primary-400">
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
						<a
							href="/{$locale}/account/{publisher}"
							class="unstyled hover:underline text-2xl text-primary-700 dark:text-primary-400"
							>{publisher}</a
						>
						{#key following}
							<UserFollowers {publisher_followers} />
						{/key}
					</div>
				</div>
				{#if $loginState}
					<button
						id="video-action"
						type="button"
						class="btn variant-ringed hover:variant-filled h-1/2"
						on:click={() =>
							action('follow', [
								{
									attribute_name: 'FOLLOWING_SUBSCRIBED',
									attribute: publisher_id
								}
							])}
					>
						{#key following}
							{$translation.VideoSection.subscribe(following)}
						{/key}
					</button>
				{:else}
					<button
						id="video-action"
						type="button"
						class="btn variant-ringed hover:variant-filled h-1/2"
						on:click={() => toastStore.trigger(popups.login_required)}
					>
						{#key following}
							{$translation.VideoSection.subscribe(following)}
						{/key}
					</button>
				{/if}
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
					class="absolute w-full flex justify-between p-2 bg-surface-50/70 dark:bg-surface-900/60"
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
					class="absolute bottom-0 w-full p-2 text-xs select-none bg-surface-50/70 dark:bg-surface-900/60"
				>
					{$translation.VideoSection.views(video_views)}
				</div>
			</div>
		</div>
		<div id="actions" class="flex flex-col justify-between gap-2 h-[700px]">
			<div class="flex flex-col justify-center h-full gap-2">
				<div class="flex flex-col gap-1">
					<button
						type="button"
						class="btn-icon variant-ringed text-4xl"
						on:click={() => fetchNextVideo({ keyCode: 38 })}
					>
						<iconify-icon icon="ic:round-arrow-left" />
					</button>
				</div>
				<div class="flex flex-col gap-1">
					<button
						type="button"
						class="btn-icon variant-ringed text-4xl"
						on:click={() => fetchNextVideo({ keyCode: 40 })}
					>
						<iconify-icon icon="ic:round-arrow-right" />
					</button>
				</div>
			</div>

			<div class="flex flex-col gap-2">
				<div class="flex flex-col gap-1">
					{#if $loginState}
						<button
							on:click={() =>
								action(Route.REQUEST_METHOD.POST.medium.feedback.root, [
									{
										attribute_name: 'FEEDBACK_TYPE',
										attribute:
											Route.REQUEST_METHOD.POST.medium.feedback.params.FEEDBACK_TYPE('positive')
									},
									{
										attribute_name: 'VS_VIDEO_ID',
										attribute:
											Route.REQUEST_METHOD.POST.medium.feedback.params.VS_VIDEO_ID(video_id)
									}
								])}
							type="button"
							class="btn-icon variant-ringed text-2xl"
						>
							{#if liked}
								<iconify-icon icon="material-symbols:thumb-up-rounded" />
							{:else}
								<iconify-icon icon="material-symbols:thumb-up-outline-rounded" />
							{/if}
						</button>
					{:else}
						<button
							on:click={() => toastStore.trigger(popups.login_required)}
							type="button"
							class="btn-icon variant-ringed text-2xl"
						>
							<iconify-icon icon="material-symbols:thumb-up-outline-rounded" />
						</button>
					{/if}
					<span class="text-xs text-center text-primary-700 dark:text-primary-500 select-none"
						>{video_likes?.length}</span
					>
				</div>
				<div class="flex flex-col gap-1">
					{#if $loginState}
						<button
							on:click={() =>
								action(Route.REQUEST_METHOD.POST.medium.feedback.root, [
									{
										attribute_name: 'FEEDBACK_TYPE',
										attribute:
											Route.REQUEST_METHOD.POST.medium.feedback.params.FEEDBACK_TYPE('positive')
									},
									{
										attribute_name: 'VS_VIDEO_ID',
										attribute:
											Route.REQUEST_METHOD.POST.medium.feedback.params.VS_VIDEO_ID(video_id)
									}
								])}
							type="button"
							class="btn-icon variant-ringed text-2xl"
						>
							{#if disliked}
								<iconify-icon icon="material-symbols:thumb-down-rounded" />
							{:else}
								<iconify-icon icon="material-symbols:thumb-down-outline-rounded" />
							{/if}
						</button>
					{:else}
						<button
							on:click={() => toastStore.trigger(popups.login_required)}
							type="button"
							class="btn-icon variant-ringed text-2xl"
						>
							<iconify-icon icon="material-symbols:thumb-down-outline-rounded" />
						</button>
					{/if}
					<span class="text-xs text-center text-primary-700 dark:text-primary-500 select-none"
						>{video_dislikes?.length}</span
					>
				</div>
				<div class="flex flex-col gap-1">
					<button type="button" class="btn-icon variant-ringed text-xl">
						<iconify-icon icon="fa:commenting-o" />
					</button>
					<button class="text-xs text-center text-primary-700 dark:text-primary-500 select-none"
						>{video_comments?.length}</button
					>
				</div>
				<div class="flex flex-col gap-1">
					<button
						type="button"
						class="btn-icon variant-ringed text-2xl"
						use:clipboard={`${video_path}${video}`}
						on:click={() => toastStore.trigger(popups.copiedURL_toClipboard_success)}
					>
						<iconify-icon icon="mdi:share" />
					</button>
				</div>
			</div>
		</div>
	</div>
{:else if display_variant == 'small'}
	<div class="video-result flex flex-col items-center gap-2">
		<div class="divide-y">
			{#if publisher == $user?.data?.USER_USERNAME}
				<div class="flex gap-2">
					<button class="text-center truncate w-full">{video_title}</button>
					<div class="flex gap-1">
						<button
							class="badge-icon variant-ghost-primary"
							on:click={modalStore.trigger(popups.editVideo)}
							><iconify-icon icon="ic:round-edit" /></button
						>
						<button
							class="badge-icon variant-ghost-primary"
							on:click={modalStore.trigger(popups.confirmVideoDeletion)}
							><iconify-icon icon="mdi:delete" /></button
						>
					</div>
				</div>
			{:else}
				<div class="flex items-center gap-1 mb-1">
					<a class="unstyled" href="/">
						<Avatar comment_username={publisher} />
					</a>
					<div class="flex flex-col">
						<a
							href="/{$locale}/account/{publisher}"
							class="unstyled hover:underline text-xs truncate text-primary-700 dark:text-primary-400"
							>{publisher}</a
						>
						<div class="text-xs text-primary-700 dark:text-primary-500">
							{#key following}
								{$translation.VideoResult.follower(publisher_followers?.length ?? 0)}
							{/key}
						</div>
					</div>
					{#if $loginState}
						<button
							type="button"
							class="ml-2 btn btn-icon variant-ringed hover:variant-filled scale-75"
							on:click={() =>
								action('follow', [
									{
										attribute_name: 'FOLLOWING_SUBSCRIBED',
										attribute: publisher_id
									}
								])}
						>
							{#if following}
								<iconify-icon class="cursor-pointer" icon="ic:outline-minus" />
							{:else}
								<iconify-icon class="cursor-pointer" icon="ic:outline-plus" />
							{/if}
						</button>
					{:else}
						<button
							type="button"
							class="ml-2 btn btn-icon variant-ringed hover:variant-filled scale-75"
							on:click={() => toastStore.trigger(popups.login_required)}
						>
							{#if following}
								<iconify-icon class="cursor-pointer" icon="ic:outline-minus" />
							{:else}
								<iconify-icon class="cursor-pointer" icon="ic:outline-plus" />
							{/if}
						</button>
					{/if}
				</div>
				<button class="text-center truncate w-full">{video_title}</button>
			{/if}
			<!-- on:click={() => fetchNextVideo({ title: video_title })} -->
		</div>
		<div class="aspect-9-16-small relative border border-gray-500 rounded-md mb-2 overflow-hidden">
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
				class="absolute w-full flex justify-between items-center p-2 bg-surface-50/70 dark:bg-surface-900/60"
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
			<div class="absolute bottom-0 w-full flex justify-between dark:bg-surface-800 bg-surface-100">
				<div class="p-2 text-xs">
					{$translation.VideoResult.views(video_views)}
				</div>
				<div class="p-2 text-xs">
					<span class="text-success-400">{video_likes?.length}</span> /
					<span class="text-error-400">{video_dislikes?.length}</span>
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
