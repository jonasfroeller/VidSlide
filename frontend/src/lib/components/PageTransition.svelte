<script lang="ts">
	import { fly } from 'svelte/transition';

	export let key: string;
</script>

{#key key}
	<div in:fly={{ delay: 500, duration: 500, x: '-100px' }} out:fly={{ duration: 500, x: '100px' }}>
		<slot />
	</div>
{/key}
