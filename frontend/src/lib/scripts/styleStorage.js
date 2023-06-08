import localStore from '$script/localStorage';
import { browser } from '$app/environment';

function fillStyleObject(cfg) {
	if (browser) {
		if (cfg.language == null || cfg.language == undefined || cfg.language == '') {
			cfg.language = (navigator.language || navigator.userLanguage).includes('de') ? 'de' : 'en';
		}
		if (cfg.theme == null || cfg.theme == undefined || cfg.theme == '') {
			cfg.theme = window.matchMedia('prefers-color-scheme: dark').matches ? 'dark' : 'light';
		}
		return cfg;
	}
}

export default class styleCfg {
	static async save(cfg) {
		if (browser) {
			// fill missing parameters in config
			cfg = fillStyleObject(cfg);

			// update document properties
			document.documentElement.classList.add(cfg.theme);
			document.documentElement.classList.remove(cfg.theme == 'dark' ? 'light' : 'dark');
			document.documentElement.lang = cfg.language;

			// save cfg to local storage
			localStore.save('VidSlide-config', cfg);
		}
	}

	static async load() {
		if (browser) {
			// load cfg from local storage
			let cfg = await localStore.load('VidSlide-config');

			// set cfg to default values if not set
			if (cfg != null && cfg != undefined && cfg != '') {
				cfg = fillStyleObject(cfg);
			} else {
				cfg = {
					language: (navigator.language || navigator.userLanguage).includes('de') ? 'de' : 'en',
					theme: window.matchMedia('prefers-color-scheme: dark').matches ? 'dark' : 'light'
				};
			}

			// update document properties
			document.documentElement.classList.add(cfg.theme);
			document.documentElement.lang = cfg.language;

			return cfg;
		}
	}
}
