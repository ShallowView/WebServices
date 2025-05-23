<script lang="ts">
  import { onMount } from "svelte";
  import Graph from "graphology";
  import type { Settings } from "sigma/settings";
  import type { NodeDisplayData, PartialButFor, PlainObject } from "sigma/types";
  import zoom_in_svg from "$lib/icons/zoom-in.svg";
  import zoom_out_svg from "$lib/icons/zoom-out.svg";
  import radio_button_svg from "$lib/icons/radio-button.svg";

  interface IGraphNode { id : string, type : string, position : { x : number, y : number}, community : number };
  interface IGraphEdge { source : string, target : string, weight : number };  
  interface IGraphPartition { id : number, main_opening : string, player_count : number, players : string[], variation_count : number, variations : string[], average_max_elo : number };
  let { nodes, edges, partitions } : { nodes : IGraphNode[], edges : IGraphEdge[], partitions? : IGraphPartition[] } = $props();

  let plot_div : HTMLDivElement;
  let plot_renderer;
  
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

    plot_renderer = new Sigma(graph, plot_div, { 
      allowInvalidContainer : true, 
      autoRescale : true, 
      autoCenter : true, 
      defaultDrawNodeLabel : drawLabel,
      defaultDrawNodeHover : drawHover
    });
  });

  const TEXT_COLOR = "#000000";

  function drawRoundRect(ctx: CanvasRenderingContext2D, x: number, y: number, width: number, height: number, radius: number) : void {
    ctx.beginPath();
    ctx.moveTo(x + radius, y);
    ctx.lineTo(x + width - radius, y);
    ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
    ctx.lineTo(x + width, y + height - radius);
    ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
    ctx.lineTo(x + radius, y + height);
    ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
    ctx.lineTo(x, y + radius);
    ctx.quadraticCurveTo(x, y, x + radius, y);
    ctx.closePath();
  }

  function drawHover(context: CanvasRenderingContext2D, data: PlainObject, settings: PlainObject) {
    const size = settings.labelSize;
    const font = settings.labelFont;
    const weight = settings.labelWeight;
    const subLabelSize = size - 2;

    const label = data.label;
    const subLabel = data.tag !== "unknown" ? data.tag : "";
    const clusterLabel = data.clusterLabel;

    // Then we draw the label background
    context.beginPath();
    context.fillStyle = "#fff";
    context.shadowOffsetX = 0;
    context.shadowOffsetY = 2;
    context.shadowBlur = 8;
    context.shadowColor = "#000";

    context.font = `${weight} ${size}px ${font}`;
    const labelWidth = context.measureText(label).width;
    context.font = `${weight} ${subLabelSize}px ${font}`;
    const subLabelWidth = subLabel ? context.measureText(subLabel).width : 0;
    context.font = `${weight} ${subLabelSize}px ${font}`;
    const clusterLabelWidth = clusterLabel ? context.measureText(clusterLabel).width : 0;

    const textWidth = Math.max(labelWidth, subLabelWidth, clusterLabelWidth);

    const x = Math.round(data.x);
    const y = Math.round(data.y);
    const w = Math.round(textWidth + size / 2 + data.size + 3);
    const hLabel = Math.round(size / 2 + 4);
    const hSubLabel = subLabel ? Math.round(subLabelSize / 2 + 9) : 0;
    const hClusterLabel = Math.round(subLabelSize / 2 + 9);

    drawRoundRect(context, x, y - hSubLabel - 12, w, hClusterLabel + hLabel + hSubLabel + 12, 5);
    context.closePath();
    context.fill();

    context.shadowOffsetX = 0;
    context.shadowOffsetY = 0;
    context.shadowBlur = 0;

    // And finally we draw the labels
    context.fillStyle = TEXT_COLOR;
    context.font = `${weight} ${size}px ${font}`;
    context.fillText(label, data.x + data.size + 3, data.y + size / 3);

    if (subLabel) {
      context.fillStyle = TEXT_COLOR;
      context.font = `${weight} ${subLabelSize}px ${font}`;
      context.fillText(subLabel, data.x + data.size + 3, data.y - (2 * size) / 3 - 2);
    }

    context.fillStyle = data.color;
    context.font = `${weight} ${subLabelSize}px ${font}`;
    context.fillText(clusterLabel, data.x + data.size + 3, data.y + size / 3 + 3 + subLabelSize);
  }

  function drawLabel(context: CanvasRenderingContext2D, data: PartialButFor<NodeDisplayData, "x" | "y" | "size" | "label" | "color">, settings: Settings) : void {
    if (!data.label) return;

    const size = settings.labelSize,
      font = settings.labelFont,
      weight = settings.labelWeight;

    context.font = `${weight} ${size}px ${font}`;
    const width = context.measureText(data.label).width + 8;

    context.fillStyle = "#ffffffcc";
    context.fillRect(data.x + data.size, data.y + size / 3 - 15, width, 20);

    context.fillStyle = "#000";
    context.fillText(data.label, data.x + data.size + 3, data.y + size / 3);
  }

  const calculateNodeSize = (id : string) => {
    return edges.filter((edges : any) => edges.source === id || edges.target === id).length;
  }
</script>

<div class="flex bg-[rgba(0,0,0,0.05)]">
  <section class="rounded-4xl shadow-2xl shadow-grey">
    <div bind:this={plot_div} class="w-[70dvw] h-dvh"></div>
    <!--
    <div class="absolute bottom-0 left-0">
      <button onclick={() => {}}>
        <img src={zoom_in_svg} alt="zoom in" />
      </button>
      <button onclick={() => {}}>
        <img src={zoom_out_svg} alt="zoom in" />
      </button>
      <button onclick={() => {}}>
        <img src={radio_button_svg} alt="zoom in" />
      </button>
    </div>
    -->
  </section>
  <section>
    <h3 class="mt-4 p-8 text-4xl">Algorithme de Louvain</h3>
    <p class="p-16 pt-8 text-justify">
      Lorem ipsum, dolor sit amet consectetur adipisicing elit. Doloremque, quasi. Est aperiam laboriosam reprehenderit nostrum atque aspernatur! Nemo tenetur, vero et aut debitis autem, alias, officia natus dolores quaerat consequuntur?
      <br><br>
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis mollitia distinctio ducimus necessitatibus sint laborum explicabo, accusamus, hic cumque error, harum dicta libero laboriosam illum amet quasi iusto in tempore.
      <br><br>
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorum rem temporibus vero similique natus, numquam inventore, enim dolores ex officiis sint, mollitia nemo nam rerum saepe quaerat suscipit quia cumque.
    </p>
  </section>
</div>
