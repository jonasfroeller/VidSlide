<script lang="ts">
	/* --- INIT --- */
	// Backend Api
	import Api from '$api/api';

	// Components
	import InfoSection from '$component/InfoSection.svelte';
	import VideoSection from '$component/VideoSection.svelte';

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// Translation
	import translation from '$translation/i18n-svelte'; // translations
	import { disableCache } from 'iconify-icon';

	// Slots
	export let page;
	export let isResultVideo = false;
	export let searchSubject = null;
	export let sortBy = null;
	export let search = null;

	/* --- LOGIC --- */
	// - medium=video [MEDIUM] // gets videos and video info
	//   - id=all [ID] // unsuffishient
	//     - medium_id=? [ID++] // all videos of user
	//   - id=title [ID]
	//     - medium_id=? [ID++] // all videos with title including text
	//   - id=random [ID]
	//   - id=? [ID]
	async function fetchVideo(id: number | string, id_specification = '') {
		if (id <= 0) {
			id = 'random';
		}
		let response = await Api.get('video', id, id_specification);
		if (response?.data?.length == 0) {
			response = await Api.get('video', 'random');
		}
		return response;
	}

	async function fetchNextVideo(event) {
		/* ArrowUp && ArrowLeft */
		if (event.keyCode === 38 || event.keyCode === 37) {
			current_video_id = current_video_id - 1;
			current_video = await fetchVideo(current_video_id);
			current_video = formatVideo(current_video);
		} else if (event.keyCode === 40 || event.keyCode === 39) {
			current_video_id = current_video_id + 1;
			current_video = await fetchVideo(current_video_id);
			current_video = formatVideo(current_video);
		} /* ArrowDown && ArrowRight */
	}

	async function getUserFollowers(id: number) {
		let userData = await Api.get('user', id);

		if (userData.error == false) {
			let userSubscribers = userData?.data?.subscribers ?? null;
			userSubscribers = JSON.parse(userSubscribers);
			return userSubscribers;
		} else {
			return [];
		}
	}

	function formatVideo(video: JSON) {
		let formattet_object;
		formattet_object = video.data;
		formattet_object['video'] = JSON.parse(formattet_object[0])[0];
		delete formattet_object['0'];
		formattet_object['user'] = JSON.parse(formattet_object['user'])[0];
		if (formattet_object && formattet_object.tags) {
			formattet_object.tags = JSON.parse(formattet_object.tags);
		}
		if (formattet_object && formattet_object.feedback) {
			formattet_object.feedback = JSON.parse(formattet_object.feedback);

			let likes = formattet_object?.feedback?.filter((f) => f.VIDEO_FEEDBACK_TYPE === 'positive');
			let dislikes = formattet_object?.feedback?.filter(
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
		if (formattet_object && formattet_object.comments) {
			if (formattet_object && formattet_object.comments_feedback) {
				formattet_object.comments_feedback = JSON.parse(formattet_object.comments_feedback);
			}

			formattet_object.comments = JSON.parse(formattet_object.comments);

			let comments = [];
			formattet_object.comments.forEach((comment) => {
				let likes = formattet_object?.comments_feedback?.filter(
					(f) => f.COMMENT_ID == comment.COMMENT_ID && f.COMMENT_FEEDBACK_TYPE === 'positive'
				);
				let dislikes = formattet_object?.comments_feedback?.filter(
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

			formattet_object.comments = comments;
		}

		current_video_id = formattet_object?.user?.VS_USER_ID;

		return formattet_object;
	}

	onMount(async () => {
		if (!isResultVideo) {
			current_video = await fetchVideo(0); // 0 => rndm
			current_video = formatVideo(current_video);
			publisher_followers = await getUserFollowers(current_video_id);
		} else {
			fetchMultipleVideos();
		}
	});

	$: publisher_followers = [];
	$: current_video_likes = [];
	$: current_video_dislikes = [];

	$: current_video = null;
	$: current_video_id = 0;

	async function fetchMultipleVideos() {
		let searched_videos = await fetchVideo(searchSubject, search);

		current_video = [];
		let amount = JSON.parse(searched_videos.data[0]).length;
		let videos = JSON.parse(searched_videos.data[0]);

		for (let i = 0; i < amount; i++) {
			let video = JSON.parse(JSON.stringify(searched_videos));
			video.data[0] = [JSON.parse(JSON.stringify(videos[i]))]; // TODO: fix wrong likes, dislikes, followers (api and frontend)

			video.data[0] = JSON.stringify(video.data[0]);
			current_video.push(formatVideo(video));
		}

		// publisher_followers = await getUserFollowers(current_video_id);

		return current_video;
	}
</script>

<svelte:window on:keydown={fetchNextVideo} />

{#key current_video}
	{#if page.includes('home')}
		<InfoSection
			video_title={current_video?.video?.VIDEO_TITLE ?? 'title loading...'}
			video_description={current_video?.video?.VIDEO_DESCRIPTION ?? 'description loading...'}
			video_date_time_posted={current_video?.video?.VIDEO_DATETIMEPOSTED ??
				'date and time loading...'}
			video_tags={current_video?.tags ?? []}
			video_comments={current_video?.comments ?? []}
		/>
	{/if}

	{#if isResultVideo}
		<div id="results" class="flex flex-col gap-2 flex-wrap justify-center">
			<p>
				{$translation.SearchSection.search_option.videos_found(current_video?.length ?? 0)}:
			</p>
			<hr />
			<div id="video-results" class="flex flex-col gap-2 justify-center">
				{#if current_video != null}
					{#each current_video as result, i}
						<VideoSection
							publisher={result?.user?.USER_USERNAME ?? 'username loading...'}
							publisher_avatar={result?.user?.USER_PROFILEPICTURE ?? null}
							publisher_followers={publisher_followers ?? []}
							video={result?.video?.VIDEO_LOCATION ?? null}
							video_id={result?.video?.VS_VIDEO_ID ?? 0}
							video_views={result?.video?.VIDEO_VIEWS ?? 0}
							video_likes={current_video_likes[i]?.length ?? 0}
							video_dislikes={current_video_dislikes[i]?.length ?? 0}
							video_comments={result?.comments?.length ?? 0}
							display_variant={'small'}
						/>
					{:else}
						<p>no results</p>
					{/each}
				{:else}
					<p>no results</p>
				{/if}
			</div>
		</div>
	{:else}
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
	{/if}
{/key}
