<script lang="ts">
	/* --- INIT --- */
	// Translation
	import translation from '$translation/i18n-svelte';

	// Stores
	import { loginState } from '$store/account';

	// Scripts
	import accountCfg from '$script/accountStorage';

	// JS-Framework/Library
	import { browser } from '$app/environment';

	// CSS-Framework/Library
	import type { ModalSettings, ToastSettings } from '@skeletonlabs/skeleton';
	import { modalStore, toastStore } from '@skeletonlabs/skeleton';

	/* --- LOGIC --- */
	/* Modals */
	// Confirmation Modal
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

	let message_confirmVideoDeletion_title = $translation.Popups.modal.confirmVideoDeletion.title();
	let message_confirmVideoDeletion_body = $translation.Popups.modal.confirmVideoDeletion.body();
	export const confirmVideoDeletion: ModalSettings = {
		type: 'confirm',
		title: message_confirmVideoDeletion_title,
		body: message_confirmVideoDeletion_body,
		buttonTextConfirm: message_button_confirm,
		buttonTextCancel: message_button_cancel,
		response: (r: boolean) => (r ? signOut() : console.log('declined video deletion'))
	};

	// Forms
	export const signInUpForm: ModalSettings = {
		type: 'component',
		component: 'signupModalComponent'
	};

	// Management
	export const uploadVideo: ModalSettings = {
		type: 'component',
		component: 'uploadVideoComponent'
	};

	export const editVideo: ModalSettings = {
		type: 'component',
		component: 'editVideoComponent'
	};

	/* Notifications/Toasts */
	// LogIn/SignIn
	let message_loggingIn_info = $translation.Popups.toast.loggingIn_info();
	export const loggingIn_info: ToastSettings = {
		message: message_loggingIn_info,
		background: 'variant-ghost-primary'
	};

	let message_registering_account_info = $translation.Popups.toast.registering_account_info();
	export const registering_account_info: ToastSettings = {
		message: message_registering_account_info,
		background: 'variant-ghost-primary'
	};

	let message_loggedIn_success = $translation.Popups.toast.loggedIn_success();
	export const loggedIn_success: ToastSettings = {
		message: message_loggedIn_success,
		background: 'variant-ghost-success'
	};

	let message_registered_success = $translation.Popups.toast.registered_success();
	export const registered_success: ToastSettings = {
		message: message_registered_success,
		background: 'variant-ghost-success'
	};

	let message_loggingIn_error = $translation.Popups.toast.loggingIn_error(); // backend/database connection problem
	export const loggingIn_error: ToastSettings = {
		message: message_loggingIn_error,
		background: 'variant-ghost-error'
	};

	let message_failed_to_authenticate = $translation.Popups.toast.failed_to_authenticate(); // authFail
	export const failed_to_authenticate: ToastSettings = {
		message: message_failed_to_authenticate,
		background: 'variant-ghost-error'
	};

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

	// Config

	let message_configSaved_success = $translation.Popups.toast.configSaved_success();
	export const configSaved_success: ToastSettings = {
		message: message_configSaved_success,
		background: 'variant-ghost-success'
	};

	// Copy To Clipboard

	let message_copiedURLtoClipboard_success =
		$translation.Popups.toast.copiedURL_toClipboard_success();
	export const copiedURL_toClipboard_success: ToastSettings = {
		message: message_copiedURLtoClipboard_success,
		background: 'variant-ghost-success'
	};

	let message_copiedUsernameToClipboard_success =
		$translation.Popups.toast.copiedUsername_toClipboard_success();
	export const copiedUsername_toClipboard_success: ToastSettings = {
		message: message_copiedUsernameToClipboard_success,
		background: 'variant-ghost-success'
	};

	// Video Fetching
	let message_failed_to_fetch_video = $translation.Popups.toast.failed_to_fetch_video();
	export const failed_to_fetch_video: ToastSettings = {
		message: message_failed_to_fetch_video,
		background: 'variant-ghost-error'
	};

	// Input Validation
	let message_loggingIn_warning = $translation.Popups.toast.loggingIn_warning(); // input wrong
	export const loggingIn_warning: ToastSettings = {
		message: message_loggingIn_warning,
		background: 'variant-ghost-warning'
	};

	let message_filetype_not_allowed = $translation.Popups.toast.filetype_not_allowed();
	export const filetype_not_allowed: ToastSettings = {
		message: message_filetype_not_allowed,
		background: 'variant-ghost-error'
	};

	/* Popup Functions */
	function signOut() {
		toastStore.trigger(loggingOut_info);
		if (accountCfg.clear()) {
			toastStore.trigger(loggedOut_success);
		}
	}

	export const openLoginModal = () => {
		if (!$loginState) {
			modalStore.trigger(signInUpForm);
		} else {
			modalStore.trigger(confirmLogOut);
		}
	};
</script>
