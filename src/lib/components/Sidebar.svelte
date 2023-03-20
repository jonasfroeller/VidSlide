<script>
	// Svelte
	import { page } from '$app/stores';

	// Translation
	import { locale } from '$translation/i18n-svelte'; // current language
	import translation from '$translation/i18n-svelte'; // translations
	import LangSelect from './LangSelect.svelte';
	import ThemeSelect from './ThemeSelect.svelte';

	export let variant = 'large';

	$: route = $page?.route?.id?.replace('/[lang]', '');
</script>

{#if variant === 'large'}
	<ul class="flex flex-col justify-between h-full relative">
		<ul class="flex flex-col gap-2 p-2">
			<li><h1 class="text-center">VidSlide</h1></li>
			<li>
				<a href="/{$locale}/home" class="unstyled">
					<button
						type="button"
						class="btn text-3xl rounded-lg w-full {route === '/home' || route === ''
							? 'variant-ghost-tertiary'
							: 'variant-ringed-secondary'}"
					>
						<span>
							{#if route === '/home' || route === ''}
								<iconify-icon
									class="cursor-pointer flex items-center"
									icon="material-symbols:home"
								/>
							{:else}
								<iconify-icon
									class="cursor-pointer flex items-center"
									icon="material-symbols:home-outline"
								/>
							{/if}
						</span>
						<span>{$translation.Sidebar.home()}</span>
					</button>
				</a>
			</li>
			<li>
				<a href="/{$locale}/search" class="unstyled">
					<button
						type="button"
						class="btn text-3xl rounded-lg w-full {route === '/search'
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
					</button>
				</a>
			</li>
			<li>
				<a href="/{$locale}/account" class="unstyled">
					<button
						type="button"
						class="btn text-3xl rounded-lg w-full {route === '/account'
							? 'variant-ghost-tertiary'
							: 'variant-ringed-secondary'}"
					>
						<span>
							{#if route === '/account'}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account" />
							{:else}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account-outline" />
							{/if}
						</span>
						<span>{$translation.Sidebar.account()}</span>
					</button>
				</a>
			</li>
			<li>
				<a href="/{$locale}/settings" class="unstyled">
					<button
						type="button"
						class="btn text-3xl rounded-lg w-full {route === '/settings'
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
					</button>
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
				<a href="/{$locale}/home" class="unstyled">
					<button
						type="button"
						class="btn-icon text-3xl rounded-lg w-fit max-w-full {route === '/home' || route === ''
							? 'variant-ghost-tertiary'
							: 'variant-ringed-secondary'}"
					>
						<span>
							{#if route === '/home' || route === ''}
								<iconify-icon
									class="cursor-pointer flex items-center"
									icon="material-symbols:home"
								/>
							{:else}
								<iconify-icon
									class="cursor-pointer flex items-center"
									icon="material-symbols:home-outline"
								/>
							{/if}
						</span>
					</button>
				</a>
			</li>
			<li class="flex justify-center">
				<a href="/{$locale}/search" class="unstyled">
					<button
						type="button"
						class="btn-icon text-3xl rounded-lg w-fit max-w-full {route === '/search'
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
					</button>
				</a>
			</li>
			<li class="flex justify-center">
				<a href="/{$locale}/account" class="unstyled">
					<button
						type="button"
						class="btn-icon text-3xl rounded-lg w-fit max-w-full {route === '/account'
							? 'variant-ghost-tertiary'
							: 'variant-ringed-secondary'}"
					>
						<span>
							{#if route === '/account'}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account" />
							{:else}
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account-outline" />
							{/if}
						</span>
					</button>
				</a>
			</li>
			<li class="flex justify-center">
				<a href="/{$locale}/settings" class="unstyled">
					<button
						type="button"
						class="btn-icon text-3xl rounded-lg w-fit max-w-full {route === '/settings'
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
					</button>
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
