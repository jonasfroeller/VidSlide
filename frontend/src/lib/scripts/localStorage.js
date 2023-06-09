import { browser } from '$app/environment';

export default class localStore {
	/**
	 * @param {string} name
	 * @param {{ language: string; theme: string; }} data
	 * @returns {Promise<boolean>}
	 * @description Saves the config to local storage.
	 */
	static async save(name, data) {
		if (browser && name && data) {
			localStorage.setItem(name, JSON.stringify(data));
			return true;
		}
		return false;
	}

	/**
	 * @param {string} name
	 * @returns {Promise<{ language: string; theme: string; } | null>}
	 * @description Loads the config from local storage.
	 */
	static async load(name) {
		if (browser && name) {
			const data = localStorage.getItem(name);
			if (data) {
				return JSON.parse(data);
			}
			return null;
		}
		return null;
	}

	/**
	 * @param {string} name
	 * @returns {Promise<boolean>}
	 * @description Clears the config from local storage.
	 */
	static async clear(name) {
		if (browser && name) {
			localStorage.removeItem(name);
			return true;
		}
		return false;
	}
}
