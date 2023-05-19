<script>
	/* --- INIT --- */
	// JS-Framework/Library
	import { page } from '$app/stores';

	// Translation
	import { locale } from '$translation/i18n-svelte'; // current language
	import translation from '$translation/i18n-svelte'; // translations
	import LangSelect from '$component/LangSelect.svelte';
	import ThemeSelect from '$component/ThemeSelect.svelte';

	// Stores
	import { user } from '$store/account';

	/* --- LOGIC --- */
	export let variant = 'large';
	$: route = $page?.route?.id?.replace('/[lang]', '');
</script>

{#if variant === 'large'}
	<ul class="flex flex-col justify-between h-full relative">
		<ul class="flex flex-col gap-2 p-2">
			<li><h1 class="text-center">VidSlide</h1></li>
			<li>
				<a
					href="/{$locale}/home"
					data-sveltekit-preload-data="hover"
					type="button"
					class="btn text-3xl rounded-lg w-full flex justify-start {route === '/home' ||
					route === ''
						? 'variant-ghost-tertiary'
						: 'variant-ringed-secondary'}"
				>
					<span>
						{#if route === '/home' || route === ''}
							<iconify-icon class="cursor-pointer flex items-center" icon="material-symbols:home" />
						{:else}
							<iconify-icon
								class="cursor-pointer flex items-center"
								icon="material-symbols:home-outline"
							/>
						{/if}
					</span>
					<span>{$translation.Sidebar.home()}</span>
				</a>
			</li>
			<li>
				<a
					href="/{$locale}/search"
					data-sveltekit-preload-data="hover"
					type="button"
					class="btn text-3xl rounded-lg w-full flex justify-start {route === '/search'
						? 'variant-ghost-tertiary'
						: 'variant-ringed-secondary'}"
				>
					<span>
						{#if route === '/search'}
							<iconify-icon class="cursor-pointer flex items-center" icon="mdi:search-web" />
						{:else}
							<iconify-icon class="cursor-pointer flex items-center" icon="mdi:search" />
						{/if}
					</span>
					<span>{$translation.Sidebar.search()}</span>
				</a>
			</li>
			{#if $user?.USER_USERNAME}
				<li>
					<a
						href="/{$locale}/account"
						data-sveltekit-preload-data="hover"
						type="button"
						class="btn text-3xl rounded-lg w-full flex justify-start {route?.includes('account')
							? 'variant-ghost-tertiary'
							: 'variant-ringed-secondary'}"
					>
						<span>
							{#if route?.includes('account')}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account" />
							{:else}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account-outline" />
							{/if}
						</span>
						<span>{$translation.Sidebar.account()}</span>
					</a>
				</li>
			{/if}
			<li>
				<a
					href="/{$locale}/settings"
					data-sveltekit-preload-data="hover"
					type="button"
					class="btn text-3xl rounded-lg w-full flex justify-start {route === '/settings'
						? 'variant-ghost-tertiary'
						: 'variant-ringed-secondary'}"
				>
					<span>
						{#if route === '/settings'}
							<iconify-icon
								class="cursor-pointer flex items-center"
								icon="material-symbols:settings-rounded"
							/>
						{:else}
							<iconify-icon
								class="cursor-pointer flex items-center"
								icon="material-symbols:settings-outline-rounded"
							/>
						{/if}
					</span>
					<span>{$translation.Sidebar.settings()}</span>
				</a>
			</li>
		</ul>
		<ul class="flex flex-col gap-2 p-2">
			<li>
				<ThemeSelect />
			</li>
			<li>
				<LangSelect />
			</li>
		</ul>
	</ul>
{:else}
	<ul class="flex flex-col justify-between h-full relative">
		<ul class="flex flex-col gap-2 p-2">
			<li><h1 class="text-center">VS</h1></li>
			<li class="flex justify-center">
				<a
					href="/{$locale}/home"
					data-sveltekit-preload-data="hover"
					type="button"
					class="btn-icon text-3xl rounded-lg w-fit max-w-full {route === '/home' || route === ''
						? 'variant-ghost-tertiary'
						: 'variant-ringed-secondary'}"
				>
					<span class="p-2">
						{#if route === '/home' || route === ''}
							<iconify-icon class="cursor-pointer flex items-center" icon="material-symbols:home" />
						{:else}
							<iconify-icon
								class="cursor-pointer flex items-center"
								icon="material-symbols:home-outline"
							/>
						{/if}
					</span>
				</a>
			</li>
			<li class="flex justify-center">
				<a
					href="/{$locale}/search"
					data-sveltekit-preload-data="hover"
					type="button"
					class="btn-icon text-3xl rounded-lg w-fit max-w-full {route === '/search'
						? 'variant-ghost-tertiary'
						: 'variant-ringed-secondary'}"
				>
					<span class="p-2">
						{#if route === '/search'}
							<iconify-icon class="cursor-pointer flex items-center" icon="mdi:search-web" />
						{:else}
							<iconify-icon class="cursor-pointer flex items-center" icon="mdi:search" />
						{/if}
					</span>
				</a>
			</li>
			{#if $user?.USER_USERNAME}
				<li class="flex justify-center">
					<a
						href="/{$locale}/account"
						data-sveltekit-preload-data="hover"
						type="button"
						class="btn-icon text-3xl rounded-lg w-fit max-w-full {route?.includes('account')
							? 'variant-ghost-tertiary'
							: 'variant-ringed-secondary'}"
					>
						<span class="p-2">
							{#if route?.includes('account')}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account" />
							{:else}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account-outline" />
							{/if}
						</span>
					</a>
				</li>
			{/if}
			<li class="flex justify-center">
				<a
					href="/{$locale}/settings"
					data-sveltekit-preload-data="hover"
					type="button"
					class="btn-icon text-3xl rounded-lg w-fit max-w-full {route === '/settings'
						? 'variant-ghost-tertiary'
						: 'variant-ringed-secondary'}"
				>
					<span class="p-2">
						{#if route === '/settings'}
							<iconify-icon
								class="cursor-pointer flex items-center"
								icon="material-symbols:settings-rounded"
							/>
						{:else}
							<iconify-icon
								class="cursor-pointer flex items-center"
								icon="material-symbols:settings-outline-rounded"
							/>
						{/if}
					</span>
				</a>
			</li>
		</ul>
		<ul class="flex flex-col gap-2 p-2">
			<li>
				<ThemeSelect {variant} />
			</li>
			<li>
				<LangSelect {variant} />
			</li>
		</ul>
	</ul>
{/if}
