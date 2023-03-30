<script>
	// TODO: Finish Translation, Responsiveness
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Components
	import { Avatar } from '@skeletonlabs/skeleton';
</script>

<svelte:head>
	<meta property="og:url" content="https://rabbidly.com/en/" />
	<meta property="og:title" content="Account" />

	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/account-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/account.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/account-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/account.svg" media="(prefers-color-scheme:light)" />

	<title>Account</title>
</svelte:head>

<section id="account-body" class="flex flex-col pt-2">
	<div id="user-info" class="flex justify-between items-center w-full pb-2">
		<!-- 1080/3 -->
		<div class="flex items-center gap-2">
			<a class="unstyled" href="/">
				<Avatar initials="JF" />
			</a>
			<div class="flex flex-col">
				<div id="username" class="text-lg">Jonesis Froellerix</div>
				<div id="subscriber" class="text-md text-primary-700 dark:text-primary-500 flex">
					<p class="unstyled">2334 Followers&nbsp;</p>
					|
					<p class="unstyled">&nbsp;22222 Views&nbsp;</p>
					|
					<p class="unstyled">&nbsp;232223 Videos&nbsp;</p>
					|
					<p class="unstyled">&nbsp;Joined on 10th of January 2022</p>
				</div>
			</div>
		</div>
		<div class="flex gap-2">
			<button class="btn variant-ringed hover:variant-filled h-1/2" type="button"> Edit </button>
			<button class="btn variant-ringed hover:variant-filled h-1/2" type="button"> Post </button>
		</div>
	</div>
	<hr />
	<div id="user-description" class="flex justify-between p-2">
		<div class="flex flex-col">
			<div class="textbox p-2">
				Lorem ipsum dolor sit amet consectetur adipisicing elit. Neque commodi ipsam, ullam quidem
				laboriosam officia? Neque aspernatur vero rem nisi odio, doloremque consectetur. Consequatur
				illum maxime quo ipsum deleniti facilis? Consequuntur libero sapiente pariatur tempore
				asperiores dolores ab modi, ratione accusantium odio similique inventore sint aut,
				distinctio animi accusamus nam consequatur! Facere nisi vero, vitae numquam et corporis
				impedit animi necessitatibus officia libero asperiores. Consectetur delectus sapiente
				corporis fugit modi earum itaque laudantium, dolores autem nostrum neque adipisci cumque ab
				cupiditate sequi, officia eum similique dolore porro mollitia doloremque doloribus omnis
				suscipit ratione? Facere?
			</div>
			<button class="btn variant-ringed hover:variant-filled h-1/2 w-fit" type="button">
				Read More
			</button>
		</div>
		<div id="user-socials" class="flex gap-2 self-top pl-2 pt-2">
			<iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:instagram" />
			<iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:youtube" />
			<iconify-icon class="cursor-pointer" width="30" height="30" icon="ic:baseline-tiktok" />
			<iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:twitter" />
		</div>
	</div>
	<hr />
	<div id="videos">
		<div id="controlls" class="flex justify-between items-center gap-8 p-2">
			<div
				id="search-bar"
				class="mt-2 input-group input-group-divider grid-cols-[auto_1fr_auto] h-fit w-fit"
			>
				<div class="input-group-shim">
					<iconify-icon class="cursor-pointer" icon="material-symbols:search-rounded" />
				</div>
				<input class="p-2 rounded-none outline-none" type="search" placeholder="Search..." />
				<button class="variant-soft-secondary"
					>{$translation.pages.search.video_search_bar.search()}</button
				>
			</div>
			<div id="search-config" class="flex flex-col gap-2">
				<div id="sort-subject" class="flex items-center gap-2">
					Subject:
					<button type="button" class="btn variant-ringed">Category</button>
					<button type="button" class="btn variant-ringed">Username</button>
					<button type="button" class="btn variant-ringed">Title</button>
				</div>
				<div id="sort-by" class="flex items-center gap-2">
					Sort By:
					<button type="button" class="btn variant-ringed">Date</button>
					<button type="button" class="btn variant-ringed">Views</button>
					<button type="button" class="btn variant-ringed">Likes</button>
				</div>
			</div>
		</div>
		2354 Videos found:
		<hr />
		<div id="results" class="flex gap-4 p-4 flex-wrap justify-center">
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<div class="absolute right-0 flex gap-2">
					<iconify-icon icon="ic:round-edit" />
					<iconify-icon icon="mdi:delete" />
				</div>
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
			<div class="video-result flex flex-col items-center relative">
				<p class="text-center">title</p>
				<iconify-icon class="absolute right-0" icon="ic:round-edit" />
				<div class="w-[180px] h-[320px] relative border border-gray-500 rounded-md mb-2">
					<!-- 1920/6 -->
					<div class="absolute w-full flex justify-between p-2">
						<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
						<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
						<!-- <iconify-icon class="cursor-pointer" icon="heroicons-solid:volume-up" /> -->
						<iconify-icon icon="heroicons-solid:volume-off" />
					</div>
					<div class="absolute bottom-0 w-full p-2 text-xs text-primary-700 dark:text-primary-500">
						200 Views
					</div>
				</div>
				<div class="video-info-tags flex justify-center flex-wrap gap-1 max-w-[180px]">
					<span class="chip variant-ringed">#dosomsing</span>
					<span class="chip variant-ringed">#sveltekitissick</span>
					<span class="chip variant-ringed">#ilovetailwindcss</span>
				</div>
			</div>
		</div>
	</div>
</section>
