// This file was auto-generated by 'typesafe-i18n'. Any manual changes will be overwritten.
/* eslint-disable */
import type { BaseTranslation as BaseTranslationType, LocalizedString, RequiredParams } from 'typesafe-i18n'

export type BaseTranslation = BaseTranslationType
export type BaseLocale = 'de'

export type Locales =
	| 'de'
	| 'en'

export type Translation = RootTranslation

export type Translations = RootTranslation

type RootTranslation = {
	LangSelect: {
		/**
		 * S​p​r​a​c​h​e
		 */
		lang: string
	}
	ThemeSelect: {
		/**
		 * S​t​i​l
		 */
		theme: string
		/**
		 * d​u​n​k​e​l
		 */
		theme_dark: string
		/**
		 * h​e​l​l
		 */
		theme_light: string
	}
	Sidebar: {
		/**
		 * S​t​a​r​t​s​e​i​t​e
		 */
		home: string
		/**
		 * S​u​c​h​e
		 */
		search: string
		/**
		 * K​o​n​t​o
		 */
		account: string
		/**
		 * E​i​n​s​t​e​l​l​u​n​g​e​n
		 */
		settings: string
	}
	Header: {
		/**
		 * A​n​m​e​l​d​e​n
		 */
		logIn: string
		/**
		 * {​u​s​e​r​n​a​m​e​|​}​ ​A​b​m​e​l​d​e​n
		 * @param {unknown} username
		 */
		logOut: RequiredParams<'username|'>
	}
	SignInUp: {
		/**
		 * R​e​g​i​s​t​r​i​e​r​e​n
		 */
		signUp: string
		/**
		 * A​n​m​e​l​d​e​n
		 */
		signIn: string
		/**
		 * U​s​e​r​n​a​m​e
		 */
		username: string
		/**
		 * P​a​s​s​w​o​r​t
		 */
		password: string
		/**
		 * P​a​s​s​w​o​r​t​ ​W​i​e​d​e​r​h​o​l​e​n
		 */
		password_retype: string
		/**
		 * A​b​b​r​e​c​h​e​n
		 */
		cancel: string
	}
	VideoResult: {
		/**
		 * {​{​k​e​i​n​e​ ​F​o​l​l​o​w​e​r​|​e​i​n​ ​F​o​l​l​o​w​e​r​|​?​?​ ​F​o​l​l​o​w​e​r​}​}
		 */
		follower: string
		/**
		 * {​{​k​e​i​n​e​ ​L​i​k​e​s​|​e​i​n​ ​L​i​k​e​|​?​?​ ​L​i​k​e​s​}​}
		 */
		likes: string
		/**
		 * {​{​k​e​i​n​e​ ​V​i​e​w​s​|​e​i​n​ ​V​i​e​w​|​?​?​ ​V​i​e​w​s​}​}
		 */
		views: string
		/**
		 * k​e​i​n​e​ ​t​a​g​s
		 */
		no_tags: string
	}
	VideoSection: {
		/**
		 * {​{​k​e​i​n​e​ ​F​o​l​l​o​w​e​r​|​e​i​n​ ​F​o​l​l​o​w​e​r​|​?​?​ ​F​o​l​l​o​w​e​r​}​}
		 */
		follower: string
		/**
		 * {​{​k​e​i​n​e​ ​V​i​e​w​s​|​e​i​n​ ​V​i​e​w​|​?​?​ ​V​i​e​w​s​}​}
		 */
		views: string
		/**
		 * {​{​F​o​l​g​e​n​|​G​e​f​o​l​g​t​}​}
		 */
		subscribe: string
	}
	InfoSection: {
		/**
		 * B​e​s​c​h​r​e​i​b​u​n​g
		 */
		description: string
		/**
		 * K​o​m​m​e​n​t​a​r​e
		 */
		comments: string
		/**
		 * {​{​k​e​i​n​e​ ​K​o​m​m​e​n​t​a​r​e​|​e​i​n​ ​K​o​m​m​e​n​t​a​r​|​?​?​ ​K​o​m​m​e​n​t​a​r​e​}​}
		 */
		comments_amount: string
		/**
		 * g​e​p​o​s​t​e​t​ ​a​m​ ​{​0​|​v​i​d​e​o​D​a​t​e​}
		 * @param {unknown} 0
		 */
		dateTime: RequiredParams<'0|videoDate'>
		/**
		 * A​n​m​e​l​d​e​n
		 */
		logIn: string
		/**
		 * u​m​ ​e​i​n​e​n​ ​K​o​m​m​e​n​t​a​r​ ​z​u​ ​s​c​h​r​e​i​b​e​n
		 */
		logIn_text: string
		/**
		 * S​c​h​r​e​i​b​e​ ​j​e​t​z​t​ ​e​i​n​e​n​ ​K​o​m​m​e​n​t​a​r​ ​u​m​ ​d​e​r​ ​E​r​s​t​e​ ​z​u​ ​s​e​i​n​!
		 */
		be_the_first_comment: string
	}
	CommentPost: {
		/**
		 * {​0​|​c​o​m​m​e​n​t​D​a​t​e​}
		 * @param {unknown} 0
		 */
		dateTime: RequiredParams<'0|commentDate'>
		/**
		 * A​n​t​w​o​r​t​e​n
		 */
		reply: string
		/**
		 * {​r​e​p​l​i​e​s​|​0​}​ ​A​n​t​w​o​r​t​e​n
		 * @param {unknown} replies
		 */
		replies: RequiredParams<'replies|0'>
	}
	UserData: {
		/**
		 * {​{​k​e​i​n​e​ ​V​i​d​e​o​s​|​e​i​n​ ​V​i​d​e​o​|​?​?​ ​V​i​d​e​o​s​}​}
		 */
		videos: string
		/**
		 * {​{​k​e​i​n​e​ ​F​o​l​l​o​w​e​r​|​e​i​n​ ​F​o​l​l​o​w​e​r​|​?​?​ ​F​o​l​l​o​w​e​r​}​}
		 */
		follower: string
		/**
		 * {​{​k​e​i​n​e​ ​L​i​k​e​s​|​e​i​n​ ​L​i​k​e​|​?​?​ ​L​i​k​e​s​}​}
		 */
		likes: string
		/**
		 * {​{​k​e​i​n​e​ ​V​i​e​w​s​|​e​i​n​ ​V​i​e​w​|​?​?​ ​V​i​e​w​s​}​}
		 */
		views: string
		/**
		 * B​e​i​g​e​t​r​e​t​e​n​ ​a​m
		 */
		joined: string
		/**
		 * B​e​a​r​b​e​i​t​e​n
		 */
		edit: string
		/**
		 * P​o​s​t​e​n
		 */
		post: string
		/**
		 * F​o​l​g​e​n
		 */
		follow: string
		/**
		 * M​e​h​r​ ​l​e​s​e​n
		 */
		more: string
	}
	Popups: {
		modal: {
			confirmLogOut: {
				/**
				 * A​u​s​l​o​g​g​e​n​?
				 */
				title: string
				/**
				 * W​ü​r​d​e​n​ ​s​i​e​ ​d​i​e​ ​W​e​b​s​i​t​e​ ​g​e​r​n​e​ ​a​l​s​ ​G​a​s​t​ ​b​e​n​u​t​z​e​n​ ​u​n​d​ ​s​i​c​h​ ​a​u​s​l​o​g​g​e​n​?
				 */
				body: string
			}
			confirmLogIn: {
				/**
				 * E​i​n​l​o​g​g​e​n​?
				 */
				title: string
				/**
				 * W​ü​r​d​e​n​ ​s​i​e​ ​d​i​e​ ​W​e​b​s​i​t​e​ ​g​e​r​n​e​ ​a​n​g​e​m​e​l​d​e​t​ ​b​e​s​u​c​h​e​n​?
				 */
				body: string
			}
			/**
			 * B​e​s​t​ä​t​i​g​e​n
			 */
			confirm: string
			/**
			 * A​b​b​r​e​c​h​e​n
			 */
			cancel: string
		}
		toast: {
			/**
			 * K​o​n​f​i​g​u​r​a​t​i​o​n​ ​g​e​s​i​c​h​e​r​t​!
			 */
			configSaved_success: string
			/**
			 * U​R​L​ ​d​e​s​ ​V​i​d​e​o​s​ ​e​r​f​o​l​g​r​e​i​c​h​ ​k​o​p​i​e​r​t​!
			 */
			copiedURL_toClipboard_success: string
			/**
			 * D​u​ ​w​u​r​d​e​s​t​ ​a​u​s​g​e​l​o​g​g​t​!
			 */
			loggedOut_success: string
			/**
			 * W​i​r​ ​l​o​g​g​e​n​ ​d​i​c​h​ ​a​u​s​!
			 */
			loggingOut_info: string
			/**
			 * E​i​n​l​o​g​g​e​n​ ​n​i​c​h​t​ ​e​r​f​o​l​g​r​e​i​c​h​!
			 */
			loggingIn_error: string
			/**
			 * E​i​n​g​a​b​e​ ​i​n​v​a​l​i​d​e​!
			 */
			loggingIn_warning: string
			/**
			 * D​u​ ​w​i​r​s​t​ ​i​n​ ​k​ü​r​z​e​ ​e​i​n​g​e​l​o​g​g​t​!
			 */
			loggingIn_info: string
			/**
			 * E​i​n​g​e​l​o​g​g​t​!
			 */
			loggedIn_success: string
		}
	}
	pages: {
		account: {
			/**
			 * S​u​c​h​e​ ​n​a​c​h​:
			 */
			subject: string
			/**
			 * K​a​t​e​g​o​r​i​e
			 */
			category: string
			/**
			 * U​s​e​r​n​a​m​e
			 */
			username: string
			/**
			 * T​i​t​e​l
			 */
			title: string
			/**
			 * S​o​r​t​i​e​r​e​ ​n​a​c​h​:
			 */
			sort_by: string
			/**
			 * D​a​t​u​m
			 */
			date: string
			/**
			 * A​u​f​r​u​f​e​n
			 */
			views: string
			/**
			 * L​i​k​e​s
			 */
			likes: string
			/**
			 * {​{​e​i​n​ ​V​i​d​e​o​ ​g​e​f​u​n​d​e​n​}​}
			 */
			videos_found: string
		}
		settings: {
			site_section: {
				/**
				 * S​e​i​t​e​n​ ​E​i​n​s​t​e​l​l​u​n​g​e​n​:
				 */
				title: string
			}
			acount_section: {
				/**
				 * A​c​c​o​u​n​t​ ​E​i​n​s​t​e​l​l​u​n​g​e​n​:
				 */
				title: string
				/**
				 * B​e​s​c​h​r​e​i​b​u​n​g​:
				 */
				description: string
				/**
				 * U​s​e​r​n​a​m​e​:
				 */
				username: string
				/**
				 * P​a​s​s​w​o​r​t​:
				 */
				password: string
				/**
				 * S​o​z​i​a​l​e​ ​M​e​d​i​e​n​:
				 */
				socials: string
				/**
				 * B​e​a​r​b​e​i​t​e​n
				 */
				edit: string
			}
		}
	}
	global: {
		/**
		 * S​u​c​h​e​n
		 */
		search: string
	}
}

export type TranslationFunctions = {
	LangSelect: {
		/**
		 * Sprache
		 */
		lang: () => LocalizedString
	}
	ThemeSelect: {
		/**
		 * Stil
		 */
		theme: () => LocalizedString
		/**
		 * dunkel
		 */
		theme_dark: () => LocalizedString
		/**
		 * hell
		 */
		theme_light: () => LocalizedString
	}
	Sidebar: {
		/**
		 * Startseite
		 */
		home: () => LocalizedString
		/**
		 * Suche
		 */
		search: () => LocalizedString
		/**
		 * Konto
		 */
		account: () => LocalizedString
		/**
		 * Einstellungen
		 */
		settings: () => LocalizedString
	}
	Header: {
		/**
		 * Anmelden
		 */
		logIn: () => LocalizedString
		/**
		 * {username|} Abmelden
		 */
		logOut: (arg: { username: unknown }) => LocalizedString
	}
	SignInUp: {
		/**
		 * Registrieren
		 */
		signUp: () => LocalizedString
		/**
		 * Anmelden
		 */
		signIn: () => LocalizedString
		/**
		 * Username
		 */
		username: () => LocalizedString
		/**
		 * Passwort
		 */
		password: () => LocalizedString
		/**
		 * Passwort Wiederholen
		 */
		password_retype: () => LocalizedString
		/**
		 * Abbrechen
		 */
		cancel: () => LocalizedString
	}
	VideoResult: {
		/**
		 * {{keine Follower|ein Follower|?? Follower}}
		 */
		follower: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Likes|ein Like|?? Likes}}
		 */
		likes: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Views|ein View|?? Views}}
		 */
		views: (arg0: number | string | boolean) => LocalizedString
		/**
		 * keine tags
		 */
		no_tags: () => LocalizedString
	}
	VideoSection: {
		/**
		 * {{keine Follower|ein Follower|?? Follower}}
		 */
		follower: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Views|ein View|?? Views}}
		 */
		views: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{Folgen|Gefolgt}}
		 */
		subscribe: (arg0: number | string | boolean) => LocalizedString
	}
	InfoSection: {
		/**
		 * Beschreibung
		 */
		description: () => LocalizedString
		/**
		 * Kommentare
		 */
		comments: () => LocalizedString
		/**
		 * {{keine Kommentare|ein Kommentar|?? Kommentare}}
		 */
		comments_amount: (arg0: number | string | boolean) => LocalizedString
		/**
		 * gepostet am {0|videoDate}
		 */
		dateTime: (arg0: unknown) => LocalizedString
		/**
		 * Anmelden
		 */
		logIn: () => LocalizedString
		/**
		 * um einen Kommentar zu schreiben
		 */
		logIn_text: () => LocalizedString
		/**
		 * Schreibe jetzt einen Kommentar um der Erste zu sein!
		 */
		be_the_first_comment: () => LocalizedString
	}
	CommentPost: {
		/**
		 * {0|commentDate}
		 */
		dateTime: (arg0: unknown) => LocalizedString
		/**
		 * Antworten
		 */
		reply: () => LocalizedString
		/**
		 * {replies|0} Antworten
		 */
		replies: (arg: { replies: unknown }) => LocalizedString
	}
	UserData: {
		/**
		 * {{keine Videos|ein Video|?? Videos}}
		 */
		videos: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Follower|ein Follower|?? Follower}}
		 */
		follower: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Likes|ein Like|?? Likes}}
		 */
		likes: (arg0: number | string | boolean) => LocalizedString
		/**
		 * {{keine Views|ein View|?? Views}}
		 */
		views: (arg0: number | string | boolean) => LocalizedString
		/**
		 * Beigetreten am
		 */
		joined: () => LocalizedString
		/**
		 * Bearbeiten
		 */
		edit: () => LocalizedString
		/**
		 * Posten
		 */
		post: () => LocalizedString
		/**
		 * Folgen
		 */
		follow: () => LocalizedString
		/**
		 * Mehr lesen
		 */
		more: () => LocalizedString
	}
	Popups: {
		modal: {
			confirmLogOut: {
				/**
				 * Ausloggen?
				 */
				title: () => LocalizedString
				/**
				 * Würden sie die Website gerne als Gast benutzen und sich ausloggen?
				 */
				body: () => LocalizedString
			}
			confirmLogIn: {
				/**
				 * Einloggen?
				 */
				title: () => LocalizedString
				/**
				 * Würden sie die Website gerne angemeldet besuchen?
				 */
				body: () => LocalizedString
			}
			/**
			 * Bestätigen
			 */
			confirm: () => LocalizedString
			/**
			 * Abbrechen
			 */
			cancel: () => LocalizedString
		}
		toast: {
			/**
			 * Konfiguration gesichert!
			 */
			configSaved_success: () => LocalizedString
			/**
			 * URL des Videos erfolgreich kopiert!
			 */
			copiedURL_toClipboard_success: () => LocalizedString
			/**
			 * Du wurdest ausgeloggt!
			 */
			loggedOut_success: () => LocalizedString
			/**
			 * Wir loggen dich aus!
			 */
			loggingOut_info: () => LocalizedString
			/**
			 * Einloggen nicht erfolgreich!
			 */
			loggingIn_error: () => LocalizedString
			/**
			 * Eingabe invalide!
			 */
			loggingIn_warning: () => LocalizedString
			/**
			 * Du wirst in kürze eingeloggt!
			 */
			loggingIn_info: () => LocalizedString
			/**
			 * Eingeloggt!
			 */
			loggedIn_success: () => LocalizedString
		}
	}
	pages: {
		account: {
			/**
			 * Suche nach:
			 */
			subject: () => LocalizedString
			/**
			 * Kategorie
			 */
			category: () => LocalizedString
			/**
			 * Username
			 */
			username: () => LocalizedString
			/**
			 * Titel
			 */
			title: () => LocalizedString
			/**
			 * Sortiere nach:
			 */
			sort_by: () => LocalizedString
			/**
			 * Datum
			 */
			date: () => LocalizedString
			/**
			 * Aufrufen
			 */
			views: () => LocalizedString
			/**
			 * Likes
			 */
			likes: () => LocalizedString
			/**
			 * {{ein Video gefunden}}
			 */
			videos_found: (arg: { keine Videos gefunden: number | string | boolean }) => LocalizedString
		}
		settings: {
			site_section: {
				/**
				 * Seiten Einstellungen:
				 */
				title: () => LocalizedString
			}
			acount_section: {
				/**
				 * Account Einstellungen:
				 */
				title: () => LocalizedString
				/**
				 * Beschreibung:
				 */
				description: () => LocalizedString
				/**
				 * Username:
				 */
				username: () => LocalizedString
				/**
				 * Passwort:
				 */
				password: () => LocalizedString
				/**
				 * Soziale Medien:
				 */
				socials: () => LocalizedString
				/**
				 * Bearbeiten
				 */
				edit: () => LocalizedString
			}
		}
	}
	global: {
		/**
		 * Suchen
		 */
		search: () => LocalizedString
	}
}

export type Formatters = {
	'': (value: unknown) => unknown
	'0': (value: unknown) => unknown
	commentDate: (value: unknown) => unknown
	videoDate: (value: unknown) => unknown
}
