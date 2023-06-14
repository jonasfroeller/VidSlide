<script lang="ts">
	/* --- INIT --- */
	// Props
	export let key: string;
	export let css = '';

	/* --- LOGIC --- */

	/**
	 * @description Fade in/out page transition.
	 * @param { Element } node
	 * @param { Object } options
	 */

	function fadeScale(
		node: Element,
		{
			delay = 0,
			duration = 200,
			easing = (x: number) => x,
			baseScale = 0
		}: { delay?: number; duration: number; easing?: Function; baseScale?: number }
	) {
		const o = +getComputedStyle(node).opacity;
		const m = getComputedStyle(node).transform.match(/scale\(([0-9.]+)\)/);
		const s = m ? parseInt(m[1]) : 1;
		const is = 1 - baseScale;

		return {
			delay,
			duration,
			css: (t: number) => {
				const eased = easing(t);
				return `opacity: ${eased * o}; transform: scale(${eased * s * is + baseScale})`;
			}
		};
	}
</script>

<section class="flex justify-center pt-2">
	{#key key}
		<div
			class="flex w-fit {css}"
			in:fadeScale={{ delay: 200, duration: 200 }}
			out:fadeScale={{ duration: 200 }}
		>
			<slot>page couldn't load</slot>
		</div>
	{/key}
</section>
