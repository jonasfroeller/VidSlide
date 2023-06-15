<script lang="ts">
	/* --- INIT --- */
	// Form Validation
	import { z } from 'zod';

	// JS-Framework/Library
	import { onMount } from 'svelte';
	import { browser } from '$app/environment';

	// CSS-Framework/Library
	import { InputChip } from '@skeletonlabs/skeleton';

	// Translation
	import translation from '$translation/i18n-svelte';
	import { locale } from '$translation/i18n-svelte';

	/* --- LOGIC --- */
	const videoInfoSchemaGerman = z.object({
		title: z
			.string({ required_error: 'Titel ist erforderlich' })
			.min(3, { message: 'Der Titel muss mindestens 3 Zeichen lang sein' })
			.max(25, { message: 'Der Titel darf maximal 25 Zeichen lang sein' }),
		video_description: z
			.string()
			.max(500, { message: 'Die Beschreibung darf maximal 500 Zeichen lang sein' })
			.optional(),
		tags: z
			.array(z.string().max(15, { message: 'Ein tag darf nur bis zu 15 Zeichen lang sein' }))
			.max(6, { message: 'Pro Video max. 6 tags' })
			.optional()
	});

	const videoInfoSchemaEnglish = z.object({
		title: z
			.string({ required_error: 'Title is required' })
			.min(3, { message: 'The title must be at least 3 characters long' })
			.max(25, { message: 'The title must be a maximum of 25 characters long' }),
		video_description: z
			.string()
			.max(500, { message: 'The description must be a maximum of 500 characters long' })
			.optional(),
		tags: z
			.array(z.string().max(15, { message: 'A tag can only be up to 15 characters long' }))
			.max(6, { message: 'Up to 6 tags per video' })
			.optional()
	});

	const videoTagSchemaGerman = z.object({
		tags: z.string().max(15, { message: 'Ein tag darf nur bis zu 15 Zeichen lang sein' })
	});

	const videoTagSchemaEnglish = z.object({
		tags: z.string().max(15, { message: 'A tag can only be up to 15 characters long' })
	});

	$: videoInfoSchema = $locale === 'de' ? videoInfoSchemaGerman : videoInfoSchemaEnglish;
	$: videoTagSchema = $locale === 'de' ? videoTagSchemaGerman : videoTagSchemaEnglish;

	function validateTags(value: string): boolean {
		return videoTagSchema.safeParse({
			tags: value
		}).success;

		validateInput();
	}

	export const validateInput = () => {
		const notAllowedWithoutClosingTag = ['meta', 'link', 'base', 'input', 'embed', 'img'];
		const notAllowedWithClosingTag = [
			'body',
			'iframe',
			'object',
			'script',
			'audio',
			'video',
			'head',
			'html',
			'body',
			'title',
			'svg',
			'form',
			'button',
			'textarea',
			'select',
			'option',
			'map'
		];

		// escape malicious html tags (TODO: maybe attributes too?)
		if (
			notAllowedWithClosingTag.some((tag) => video_description.includes(tag)) ||
			notAllowedWithoutClosingTag.some((tag) => video_description.includes(tag))
		) {
			notAllowedWithClosingTag.forEach((tag) => {
				const regex = new RegExp(`<${tag}[^>]*>|<\/${tag}>`, 'g');
				video_description = video_description.replace(regex, (match) => {
					return match.replace(/</g, '&lt;').replace(/>/g, '&gt;');
				});
			});

			notAllowedWithoutClosingTag.forEach((tag) => {
				const regex = new RegExp(`<${tag}[^>]*>`, 'g');
				video_description = video_description.replace(regex, (match) => {
					return match.replace(/</g, '&lt;');
				});
			});
		}

		let inputParseResult = videoInfoSchema.safeParse({
			title: video_title,
			video_description: video_description,
			tags: video_tags
		});

		if (!inputParseResult.success) {
			let formattedError = inputParseResult.error.format();

			titleErrors = formattedError?.title?._errors ?? [];
			descriptionErrors = formattedError?.video_description?._errors ?? [];
			tagsErrors = formattedError?.tags?._errors ?? [];

			lockedState = true; // external
		} else {
			lockedState = false; // external
			titleErrors = [];
			descriptionErrors = [];
			tagsErrors = [];
		}
	};

	onMount(async () => {
		validateInput();
	});

	function applyFormat(tag) {
		if (browser) {
			const textarea = document.getElementById('editor');
			const { selectionStart, selectionEnd } = textarea;
			let selectedText = textarea.value.substring(selectionStart, selectionEnd);

			let formattedText = '';
			if (selectedText.includes(tag)) {
				const tagWithAttributes = new RegExp(`<${tag}[^>]*>|<\/${tag}>`, 'g'); // remove tag with attributes
				selectedText = selectedText
					.replaceAll(tagWithAttributes, '')
					.replaceAll(tagWithAttributes, '');
				formattedText = selectedText;
			} else {
				formattedText = `<${tag}>${selectedText}</${tag}>`;
			}

			const newText =
				textarea.value.substring(0, selectionStart) +
				formattedText +
				textarea.value.substring(selectionEnd);

			video_description = newText;
		}
	}

	export let lockedState: boolean; // sync with UploadVideo.svelte

	export let video_title: string = ''; // use name of video file as default
	export let video_description: string = '';
	export let video_tags: string[];

	let titleErrors = [];
	let descriptionErrors = [];
	let tagsErrors = [];
</script>

<label class="label">
	<span>{$translation.UploadVideo.step_02.video_title_label()}</span>
	<input
		bind:value={video_title}
		on:input={validateInput}
		class="input p-2 rounded-md outline-none hover:outline-none {titleErrors?.length > 0
			? 'input-error'
			: ''}"
		type="text"
		placeholder={$translation.UploadVideo.step_02.video_title()}
	/>
</label>
{#each titleErrors as error}
	<p class="text-center text-error-800 dark:text-error-100">
		{error}
	</p>
{/each}
<div
	class="flex items-center p-2 rounded-md bg-surface-50 dark:bg-surface-700 text-tertiary-700 dark:text-tertiary-400 border-2 border-tertiary-300"
>
	<button on:click={() => applyFormat('b')}
		><iconify-icon
			class="cursor-pointer flex items-center hover:opacity-75 text-2xl"
			icon="tabler:bold"
		/></button
	>
	<button on:click={() => applyFormat('i')}
		><iconify-icon
			class="cursor-pointer flex items-center hover:opacity-75 text-2xl"
			icon="tabler:italic"
		/></button
	>
	<button on:click={() => applyFormat('u')}
		><iconify-icon
			class="cursor-pointer flex items-center hover:opacity-75 text-2xl"
			icon="tabler:underline"
		/></button
	>
	<button on:click={() => applyFormat('s')}
		><iconify-icon
			class="cursor-pointer flex items-center hover:opacity-75 text-2xl"
			icon="tabler:strikethrough"
		/></button
	>
</div>
<div class="grid grid-cols-2 gap-4">
	<label class="label">
		<span>{$translation.UploadVideo.step_02.video_description_label()}</span>
		<textarea
			id="editor"
			bind:value={video_description}
			on:input={validateInput}
			class="textarea p-2 flex flex-1 outline-none hover:outline-none {descriptionErrors?.length > 0
				? 'input-error'
				: ''}"
			rows="4"
			placeholder={$translation.UploadVideo.step_02.video_description()}
		/>
	</label>
	<div class="bg-surface-50 dark:bg-surface-700 rounded-md p-2 break-all">
		{@html video_description}
	</div>
</div>
{#each descriptionErrors as error}
	<p class="text-center text-error-800 dark:text-error-100">
		{error}
	</p>
{/each}
<!-- svelte-ignore a11y-label-has-associated-control -->
<label class="label">
	<span>{$translation.UploadVideo.step_02.video_tags_label()}</span>
	<InputChip
		bind:value={video_tags}
		on:input={validateInput}
		validation={validateTags}
		name="chips"
		class="input p-2 outline-none hover:outline-none {tagsErrors?.length > 0 ? 'input-error' : ''}"
		placeholder={$translation.UploadVideo.step_02.video_tags()}
	/>
	{#each tagsErrors as error}
		<p class="text-center text-error-800 dark:text-error-100">
			{error}
		</p>
	{/each}
</label>
