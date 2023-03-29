<script lang="ts">
	// Api
	import Api from '$main/routes/api/api';

	async function test() {
		return await Api.get();
	}

	// Skeleton
	import { TabGroup, Tab } from '@skeletonlabs/skeleton';

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

	let userIsRegistered: number = 0;

	function onFormSubmit(): void {
		if ($modalStore[0].response) $modalStore[0].response(formData);
		console.log(formData);
		modalStore.close();
	}

	// Base Classes
	const cBase = 'card p-4 w-modal shadow-xl space-y-4';
	const cForm = 'border border-surface-500 p-4 space-y-4 rounded-container-token';
</script>

<div class="modal-example-form {cBase}">
	<!-- debugging: -->
	<!-- <pre>{JSON.stringify(formData, null, 2)}</pre> -->

	<TabGroup>
		<Tab bind:group={userIsRegistered} name="tab1" value={0}>SignUp</Tab>
		<Tab bind:group={userIsRegistered} name="tab2" value={1}>SignIn</Tab>
		<!-- Tab Panels --->
		<svelte:fragment slot="panel">
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
				{#if userIsRegistered === 0}
					<label class="label">
						<span>Retype Password</span>
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
				{/if}
			</form>
		</svelte:fragment>
	</TabGroup>
	<!-- prettier-ignore -->
	<footer class="modal-footer {parent.regionFooter}">
        <button class="btn {parent.buttonNeutral}" on:click={parent.onClose}>{parent.buttonTextCancel}</button>
		{#if true}
        <button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit()}>Log In</button>
		{:else}
		<button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit()}>Sign Up</button>
		{/if}
    </footer>
</div>
