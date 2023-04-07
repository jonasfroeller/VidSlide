<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { user, user_following, user_social, user_stats } from '$store/account';

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	/* --- LOGIC --- */
	// TODO: fetch and parse data
</script>

<div id="user-info" class="flex justify-between items-center w-full pb-2">
	<!-- 1080/3 -->
	<div class="flex items-center gap-2">
		<a class="unstyled" href="/">
			<Avatar initials={$user?.username?.charAt(0) ?? 'UN'} />
		</a>
		<div class="flex flex-col">
			<div id="username" class="text-lg">{$user?.username ?? 'jonesisfroellerix'}</div>
			<div id="subscriber" class="text-md text-primary-700 dark:text-primary-500 flex">
				<p class="unstyled">{$translation.UserData.follower($user_stats?.followers ?? 0)}&nbsp;</p>
				|
				<p class="unstyled">&nbsp;{$translation.UserData.views($user_stats?.views ?? 0)}&nbsp;</p>
				|
				<p class="unstyled">&nbsp;{$translation.UserData.videos($user_stats?.videos ?? 0)}&nbsp;</p>
				|
				<p class="unstyled">&nbsp;{$translation.UserData.joined($user?.joined ?? 0)}</p>
			</div>
		</div>
	</div>
	<div class="flex gap-2">
		<button class="btn variant-ringed hover:variant-filled h-1/2" type="button">
			{$translation.UserData.edit()}
		</button>
		<button class="btn variant-ringed hover:variant-filled h-1/2" type="button">
			{$translation.UserData.post()}
		</button>
	</div>
</div>
<hr />
<div id="user-description" class="flex justify-between p-2">
	<div class="flex flex-col">
		<div class="textbox p-2">
			{user?.description ?? 'lorem ipsum'}
		</div>
		<button class="btn variant-ringed hover:variant-filled h-1/2 w-fit" type="button">
			{$translation.UserData.more()}
		</button>
	</div>
	<div id="user-socials" class="flex gap-2 self-top pl-2 pt-2">
		<a href="/"
			><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:instagram" /></a
		>
		<a href="/"><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:youtube" /></a
		>
		<a href="/"
			><iconify-icon class="cursor-pointer" width="30" height="30" icon="ic:baseline-tiktok" /></a
		>
		<a href="/"><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:twitter" /></a
		>
	</div>
</div>
<hr />
