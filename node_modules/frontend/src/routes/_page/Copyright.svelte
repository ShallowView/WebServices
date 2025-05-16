<script>
  let { showModal = $bindable() } = $props();

  import PopupWindow from "../_components/PopupWindow.svelte";
</script>

<PopupWindow bind:showModal>
  {#snippet header()}
    <h2>Copyright &copy;</h2>
  {/snippet}
  <p>
    aaa
  </p>
</PopupWindow>