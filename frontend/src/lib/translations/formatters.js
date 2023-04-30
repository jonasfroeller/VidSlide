// @ts-check

import { date } from 'typesafe-i18n/formatters';

/**
 * @typedef { import('typesafe-i18n').FormattersInitializer<Locales, Formatters> } FormattersInitializer
 * @typedef { import('./i18n-types').Locales } Locales
 * @typedef { import('./i18n-types').Formatters } Formatters
 */

/**
 * @param { Locales } locale
 * @return { Formatters }
 */
export const initFormatters = (locale) => {
	/** @type { Formatters } */
	const formatters = {
		commentDate: date(locale, { dateStyle: 'short', timeStyle: 'medium' }),
		videoDate: date(locale, { dateStyle: 'medium', timeStyle: 'medium' })
	};

	return formatters;
};
