// https://kit.svelte.dev/docs/routing#pages

/** @type {import('./$types').PageLoad} */
export function load({ params }) {
	return {
		lang: params.lang.replace('.html', '')
	};
}