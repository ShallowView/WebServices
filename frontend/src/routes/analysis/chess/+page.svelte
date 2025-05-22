<script lang="ts">
	import ModalNetworkGraph from "../../_components/ModalNetworkGraph.svelte";

	let urls_promise : Promise<{ plot_type : string, plot_urls : string }> = fetch("http://127.0.0.1:31900/graph/chess").then(response => { 
		return response.json(); 
	});

	const loadGraph = (url : string) : Promise<any> => {
		url = url.replace("api.shallowview.org", "127.0.0.1"); // TODO : just keep while on localhost
		return fetch(url).then(response => { return response.json(); });
	};
</script>

<svelte:head>
  <title>Shallow View · Chess Analysis</title>
</svelte:head>

<main>
	<h2>List of analysis</h2>
	{#await urls_promise}
		<p>loading...</p>
	{:then plots} 
		<section class="grid">
			{#each Object.entries(plots) as [plot_type, plot_urls]}
				{#each plot_urls as plot_url}
					{#await loadGraph(plot_url)}
						<p>loading...</p>
					{:then plot_json}
						<ModalNetworkGraph nodes={plot_json.nodes} edges={plot_json.edges} />
					{/await}
				{/each}
			{/each}
		</section>
	{/await}
</main>
