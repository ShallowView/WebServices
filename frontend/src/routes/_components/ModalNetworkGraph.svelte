<script lang="ts">
  import { onMount } from "svelte";
  import ModalWindow from "./ModalWindow.svelte";
  import Graph from "graphology";

  interface IGraphNode { id : string, type : string, position : { x : number, y : number}, community : number };
  interface IGraphEdge { source : string, target : string, weight : number };  
  interface IGraphPartition { id : number, main_opening : string, player_count : number, players : string[], variation_count : number, variations : string[], average_max_elo : number };
  let { nodes, edges, partitions, ...others } : { nodes : IGraphNode[], edges : IGraphEdge[], partitions? : IGraphPartition[] } = $props();

  let preview_div : HTMLDivElement;
  let plot_div : HTMLDivElement;
  let plot_renderer : any | undefined;
  
  const graph = new Graph();

  const colorPalette : string[] = [
    "#3D00E6", "#D300E6", "#005EEA", "#01FA10", "#F03B01", "#00EAB8", "#C2DB00",
    "#663600", "#00662C", "#664D00", "#296B00", "#00AFE6", "#A99900", "#A86C00",
    "#A90800", "#A94101", "#8800E6", "#000EE6", "#E6007E", "#E60030", "#01D7E6",
    "#E6C672", "#B579E5", "#5370E6", "#8AE5A1", "#E5004F", "#E600DA", "#804840",
    "#5E4080", "#804049", "#734080", "#FF0026", "#FF00C1", "#7B00FF", "#FF2100"
  ];

  onMount(async () => {
    const Sigma = (await import("sigma")).Sigma;

    nodes.forEach(node => {
      interface INodeAttributes { x : number, y : number, label : string, size? : number, color : string }
      let attributes : INodeAttributes = { x : node.position.x, y : node.position.y, label : node.id, color : colorPalette[node.community]};

      if (node.type === "player") { attributes.size = 1; } 
      else { attributes.size = 3; }
      
      graph.addNode(node.id, attributes);
    });

    edges.forEach(edge => {
      graph.addEdge(edge.source, edge.target, { size : edge.weight });
    });

    const preview_renderer = new Sigma(graph, preview_div, { enableCameraPanning : false, enableCameraZooming : false, enableEdgeEvents : false, defaultDrawNodeHover : undefined });
    plot_renderer = new Sigma(graph, plot_div, { allowInvalidContainer : true, autoRescale : true, autoCenter : true });
  });

  const calculateNodeSize = (id : string) => {
    return edges.filter((edges : any) => edges.source === id || edges.target === id).length;
  }

  let showModal : boolean = $state(false);

  $effect(() => { if (showModal && plot_renderer) { plot_renderer.resize(); plot_renderer.refresh(); } })
</script>

<section class="opacity-[0.9] rounded-2xl bg-[grey] w-[20rem]">
  <article>
    <button onclick={() => { showModal = true; }} aria-label="show plot">
      <div bind:this={preview_div} class="w-[20rem] h-[20rem] cursor-pointer"></div>
    </button>
  </article>
  <article class="p-8">
    aaa
  </article>
  <article class="p-8">
    <!-- svelte-ignore a11y_click_events_have_key_events -->
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <!-- svelte-ignore a11y_consider_explicit_label -->
    <!-- svelte-ignore a11y_missing_attribute -->
    <a onclick={() => {}} class="flex flex-col justify-center items-center h-[50px] w-[260px] hover:[&>span]:opacity-[40%] hover:[&>button]:translate-y-[5px]">
      <button class="analysis_button h-[50px] w-[260px] bg-[white] text-xl cursor-pointer transition-all">
            aaa
      </button>
      <span class="absolute bottom-[-10px] w-[200px] h-[20px] bg-[black] rounded-full blur-xl opacity-[60%] transition-all"></span>
    </a>
  </article>
</section>

<ModalWindow bind:showModal>
  {#snippet header()}
    <h2>a</h2>
  {/snippet}
  <div bind:this={plot_div} class="w-[90dvw] h-[80dvh]"></div>
</ModalWindow>

<style>
    .analysis_button {
    clip-path: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 260 260' preserveAspectRatio='none'%3E%3Cpath d='M 0 25 C 0 -5, -5 0, 80 0 S 160 -5, 160 25, 165 50 80 50, 0 55, 0 25'/%3E%3C/svg%3E");
    border-radius: 13px;
    box-shadow: 0px 0px 2px 2px rgba(0, 0, 0, 0.3) inset;
  }
</style>