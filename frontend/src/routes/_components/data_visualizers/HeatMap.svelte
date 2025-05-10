<script lang="ts">
  let { showModal = $bindable() } = $props();

  import PopupWindow from "../PopupWindow.svelte";
</script>

<PopupWindow bind:showModal>
  {#snippet header()}
    <h2>a</h2>
  {/snippet}
  <p>a</p>
</PopupWindow>