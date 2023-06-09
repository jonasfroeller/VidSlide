import { detectLocale } from '$translation/i18n-util';
import { initAcceptLanguageHeaderDetector } from 'typesafe-i18n/detectors';

/**
 * @typedef {import('@sveltejs/kit').Handle} Handle
 * @typedef {import('@sveltejs/kit').RequestEvent} RequestEvent
 */

/**
 * @param {Handle} param0
 * @returns {Promise<Response>}
 * @see https://kit.svelte.dev/docs#hooks
 * @description This hook is called when a request is received from the browser or server. 
 */
// @ts-ignore
export const handle = async ({ event, resolve }) => {
	// read language slug
	let [, lang] = event.url.pathname.split('/'); // ip||domain/[lang]

	if (!lang) {
		const locale = getPreferredLocale(event);

		return new Response(null, {
			status: 302,
			headers: { Location: `/${locale}/home` }
		});
	}

	// @ts-ignore replace html lang attribute with correct language
	return resolve(event, { transformPageChunk: ({ html }) => html.replace('%lang%', lang) });
};

/** @type { (event: RequestEvent) => string } */
const getPreferredLocale = ({ request }) => {
	// detect the preferred language the user has configured in his browser
	// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
	const acceptLanguageDetector = initAcceptLanguageHeaderDetector(request);

	return detectLocale(acceptLanguageDetector);
};
