// @ts-check

/**
 * @typedef { import('../i18n-types').Translation } Translation
 */

/** @type { Translation } */
const en = {
	Error: {
		query: "searched path",
		back: "back home"
	},
	LangSelect: {
		lang: 'Language'
	},
	ThemeSelect: {
		theme: 'Style',
		theme_dark: 'dark',
		theme_light: 'light'
	},
	Sidebar: {
		home: 'Home',
		search: 'Search',
		account: 'Account',
		settings: 'Settings'
	},
	Header: {
		logIn: 'LogIn',
		logOut: 'LogOut {username|}'
	},
	SignInUp: {
		signUp: 'SignUp',
		signIn: 'SignIn',
		username: 'Username',
		password: 'Password',
		password_retype: 'Retype Password',
		cancel: 'Cancel'
	},
	VideoResult: {
		follower: '{{ no Followers | one Follower | ?? Followers }}',
		likes: '{{ no Likes | one Like | ?? Likes }}',
		views: '{{ no Views | one View | ?? Views }}',
		no_tags: 'no tags',
		no_input: 'no input'
	},
	VideoSection: {
		follower: '{{ no Followers | one Follower | ?? Followers }}',
		views: '{{ no Views | one View | ?? Views }}',
		subscribe: '{{ Subscribed | Subscribe }}'
	},
	InfoSection: {
		description: 'Description',
		comments: 'Comments',
		comments_amount: '{{ no Comments | one Comment | ?? Comments }}',
		dateTime: 'posted on {0|videoDate}',
		logIn: 'LogIn',
		logIn_text: 'to write a comment',
		be_the_first_comment: 'Be the first to comment on this post!'
	},
	CommentPost: {
		dateTime: '{0|commentDate}',
		reply: 'reply',
		replies: '{replies|0} Replies'
	},
	UserData: {
		videos: '{{ no Videos | one Video | ?? Videos }}',
		follower: '{{ no Followers | one Follower | ?? Followers }}',
		likes: '{{ no Likes | one Like | ?? Likes }}',
		views: '{{ no Views | one View | ?? Views }}',
		joined: 'Joined on {0|}',
		edit: 'Edit',
		post: 'Post',
		follow: 'Follow',
		more: 'Read more ',
		no_description: 'No Description!',
		upload_avatar: 'Upload Avatar',
	},
	Popups: {
		modal: {
			confirmLogOut: {
				title: 'Sign Out?',
				body: 'Would you like to sign out?'
			},
			confirmLogIn: {
				title: 'Sign In?',
				body: 'Would you like to create an Account or log in an existing account?'
			},
			confirm: 'Confirm',
			cancel: 'Cancel'
		},
		toast: {
			configSaved_success: 'Config saved!',
			copiedURL_toClipboard_success: 'Successfully copied URL to clipboard!',
			copiedUsername_toClipboard_success: 'Successfully copied username to clipboard!',
			loggedOut_success: 'You are now logged out!',
			loggingOut_info: 'Logging you out!',
			loggingIn_error: "Couldn't log in!",
			loggingIn_warning: 'Input invalid!',
			loggingIn_info: 'Logging you in!',
			loggedIn_success: 'Logged in!',
			registered_success: 'Registered new account!',
			failed_to_fetch_video: 'Couldn\'t fetch video!',
			failed_to_authenticate: 'Couldn\'t authenticate!',
		}
	},
	SearchSection: {
		search: 'Search',
		search_option: {
			subject: 'Subject:',
			category: 'Category',
			username: 'Username',
			title: 'Title',
			sort_by: 'Sort By:',
			date: 'Date',
			views: 'Views',
			likes: 'Likes',
			dislikes: 'Dislikes',
			videos_found: '{{ no Videos found | one Video found | ?? Videos found }}'
		}
	},
	global: {
		loading: 'loading...'
	},
	pages: {
		settings: {
			site_section: {
				title: 'Site Settings:'
			},
			account_section: {
				title: 'Account Settings:',
				description: 'Description:',
				username: 'Username:',
				password: 'Password:',
				socials: 'Social Media:',
				avatar: "Profile Picture:",
				edit: 'Edit',
				delete_account: 'Delete Account'
			}
		}
	}
};

export default en;
