<script lang="ts">
	import PlotlyPopup from "../../_components/PlotlyPopup.svelte";

	let show = $state(false);
</script>

<PlotlyPopup bind:showModal={show} />

<button class="opacity-[0.8] text-lg underline underline-offset-3 cursor-pointer" onclick={() => { show = true; }}>Copyright &copy;</button>