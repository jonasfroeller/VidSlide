<script>
	// TODO: Finish Translation, Responsiveness, Create Components from Wireframe Code (change video frame to aspect-ratio 9:16)
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Components
	import { Avatar } from '@skeletonlabs/skeleton';

	/* --- LOGIC --- */
	// true if comments are selected
	$: selectedBox = true;
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
	<article id="video-info-section" class="flex flex-col gap-2">
		<div id="video-info-actions">
			<div class="btn-group variant-ringed">
				<button
					class={selectedBox ? 'variant-ghost' : ''}
					on:click={() => {
						selectedBox = true;
					}}>{$translation.pages.home.video_info_display_actions.comments()}</button
				>
				<button
					class={!selectedBox ? 'variant-ghost' : ''}
					on:click={() => {
						selectedBox = false;
					}}>{$translation.pages.home.video_info_display_actions.description()}</button
				>
			</div>
		</div>
		<div
			id="video-info-content"
			class="border border-gray-500 rounded-md p-4 w-[360px] h-[640px] overflow-auto relative"
		>
			{#if selectedBox}
				<div id="comments">
					<h3>232 comments:</h3>
					<div id="write-comment" class="flex items-center gap-2 justify-center">
						<Avatar class="scale-75" initials="JF" />
						<textarea
							class="textarea p-2 w-fit resize-none"
							rows="1"
							maxlength="200"
							placeholder="I think..."
						/>
						<button type="button" class="btn-icon variant-soft-secondary h-fit">
							<iconify-icon class="cursor-pointer scale-150" icon="material-symbols:send" />
						</button>
					</div>
					<hr />
					<div class="comment comment-body flex flex-col">
						<div class="comment-header flex items-center ml-[-10px] mb-[-5px]">
							<Avatar class="scale-50" initials="JP" />
							<p>
								<span class="comment-author">Jordan Peterson</span> |
								<span class="comment-datetime text-md text-primary-700 dark:text-primary-500"
									>5 days ago</span
								>
							</p>
						</div>
						<div
							class="comment-text w-full h-14 text-md text-primary-700 dark:text-primary-500 p-2 rounded-md bg-surface-200 dark:bg-surface-600 mb-1"
						>
							Hello world!
						</div>
						<div class="comment-actions flex justify-between">
							<div class="flex">
								<div class="flex flex-col gap-1 scale-[80%]">
									<button type="button" class="btn-icon variant-ringed">
										<iconify-icon
											class="scale-125"
											icon="material-symbols:thumb-up-outline-rounded"
										/>
										<!-- <iconify-icon icon="material-symbols:thumb-up-rounded" /> -->
									</button>
									<span class="text-xs text-center text-primary-700 dark:text-primary-500">23</span>
								</div>
								<div class="flex flex-col gap-1 scale-[80%]">
									<button type="button" class="btn-icon variant-ringed">
										<iconify-icon
											class="scale-125"
											icon="material-symbols:thumb-down-outline-rounded"
										/>
										<!-- <iconify-icon icon="material-symbols:thumb-up-rounded" /> -->
									</button>
									<span class="text-xs text-center text-primary-700 dark:text-primary-500">12</span>
								</div>
							</div>
							<div class="flex">
								<div class="flex flex-col scale-[80%]">
									<button type="button" class="btn variant-ringed"> Reply </button>
								</div>
								<div class="flex flex-col gap-1 scale-[80%]">
									<button type="button" class="btn variant-ringed"> Show 23 Replies </button>
								</div>
							</div>
						</div>
					</div>
				</div>
			{:else}
				<div id="info">
					<h3>title</h3>
					<p class="text-md">
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus possimus ratione modi
						ex, iste fugit ipsum quaerat placeat aspernatur in natus. Minima, quaerat suscipit quis
						ducimus dolore quod error! Nihil esse eaque dolore necessitatibus aut architecto
						sapiente impedit, laborum asperiores recusandae natus illo velit consequuntur odio.
						Inventore adipisci soluta ex porro fugiat omnis voluptatem animi ducimus, quia,
						cupiditate itaque quisquam recusandae maiores odio amet saepe dolor veniam eum magnam
						excepturi deleniti nobis suscipit reprehenderit tempora. Asperiores necessitatibus
						aperiam quam eligendi voluptatibus a omnis! Cum hic modi sunt eveniet, mollitia ipsum
						beatae ea ab sint id nemo nulla ut doloribus deserunt omnis nam? Debitis eveniet
						voluptas tempore est labore soluta esse minima reiciendis tempora autem officia velit,
						dolores ut nobis? Suscipit libero asperiores totam, optio amet aliquam odio doloribus
						mollitia enim atque veritatis eaque nesciunt quos architecto, magnam, maxime consequatur
						autem. Eos repellat placeat beatae minima inventore excepturi saepe maxime vel natus,
						quis dicta eius aliquid perferendis facilis odit, dolore voluptates rem nihil neque,
						asperiores corrupti eligendi voluptas esse. At, harum veniam voluptatum esse neque
						accusamus totam labore et necessitatibus nam expedita nobis praesentium doloremque
						autem, vitae quae dolores ipsa eligendi ab sapiente dolor aliquid eaque sequi eos. Hic
						facilis veniam velit quas, corrupti aliquid earum placeat nihil dignissimos omnis
						molestias laboriosam rem, voluptas voluptates molestiae provident blanditiis non eius
						accusamus et officiis veritatis! Inventore voluptate perspiciatis odio quaerat
						similique!
					</p>
				</div>
			{/if}
		</div>
		<hr />
		<p class="text-xs text-primary-700 dark:text-primary-500">
			postet on
			<span id="date-posted"> 16th of March 2023 </span>
			<span id="time-posted">23:07 AM</span>
		</p>
		<div id="video-info-tags">
			<span class="chip variant-ringed">#dosomsing</span>
			<span class="chip variant-ringed">#sveltekitissick</span>
			<span class="chip variant-ringed">#ilovetailwindcss</span>
		</div>
	</article>

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
			<div id="video-player" class="w-[360px] h-[640px] relative border border-gray-500 rounded-md">
				<!-- 1920/3 -->
				<div id="video-player-controlls-top" class="absolute w-full flex justify-between p-2">
					<iconify-icon class="cursor-pointer" icon="material-symbols:play-arrow-rounded" />
					<!-- <iconify-icon icon="material-symbols:pause-rounded" /> -->
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
