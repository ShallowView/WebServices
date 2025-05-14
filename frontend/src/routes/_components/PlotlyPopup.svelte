<script lang="ts">
  import { onMount } from "svelte";
  import PopupWindow from "./PopupWindow.svelte";

  let { showModal = $bindable() } = $props();

  let plotly_div;

  onMount(async () => {
    
  });
</script>


<PopupWindow bind:showModal>
  {#snippet header()}
    <h2>a</h2>
  {/snippet}
  <div bind:this={plotly_div}>

  </div>
</PopupWindow>

<!--https://plotly.com/javascript/heatmaps/-->