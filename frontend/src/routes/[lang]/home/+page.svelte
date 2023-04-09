<script lang="ts">
	/* --- INIT --- */
	// Backend Api
	import Api from '$api/api';

	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Components
	import InfoSection from '$component/InfoSection.svelte';
	import VideoSection from '$component/VideoSection.svelte';

	// JS-Framework/Library
	import { onMount } from 'svelte';

	/* --- LOGIC --- */
	// - medium=video [MEDIUM] // gets videos and video info
	//   - id=all [ID] // unsuffishient
	//     - medium_id=? [ID++] // all videos of user
	//   - id=title [ID]
	//     - medium_id=? [ID++] // all videos with title including text
	//   - id=random [ID]
	//   - id=? [ID]
	async function getVid(id: number, id_specification = '') {
		id == 0 ? (id = 'random') : id;
		return await Api.get('video', id, id_specification);
	}

	// TODO: put me in load file => create class that is loaded in load file and loads videos, user...
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
				).length;
				let dislikes = formattet_object?.comments_feedback?.filter(
					(f) => f.COMMENT_ID == comment.COMMENT_ID && f.COMMENT_FEEDBACK_TYPE === 'negative'
				).length;

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

			console.log(formattet_object.comments);
		}

		return formattet_object;
	}

	onMount(async () => {
		current_video = await getVid(0);
		current_video = formatVideo(current_video);
		console.log(current_video);
	});

	$: current_video = null;
</script>

<svelte:head>
	<meta property="og:url" content="https://rabbidly.com/en/" />
	<meta property="og:title" content="Home" />

	<!-- %sveltekit.assets% and $images fails -->
	<!-- prefers-color-scheme and not $themestate because browser has different theme than website -->
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/home-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/home.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/home-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/home.svg" media="(prefers-color-scheme:light)" />

	<title>Home</title>
</svelte:head>

<section id="home-body" class="flex justify-center pt-2 gap-6 flex-wrap">
	<InfoSection
		video_title={current_video?.video?.VIDEO_TITLE}
		video_description={current_video?.video?.VIDEO_DESCRIPTION}
		video_date_time_posted={current_video?.video?.VIDEO_DATETIMEPOSTED}
		video_tags={current_video?.tags}
		video_comments={current_video?.comments}
	/>

	<VideoSection
		publisher={current_video?.user?.USER_USERNAME}
		publisher_avatar={current_video?.user?.USER_PROFILEPICTURE}
		publisher_followers={0}
		video={current_video?.video?.VIDEO_LOCATION}
		video_id={current_video?.video?.VIDEO_ID}
		video_views={0}
		video_likes={0}
		video_dislikes={0}
		video_comments={current_video?.comments?.length}
	/>

	<!-- TODO: followers, likes, dislikes, views -->
</section>
