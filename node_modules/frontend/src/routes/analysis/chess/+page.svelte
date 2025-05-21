<script lang="ts">
	import OpeningsNetworkGraph from "./_page/OpeningsNetworkGraph.svelte";

	let show_openings_network_graph = $state(false);
</script>

<svelte:head>
  <title>Shallow View · Chess Analysis</title>
</svelte:head>

<OpeningsNetworkGraph bind:showModal={show_openings_network_graph} />

<button class="opacity-[0.8] text-lg underline underline-offset-3 cursor-pointer" onclick={() => { show_openings_network_graph = true; }}>Copyright &copy;</button>