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
		theme: 'Stil',
		theme_dark: "dunkel",
		theme_light: "hell"
	},
	Sidebar: {
		home: "Startseite",
		search: "Suche",
		account: "Konto",
		settings: "Einstellungen"
	},
	Header: {
		logIn: "Anmelden", 
		logOut: "{username|} Abmelden"
	},
	SignInUp: {
		signUp: "Registrieren",
		signIn: "Anmelden",
		username: "Username",
		password: "Passwort",
		password_retype: "Passwort Wiederholen",
		cancel: "Abbrechen",
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
		dateTime: "gepostet am {0|videoDate}",
		logIn: "Anmelden",
		logIn_text: "um einen Kommentar zu schreiben",
		be_the_first_comment: "Schreibe jetzt einen Kommentar um der Erste zu sein!",
	},
	CommentPost: {
		dateTime: "{0|commentDate}",
		reply: "Antworten",
		replies: "{replies|0} Antworten"
	},
	UserData: {
		videos: "{{ keine Videos | ein Video | ?? Videos }}",
		follower: "{{ keine Follower | ein Follower | ?? Follower }}",
		likes: "{{ keine Likes | ein Like | ?? Likes }}",
		views: "{{ keine Views | ein View | ?? Views }}",
		joined: "Beigetreten am",
		edit: "Bearbeiten",
		post: "Posten",
		follow: "Folgen",
		more: "Mehr lesen",
	},
	Popups: {
		modal: {
			confirmLogOut: {
				title: "Ausloggen?",
				body: "Würden sie die Website gerne als Gast benutzen und sich ausloggen?"
			},
			confirmLogIn: {
				title: "Einloggen?",
				body: "Würden sie die Website gerne angemeldet besuchen?"
			},
			confirm: "Bestätigen",
			cancel: "Abbrechen"
		},
		toast: {
			configSaved_success: "Konfiguration gesichert!",
			copiedURL_toClipboard_success: "URL des Videos erfolgreich kopiert!",
			loggedOut_success: "Du wurdest ausgeloggt!",
			loggingOut_info: "Wir loggen dich aus!",
			loggingIn_error: "Einloggen nicht erfolgreich!",
			loggingIn_warning: "Eingabe invalide!",
			loggingIn_info: "Du wirst in kürze eingeloggt!",
			loggedIn_success: "Eingeloggt!"
		}
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
