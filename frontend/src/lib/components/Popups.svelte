<script lang="ts">
	// Translation
	import translation from '$translation/i18n-svelte'; // translations

	// Stores
	import { loginState } from '$store/account';

	// JS-Framework/Library
	import { browser } from '$app/environment';

	import type { ModalSettings, ToastSettings } from '@skeletonlabs/skeleton';
	import { modalStore, toastStore } from '@skeletonlabs/skeleton';

	// Popups
	/* Confirmation Modal */
	let message_button_confirm = $translation.Popups.modal.confirm();
	let message_button_cancel = $translation.Popups.modal.cancel();

	let message_confirmLogIn_title = $translation.Popups.modal.confirmLogIn.title();
	let message_confirmLogIn_body = $translation.Popups.modal.confirmLogIn.body();
	export const confirmLogIn: ModalSettings = {
		type: 'confirm',
		title: message_confirmLogIn_title,
		body: message_confirmLogIn_body,
		buttonTextConfirm: message_button_confirm,
		buttonTextCancel: message_button_cancel,
		response: (r: boolean) => (r ? openLoginModal() : console.log('declined opening form'))
	};

	let message_confirmLogOut_title = $translation.Popups.modal.confirmLogOut.title();
	let message_confirmLogOut_body = $translation.Popups.modal.confirmLogOut.body();
	export const confirmLogOut: ModalSettings = {
		type: 'confirm',
		title: message_confirmLogOut_title,
		body: message_confirmLogOut_body,
		buttonTextConfirm: message_button_confirm,
		buttonTextCancel: message_button_cancel,
		response: (r: boolean) => (r ? signOut() : console.log('declined log out'))
	};

	/* Form */
	export const signInUpForm: ModalSettings = {
		type: 'component',
		component: 'signupModalComponent'
	};

	/* Notifications */
	// logIn
	let message_loggingIn_info = $translation.Popups.toast.loggingIn_info();
	export const loggingIn_info: ToastSettings = {
		message: message_loggingIn_info,
		background: 'variant-ghost-primary'
	};

	let message_loggedIn_success = $translation.Popups.toast.loggedIn_success();
	export const loggedIn_success: ToastSettings = {
		message: message_loggedIn_success,
		background: 'variant-ghost-success'
	};

	let message_registered_success = $translation.Popups.toast.registered__success();
	export const registered_success: ToastSettings = {
		message: message_registered_success,
		background: 'variant-ghost-success'
	};

	let message_loggingIn_warning = $translation.Popups.toast.loggingIn_warning(); // input wrong
	export const loggingIn_warning: ToastSettings = {
		message: message_loggingIn_warning,
		background: 'variant-ghost-warning'
	};

	let message_loggingIn_error = $translation.Popups.toast.loggingIn_error(); // backend/database connection problem
	export const loggingIn_error: ToastSettings = {
		message: message_loggingIn_error,
		background: 'variant-ghost-error'
	};

	// logOut
	let message_loggingOut_info = $translation.Popups.toast.loggingOut_info();
	export const loggingOut_info: ToastSettings = {
		message: message_loggingOut_info,
		background: 'variant-ghost-primary'
	};

	let message_loggedOut_success = $translation.Popups.toast.loggedOut_success();
	export const loggedOut_success: ToastSettings = {
		message: message_loggedOut_success,
		background: 'variant-ghost-success'
	};

	// config
	let message_configSaved_success = $translation.Popups.toast.configSaved_success();
	export const configSaved_success: ToastSettings = {
		message: message_configSaved_success,
		background: 'variant-ghost-success'
	};

	// copyURL
	let message_copiedURLtoClipboard_success =
		$translation.Popups.toast.copiedURL_toClipboard_success();
	export const copiedURL_toClipboard_success: ToastSettings = {
		message: message_copiedURLtoClipboard_success,
		background: 'variant-ghost-success'
	};

	// Popup Functions
	function signOut() {
		toastStore.trigger(loggingOut_info);
		toastStore.trigger(loggedOut_success);
		$loginState = false;
	}

	export const openLoginModal = () => {
		if (!$loginState) {
			modalStore.trigger(signInUpForm);
		} else {
			modalStore.trigger(confirmLogOut);
		}
	};
</script>