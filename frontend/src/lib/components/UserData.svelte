<script lang="ts">
	/* --- INIT --- */
	// Backend Api
	import Api from '$api/api';

	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

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
	// - medium=user [MEDIUM]
	//   - id=video [ID] // unsuffishient
	//     - medium_id=? [ID++] // creator of video
	//   - id=username [ID]
	//     - medium_id=? [ID++] // username of user
	//   - id=? [ID]
	async function fetchUser(id_specification = '') {
		let response = await Api.get('user', 'username', id_specification);
		return response;
	}

	function formatUser(user: JSON) {
		let formattet_object = user?.data;
		formattet_object['user'] = JSON.parse(formattet_object[0])[0];
		delete formattet_object['0'];

		formattet_object['socials'] = JSON.parse(formattet_object['socials']);
		formattet_object['stats']['likes'] = JSON.parse(formattet_object['stats']['likes']);
		formattet_object['stats']['shares'] = JSON.parse(formattet_object['stats']['shares']);
		formattet_object['stats']['videos'] = JSON.parse(formattet_object['stats']['videos']);
		formattet_object['stats']['views'] = JSON.parse(formattet_object['stats']['views']);

		formattet_object['subscribed'] = JSON.parse(formattet_object['subscribed']);
		formattet_object['subscribers'] = JSON.parse(formattet_object['subscribers']);

		return formattet_object;
	}

	onMount(async () => {
		let user = await fetchUser('SamJones');
		current_user = formatUser(user);

		current_user_socials = {};
		current_user_socials['instagram'] = current_user?.socials.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'instagram'
		);
		current_user_socials['youtube'] = current_user?.socials.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'youtube'
		);
		current_user_socials['tiktok'] = current_user?.socials.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'tiktok'
		);
		current_user_socials['twitter'] = current_user?.socials.find(
			(social) => social.SOCIAL_PLATFORM.toLowerCase() === 'twitter'
		);

		if (browser) {
			console.log($user);
		}

		const socials = document.querySelectorAll('.social-media-site');
		if (socials) {
			let maxBoxWidth = 0;

			socials.forEach((box) => {
				const boxWidth = box.offsetWidth;
				if (boxWidth > maxBoxWidth) {
					maxBoxWidth = boxWidth;
				}
			});

			socials.forEach((box) => {
				box.style.width = maxBoxWidth + 'px';
			});
		}
	});

	$: current_user = null;
	$: current_user_socials = null;
</script>

{#if page.includes('account')}
	<div id="user-info" class="flex flex-wrap justify-between gap-4 items-center w-full pb-2">
		<!-- 1080/3 -->
		<div class="flex items-center gap-2">
			<a class="unstyled" href="/">
				<Avatar initials={current_user?.user?.USER_USERNAME?.charAt(0) ?? '??'} />
			</a>
			<div class="flex flex-col">
				<div id="username" class="text-lg">{current_user?.user?.USER_USERNAME ?? '??'}</div>
				<div id="stats" class="text-md text-primary-700 dark:text-primary-500 flex">
					<p class="unstyled">
						{$translation.UserData.follower(current_user?.stats?.views?.length ?? 0)}&nbsp;
					</p>
					|
					<p class="unstyled">
						&nbsp;{$translation.UserData.views(current_user?.stats?.views?.length ?? 0)}&nbsp;
					</p>
					|
					<p class="unstyled">
						&nbsp;{$translation.UserData.videos(current_user?.stats?.videos?.length ?? 0)}&nbsp;
					</p>
					|
					<p class="unstyled">
						&nbsp;{$translation.UserData.joined(current_user?.user?.USER_DATETIMECREATED)}
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
				{current_user?.user?.USER_PROFILEDESCRIPTION ?? 'no description...'}
			</div>
			<button class="btn variant-ringed hover:variant-filled h-1/2 w-fit" type="button">
				{$translation.UserData.more()}
			</button>
		</div>
		<div id="user-socials" class="flex gap-2 pl-2 pt-2 h-min">
			{#if current_user_socials?.instagram}
				<a href={current_user_socials.instagram.SOCIAL_URL} class="transition"
					><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:instagram" /></a
				>
			{/if}
			{#if current_user_socials?.youtube}
				<a href={current_user_socials.youtube.SOCIAL_URL} class="transition"
					><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:youtube" /></a
				>
			{/if}
			{#if current_user_socials?.tiktok}
				<a href={current_user_socials.tiktok.SOCIAL_URL} class="transition"
					><iconify-icon
						class="cursor-pointer"
						width="30"
						height="30"
						icon="ic:baseline-tiktok"
					/></a
				>
			{/if}
			{#if current_user_socials?.twitter}
				<a href={current_user_socials.twitter.SOCIAL_URL} class="transition"
					><iconify-icon class="cursor-pointer" width="30" height="30" icon="mdi:twitter" /></a
				>
			{/if}
		</div>
	</div>
	<hr />
{:else}
	<div class="pt-4">
		<h3>{$translation.pages.settings.account_section.title()}</h3>
		<div class="flex flex-col flex-wrap gap-8 divide-y">
			<div id="account" class="flex flex-col p-2">
				<div class="flex flex-col md:flex-row gap-2">
					<label class="label">
						<span>{$translation.pages.settings.account_section.username()}</span>
						<div class="input-group input-group-divider grid-cols-[1fr_auto]">
							<input
								disabled
								class="input p-2"
								type="text"
								placeholder={$user?.USER_USERNAME ?? '??'}
								value={$user?.USER_USERNAME ?? '??'}
							/>
							<button class="variant-soft-secondary"
								>{$translation.pages.settings.account_section.edit()}</button
							>
						</div>
					</label>
					<label class="label">
						<span>{$translation.pages.settings.account_section.password()}</span>
						<div class="input-group input-group-divider grid-cols-[1fr_auto]">
							<input
								disabled
								class="input p-2"
								type="password"
								placeholder="●●●●●●●●"
								value="●●●●●●●●"
							/>
							<button class="variant-soft-secondary"
								>{$translation.pages.settings.account_section.edit()}</button
							>
						</div>
					</label>
				</div>
			</div>
			<div id="socials" class="flex flex-col gap-2 p-2">
				<h4>{$translation.pages.settings.account_section.socials()}</h4>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim text-center md:text-left">
						https://www.instagram.com/
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary text-center md:text-left"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim text-center md:text-left">
						https://www.youtube.com/
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary text-center md:text-left"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim text-center md:text-left">
						https://www.twitter.com/
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary text-center md:text-left"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider md:grid-cols-[auto_1fr_auto]">
					<div class="social-media-site input-group-shim text-center md:text-left">
						https://www.tiktok.com/
					</div>
					<input disabled class="outline-none p-2" type="text" placeholder="@your_username" />
					<button class="variant-soft-secondary text-center md:text-left"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
			</div>
			<div id="description" class="p-2">
				<div class="flex flex-col gap-2 w-fit">
					<h4>{$translation.pages.settings.account_section.description()}</h4>
					<textarea
						disabled
						class="textarea p-2 w-96 max-w-[50vw]"
						rows="4"
						placeholder={$user?.USER_PROFILEDESCRIPTION ?? 'no descroption...'}
						value={$user?.USER_PROFILEDESCRIPTION ?? 'no descroption...'}
					/>
					<button class="btn btn-sm variant-ringed-secondary w-full"
						>{$translation.pages.settings.account_section.edit()}</button
					>
				</div>
			</div>
		</div>
	</div>
{/if}
