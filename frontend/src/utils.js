/**
 * @param { Location } location
 * @param { string } locale
 * @returns string
 */
export const replaceLocaleInUrl = ({ pathname, search }, locale) => {
	let [, , ...rest] = pathname?.split('/') ?? ''; // => /

	return `/${[locale, ...rest].join('/')}${search}`;
};
