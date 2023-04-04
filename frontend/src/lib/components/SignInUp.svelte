<script lang="ts">
	/* --- INIT --- */
	// Backend Api
	import Api from '$api/api';

	// Form Validation
	import { z } from 'zod';

	/* -- Form Data -- */
	const userData = {
		username: 'Jonesisfroellerix',
		password: 'Password2$'
	};

	let passwordConfirmation = 'Password2$';

	const userDataSchema = z.object({
		username: z
			.string({ required_error: 'username is required' })
			.trim()
			.min(2, { message: 'username must be at least 2 characters long' })
			.max(25, { message: 'username must be less than 26 characters long' })
			.regex(RegExp('^(?=.*[A-Za-z])(?!.*[-_]{2})[A-Za-z0-9_-]*$'), {
				message:
					'min. 1 char, numbers are valid too, - and _ are allowed but not 2x next to each other'
			}) /* min 1 char, - and _ are allowed but not 2x next to each other, numbers are valid too */,
		password: z
			.string({ required_error: 'password is required' })
			.trim()
			.min(8)
			.max(25)
			.regex(RegExp('^(?=.*[A-Z])(?=.*[a-z])(?=.*d)(?=.*[?!#@$%&*])[A-Za-z0-9?!#@$%&*]+$'), {
				message:
					'min. 1 symbol/special char out of: ?!#@$%&*, min. 1 digit, min. 1 uppercase char, min. 1 lowercase char'
			}) /* min 1 symbol/special char, 1 digit, 1 uppercase char, 1 lowercase char */,
		password_confirmation: z
			.string({ required_error: 'password repeation is required' })
			.trim()
			.min(8)
			.max(25)
			.regex(RegExp('^(?=.*[A-Z])(?=.*[a-z])(?=.*d)(?=.*[?!#@$%&*])[A-Za-z0-9?!#@$%&*]+$'), {
				message:
					'min. 1 symbol/special char out of: ?!#@$%&*, min. 1 digit, min. 1 uppercase char, min. 1 lowercase char'
			})
			.refine(() => userData.password === passwordConfirmation, {
				message: 'retyped password must match password'
			}) /* min 1 symbol/special char, 1 digit, 1 uppercase char, 1 lowercase char */
	});

	$: username_error = 'undefined';
	$: password_error = 'undefined';
	$: password_confirm_error = 'undefined';

	// JS-Framework/Library
	import { onMount } from 'svelte';

	// CSS-Framework/Library
	import { TabGroup, Tab } from '@skeletonlabs/skeleton';

	// Stores
	import { modalStore } from '@skeletonlabs/skeleton';

	/* --- LOGIC --- */
	/* Form */
	const cBase = 'card p-4 w-modal shadow-xl space-y-4';
	const cForm = 'border border-surface-500 p-4 space-y-4 rounded-container-token';

	export let parent: any;
	let signInOrUp: number = 0;

	/* -- Form Submit -- */
	function onFormSubmit(userMayExist: boolean): void {
		if ($modalStore[0].response) $modalStore[0].response(userData);
		validateForm(true);

		if (userMayExist) {
			signIn(userData.username, userData.password).then((response) => {
				console.log(response);
			});
		} else {
			signIn(userData.username, userData.password).then((response) => {
				console.log(response);
			});
		}
	}

	function validateForm(close = false) {
		let inputParseResult = userDataSchema.safeParse({
			username: userData.username,
			password: userData.password,
			password_confirmation: passwordConfirmation
		});

		if (!inputParseResult.success) {
			let formattedError = inputParseResult.error.format();

			console.log(formattedError);

			username_error = formattedError.username?._errors[0] ?? 'null';
			password_error = formattedError.password?._errors[0] ?? 'null';
			password_confirm_error = formattedError.password_confirmation?._errors[0] ?? 'null';
		} else {
			username_error = 'null';
			password_error = 'null';
			password_confirm_error = 'null';

			if (close) {
				setTimeout(() => modalStore.close(), 200);
			}
		}
	}

	onMount(async () => {
		validateForm();
	});

	/* Database Connection */
	async function signIn(username: string, password: string) {
		return await Api.get("user", "1");
	}

	async function signUp(username: string, password: string) {
		return await Api.auth(username, password);
	}
</script>

<div class="modal-example-form {cBase}">
	<!-- debugging: -->
	<!-- <pre>{JSON.stringify(userData, null, 2)}</pre> -->

	<TabGroup>
		<Tab bind:group={signInOrUp} name="tab1" value={0}>SignUp</Tab>
		<Tab bind:group={signInOrUp} name="tab2" value={1}>SignIn</Tab>
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
							class="input p-2 rounded-l-none {username_error == 'undefined'
								? 'input-warning'
								: username_error == 'null'
								? 'input-success'
								: 'input-error'}"
							type="text"
							id="username"
							bind:value={userData.username}
							on:input={() => validateForm()}
							placeholder="Enter username..."
						/>
					</div>
					{#if username_error != 'null'}
						<p
							class="text-center {username_error == 'undefined'
								? 'text-warning-100'
								: 'text-error-100'}"
						>
							{username_error == 'undefined' ? 'required' : username_error}
						</p>
					{/if}
				</label>
				<label class="label">
					<span>Password</span>
					<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
						<div class="input-group-shim">
							<iconify-icon class="cursor-pointer flex items-center" icon="mdi:key" />
						</div>
						<input
							class="input p-2 rounded-l-none {password_error == 'undefined'
								? 'input-warning'
								: password_error == 'null'
								? 'input-success'
								: 'input-error'}"
							type="password"
							id="password"
							bind:value={userData.password}
							on:input={() => validateForm()}
							placeholder="Enter password..."
						/>
					</div>
					{#if password_error != 'null'}
						<p
							class="text-center {password_error == 'undefined'
								? 'text-warning-100'
								: 'text-error-100'}"
						>
							{password_error == 'undefined' ? 'required' : password_error}
						</p>
					{/if}
				</label>
				{#if signInOrUp === 0}
					<label class="label">
						<span>Retype Password</span>
						<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
							<div class="input-group-shim">
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:key" />
							</div>
							<input
								class="input p-2 rounded-l-none {password_confirm_error == 'undefined'
									? 'input-warning'
									: password_confirm_error == 'null'
									? 'input-success'
									: 'input-error'}"
								type="password"
								id="password-retype"
								bind:value={passwordConfirmation}
								on:input={() => validateForm(false)}
								placeholder="Enter password again..."
							/>
						</div>
						{#if password_confirm_error != 'null'}
							<p
								class="text-center {password_confirm_error == 'undefined'
									? 'text-warning-100'
									: 'text-error-100'}"
							>
								{password_confirm_error == 'undefined' ? 'required' : password_confirm_error}
							</p>
						{/if}
					</label>
				{/if}
			</form>
		</svelte:fragment>
	</TabGroup>
	<!-- prettier-ignore -->
	<footer class="modal-footer {parent.regionFooter}">
		<button class="btn {parent.buttonNeutral}" on:click={parent.onClose}>{parent.buttonTextCancel}</button>
		{#if signInOrUp == 1}
			{#if username_error != "null" || password_error != "null" || password_confirm_error != "null" || username_error == "undefined" || password_error == "undefined" || password_confirm_error == "undefined"}
				<button class="btn {parent.buttonPositive}" disabled>Log In</button>
			{:else}
				<button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit(true)}>Log In</button>
			{/if}
		{:else}
			{#if username_error != "null" || password_error != "null" || password_confirm_error != "null" || username_error == "undefined" || password_error == "undefined" || password_confirm_error == "undefined"}
			<button class="btn {parent.buttonPositive}" disabled>Sign Up</button>
			{:else}
			<button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit(false)}>Sign Up</button>
			{/if}
		{/if}
	</footer>
</div>
