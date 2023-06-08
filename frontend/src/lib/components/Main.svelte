<script lang="ts">
	function fadeScale(
		node: Element,
		{
			delay = 0,
			duration = 200,
			easing = (x: any) => x,
			baseScale = 0
		}: { delay?: number; duration: number; easing?: Function; baseScale?: number }
	) {
		const o = +getComputedStyle(node).opacity;
		const m = getComputedStyle(node).transform.match(/scale\(([0-9.]+)\)/);
		const s = m ? m[1] : 1;
		const is = 1 - baseScale;

		return {
			delay,
			duration,
			css: (t: any) => {
				const eased = easing(t);
				// @ts-ignore
				return `opacity: ${eased * o}; transform: scale(${eased * s * is + baseScale})`;
			}
		};
	}

	export let key: string;

	export let css = '';
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
