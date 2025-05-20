<script lang="ts">
  import { onMount } from "svelte";
  import PopupWindow from "./PopupWindow.svelte";

  let { showModal = $bindable() } = $props();

  let plotly_div : HTMLDivElement;

  onMount(async () => {
    const Plotly = await import("plotly.js-dist-min");
    
    Plotly.newPlot(plotly_div, [{
      x: [1, 2, 3, 4, 5],
      y: [1, 2, 4, 8, 16] }], {
      margin: { t: 0 } });
  });
</script>

<PopupWindow bind:showModal>
  {#snippet header()}
    <h2>a</h2>
  {/snippet}
  <div bind:this={plotly_div}>

  </div>
</PopupWindow>
