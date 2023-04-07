<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	// Components
	import InfoSection from '$component//InfoSection.svelte';
	import VideoSection from '$component/VideoSection.svelte';

	/* --- LOGIC --- */
	// TODO: fetch and parse data
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
		video_title={'another title'}
		video_description={'lorem ipsum'}
		video_date_time_posted={'16th of March 2023 23:07 AM'}
		video_tags={['love', 'fashion']}
		video_comments={[
			{
				username: 'xx_witchfighter_xx',
				avatar: null,
				date_time_posted: '5 days ago',
				text: 'nice',
				likes: 6,
				dislikes: 2,
				replies: [
					{
						username: 'jordan_peterson_422',
						avatar: null,
						date_time_posted: '2 days ago',
						text: 'sick bro',
						likes: 22,
						dislikes: 1,
						replies: null
					}
				]
			},
			{
				username: 'jordan_peterson_422',
				avatar: null,
				date_time_posted: '2 days ago',
				text: 'sick bro',
				likes: 22,
				dislikes: 1,
				replies: [
					{
						username: 'xx_witchfighter_xx',
						avatar: null,
						date_time_posted: '5 days ago',
						text: 'nice',
						likes: 6,
						dislikes: 2,
						replies: null
					}
				]
			}
		]}
	/>

	<VideoSection
		publisher={'Mitch'}
		publisher_following={true}
		publisher_followers={'0'}
		video={'vid_5.MP4'}
		video_views={'223'}
		video_likes={'2222'}
		video_dislikes={'209'}
		video_comments={'2'}
	/>
</section>
