<script lang="ts">
	import ModalNetworkGraph from "../../_components/ModalNetworkGraph.svelte";

	import openings_data from "$lib/output (1).json";
</script>

<svelte:head>
  <title>Shallow View · Chess Analysis</title>
</svelte:head>

<main>
	<h2>List of analysis</h2>
	<section class="grid">
		
		<ModalNetworkGraph nodes={openings_data.nodes} edges={openings_data.edges} />
	</section>
</main>
