<script lang="ts">
  export let src : string;
  export let alt : string;
  export let css_class : string;

  import { onMount } from "svelte";

  let loaded = false;
  let image : HTMLImageElement;

  let intersecting = false;
  let container;

  onMount(() => { 
    // TODO
    image.onload = () => { loaded = true; }; 
  });
</script>

<div>
  <img {src} {alt} class={css_class} class:loaded bind:this={image} loading="lazy" />
</div>

<style>
  img {
    opacity: 0;
    transition: opacity 1200ms ease-out;
  }

  img.loaded {
    opacity: 1;
  }
</style>