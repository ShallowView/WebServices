<script lang="ts">
  let { showModal = $bindable(), header, children } = $props();

  import close_svg from "$lib/icons/close.svg";

  let dialog : HTMLDialogElement | undefined = $state();

  $effect(() => { if (showModal) { dialog?.showModal(); } });

  const close = () => { dialog?.close(); }
</script>

<dialog 
  bind:this={dialog} 
  class="rounded-xl top-[50%] left-[50%] -translate-x-[50%] -translate-y-[50%] backdrop:bg-balck overflow-hidden"
  onclose={() => { showModal = false; }} 
  onclick={(e) => { if (e.target === dialog) { dialog?.close(); }}}
  >
  <div class="min-w-[30dvw] min-h-[40dvh] p-4">
    <header class="flex h-[2rem] justify-between pb-2">
      <section>{@render header?.()}</section>
      <section><button class="cursor-pointer" onclick={close}><img src={close_svg} class="w-[1.75rem]" alt="close" /></button></section>
    </header>
    <hr class="pb-2 opacity-[0.3]" />
    <main>
      {@render children?.()}
    </main>
  </div>
</dialog>

<style>
  dialog[open] {
		animation: zoom 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  dialog[open]::backdrop {
		animation: fade 0.2s ease-out;
  }

  @keyframes zoom {
    from {
			transform: scale(0.95);
		} to {
			transform: scale(1);
		}
  }

  @keyframes fade {
    from {
			opacity: 0;
		} to {
			opacity: 1;
		}
  }
</style>