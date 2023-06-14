<script lang="ts">
	/* --- INIT --- */
	// CSS-Framework/Library
	import { modalStore } from '@skeletonlabs/skeleton';

	// Components
	import VideoInfoInputFields from '$component/VideoInfoInputFields.svelte';

	/* --- LOGIC --- */
</script>

<div class="card p-4 w-modal shadow-xl space-y-4">
	<div class="grid grid-cols-[1fr_auto] gap-2">
		<div class="flex flex-col gap-2">
			<VideoInfoInputFields />
		</div>

		<div class="btn-group-vertical variant-soft-surface">
			<button
				class="variant-soft-warning hover:variant-soft-warning"
				on:click={() => modalStore.close()}
				><iconify-icon class="cursor-pointer flex items-center text-2xl" icon="mdi:close" /></button
			>
			<button class="variant-soft-error hover:variant-soft-error">
				<iconify-icon class="cursor-pointer flex items-center text-2xl" icon="mdi:delete" /></button
			>
			<button class="variant-soft-success hover:variant-soft-success">
				<iconify-icon
					class="cursor-pointer flex items-center text-2xl"
					icon="material-symbols:save"
				/></button
			>
		</div>
	</div>
</div>
