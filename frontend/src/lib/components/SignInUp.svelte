<script lang="ts">
	/* --- INIT --- */
	// Env Vars
	import { dev } from '$app/environment';

	// Backend Api
	import Api from '$api/api';

	// Stores
	import { loginState, jwt, user } from '$store/account';

	// Scripts
	import accountCfg from '$script/accountStorage';

	// Translation
	import translation from '$translation/i18n-svelte';
	import { locale } from '$translation/i18n-svelte';

	// Form Validation
	import { z } from 'zod';

	/* -- Form Data -- */
	const userData = {
		username: '',
		password: ''
	};

	let passwordConfirmation = '';

	/* -- Test/Developement Form Data -- */
	if (dev) {
		// insert test account into form, if in developement mode
		userData.username = 'Jonesisfroellerix';
		userData.password = 'Password2$';
		passwordConfirmation = userData.password;
	}

	/* -- Form Input Errors -- */
	$: username_error = 'undefined';
	$: password_error = 'undefined';
	$: password_confirm_error = 'undefined';

	/* -- Form Validation Schema -- */
	const userDataSchemaGerman = z.object({
		username: z
			.string({ required_error: 'Benutzername ist erforderlich' })
			.trim()
			.min(2, { message: 'Der Benutzername muss mindestens 2 Zeichen lang sein' })
			.max(25, { message: 'Der Benutzername darf maximal 25 Zeichen lang sein' })
			.regex(RegExp('^(?=.*[A-Za-z])(?!.*[-_]{2})[A-Za-z0-9_-]*$'), {
				message:
					'Mindestens 1 Buchstabe, Zahlen sind ebenfalls erlaubt. Bindestrich (-) und Unterstrich (_) sind erlaubt, dürfen aber nicht direkt aufeinander folgen.'
			}),
		password: z
			.string({ required_error: 'Passwort ist erforderlich' })
			.trim()
			.min(8)
			.max(25)
			.regex(RegExp('^(?=.*[A-Z])(?=.*[a-z])(?=.*\\d)(?=.*[?!#@$%&*])[A-Za-z0-9?!#@$%&*]+$'), {
				message:
					'Mindestens 1 Symbol/Sonderzeichen aus: ?!#@$%&*, mindestens 1 Zahl, mindestens 1 Großbuchstabe, mindestens 1 Kleinbuchstabe.'
			}),
		password_confirmation: z
			.string({ required_error: 'Passwort-Wiederholung ist erforderlich' })
			.trim()
			.min(8)
			.max(25)
			.regex(RegExp('^(?=.*[A-Z])(?=.*[a-z])(?=.*\\d)(?=.*[?!#@$%&*])[A-Za-z0-9?!#@$%&*]+$'), {
				message:
					'Mindestens 1 Symbol/Sonderzeichen aus: ?!#@$%&*, mindestens 1 Zahl, mindestens 1 Großbuchstabe, mindestens 1 Kleinbuchstabe.'
			})
			.refine(() => userData.password === passwordConfirmation, {
				message: 'Das wiederholte Passwort muss mit dem Passwort übereinstimmen'
			})
	});

	const userDataSchemaEnglish = z.object({
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

	$: userDataSchema = $locale === 'de' ? userDataSchemaGerman : userDataSchemaEnglish;

	// JS-Framework/Library
	import { onMount } from 'svelte';

	// CSS-Framework/Library
	import { TabGroup, Tab } from '@skeletonlabs/skeleton';
	import { toastStore, modalStore } from '@skeletonlabs/skeleton';

	// Components
	import Popups from '$component/Popups.svelte';
	let popups: Popups; // popups in Popups.svelte

	/* --- LOGIC --- */
	/* Form */
	const cBase = 'card p-4 w-modal shadow-xl space-y-4';
	const cForm = 'border border-surface-500 p-4 space-y-4 rounded-container-token';

	export let parent: any;
	let signInOrUp: number = 0;

	/* -- Form Submit -- */
	/* Database Connection */
	async function signIn(username: string, password: string) {
		toastStore.trigger(popups.loggingIn_info);
		return await Api.auth(username, password);
	}

	async function signUp(username: string, password: string) {
		toastStore.trigger(popups.loggingIn_info);
		return await Api.auth(username, password);
	}

	async function onFormSubmit(userMayExist: boolean) {
		// VidSlide-account
		let response;
		if (userMayExist) {
			response = await signIn(userData.username, userData.password);
		} else {
			response = await signUp(userData.username, userData.password);
		}

		let status = await setAccountVariables(response);

		if (popups) {
			if (status) {
				toastStore.trigger(popups.loggedIn_success);
				$loginState = true;
				await saveAccountData();
				setTimeout(() => modalStore.close(), 200);
			} else if (status == false) {
				toastStore.trigger(popups.registered_success);
				$loginState = true;
				await saveAccountData();
				setTimeout(() => modalStore.close(), 200);
			} else {
				toastStore.trigger(popups.failed_to_authenticate);
			}
		}
	}

	async function saveAccountData() {
		let cfg = {
			loginState: $loginState,
			jwt: $jwt,
			user: $user
		};

		return accountCfg.save(cfg);
	}

	async function setAccountVariables(response: Promise) {
		$jwt = response?.token ?? null;
		$user = response?.user ?? null;

		return response?.accountExisted ?? null;
	}

	/* -- Form Validation -- */
	async function validateForm(close = false) {
		let inputParseResult = userDataSchema.safeParse({
			username: userData.username,
			password: userData.password,
			password_confirmation: passwordConfirmation
		});

		if (!inputParseResult.success) {
			let formattedError = inputParseResult.error.format();

			// console.log(formattedError);
			// toastStore.trigger(popups.loggingIn_warning);

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

	$: usernamePlaceholder = $locale === 'de' ? 'Username eingeben...' : 'Enter username...';
	$: passwordPlaceholder = $locale === 'de' ? 'Passwort eingeben...' : 'Enter password...';
	$: passwordConfirmationPlaceholder = $locale === 'de' ? 'Passwort erneut eingeben...' : 'Enter password again...'; // prettier-ignore

	onMount(async () => {
		validateForm(); // validate on load to work with test values
	});
</script>

{#key $translation}
	<Popups bind:this={popups} />
{/key}

<div class="modal-example-form {cBase}">
	<!-- debugging: -->
	<!-- <pre>{JSON.stringify(userData, null, 2)}</pre> -->

	<TabGroup>
		<Tab bind:group={signInOrUp} name="tab1" value={0}>{$translation.SignInUp.signUp()}</Tab>
		<Tab bind:group={signInOrUp} name="tab2" value={1}>{$translation.SignInUp.signIn()}</Tab>
		<!-- Tab Panels --->
		<svelte:fragment slot="panel">
			<form class="modal-form {cForm}">
				<label class="label">
					<span>{$translation.SignInUp.username()}</span>
					<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
						<div
							class="input-group-shim {username_error == 'undefined'
								? 'input-warning'
								: username_error == 'null'
								? 'input-success'
								: 'input-error'}"
						>
							<iconify-icon class="cursor-pointer flex items-center" icon="mdi:account" />
						</div>
						<input
							class="input p-2 rounded-l-none outline-none hover:outline-none {username_error ==
							'undefined'
								? 'input-warning'
								: username_error == 'null'
								? 'input-success'
								: 'input-error'}"
							type="text"
							id="username"
							bind:value={userData.username}
							on:input={() => validateForm()}
							placeholder={usernamePlaceholder}
						/>
					</div>
					{#if username_error != 'null'}
						<p
							class="text-center {username_error == 'undefined'
								? 'text-warning-800 dark:text-warning-100'
								: 'text-error-800 dark:text-error-100'}"
						>
							{username_error == 'undefined' ? 'required' : username_error}
						</p>
					{/if}
				</label>
				<label class="label">
					<span>{$translation.SignInUp.password()}</span>
					<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
						<div
							class="input-group-shim {password_error == 'undefined'
								? 'input-warning'
								: password_error == 'null'
								? 'input-success'
								: 'input-error'}"
						>
							<iconify-icon class="cursor-pointer flex items-center" icon="mdi:key" />
						</div>
						<input
							class="input p-2 rounded-l-none outline-none hover:outline-none {password_error ==
							'undefined'
								? 'input-warning'
								: password_error == 'null'
								? 'input-success'
								: 'input-error'}"
							type="password"
							id="password"
							bind:value={userData.password}
							on:input={() => validateForm()}
							placeholder={passwordPlaceholder}
						/>
					</div>
					{#if password_error != 'null'}
						<p
							class="text-center {password_error == 'undefined'
								? 'text-warning-800 dark:text-warning-100'
								: 'text-error-800 dark:text-error-100'}"
						>
							{password_error == 'undefined' ? 'required' : password_error}
						</p>
					{/if}
				</label>
				{#if signInOrUp === 0}
					<label class="label">
						<span>{$translation.SignInUp.password_retype()}</span>
						<div class="input-group input-group-divider grid-cols-[auto_1fr_auto]">
							<div
								class="input-group-shim {password_confirm_error == 'undefined'
									? 'input-warning'
									: password_confirm_error == 'null'
									? 'input-success'
									: 'input-error'}"
							>
								<iconify-icon class="cursor-pointer flex items-center" icon="mdi:key" />
							</div>
							<input
								class="input p-2 rounded-l-none outline-none hover:outline-none {password_confirm_error ==
								'undefined'
									? 'input-warning'
									: password_confirm_error == 'null'
									? 'input-success'
									: 'input-error'}"
								type="password"
								id="password-retype"
								bind:value={passwordConfirmation}
								on:input={() => validateForm(false)}
								placeholder={passwordConfirmationPlaceholder}
							/>
						</div>
						{#if password_confirm_error != 'null'}
							<p
								class="text-center {password_confirm_error == 'undefined'
									? 'text-warning-800 dark:text-warning-100'
									: 'text-error-800 dark:text-error-100'}"
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
		<button class="btn {parent.buttonNeutral}" on:click={parent.onClose}
			>{$translation.SignInUp.cancel()}</button
		>
		{#if signInOrUp == 0}
			{#if username_error != 'null' || password_error != 'null' || password_confirm_error != 'null' || username_error.includes('undefined')  || password_error.includes('undefined') || password_confirm_error.includes('undefined')}
			<button class="btn {parent.buttonPositive}" disabled>{$translation.SignInUp.signUp()}</button>
			{:else}
			<button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit(false)}
				>{$translation.SignInUp.signUp()}</button
			>
			{/if}
		{:else if username_error != 'null' || password_error != 'null' || username_error.includes('undefined') || password_error.includes('undefined') }
		<button class="btn {parent.buttonPositive}" disabled>{$translation.SignInUp.signIn()}</button>
		{:else} 
		<button class="btn {parent.buttonPositive}" on:click={() => onFormSubmit(true)}
			>{$translation.SignInUp.signIn()}</button
		>
		{/if}
	</footer>
</div>
