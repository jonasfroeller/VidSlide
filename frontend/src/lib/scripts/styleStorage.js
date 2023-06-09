import localStore from '$script/localStorage';
import { browser } from '$app/environment';

/**
 * @param {{ language: string | null; theme: string | null; }} cfg
 * @returns {{ language: string; theme: string; } | null }
 * @description Fills missing parameters in config.
 */
function fillStyleObject(cfg) {
	if (browser) {
		if (cfg.language == null || cfg.language == undefined || cfg.language == '') {
			cfg.language = (navigator.language || navigator.language).includes('de') ? 'de' : 'en';
		}
		if (cfg.theme == null || cfg.theme == undefined || cfg.theme == '') {
			cfg.theme = window.matchMedia('prefers-color-scheme: dark').matches ? 'dark' : 'light';
		}
		return /** @type {{ language: string; theme: string; }} */ (cfg) ;
	}
	return null; // only triggers if browser is not defined => never happens
}

export default class styleCfg {
	/**
	 * @param {{ language: string; theme: string; }} cfg
	 * @description Saves the config to local storage and updates the document properties.
	 */
	static async save(cfg) {
		if (browser) {
			// fill missing parameters in config
			cfg = /** @type {{ language: string; theme: string; }} */ (fillStyleObject(cfg));

			// update document properties
			document.documentElement.classList.add(cfg.theme);
			document.documentElement.classList.remove(cfg.theme == 'dark' ? 'light' : 'dark');
			document.documentElement.lang = cfg.language;

			// save cfg to local storage
			localStore.save('VidSlide-config', cfg);
		}
	}

	/**
	 * @returns { Promise<{ language: string; theme: string; } | null> }
	 * @description Loads the config from local storage and updates the document properties.
	 */
	static async load() {
		if (browser) {
			// load cfg from local storage
			let cfg = await localStore.load('VidSlide-config');

			// set cfg to default values if not set
			if (cfg == null || cfg == undefined) {
				cfg = {
					language: (navigator.language || navigator.language).includes('de') ? 'de' : 'en',
					theme: window.matchMedia('prefers-color-scheme: dark').matches ? 'dark' : 'light'
				};
			} else {
				cfg = /** @type {{ language: string; theme: string; }} */  (fillStyleObject(cfg));
			}

			// update document properties
			document.documentElement.classList.add(cfg.theme);
			document.documentElement.lang = cfg.language;

			return cfg;
		}
		return null; // only triggers if browser is not defined => never happens
	}
}
