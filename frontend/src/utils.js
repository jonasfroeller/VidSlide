/**
 * @param { Location } location
 * @param { string } locale
 * @returns string
 */
export const replaceLocaleInUrl = ({ pathname, search }, locale) => {
	// fix for defunction%20search()%20%7B%20[native%20code]%20%7D (if path is invalid, forward to home)
	pathname = pathname ?? `/${locale}/home`;
	search = typeof search === "string" ? search : '';

	let [, , ...rest] = pathname.split('/'); // => /

	return `/${[locale, ...rest].join('/')}${search}`;
};
