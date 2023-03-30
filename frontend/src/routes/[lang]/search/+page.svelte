<script>
	// TODO: Finish Translation, Responsiveness, Create Components from Wireframe Code (change video frame to aspect-ratio 9:16)
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Components
	import { Avatar } from '@skeletonlabs/skeleton';
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
			<input class="p-2 rounded-none outline-none" type="search" placeholder="Search..." />
			<button class="variant-soft-secondary"
				>{$translation.pages.search.video_search_bar.search()}</button
			>
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
				<div class="video-result flex flex-col items-center">
					<div class="divide-y">
						<div class="flex items-center">
							<a class="unstyled" href="/">
								<Avatar class="scale-75" initials="JF" />
							</a>
							<div class="flex flex-col">
								<div class="text-xs">Jonesis Froellerix</div>
								<div class="text-xs text-primary-700 dark:text-primary-500">
									<a class="unstyled" href="/">2334 Followers</a>
								</div>
							</div>
							<button
								type="button"
								class="ml-2 btn btn-icon variant-ringed hover:variant-filled scale-75"
							>
								<iconify-icon class="cursor-pointer" icon="ic:outline-plus" />
							</button>
							<!-- <iconify-icon class="cursor-pointer" icon="ic:outline-minus" /> -->
						</div>
						<p class="text-center">title</p>
					</div>
					<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
						<!-- 1920/6 -->
						<div class="absolute w-full flex justify-between p-2">
							<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
							<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
							<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
							<iconify-icon icon="heroicons-solid:volume-off" />
						</div>
						<div
							class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500"
						>
							200 Views
						</div>
					</div>
					<div class="video-info-tags flex flex-wrap gap-1 max-w-[180px]">
						<span class="chip variant-ringed">#dosomsing</span>
						<span class="chip variant-ringed">#sveltekitissick</span>
						<span class="chip variant-ringed">#ilovetailwindcss</span>
					</div>
				</div>
				<div class="video-result flex flex-col items-center">
					<div class="divide-y">
						<div class="flex items-center">
							<a class="unstyled" href="/">
								<Avatar class="scale-75" initials="JF" />
							</a>
							<div class="flex flex-col">
								<div class="text-xs">Jonesis Froellerix</div>
								<div class="text-xs text-primary-700 dark:text-primary-500">
									<a class="unstyled" href="/">2334 Followers</a>
								</div>
							</div>
							<button
								type="button"
								class="ml-2 btn btn-icon variant-ringed hover:variant-filled scale-75"
							>
								<iconify-icon class="cursor-pointer" icon="ic:outline-plus" />
							</button>
							<!-- <iconify-icon class="cursor-pointer" icon="ic:outline-minus" /> -->
						</div>
						<p class="text-center">title</p>
					</div>
					<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
						<!-- 1920/6 -->
						<div class="absolute w-full flex justify-between p-2">
							<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
							<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
							<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
							<iconify-icon icon="heroicons-solid:volume-off" />
						</div>
						<div
							class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500"
						>
							200 Views
						</div>
					</div>
					<div class="video-info-tags flex flex-wrap gap-1 max-w-[180px]">
						<span class="chip variant-ringed">#dosomsing</span>
						<span class="chip variant-ringed">#sveltekitissick</span>
						<span class="chip variant-ringed">#ilovetailwindcss</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="video-section" class="flex gap-4">
		<div id="video" class="flex flex-col gap-2">
			<div id="video-publisher" class="flex justify-between items-center w-[360px]">
				<!-- 1080/3 -->
				<div id="video-info" class="flex items-center gap-2">
					<a class="unstyled" href="/">
						<Avatar initials="JF" />
					</a>
					<div id="video-publisher-info" class="flex flex-col">
						<div id="username" class="text-lg">Jonesis Froellerix</div>
						<div id="subscriber" class="text-md text-primary-700 dark:text-primary-500">
							<a class="unstyled" href="/">2334 Followers</a>
						</div>
					</div>
				</div>
				<button
					id="video-action"
					type="button"
					class="btn variant-ringed hover:variant-filled h-1/2"
				>
					Subscribe
				</button>
			</div>
			<div class="w-[360px] h-[640px] relative border border-gray-500 rounded-md">
				<!-- 1920/3 -->
				<div id="video-player-controlls-top" class="absolute w-full flex justify-between p-2">
					<!-- <iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" /> -->
					<iconify-icon icon="material-symbols:pause-rounded" />
					<iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" />
					<!-- <iconify-icon icon="heroicons-solid:volume-off" /> -->
				</div>
				<div
					id="video-player-info-bottom"
					class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500"
				>
					200 Views
				</div>
			</div>
		</div>
		<div id="actions" class="flex justify-end flex-col gap-2 h-[710px]">
			<div class="flex flex-col gap-1">
				<button type="button" class="btn-icon variant-ringed">
					<iconify-icon class="scale-125" icon="material-symbols:thumb-up-outline-rounded" />
					<!-- <iconify-icon icon="material-symbols:thumb-up-rounded" /> -->
				</button>
				<span class="text-xs text-center text-primary-700 dark:text-primary-500">23</span>
			</div>
			<div class="flex flex-col gap-1">
				<button type="button" class="btn-icon variant-ringed">
					<iconify-icon class="scale-125" icon="material-symbols:thumb-down-outline-rounded" />
					<!-- <iconify-icon icon="material-symbols:thumb-up-rounded" /> -->
				</button>
				<span class="text-xs text-center text-primary-700 dark:text-primary-500">12</span>
			</div>
			<div class="flex flex-col gap-1">
				<button type="button" class="btn-icon variant-ringed">
					<iconify-icon class="scale-125" icon="fa:commenting-o" />
				</button>
				<span class="text-xs text-center text-primary-700 dark:text-primary-500">2</span>
			</div>
			<div class="flex flex-col gap-1">
				<button type="button" class="btn-icon variant-ringed">
					<iconify-icon class="scale-150" icon="mdi:share" />
				</button>
				<span class="text-xs text-center text-primary-700 dark:text-primary-500">8</span>
			</div>
		</div>
	</div>
</section>
