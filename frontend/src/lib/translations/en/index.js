// @ts-check

/**
 * @typedef { import('../i18n-types').Translation } Translation
 */

/** @type { Translation } */
const en = {
	LangSelect: {
		lang: 'Language'
	},
	ThemeSelect: {
		theme: 'Style'
	},
	Sidebar: {
		home: "Home",
		search: "Search",
		account: "Account",
		settings: "Settings"
	},
	Header: {
		logIn: "LogIn", 
		logOut: "LogOut"
	},
	SignInUp: {
		
	},
	VideoResult: {
		follower: "{{ no Followers | one Follower | ?? Followers }}",
		likes: "{{ no Likes | one Like | ?? Likes }}",
		views: "{{ no Views | one View | ?? Views }}",
		no_tags: "no tags"
	},
	VideoSection: {
		follower: "{{ no Followers | one Follower | ?? Followers }}",
		views: "{{ no Views | one View | ?? Views }}",
		subscribe: "{{ Subscribe | Subscribed }}"
	},
	InfoSection: {
		description: 'Description',
		comments: 'Comments',
		comments_amount: "{{ no Comments | one Comment | ?? Comments }}",
		posted_on: "posted on",
	},
	CommentPost: {
		date: "",
		reply: "reply",
		replies: "{replies|0} Replies"
	},
	UserData: {
		videos: "{{ no Videos | one Video | ?? Videos }}",
		follower: "{{ no Followers | one Follower | ?? Followers }}",
		likes: "{{ no Likes | one Like | ?? Likes }}",
		views: "{{ no Views | one View | ?? Views }}",
		joined: "Joined on",
		edit: "Edit",
		post: "Post",
		follow: "Follow",
		more: "Read more ",
	},
	Popups: {

	},
	pages: {
		account: {
			subject: "Subject:",
			category: "Category",
			username: "Username",
			title: "Title",
			sort_by: "Sort By:",
			date: "Date",
			views: "Views",
			likes: "Likes",
			videos_found: "{{ no Videos found: | one Video found: | ?? Videos found: }}"
		},
		settings: {
			site_section: {
				title: "Site Settings:",
			},
			acount_section: {
				title: "Account Settings:",
				description: "Description:",
				username: "Username:",
				password: "Password:",
				socials: "Social Media:",
				edit: "Edit"
			}
		}
	},
	global: {
		search: "Search"
	}
}

export default en
