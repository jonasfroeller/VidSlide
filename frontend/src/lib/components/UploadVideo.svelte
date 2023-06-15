<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';

	// Backend API
	import Api from '$api/api';
	import { Route } from '$api/api-routes';

	// Components
	import VideoInfoInputFields from '$component/VideoInfoInputFields.svelte';
	import Popups from '$component/Popups.svelte';
	let popups; // popups in Popups.svelte

	// CSS-Framework/Library
	import { Stepper, Step } from '@skeletonlabs/skeleton';
	import { FileDropzone } from '@skeletonlabs/skeleton';
	import { modalStore, toastStore } from '@skeletonlabs/skeleton';

	// Form Validation
	import { z } from 'zod';

	/* --- LOGIC --- */
	let lockedState: boolean = true;
	let files: FileList;
	let video_title: string;
	let video_description: string;
	let video_tags: string[];

	const allowedVideoTypes = [
		'video/mp4',
		'video/mpeg',
		'video/quicktime',
		'video/x-matroska',
		'video/webm',
		'video/x-msvideo',
		'video/x-ms-wmv',
		'video/x-flv',
		'video/3gpp'
	];

	const fileSchema = z.object({
		size: z.number().max(25_000_000), // 25MB
		type: z.enum(allowedVideoTypes) // common video types
	});

	function onFileInputHandler(e: Event): void {
		if (files) {
			// TODO replace with zod schema above
			const video_size = files[files.length - 1].size;
			const video_type = files[files.length - 1].type;
			const video_name = files[files.length - 1].name;

			if (
				fileSchema.safeParse({
					size: video_size,
					type: video_type
				}).success
			) {
				lockedState = false;
				video_title = video_name;
			} else {
				toastStore.trigger(popups.filetype_not_allowed);
			}
		}
	}

	async function onCompleteHandler(e: Event): void {
		// TODO: add tags: video_tags
		const params = [
			{
				attribute_name: 'VIDEO_MEDIA',
				attribute: Route.REQUEST_METHOD.POST.medium.video.params.VIDEO_MEDIA(files)
			},
			{
				attribute_name: 'VIDEO_TITLE',
				attribute: Route.REQUEST_METHOD.POST.medium.video.params.VIDEO_TITLE(video_title)
			},
			{
				attribute_name: 'VIDEO_DESCRIPTION',
				attribute:
					Route.REQUEST_METHOD.POST.medium.video.params.VIDEO_DESCRIPTION(video_description)
			}
		];

		if (video_tags.length > 0) {
			params.push({
				attribute_name: 'VIDEO_TAGS',
				attribute: Route.REQUEST_METHOD.POST.medium.video.params.VIDEO_TAGS(video_tags)
			});
		}

		const response = await Api.post(params, Route.REQUEST_METHOD.POST.medium.video.root);

		if (response?.success) {
			modalStore.close();
		} else {
			toastStore.trigger(popups.failed_to_upload_video);
		}
	}

	function handleChange(newValue) {
		lockedState = newValue;
	}
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<div class="card p-4 w-modal shadow-xl space-y-4">
	<Stepper
		class="border border-surface-500 p-4 space-y-4 rounded-container-token"
		on:complete={onCompleteHandler}
		buttonNextLabel={$translation.UploadVideo.next()}
		buttonBackLabel={$translation.UploadVideo.back()}
		buttonCompleteLabel={$translation.UploadVideo.complete()}
		stepTerm={$translation.UploadVideo.step()}
		buttonComplete={'variant-ghost-tertiary'}
	>
		<Step locked={lockedState}>
			<svelte:fragment slot="header">{$translation.UploadVideo.step_01.title()}</svelte:fragment>
			<FileDropzone name="files" bind:files on:change={onFileInputHandler}>
				<svelte:fragment slot="lead"
					><iconify-icon
						class="cursor-pointer"
						width="30"
						height="30"
						icon="mdi:file-upload"
					/></svelte:fragment
				>
				<svelte:fragment slot="message"
					>{$translation.UploadVideo.step_01.video_dropzone()}</svelte:fragment
				>
				<svelte:fragment slot="meta">MP4, MPEG, MOV, MKV, WebM, AVI, WMV, FLV, 3GP</svelte:fragment>
			</FileDropzone>
			<svelte:fragment slot="navigation">
				<button class="btn variant-ghost-tertiary" on:click={() => modalStore.close()}
					>{$translation.Popups.modal.close()}</button
				>
			</svelte:fragment>
		</Step>
		<Step locked={lockedState}>
			<svelte:fragment slot="header">{$translation.UploadVideo.step_02.title()}</svelte:fragment>
			<VideoInfoInputFields
				bind:lockedState
				bind:video_title
				bind:video_description
				bind:video_tags
				on:change={handleChange}
			/>
		</Step>
	</Stepper>
</div>
