<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	// Components
	import VideoSection from '$component/VideoSection.svelte';
	import VideoResult from '$component/VideoResult.svelte';

	/* --- LOGIC --- */
	// TODO: fetch and parse data
</script>

<svelte:head>
	<meta property="og:url" content="https://rabbidly.com/en/" />
	<meta property="og:title" content="Search" />

	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/search-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/search.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/search-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/search.svg" media="(prefers-color-scheme:light)" />

	<title>Search</title>
</svelte:head>

<section id="search-body" class="flex justify-center pt-2 gap-6 flex-wrap">
	<div id="search-section" class="flex flex-col gap-4">
		<div id="search-bar" class="mt-2 input-group input-group-divider grid-cols-[auto_1fr_auto]">
			<div class="input-group-shim">
				<iconify-icon class="cursor-pointer" icon="material-symbols:search-rounded" />
			</div>
			<input
				class="p-2 rounded-none outline-none"
				type="search"
				placeholder="{$translation.global.search()}..."
			/>
			<button class="variant-soft-secondary">{$translation.global.search()}</button>
		</div>
		<div id="search-config" class="flex flex-col gap-2">
			Subject:
			<hr />
			<div id="sort-subject" class="flex gap-2">
				<button type="button" class="btn variant-ringed">Category</button>
				<button type="button" class="btn variant-ringed">Username</button>
				<button type="button" class="btn variant-ringed">Title</button>
			</div>
			Sort By:
			<hr />
			<div id="sort-by" class="flex gap-2">
				<button type="button" class="btn variant-ringed">Date</button>
				<button type="button" class="btn variant-ringed">Views</button>
				<button type="button" class="btn variant-ringed">Likes</button>
			</div>
		</div>
		<div id="search-result" class="flex flex-col gap-2 divide-y">
			<p>232 Videos found:</p>
			<div id="video-results" class="flex gap-2">
				<VideoResult
					publisher={'Peter'}
					publisher_following={false}
					publisher_followers={'1'}
					video={'vid_1.MP4'}
					video_title={'some title'}
					video_views={'20'}
					video_likes={'8'}
				/>
				<VideoResult
					publisher={'Mitch'}
					publisher_following={true}
					publisher_followers={'0'}
					video={'vid_2.MP4'}
					video_title={'another title'}
					video_tags={['love', 'fashion']}
					video_views={'223'}
					video_likes={'2222'}
				/>
			</div>
		</div>
	</div>

	<VideoSection
		publisher={'Mitch'}
		publisher_following={true}
		publisher_followers={'0'}
		video={'vid_4.MP4'}
		video_views={'223'}
		video_likes={'2222'}
		video_dislikes={'209'}
		video_comments={'2'}
	/>
</section>
