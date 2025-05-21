<script lang="ts">
  import { onMount } from "svelte";
  import PopupWindow from "../../../_components/PopupWindow.svelte";

  import data from "$lib/output (1).json";

  let { showModal = $bindable() } = $props();

  const colorPalette : string[] = [
    "#3D00E6",
    "#D300E6",
    "#005EEA",
    "#01FA10",
    "#F03B01",
    "#00EAB8",
    "#C2DB00",
    "#663600",
    "#00662C",
    "#664D00",
    "#296B00",
    "#00AFE6",
    "#A99900",
    "#A86C00",
    "#A90800",
    "#A94101",
    "#8800E6",
    "#000EE6",
    "#E6007E",
    "#E60030",
    "#01D7E6",
    "#E6C672",
    "#B579E5",
    "#5370E6",
    "#8AE5A1",
    "#E5004F",
    "#E600DA",
    "#804840",
    "#5E4080",
    "#804049",
    "#734080",
    "#FF0026",
    "#FF00C1",
    "#7B00FF",
    "#FF2100"
  ];

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
        graph.addNode(node.id, { x : node.position.x, y : node.position.y, label : node.id, size : 3, color : colorPalette[node.community] });
      } else {
        graph.addNode(node.id, { x : node.position.x, y : node.position.y, label : node.id, color : colorPalette[node.community] });
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
