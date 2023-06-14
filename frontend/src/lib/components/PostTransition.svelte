<script lang="ts">
	/* --- INIT --- */

	// JS-Framework/Library
	import { fly } from 'svelte/transition';

	// Props
	export let key: string;

	/* --- LOGIC --- */
</script>

{#key key}
	<div in:fly={{ delay: 250, duration: 125, y: '-50px' }} out:fly={{ duration: 125, y: '50px' }}>
		<slot />
	</div>
{/key}
