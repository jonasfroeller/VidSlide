<script lang="ts">
	import { fly } from 'svelte/transition';

	export let key: string;
</script>

{#key key}
	<div in:fly={{ delay: 250, duration: 125, y: '-50px' }} out:fly={{ duration: 125, y: '50px' }}>
		<slot />
	</div>
{/key}
