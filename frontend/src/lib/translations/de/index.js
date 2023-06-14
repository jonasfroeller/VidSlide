// @ts-check

/**
 * @typedef { import('../i18n-types').BaseTranslation } BaseTranslation
 */

/** @type { BaseTranslation } */
const de = {
	Error: {
		query: "gesuchter Pfad",
		back: "zurück zur Startseite"
	},
	LangSelect: {
		lang: 'Sprache'
	},
	ThemeSelect: {
		theme: 'Stil',
		theme_dark: 'dunkel',
		theme_light: 'hell'
	},
	Sidebar: {
		home: 'Startseite',
		search: 'Suche',
		account: 'Konto',
		settings: 'Einstellungen'
	},
	Header: {
		logIn: 'Anmelden',
		logOut: '{username|} Abmelden'
	},
	UploadVideo: {
		step_01: {
			title: 'Wähle ein Video, dass du veröffentlichen willst:',
			video_dropzone: 'Wähle ein Video aus, welches du veröffentlichen möchtest. Klicke auf die Dropzone oder ziehe dein Video in die Dropzone.',
		},
		step_02: {
			title: 'Um was geht es? Schreibe einen Titel, eine Beschreibung und füge eventuell tags hinzu:',
			video_title_label: 'Titel:',
			video_title: 'Dein Video Titel...',
			video_description_label: 'Beschreibung:',
			video_description: 'Deine Video Beschreibung...',
			video_tags_label: 'Tags:',
			video_tags: 'Schreibe #tag und drücke ENTER um einen Tag hinzuzufügen...',
		},
		next: 'Weiter',
		back: 'Zurück',
		step: 'Schritt',
		complete: 'Hochladen'
	},
	EditVideo: {

	},
	SignInUp: {
		signUp: 'Registrieren',
		signIn: 'Anmelden',
		username: 'Username',
		password: 'Passwort',
		password_retype: 'Passwort Wiederholen',
		cancel: 'Abbrechen'
	},
	VideoResult: {
		follower: '{{ keine Follower | ein Follower | ?? Follower }}',
		likes: '{{ keine Likes | ein Like | ?? Likes }}',
		views: '{{ keine Views | ein View | ?? Views }}',
		no_tags: 'keine tags',
		no_input: 'keine Eingabe'
	},
	VideoSection: {
		follower: '{{ keine Follower | ein Follower | ?? Follower }}',
		views: '{{ keine Views | ein View | ?? Views }}',
		subscribe: '{{ Gefolgt | Folgen }}'
	},
	InfoSection: {
		description: 'Beschreibung',
		comments: 'Kommentare',
		comments_amount: '{{ keine Kommentare | ein Kommentar | ?? Kommentare }}',
		dateTime: 'gepostet am {0|videoDate}',
		logIn: 'Anmelden',
		logIn_text: 'um einen Kommentar zu schreiben',
		comment_placeholder: 'Kommentar...',
		be_the_first_comment: 'Schreibe jetzt einen Kommentar um der Erste zu sein!'
	},
	CommentPost: {
		dateTime: '{0|commentDate}',
		reply: 'Antworten',
		replies: '{replies|0} Antworten'
	},
	UserData: {
		videos: '{{ keine Videos | ein Video | ?? Videos }}',
		follower: '{{ keine Follower | ein Follower | ?? Follower }}',
		likes: '{{ keine Likes | ein Like | ?? Likes }}',
		views: '{{ keine Views | ein View | ?? Views }}',
		joined: 'Beigetreten am {0|}',
		edit: 'Bearbeiten',
		post: 'Posten',
		follow: 'Folgen',
		more: 'Mehr lesen',
		no_description: 'Keine Beschreibung!',
		upload_avatar: 'Profilbild hochladen'
	},
	Popups: {
		modal: {
			confirmLogOut: {
				title: 'Ausloggen?',
				body: 'Würden sie die Website gerne als Gast benutzen und sich ausloggen?'
			},
			confirmLogIn: {
				title: 'Einloggen?',
				body: 'Würden sie die Website gerne angemeldet besuchen?'
			},
			confirmVideoDeletion: {
				title: 'Video löschen?',
				body: 'Das Video wird permanent gelöscht, bist du dir sicher, dass du es löschen möchtest?'
			},
			confirm: 'Bestätigen',
			cancel: 'Abbrechen',
			close: 'Schließen',
		},
		toast: {
			configSaved_success: 'Konfiguration gesichert!',
			copiedURL_toClipboard_success: 'URL des Videos erfolgreich kopiert!',
			copiedUsername_toClipboard_success: 'Username erfolgreich kopiert!',
			loggedOut_success: 'Du wurdest ausgeloggt!',
			loggingOut_info: 'Wir loggen dich aus!',
			loggingIn_error: 'Einloggen nicht erfolgreich!',
			loggingIn_warning: 'Eingabe invalide!',
			loggingIn_info: 'Du wirst in kürze eingeloggt!',
			registering_account_info: 'Wir registrieren deinen Account!',
			loggedIn_success: 'Eingeloggt!',
			registered_success: 'Ein neuer Account wurde registriert!',
			failed_to_fetch_video: 'Video konnte nicht geladen werden!',
			failed_to_authenticate: 'Authentifizierung fehlgeschlagen!',
			filetype_not_allowed: 'Dateityp nicht erlaubt!'
		}
	},
	SearchSection: {
		search: 'Suchen',
		search_option: {
			subject: 'Suche nach:',
			category: 'Kategorie',
			username: 'Username',
			title: 'Titel',
			sort_by: 'Sortiere nach:',
			date: 'Datum',
			views: 'Aufrufen',
			likes: 'Likes',
			dislikes: 'Dislikes',
			videos_found: '{{ keine Videos gefunden | ein Video gefunden | ?? Videos gefunden }}'
		}
	},
	global: {
		loading: 'laden...'
	},
	pages: {
		settings: {
			site_section: {
				title: 'Seiten Einstellungen:'
			},
			account_section: {
				title: 'Account Einstellungen:',
				description: 'Beschreibung:',
				username: 'Username:',
				password: 'Passwort:',
				socials: 'Soziale Medien:',
				avatar: "Profil Bild:",
				edit: 'Bearbeiten',
				delete_account: 'Account löschen',
			}
		}
	}
};

export default de;
