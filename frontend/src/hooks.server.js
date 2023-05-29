import { detectLocale } from '$translation/i18n-util';
import { initAcceptLanguageHeaderDetector } from 'typesafe-i18n/detectors';

/**
 * @param {import('@sveltejs/kit').Handle} param0
 * @returns {Promise<Response>}
 */

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

	// replace html lang attribute with correct language
	
	return resolve(event, { transformPageChunk: ({ html }) => html.replace('%lang%', lang) });
};

/** @type { (event: import('@sveltejs/kit').RequestEvent) => string } */
const getPreferredLocale = ({ request }) => {
	// detect the preferred language the user has configured in his browser
	// https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Accept-Language
	const acceptLanguageDetector = initAcceptLanguageHeaderDetector(request);

	return detectLocale(acceptLanguageDetector);
};
