<script>
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { user, user_following, user_social } from '$store/account';

	// Components
	import LangSelect from '$component/LangSelect.svelte';
	import ThemeSelect from '$component/ThemeSelect.svelte';

	/* --- LOGIC --- */
	// TODO: fetch and parse data
</script>

<svelte:head>
	<meta property="og:url" content="https://rabbidly.com/en/" />
	<meta property="og:title" content="Settings" />

	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/settings-light.svg"
		media="(prefers-color-scheme:dark)"
	/>
	<link
		rel="icon"
		type="image/svg+xml"
		sizes="any"
		href="/settings.svg"
		media="(prefers-color-scheme:light)"
	/>

	<!-- Safari -->
	<link rel="mask-icon" href="/settings-light.svg" media="(prefers-color-scheme:dark)" />
	<link rel="mask-icon" href="/settings.svg" media="(prefers-color-scheme:light)" />

	<title>Settings</title>
</svelte:head>

<section id="settings-body" class="flex flex-col pt-2 divide-y">
	<h3>{$translation.settings.site_section.title()}</h3>
	<div id="site-settings" class="flex justify-between gap-8 pt-4 pb-4">
		<ThemeSelect />
		<LangSelect />
	</div>
	<div id="account-settings" class="pt-4">
		<h3>{$translation.settings.acount_section.title()}</h3>
		<div class="flex flex-wrap gap-8">
			<div id="description" class="flex flex-col w-full">
				<div class="flex gap-2">
					<label class="label">
						<span>{$translation.settings.acount_section.username()}</span>
						<input disabled class="input p-2" type="text" placeholder="jonesis" />
					</label>
					<label class="label">
						<span>{$translation.settings.acount_section.password()}</span>
						<input disabled class="input p-2" type="password" placeholder="●●●●●●●●" />
					</label>
				</div>
			</div>
			<div id="socials" class="flex flex-col gap-2">
				<h4>{$translation.settings.acount_section.socials()}</h4>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="input-group-shim">https://www.instagram.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="jonesisfroellerix"
					/>
					<button class="variant-filled-secondary"
						>{$translation.settings.acount_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="input-group-shim">https://www.youtube.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="jonesisfroellerix"
					/>
					<button class="variant-filled-secondary"
						>{$translation.settings.acount_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="input-group-shim">https://www.twitter.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="jonesisfroellerix"
					/>
					<button class="variant-filled-secondary"
						>{$translation.settings.acount_section.edit()}</button
					>
				</div>
				<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
					<div class="input-group-shim">https://www.tiktok.com/</div>
					<input
						disabled
						class="outline-none text-center p-2"
						type="text"
						placeholder="jonesisfroellerix"
					/>
					<button class="variant-filled-secondary"
						>{$translation.settings.acount_section.edit()}</button
					>
				</div>
			</div>
			<div id="description" class="flex flex-col gap-2">
				<h4>{$translation.settings.acount_section.description()}</h4>
				<textarea disabled class="textarea p-2 w-96" rows="4" placeholder="some text..." />
			</div>
		</div>
	</div>
</section>
