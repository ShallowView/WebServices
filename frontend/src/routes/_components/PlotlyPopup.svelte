<script lang="ts">
  import { onMount } from "svelte";
  import PopupWindow from "./PopupWindow.svelte";

  import data from "$lib/output (1).json";

  let { showModal = $bindable() } = $props();

  const calculateNodeSize = (id : string) => {
    return data.edges.filter(edges => edges.source === id || edges.target === id).length;
  }






  let plotly_div : HTMLDivElement;

  let renderer : any;

  onMount(async () => {
    const Graph = (await import("graphology")).default;
    const Sigma = (await import("sigma")).Sigma;
    
    const graph = new Graph();

    data.nodes.forEach(node => {
      if (node.type === "opening") {
        graph.addNode(node.id, { x : node.position.x, y : node.position.y, label : node.id, size : calculateNodeSize(node.id), color : "#777" });
      } else {
        graph.addNode(node.id, { x : node.position.x, y : node.position.y, label : node.id });
      }
    });
    data.edges.forEach(edge => graph.addEdge(edge.source, edge.target, { size : edge.weight }));

    renderer = new Sigma(graph, plotly_div, { allowInvalidContainer : true });
  });

  $effect(() => { if (showModal) { renderer.resize(true); } });
</script>

<PopupWindow bind:showModal>
  {#snippet header()}
    <h2>a</h2>
  {/snippet}
  <div bind:this={plotly_div} class="w-[90dvw] h-[80dvh]"></div>
</PopupWindow>
