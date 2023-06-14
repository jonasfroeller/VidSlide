<script lang="ts">
	/* --- INIT --- */
	// Backend Api
	import Api from '$api/api';

	// Components
	import InfoSection from '$component/InfoSection.svelte';
	import VideoSection from '$component/VideoSection.svelte';
	import PostTransition from '$component/PostTransition.svelte';
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte

	// Scripts
	import { searchedVideos, filter, filteredVideos } from '$store/searchedVideos';

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// CSS-Framework/Library
	import { ProgressRadial } from '@skeletonlabs/skeleton';
	import { toastStore } from '@skeletonlabs/skeleton';

	// Translation
	import translation from '$translation/i18n-svelte';
	import { disableCache } from 'iconify-icon';

	// Slots
	export let page;
	export let isResultVideo = false;
	export let searchSubject = 'username';
	export let sortBy = 'date';
	export let searchedText = '';

	/* --- LOGIC --- */
	async function fetchVideo(id: number | string, id_specification = '') {
		if (typeof id === 'number' && id <= 0) {
			failedToFetch = true;
			id = 'random';
		}

		let response = await Api.get('video', id, id_specification);

		if (typeof id === 'number' && response?.data?.length === 0) {
			failedToFetch = true;
			response = await Api.get('video', 'random');
		}
		return response;
	}

	async function fetchMultipleVideos() {
		let fetched_videos = await fetchVideo(searchSubject, searchedText); // sortBy
		let formatted_videos = [];

		if (fetched_videos?.data[0] || fetched_videos?.data?.stats?.videos) {
			let amount = fetched_videos.data[0].length;
			let videos = fetched_videos.data[0];
			let user = fetched_videos.data.user;
			let feedback = fetched_videos.data.feedback;
			let tags = fetched_videos.data.tags;

			for (let i = 0; i < amount; i++) {
				const videoId = videos[i].VS_VIDEO_ID;

				const video = {
					data: {
						0: [videos[i]],
						user: user[i]
					}
				};

				let feedback_temp = [];
				for (let j = 0; j < feedback.length; j++) {
					for (let k = 0; k < feedback[j].length; k++) {
						if (feedback[j][k].VS_VIDEO_ID == videoId) {
							feedback_temp.push(feedback[j][k]);
						}
					}
				}

				video.data.feedback = feedback_temp;

				let tags_temp = [];
				for (let j = 0; j < tags.length; j++) {
					for (let k = 0; k < tags[j].length; k++) {
						if (tags[j][k].VS_VIDEO_ID == videoId) {
							tags_temp.push(tags[j][k]);
						}
					}
				}

				video.data.tags = tags_temp;

				formatted_videos.push(await formatVideo(video));
			}

			$searchedVideos = formatted_videos;
		}
	}

	let fetchNextVideo = async function (event) {
		if (event?.title) {
			// TODO: use api.routes.js and implement getting video by title
		} else {
			// ArrowUp && ArrowLeft
			if (event.keyCode === 38 || event.keyCode === 37) {
				current_video_id -= 1;
				let video = await fetchVideo(current_video_id);
				current_video = await formatVideo(video);
				current_video_publisher_followers = await getUserFollowers(current_video_id);
			} else if (event.keyCode === 40 || event.keyCode === 39) {
				current_video_id += 1;
				let video = await fetchVideo(current_video_id);
				current_video = await formatVideo(video);
				current_video_publisher_followers = await getUserFollowers(current_video_id);
			} // ArrowDown && ArrowRight
		}

		if (failedToFetch) {
			failedToFetch = false;
			current_video_id = 0;
		}
	};

	async function getUserFollowers(id: number) {
		let userData = await Api.get('user', id);

		if (userData) {
			let userSubscribers = userData?.data?.subscribers ?? null;
			return userSubscribers;
		} else {
			return [];
		}
	}

	async function formatVideo(video: JSON) {
		let formatted_object;
		formatted_object = video.data;

		try {
			// error thrown but the object is alright
			formatted_object['video'] = formatted_object[0][0];
			formatted_object['user'] = formatted_object['user'][0];
		} catch {}

		delete formatted_object['0'];

		if (formatted_object && formatted_object.feedback && formatted_object.feedback !== null) {
			let likes = formatted_object?.feedback?.filter((f) => f.VIDEO_FEEDBACK_TYPE === 'positive');
			let dislikes = formatted_object?.feedback?.filter(
				(f) => f.VIDEO_FEEDBACK_TYPE === 'negative'
			);

			if (!isResultVideo) {
				current_video_likes = likes;
				current_video_dislikes = dislikes;
			} else {
				current_video_likes.push(likes);
				current_video_dislikes.push(dislikes);
			}
		}
		if (
			formatted_object &&
			formatted_object.comments &&
			formatted_object.comments !== null &&
			!isResultVideo
		) {
			let comments = [];
			formatted_object.comments.forEach((comment) => {
				let likes = formatted_object?.comments_feedback?.filter(
					(f) => f.COMMENT_ID == comment.COMMENT_ID && f.COMMENT_FEEDBACK_TYPE === 'positive'
				);
				let dislikes = formatted_object?.comments_feedback?.filter(
					(f) => f.COMMENT_ID == comment.COMMENT_ID && f.COMMENT_FEEDBACK_TYPE === 'negative'
				);

				comments.push({
					USER_USERNAME: comment.USER_USERNAME,
					USER_PROFILEPICTURE: comment.USER_PROFILEPICTURE,
					COMMENT_ID: comment.COMMENT_ID,
					COMMENT_MESSAGE: comment.COMMENT_MESSAGE,
					COMMENT_LIKES: likes,
					COMMENT_DISLIKES: dislikes,
					COMMENT_DATETIMEPOSTED: comment.COMMENT_DATETIMEPOSTED,
					COMMENT_PARENT_ID: comment.COMMENT_PARENT_ID
				});
			});

			formatted_object.comments = comments;
		}

		return formatted_object;
	}

	onMount(async () => {
		if (!isResultVideo) {
			let video = await fetchVideo(0); // 0 => rndm

			if (video) {
				current_video = await formatVideo(video);
				current_video_publisher_followers = await getUserFollowers(
					current_video?.video?.VS_VIDEO_ID
				);
			} else {
				toastStore.trigger(popups.failed_to_fetch_video);
			}
		} else {
			await fetchMultipleVideos();
		}
	});

	// CURRENT VIDEO
	$: current_video = null;
	$: current_video_id = null;
	$: current_video_publisher_followers = [];
	$: current_video_likes = [];
	$: current_video_dislikes = [];

	let failedToFetch = false;

	// SEARCHED VIDEOS
	$filter = sortBy;
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<svelte:window on:keydown={fetchNextVideo} />

{#if page.includes('home') && current_video}
	<PostTransition key={current_video?.video?.VS_VIDEO_ID}>
		<InfoSection
			video_title={current_video?.video?.VIDEO_TITLE ?? 'title loading...'}
			video_description={current_video?.video?.VIDEO_DESCRIPTION ?? 'description loading...'}
			video_date_time_posted={current_video?.video?.VIDEO_DATETIMEPOSTED ??
				'date and time loading...'}
			video_tags={current_video?.tags ?? []}
			video_comments={current_video?.comments ?? []}
		/>
	</PostTransition>
{/if}

{#if isResultVideo}
	<div id="results" class="flex flex-col gap-2 flex-wrap justify-center">
		<p>
			{$translation.SearchSection.search_option.videos_found($filteredVideos?.length ?? 0)}:
		</p>
		<hr />
		<div id="video-results" class="flex flex-wrap gap-2 justify-center overflow-y-auto">
			{#if searchedText != '' && $filteredVideos}
				{#each $filteredVideos as result, i}
					<VideoSection
						publisher_id={result?.user?.VS_USER_ID ?? -1}
						publisher={result?.user?.USER_USERNAME ?? 'username loading...'}
						publisher_avatar={result?.user?.USER_PROFILEPICTURE ?? null}
						publisher_followers={result?.user?.subscribers ? result?.user?.subscribers : []}
						video={result?.video?.VIDEO_LOCATION ?? null}
						video_id={result?.video?.VS_VIDEO_ID ?? 0}
						video_views={result?.video?.VIDEO_VIEWS ?? 0}
						video_likes={result?.feedback?.filter(
							(feedback) => feedback?.VIDEO_FEEDBACK_TYPE === 'positive'
						)?.length ?? 0}
						video_dislikes={result?.feedback?.filter(
							(feedback) => feedback?.VIDEO_FEEDBACK_TYPE === 'negative'
						)?.length ?? 0}
						video_tags={result?.tags ?? []}
						video_title={result?.video?.VIDEO_TITLE ?? 'title loading...'}
						display_variant={'small'}
					/>
				{:else}
					<div class="flex justify-center">
						<ProgressRadial
							stroke={50}
							width="w-12"
							meter="stroke-primary-500"
							track="stroke-primary-500/30"
						/>
					</div>
				{/each}
			{:else}
				<p>{$translation.VideoResult.no_input()}</p>
			{/if}
		</div>
	</div>
{:else if current_video}
	<PostTransition key={current_video?.video?.VS_VIDEO_ID}>
		<VideoSection
			publisher_id={current_video?.user?.VS_USER_ID ?? -1}
			publisher={current_video?.user?.USER_USERNAME ?? 'username loading...'}
			publisher_avatar={current_video?.user?.USER_PROFILEPICTURE ?? null}
			publisher_followers={current_video_publisher_followers ?? []}
			video={current_video?.video?.VIDEO_LOCATION ?? null}
			video_id={current_video?.video?.VS_VIDEO_ID ?? 0}
			video_views={current_video?.video?.VIDEO_VIEWS ?? 0}
			video_likes={current_video_likes?.length ?? 0}
			video_dislikes={current_video_dislikes?.length ?? 0}
			video_comments={current_video?.comments?.length ?? 0}
			display_variant={'default'}
			bind:fetchNextVideo
		/>
	</PostTransition>
{:else}
	<div class="flex justify-center">
		<ProgressRadial
			stroke={50}
			width="w-12"
			meter="stroke-primary-500"
			track="stroke-primary-500/30"
		/>
	</div>
{/if}
