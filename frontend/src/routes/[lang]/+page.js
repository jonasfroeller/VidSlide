/** @type {import('./$types').PageLoad} */
export function load({ params }) {
	return {
		lang: params.lang
	};
}
