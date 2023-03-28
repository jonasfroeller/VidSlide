<script lang="ts">
	// Props
	/** Exposes parent props to this component. */
	export let parent: any;
	// Stores
	import { modalStore } from '@skeletonlabs/skeleton';
	// Form Data
	const formData = {
		username: 'Jonesis Froellerix',
		password: 'password'
	};

	function onFormSubmit(): void {
		if ($modalStore[0].response) $modalStore[0].response(formData);
		modalStore.close();
	}

	// Base Classes
	const cBase = 'card p-4 w-modal shadow-xl space-y-4';
	const cHeader = 'text-2xl font-bold';
	const cForm = 'border border-surface-500 p-4 space-y-4 rounded-container-token';
</script>

<div class="modal-example-form {cBase}">
	<header class={cHeader}>{$modalStore[0]?.title ?? 'SignIn/Up'}</header>
	<article>
		{$modalStore[0]?.body ?? 'If you do not have an account yet it will be created automatically.'}
	</article>
	<!-- debugging: -->
	<!-- <pre>{JSON.stringify(formData, null, 2)}</pre> -->
	<form class="modal-form {cForm}">
		<label class="label">
			<span>Username</span>
			<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
				<div class="input-group-shim">
					<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account" />
				</div>
				<input
					class="input p-2 rounded-l-none"
					type="text"
					bind:value={formData.username}
					placeholder="Enter username..."
				/>
			</div>
		</label>
		<label class="label">
			<span>Password</span>
			<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
				<div class="input-group-shim">
					<iconify-icon class="cursor-pointer flex items-center" icon="mdi:key" />
				</div>
				<input
					class="input p-2 rounded-l-none"
					type="password"
					bind:value={formData.password}
					placeholder="Enter password..."
				/>
			</div>
		</label>
	</form>
	<!-- prettier-ignore -->
	<footer class="modal-footer {parent.regionFooter}">
        <button class="btn {parent.buttonNeutral}" on:click={parent.onClose}>{parent.buttonTextCancel}</button>
        <button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit()}>Log In</button>
		<button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit()}>Sign Up</button>
    </footer>
</div>
