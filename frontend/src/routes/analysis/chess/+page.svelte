<script lang="ts">
	import Loader from "../../_components/Loader.svelte";
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

<main class="min-h-dvh flex justify-center items-center">
	{#await urls_promise}
		<Loader />
	{:then plots} 
		<div class="h-dvh w-dvw flex justify-center items-center">
			{#each Object.entries(plots) as [plot_type, plot_urls]}
				{#each plot_urls as plot_url}
					{#await loadGraph(plot_url)}
						<Loader />
					{:then plot_json}
						<ModalNetworkGraph nodes={plot_json.nodes} edges={plot_json.edges} />
					{/await}
				{/each}
			{/each}
		</div>
	{/await}
</main>
