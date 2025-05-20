<script lang="ts">
	// Locales

	import { setLocale } from "$lib/paraglide/runtime";
  import { locales } from "$lib/paraglide/runtime";
	import { m } from "$lib/paraglide/messages";

  import en_svg from "$lib/icons/locales_flag/en.svg";
  import fr_svg from "$lib/icons/locales_flag/fr.svg";

  interface ILocalExt { [key : string] : { full_title : string, img_src : string } }
  const locales_ext : ILocalExt = {
      "en" : { full_title : "english", img_src : en_svg },
      "fr" : { full_title : "français", img_src : fr_svg }
  };

	// Game Analysis Selector

	import chess_board_svg from "$lib/images/chess_board.svg"; // taken from https://www.vecteezy.com/vector-art/27622972-wooden-chessboard-with-chess-pieces-gameboard-leisure-activity

  import arrow_left_svg from "$lib/icons/arrow-left.svg";
  import arrow_right_svg from "$lib/icons/arrow-right.svg";

  const getGameName = () => {
    return m["entry_page.analysis_title.chess"]();
  }

  const getGameGlobalAnalysis = () => {
    return "/analysis/chess";
  }

	//

	import { useLazyImage } from "svelte-lazy-image";

	import Copyright from "./_page/Copyright.svelte";

	let show_copy = $state(false);
</script>

<svelte:head>
  <title>Shallow View</title>
</svelte:head>

<Copyright bind:showModal={show_copy} />

<main class="w-dvw h-dvh">
	<section class="absolute flex justify-between w-full top-[6.25%] left-0 -translate-y-[50%]">
		<article class="ml-[4rem]">
			<button class="opacity-[0.8] text-lg underline underline-offset-3 cursor-pointer" onclick={() => { show_copy = true; }}>Copyright &copy;</button>
		</article>
		<article class="mr-[4rem]">
			<ul class="flex justify-end list-none space-x-[2rem]">
				{#each locales as locale}
					<li>
						<button class="cursor-pointer hover:scale-125 transition-transform" onclick={() => { setLocale(locale); }}>
							<img src={locales_ext[locale].img_src} class="w-[2rem]" alt={locales_ext[locale].full_title} />
						</button>
					</li>
				{/each}
			</ul>
		</article>
	</section>
	<section class="absolute top-[25%] space-y-16 lg:top-[50%] left-[50%] lg:left-[30%] -translate-[50%] w-[75dvw] lg:w-[50dvw]">
		<article>
			<h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-8xl font-extrabold whitespace-nowrap uppercase">Shallow View</h1>
		</article>
		<article class="text-xl">
			<p>{m["entry_page.description"]()}</p>
		</article>
		<article>
			<a href={getGameGlobalAnalysis()} class="flex flex-col justify-center items-center h-[50px] w-[260px]
        hover:[&>span]:opacity-[40%] hover:[&>button]:translate-y-[5px]">
        <button class="analysis_button h-[50px] w-[260px] bg-[white] text-xl cursor-pointer transition-all">
          {m["entry_page.btn_global_analysis"]()}
        </button>
        <span class="absolute bottom-[-10px] w-[200px] h-[20px] bg-[black] rounded-full blur-xl opacity-[60%] transition-all"></span>
      </a>
      <!--TODO : form for analysis by uid-->
		</article>
	</section>
	<section class="absolute bottom-0 left-[50%] -translate-x-[50%] translate-y-[25%] w-[75dvw] h-[75dvw]
		lg:top-[50%] lg:right-0 lg:translate-x-[50%] lg:-translate-y-[50%] lg:w-[75dvh] lg:max-w-none lg:h-[75dvh]">
		<img data-src={chess_board_svg} class="rounded-2xl" alt={getGameName()} use:useLazyImage />
	</section>
  <section class="absolute flex bottom-[56.25dvw] right-[50%] -translate-y-[50%] translate-x-[50%]
    lg:bottom-[6.25%] lg:right-0 lg:translate-y-[50%] lg:translate-x-0 lg:mr-[4rem]">
    <button class="opacity-[0.5]" onclick={() => {}}> <!--TODO : add when more games cursor-pointer hover:scale-125 transition-transform-->
        <img src={arrow_left_svg} class="w-[1.75rem]" alt={m["entry_page.btn_previous_game"]()} />
      </button>
			<h2 class="text-xl p-2 uppercase no-wrap">{getGameName()}</h2>
      <button class="opacity-[0.5]" onclick={() => {}}> <!--TODO : add when more games cursor-pointer hover:scale-125 transition-transform-->
        <img src={arrow_right_svg} class="w-[1.75rem]" alt={m["entry_page.btn_next_game"]()} />
      </button>
  </section>
</main>

<style>
  .analysis_button {
    clip-path: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 260 260' preserveAspectRatio='none'%3E%3Cpath d='M 0 25 C 0 -5, -5 0, 80 0 S 160 -5, 160 25, 165 50 80 50, 0 55, 0 25'/%3E%3C/svg%3E");
    border-radius: 13px;
    box-shadow: 0px 0px 2px 2px rgba(0, 0, 0, 0.3) inset;
  }
</style>