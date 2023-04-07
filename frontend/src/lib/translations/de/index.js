// @ts-check

/**
 * @typedef { import('../i18n-types').BaseTranslation } BaseTranslation
 */

/** @type { BaseTranslation } */
const de = {
	LangSelect: {
		lang: 'Sprache'
	},
	ThemeSelect: {
		theme: 'Stil'
	},
	Sidebar: {
		home: "Startseite",
		search: "Suche",
		account: "Konto",
		settings: "Einstellungen"
	},
	Header: {
		logIn: "Anmelden", 
		logOut: "Abmelden"
	},
	SignInUp: { // TODO
		
	},
	VideoResult: {
		follower: "{{ keine Follower | ein Follower | ?? Follower }}",
		likes: "{{ keine Likes | ein Like | ?? Likes }}",
		views: "{{ keine Views | ein View | ?? Views }}",
		no_tags: "keine tags"
	},
	VideoSection: {
		follower: "{{ keine Follower | ein Follower | ?? Follower }}",
		views: "{{ keine Views | ein View | ?? Views }}",
		subscribe: "{{ Folgen | Gefolgt }}"
	},
	InfoSection: {
		description: 'Beschreibung',
		comments: 'Kommentare',
		comments_amount: "{{ keine Kommentare | ein Kommentar | ?? Kommentare }}",
		posted_on: "gepostet am", // TODO: DATE 
	},
	CommentPost: {
		reply: "reply",
		replies: "{replies} Antworten"
	},
	UserData: {
		videos: "{{ keine Videos | ein Video | ?? Videos }}",
		follower: "{{ keine Follower | ein Follower | ?? Follower }}",
		likes: "{{ keine Likes | ein Like | ?? Likes }}",
		views: "{{ keine Views | ein View | ?? Views }}",
		joined: "Beigetreten am ",
		edit: "Bearbeiten ",
		post: "Posten ",
		more: "Mehr lesen ",
	},
	Popups: {

	},
	pages: {
		account: {
			subject: "Suche nach:",
			category: "Kategorie",
			username: "Username",
			title: "Titel",
			sort_by: "Sortiere nach:",
			date: "Datum",
			views: "Aufrufen",
			likes: "Likes",
			videos_found: "{{ keine Videos gefunden: | ein Video gefunden: | ?? Videos gefunden: }}"
		},
		settings: {
			site_section: {
				title: "Seiten Einstellungen:",
			},
			acount_section: {
				title: "Account Einstellungen:",
				description: "Beschreibung:",
				username: "Username:",
				password: "Passwort:",
				socials: "Soziale Medien:",
				edit: "Bearbeiten"
			}
		}
	},
	global: {
		search: "Suchen"
	}
}

export default de
