<script lang="ts">
  import { m } from "$lib/paraglide/messages";

  import ModalWindow from "../_components/ModalWindow.svelte";

  let showModal : boolean = $state(false);
</script>

<button class="opacity-[0.8] text-lg underline underline-offset-3 cursor-pointer" onclick={() => { showModal = true; }}>
  Copyright &copy;
</button>

<ModalWindow bind:showModal>
  {#snippet header()}
    <h2>Copyright &copy;</h2>
  {/snippet}
  <main class="w-[30dvw] h-[40dhv]">
    <p class="text-justify">
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Molestias, unde molestiae. Quasi laborum fugiat, repellendus suscipit molestias deserunt vero ad! Quaerat praesentium voluptatum, recusandae voluptates dolorem voluptate odit atque eos.
    </p>
  </main>
</ModalWindow>