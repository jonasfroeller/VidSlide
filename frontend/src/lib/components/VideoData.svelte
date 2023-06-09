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

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// CSS-Framework/Library
	import { ProgressRadial } from '@skeletonlabs/skeleton';
	import { toastStore } from '@skeletonlabs/skeleton';

	// Translation
	import translation from '$translation/i18n-svelte'; // translations
	import { disableCache } from 'iconify-icon';

	// Slots
	export let page;
	export let isResultVideo = false;
	export let searchSubject = 'username';
	export let sortBy = 'date';
	export let searchedText = '';

	/* --- LOGIC --- */
	// - medium=video [MEDIUM] // gets videos and video info
	//   - id=all [ID] // insufficient
	//     - medium_id=? [ID++] // all videos of user
	//   - id=title [ID]
	//     - medium_id=? [ID++] // all videos with title including text
	//   - id=tag [ID]
	//     - medium_id=? [ID++] // all videos with tag including text
	//   - id=username [ID]
	//     - medium_id=? [ID++] // all videos with username of creator including text
	//   - id=random [ID]
	//   - id=? [ID]
	async function fetchVideo(id: number | string, id_specification = '') {
		if (typeof id === 'number' && id <= 0) {
			id = 'random';
		}

		let response = await Api.get('video', id, id_specification);

		if (typeof id === 'number' && response?.data?.length === 0) {
			response = await Api.get('video', 'random');
		}
		return response;
	}

	async function fetchMultipleVideos() {
		let searched_videos = await fetchVideo(searchSubject, searchedText); // sortBy
		result_videos = [];

		if (searched_videos?.data[0] || searched_videos?.data?.stats?.videos) {
			let amount = 0;
			let videos = [];
			let user = [];
			let feedback = [];
			let tags = [];

			amount = JSON.parse(searched_videos.data[0]).length;
			videos = JSON.parse(searched_videos.data[0]);

			for (let i = 0; i < searched_videos.data.user.length; i++) {
				user.push(searched_videos.data.user[i]);
			}

			if (searched_videos.data.feedback) {
				for (let i = 0; i < searched_videos.data.feedback.length; i++) {
					feedback.push(JSON.parse(searched_videos.data.feedback[i]));
				}
			}

			if (searched_videos.data.tags) {
				for (let i = 0; i < searched_videos.data.tags.length; i++) {
					tags.push(JSON.parse(searched_videos.data.tags[i]));
				}
			}

			for (let i = 0; i < amount; i++) {
				const videoId = videos[i].VS_VIDEO_ID;

				const video = {
					data: {
						0: JSON.stringify([videos[i]]),
						user: user[i]
					}
				};

				for (let j = 0; j < feedback.length; j++) {
					for (let k = 0; k < feedback[j].length; k++) {
						if (feedback[j][k].VS_VIDEO_ID == videoId) {
							video.data.feedback = JSON.stringify([feedback[j][k]]);
						}
					}
				}

				for (let j = 0; j < tags.length; j++) {
					for (let k = 0; k < tags[j].length; k++) {
						if (tags[j][k].VS_VIDEO_ID == videoId) {
							video.data.tags = JSON.stringify([tags[j][k]]);
						}
					}
				}

				result_videos.push(await formatVideo(video));
			}

			publisher_followers = [];
		}
	}

	async function fetchNextVideo(event) {
		// ArrowUp && ArrowLeft
		if (event.keyCode === 38 || event.keyCode === 37) {
			let video = await fetchVideo(current_video_id);
			current_video = await formatVideo(video);
		} else if (event.keyCode === 40 || event.keyCode === 39) {
			let video = await fetchVideo(current_video_id);
			current_video = await formatVideo(video);
		} // ArrowDown && ArrowRight
	}

	async function getUserFollowers(id: number) {
		let userData = await Api.get('user', id);

		if (userData) {
			let userSubscribers = userData?.data?.subscribers ?? null;
			userSubscribers = JSON.parse(userSubscribers);
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
			formatted_object['video'] = JSON.parse(formatted_object[0])[0];
			formatted_object['user'] = JSON.parse(formatted_object['user'])[0];
		} catch {}

		delete formatted_object['0'];

		if (formatted_object && formatted_object.tags && formatted_object.tags !== null) {
			formatted_object.tags = JSON.parse(formatted_object.tags);
		}
		if (formatted_object && formatted_object.feedback && formatted_object.feedback !== null) {
			formatted_object.feedback = JSON.parse(formatted_object.feedback);

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
			if (
				formatted_object &&
				formatted_object.comments_feedback &&
				formatted_object.comments_feedback !== null
			) {
				formatted_object.comments_feedback = JSON.parse(formatted_object.comments_feedback);
			}

			formatted_object.comments = JSON.parse(formatted_object.comments);

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
				publisher_followers = await getUserFollowers(current_video_id);
			} else {
				toastStore.trigger(popups.failed_to_fetch_video);
			}
		} else {
			await fetchMultipleVideos();
		}
	});

	$: publisher_followers = [];
	$: current_video_likes = [];
	$: current_video_dislikes = [];

	$: current_video = null;
	$: current_video_id = current_video?.user?.VS_USER_ID;

	$: result_videos = [];
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<svelte:window on:keydown={fetchNextVideo} />

{#if current_video != null || result_videos != null}
	{#key current_video_id}
		{#if page.includes('home')}
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
					{$translation.SearchSection.search_option.videos_found(result_videos?.length ?? 0)}:
				</p>
				<hr />
				<div id="video-results" class="flex flex-wrap gap-2 justify-center overflow-y-auto">
					{#if searchedText != ''}
						{#each result_videos as result, i}
							<VideoSection
								publisher={result?.user?.USER_USERNAME ?? 'username loading...'}
								publisher_avatar={result?.user?.USER_PROFILEPICTURE ?? null}
								publisher_followers={publisher_followers ?? []}
								video={result?.video?.VIDEO_LOCATION ?? null}
								video_id={result?.video?.VS_VIDEO_ID ?? 0}
								video_views={result?.video?.VIDEO_VIEWS ?? 0}
								video_likes={current_video_likes[i]?.length ?? 0}
								video_dislikes={current_video_dislikes[i]?.length ?? 0}
								display_variant={'small'}
							/>
						{:else}
							<p>no results</p>
						{/each}
					{:else}
						<p>no input</p>
					{/if}
				</div>
			</div>
		{:else}
			<PostTransition key={current_video?.video?.VS_VIDEO_ID}>
				<VideoSection
					publisher={current_video?.user?.USER_USERNAME ?? 'username loading...'}
					publisher_avatar={current_video?.user?.USER_PROFILEPICTURE ?? null}
					publisher_followers={publisher_followers ?? []}
					video={current_video?.video?.VIDEO_LOCATION ?? null}
					video_id={current_video?.video?.VS_VIDEO_ID ?? 0}
					video_views={current_video?.video?.VIDEO_VIEWS ?? 0}
					video_likes={current_video_likes?.length ?? 0}
					video_dislikes={current_video_dislikes?.length ?? 0}
					video_comments={current_video?.comments?.length ?? 0}
					display_variant={'default'}
				/>
			</PostTransition>
		{/if}
	{/key}
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
