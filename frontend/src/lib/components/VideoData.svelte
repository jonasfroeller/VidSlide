<script lang="ts">
	/* --- INIT --- */
	// Backend Api
	import Api from '$api/api';

	// Components
	import InfoSection from '$component/InfoSection.svelte';
	import VideoSection from '$component/VideoSection.svelte';

	// JS-Framework/Library
	import { onMount } from 'svelte';

	// Slots
	export let page;
	export let isResultVideo = false;
	export let searchSubject = '';
	export let sortBy = '';
	export let search = '';

	/* --- LOGIC --- */
	// - medium=video [MEDIUM] // gets videos and video info
	//   - id=all [ID] // unsuffishient
	//     - medium_id=? [ID++] // all videos of user
	//   - id=title [ID]
	//     - medium_id=? [ID++] // all videos with title including text
	//   - id=random [ID]
	//   - id=? [ID]
	async function fetchVideo(id: number, id_specification = '') {
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
			return 0;
		}
	}

	function formatVideo(video: JSON) {
		let formattet_object;
		formattet_object = current_video.data;
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

			current_video_likes = likes;
			current_video_dislikes = dislikes;
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

		current_video_id = formattet_object?.user?.USER_ID;

		return formattet_object;
	}

	onMount(async () => {
		current_video = await fetchVideo(0); // 0 => rndm
		current_video = formatVideo(current_video);
		publisher_followers = await getUserFollowers(current_video_id);
	});

	$: publisher_followers = null;
	$: current_video_likes = null;
	$: current_video_dislikes = null;

	$: current_video = null;
	$: current_video_id = 0;
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
		<div id="search-result" class="flex flex-col gap-2 divide-y">
			<p>one Video found:</p>
			<div id="video-results" class="flex gap-2 justify-center">
				<VideoSection
					publisher={current_video?.user?.USER_USERNAME ?? 'username loading...'}
					publisher_avatar={current_video?.user?.USER_PROFILEPICTURE ?? null}
					publisher_followers={publisher_followers?.length ?? 0}
					video={current_video?.video?.VIDEO_LOCATION ?? null}
					video_id={current_video?.video?.VIDEO_ID ?? null}
					video_views={current_video?.video?.VIDEO_VIEWS ?? 0}
					video_likes={current_video_likes?.length ?? 0}
					video_dislikes={current_video_dislikes?.length ?? 0}
					video_tags={current_video?.tags ?? []}
					video_title={current_video?.video?.VIDEO_TITLE ?? 'title loading...'}
					display_variant={'small'}
				/>
				<VideoSection
					publisher={current_video?.user?.USER_USERNAME ?? 'username loading...'}
					publisher_avatar={current_video?.user?.USER_PROFILEPICTURE ?? null}
					publisher_followers={publisher_followers?.length ?? 0}
					video={current_video?.video?.VIDEO_LOCATION ?? null}
					video_id={current_video?.video?.VIDEO_ID ?? null}
					video_views={current_video?.video?.VIDEO_VIEWS ?? 0}
					video_likes={current_video_likes?.length ?? 0}
					video_dislikes={current_video_dislikes?.length ?? 0}
					video_tags={current_video?.tags ?? []}
					video_title={current_video?.video?.VIDEO_TITLE ?? 'title loading...'}
					display_variant={'small'}
				/>
				<VideoSection
					publisher={current_video?.user?.USER_USERNAME ?? 'username loading...'}
					publisher_avatar={current_video?.user?.USER_PROFILEPICTURE ?? null}
					publisher_followers={publisher_followers?.length ?? 0}
					video={current_video?.video?.VIDEO_LOCATION ?? null}
					video_id={current_video?.video?.VIDEO_ID ?? null}
					video_views={current_video?.video?.VIDEO_VIEWS ?? 0}
					video_likes={current_video_likes?.length ?? 0}
					video_dislikes={current_video_dislikes?.length ?? 0}
					video_tags={current_video?.tags ?? []}
					video_title={current_video?.video?.VIDEO_TITLE ?? 'title loading...'}
					display_variant={'small'}
				/>
			</div>
		</div>
	{:else}
		<VideoSection
			publisher={current_video?.user?.USER_USERNAME ?? 'username loading...'}
			publisher_avatar={current_video?.user?.USER_PROFILEPICTURE ?? null}
			publisher_followers={publisher_followers ?? []}
			video={current_video?.video?.VIDEO_LOCATION ?? null}
			video_id={current_video?.video?.VIDEO_ID ?? null}
			video_views={current_video?.video?.VIDEO_VIEWS ?? 0}
			video_likes={current_video_likes?.length ?? 0}
			video_dislikes={current_video_dislikes?.length ?? 0}
			video_comments={current_video?.comments?.length ?? 0}
			display_variant={'default'}
		/>
	{/if}
{/key}
