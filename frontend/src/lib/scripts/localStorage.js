// @ts-nocheck
import { browser } from '$app/environment';

export default class localStore {
	static async save(name, data) {
		if (browser) {
			localStorage.setItem(name, JSON.stringify(data));
		}
	}

	static async load(name) {
		if (browser) {
			return JSON.parse(localStorage.getItem(name));
		}
	}
}
