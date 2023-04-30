<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// JS-Framework/Library
	import { onMount } from 'svelte';

	// CSS-Framework/Library
	import { Avatar } from '@skeletonlabs/skeleton';

	// Stores
	import {
		loginState,
		user,
		user_social,
		user_stats,
		user_subscribed,
		user_subscribers
	} from '$store/account';

	// Props
	export let page;

	/* --- LOGIC --- */
	onMount(async () => {
		const boxes = document.querySelectorAll('.social-media-site');
		if (boxes) {
			let maxBoxWidth = 0;

			// Schritt 1: das breiteste Element finden und die Breite speichern
			boxes.forEach((box) => {
				const boxWidth = box.offsetWidth;
				if (boxWidth > maxBoxWidth) {
					maxBoxWidth = boxWidth;
				}
			});

			// Schritt 2: die Breite des breitesten Elements auf alle anderen übertragen
			boxes.forEach((box) => {
				box.style.width = maxBoxWidth + 'px';
			});
		}
	});

	// TODO: fetch and parse data
</script>

{#if page.includes('account')}
	<div id="user-info" class="flex justify-between items-center w-full pb-2">
		<!-- 1080/3 -->
		<div class="flex items-center gap-2">
			<a class="unstyled" href="/">
				<Avatar initials={$user?.USER_USERNAME?.charAt(0) ?? '??'} />
			</a>
			<div class="flex flex-col">
				<div id="username" class="text-lg">{$user?.USER_USERNAME ?? '??'}</div>
				<div id="subscriber" class="text-md text-primary-700 dark:text-primary-500 flex">
					<p class="unstyled">
						{$translation.UserData.follower($user_subscribers?.length ?? 0)}&nbsp;
					</p>
					|
					<p class="unstyled">&nbsp;{$translation.UserData.views($user_stats?.VIEWS ?? 0)}&nbsp;</p>
					|
					<p class="unstyled">
						&nbsp;{$translation.UserData.videos($user_stats?.VIDEOS ?? 0)}&nbsp;
					</p>
					|
					<p class="unstyled">
						&nbsp;{$translation.UserData.joined($user?.USER_DATETIMECREATED ?? 0)}
					</p>
				</div>
			</div>
		</div>
		<div class="flex gap-2">
			{#if $loginState}
				<button class="btn variant-ringed hover:variant-filled h-1/2" type="button">
					{$translation.UserData.edit()}
				</button>
				<button class="btn variant-ringed hover:variant-filled h-1/2" type="button">
					{$translation.UserData.post()}
				</button>
			{:else}
				<button class="btn variant-ringed hover:variant-filled h-1/2" type="button">
					{$translation.UserData.follow()}
				</button>
			{/if}
		</div>
	</div>
	<hr />
	<div id="user-description" class="flex justify-between p-2">
		<div class="flex flex-col">
			<div class="textbox p-2">
				{$user?.USER_PROFILEPICTURE ?? '??'}
			</div>
			<button class="btn variant-ringed hover:variant-filled h-1/2 w-fit" type="button">
				{$translation.UserData.more()}
			</button>
		</div>
		<div id="user-socials" class="flex gap-2 pl-2 pt-2 h-min">
			<a href="/" class="transition"
				><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:instagram" /></a
			>
			<a href="/" class="transition"
				><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:youtube" /></a
			>
			<a href="/" class="transition"
				><iconify-icon class="cursor-pointer" width="30" height="30" icon="ic:baseline-tiktok" /></a
			>
			<a href="/" class="transition"
				><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:twitter" /></a
			>
		</div>
	</div>
	<hr />
{:else}
	<div class="pt-4">
		<h3>{$translation.pages.settings.account_section.title()}</h3>
		<div class="flex flex-col flex-wrap gap-8 divide-y">
			<div id="account" class="flex flex-col p-2">
				<div class="flex gap-2">
					<label class="label">
						<span>{$translation.pages.settings.account_section.username()}</span>
						<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
							<input disabled class="input p-2" type="text" placeholder="jonesis" />
							<button class="variant-soft-secondary"
								>{$translation.pages.settings.account_section.edit()}</button
							>
						</div>
					</label>
					<label class="label">
						<span>{$translation.pages.settings.account_section.password()}</span>
						<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
							<input disabled class="input p-2" type="password" placeholder="●●●●●●●●" />
							<button class="variant-soft-secondary"
								>{$translation.pages.settings.account_section.edit()}</button
							>
						</div>
					</label>
				</div>
			</div>
			<div id="socials" class="flex flex-col gap-2 p-2">
				<h4>{$translation.pages.settings.account_section.socials()}</h4>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">https://www.instagram.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="@your_username"
					/>
					<button class="variant-soft-secondary"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">https://www.youtube.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="@your_username"
					/>
					<button class="variant-soft-secondary"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">https://www.twitter.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="@your_username"
					/>
					<button class="variant-soft-secondary"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim">https://www.tiktok.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="@your_username"
					/>
					<button class="variant-soft-secondary"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
			</div>
			<div id="description" class="p-2">
				<div class="flex flex-col gap-2 w-fit">
					<h4>{$translation.pages.settings.account_section.description()}</h4>
					<textarea
						disabled
						class="textarea p-2 w-96"
						rows="4"
						placeholder="your profile description..."
					/>
					<button class="btn btn-sm variant-ringed-secondary w-full"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
			</div>
		</div>
	</div>
{/if}
