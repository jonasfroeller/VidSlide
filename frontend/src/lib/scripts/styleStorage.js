// @ts-nocheck
import localStore from '$script/localStorage';
import { browser } from '$app/environment';

function fillStyleObject(cfg) {
	if (browser) {
		// wegen document.referrer
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
			cfg = fillStyleObject(cfg);
			document.documentElement.classList.add(cfg.theme);
			document.documentElement.classList.remove(cfg.theme == 'dark' ? 'light' : 'dark');
			localStore.save('VidSlide-config', cfg);
		}
	}

	static async load() {
		if (browser) {
			let cfg = await localStore.load('VidSlide-config');

			if (cfg != null && cfg != undefined && cfg != '') {
				// prevent cannot get property of undefined
				cfg = fillStyleObject(cfg);
			} else {
				cfg = {
					language: (navigator.language || navigator.userLanguage).includes('de') ? 'de' : 'en',
					theme: window.matchMedia('prefers-color-scheme: dark').matches ? 'dark' : 'light'
				};
			}

			document.documentElement.classList.add(cfg.theme);
			document.documentElement.lang = cfg.language;
			return cfg;
		}
	}
}
