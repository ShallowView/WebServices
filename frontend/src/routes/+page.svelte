<script lang="ts">
	// Background

  const tools = {
    drawPath(ctx : CanvasRenderingContext2D, fn : () => void) { ctx.save(); ctx.beginPath(); fn(); ctx.closePath(); ctx.restore(); },
    random(min : number, max : number, int? : boolean) { let result = min + Math.random() * (max + (int ? 1 : 0) - min); return int ? Math.floor(result) : result; },
    easing(t : number, b : number, c : number, d : number) { return c * ((t = t / d - 1) * t * t + 1) + b; },
    cellEasing(t : number, b : number, c : number, d : number) { return c * (t /= d) * t * t * t + b; }
  }

  interface IDoc { width : number, height : number };
  const doc : IDoc = { width : 0, height : 0 }

  interface IPlane { xCell : number, yCell : number, xCenter? : number, yCenter? : number, cells : number[], centerCoords? : number[] };
  const plane : IPlane = { xCell : 0, yCell : 0, cells : [] };

  interface ICtx { plane? : CanvasRenderingContext2D | null, main? : CanvasRenderingContext2D | null };
  const ctx : ICtx = { plane : null, main : null };

  interface ICanvasCfg { cell : number, sectionWidth : number, sectionHeight : number, numberOffset : number, shadowBlur : boolean };
  const canvas_cfg : ICanvasCfg = { cell : 50, sectionWidth : 8, sectionHeight : 1, numberOffset : 5, shadowBlur : true };
  
  interface IUi { plane : HTMLCanvasElement | null, main : HTMLCanvasElement | null };
  const ui : IUi = { plane : null, main : null };

  const getDimensions = () => { doc.height = document.documentElement.clientHeight; doc.width = document.documentElement.clientWidth; }

  interface IGlitchItem { 
    p : number, 
    color : string, 
    blinks : { at : number, dur : number }[], 
    pf : number, 
    x : number, 
    y : number, 
    width : number, 
    height : number 
  };

  interface ICanvasState { 
    area : number, 
    time : number, 
    lt : number, 
    planeProgress : number,
    dotsProgress : number,
    fadeInProgress : number,
    glitches : IGlitchItem[],
    tabIsActive : boolean, 
    planeIsDrawn : boolean, 
    delta : number, 
    dlt : number, 
    needRedraw : boolean 
  };

  class BackgroundCanvas {
    state : ICanvasState;

    constructor() {
      this.state = {
        area : 0,
        time : Date.now(),
        lt : 0,
        planeProgress : 0,
        dotsProgress : 0, 
        fadeInProgress : 0,
        glitches : [],
        tabIsActive : true,
        planeIsDrawn : false,
        delta : 0,
        dlt : performance.now(),
        needRedraw : true
      };

      getDimensions();
      this.resizeHandler();

      this.initCanvas();
      this.loop();
      this.initCheckingInterval();
    }

    resizeHandler() {
      const state = this.state;
      state.area = doc.width * doc.height / 1_000_000;
      
      if (ui.plane) {
        ui.plane.height = doc.height;
        ui.plane.width = doc.width;
      }
      if (ui.main) {
        ui.main.height = doc.height;
        ui.main.width = doc.width;
      }

      this.updatePlane();
      state.needRedraw = true;
    }

    initCanvas() {
      const lineCapAndJoin = 'round';
      const color = 'rgba(14, 13, 9, 0.9)';
      
      ctx.plane = ui.plane?.getContext("2d");
      if (ctx.plane) {
        ctx.plane.lineCap = lineCapAndJoin;
        ctx.plane.lineJoin = lineCapAndJoin;
        ctx.plane.fillStyle = color;
        ctx.plane.strokeStyle = color;
      }

      ctx.main = ui.main?.getContext("2d");
      if (ctx.main) {
        ctx.main.lineCap = lineCapAndJoin;
        ctx.main.lineJoin = lineCapAndJoin;
        ctx.main.fillStyle = color;
        ctx.main.strokeStyle = color;
      }
    }

    initCheckingInterval() {
      const state = this.state;
      
      setInterval(() => {
        state.tabIsActive = state.time > state.lt ? true : false;
        state.lt = state.time;
        state.needRedraw = !state.needRedraw;
      }, 100);
    }

    loop() {
      const loop = () => {
        const context = ctx.main;
        const state = this.state;
        state.time = Date.now();
        context?.clearRect(0, 0, doc.width, doc.height);
        this.updateState();
        this.draw();
        if (state.needRedraw) { state.needRedraw = false; }
        requestAnimationFrame(loop);
      };

      loop();
    }

    updateState() {
      const state = this.state;

      const now = performance.now();
      state.delta = now - state.dlt;
      state.dlt = now;

      const dt = state.delta;

      if (state.planeProgress >= 0.2) {
        state.dotsProgress += 0.0001 * dt;
        if (state.dotsProgress > 1) { state.dotsProgress = 1; }
      }

      state.planeProgress += 0.00035 * dt;
      if (state.planeProgress > 1) { state.planeProgress = 1; }

      if (!state.planeIsDrawn) { return; }

      state.fadeInProgress += 0.00015 * dt;
      if (state.fadeInProgress > 1) { state.fadeInProgress = 1; }
    }

    updatePlane() {
      const { width : w, height : h } : IDoc = doc;

      const cell = Math.round(w / canvas_cfg.cell);

      const xPreSize = w / cell;
      plane.xCell = w / xPreSize % 2 !== 0 ? w / (w / xPreSize + 1) : xPreSize;
      const yPreSize = h / Math.round(cell * (h / w));
      plane.yCell = h / yPreSize % 2 !== 0 ? h / (h / yPreSize + 1) : yPreSize;

      plane.cells = [Math.round(w / plane.xCell), Math.round(h / plane.yCell)];
      plane.xCenter = Math.round(plane.cells[1] / 2);
      plane.yCenter = Math.round(plane.cells[0] / 2);
      plane.centerCoords = [plane.yCenter * plane.xCell, plane.xCenter * plane.yCell];
    }

    draw() {
      const state = this.state;

      if (this.state.planeProgress >= 1 && !state.planeIsDrawn) { 
        state.planeIsDrawn = true; 
        this.startGeneratingGlitches();
      }

      if (!state.planeIsDrawn || state.dotsProgress < 1 || state.planeIsDrawn && state.needRedraw) { this.drawPlane(); }

      if (!state.planeIsDrawn) { return; }

      this.drawGlitches();
    }

    drawPlane() {
      const state = this.state;
      const context = ctx.plane;

      context?.clearRect(0, 0, doc.width, doc.height);

      const { xCell, yCell, xCenter, yCenter, cells } = plane;

      const p = tools.easing(state.planeProgress, 0, 1, 1);
      const cp = state.planeProgress;
      const dp = state.dotsProgress;

      for (let i = 0; i < cells[0]; i++) {
        for (let i2 = 0; i2 < cells[1]; i2++) {
          const x = i * xCell;
          const y = i2 * yCell;

          if (i !== yCenter && i2 !== xCenter) { this.drawPlaneDotsAnimation({ dp, i, i2, x, y }); }

          if (i === yCenter) { this.drawXLines({ i, i2, p, cp, x, y }); }
          if (i2 === xCenter) { this.drawYLines({ i, i2, p, cp, x, y }); }
        }
      }
    } 

    drawPlaneDotsAnimation(props : any) {
      const context = ctx.plane;
      const { xCenter, yCenter } = plane;

      if (xCenter == undefined || yCenter == undefined || context == undefined) { return; }

      const { dp, i, i2, x, y } = props;

      const position = [Math.abs(i2 - xCenter), Math.abs(i - yCenter)];
      const index = position[0] * position[1];
      const maxIndex = xCenter * yCenter;
      const percent = 1 / maxIndex;
      const point = percent * index;

      let f = dp * (dp / point);
      if (f > 1) { f = 1; }
      const mf = f >= 0.5 ? (1 - f) / 0.5 : f / 0.5;
      const size = 3;

      if (!mf) { return; }
      
      tools.drawPath(context, () => {
        context.fillStyle = `rgba(14, 13, 9, ${mf * 0.25})`;
        context.fillRect(x - 1, y - 1, size, size);
      });
    } 

    drawXLines(props : any) {
      const context = ctx.plane;
      const { xCenter } = plane;

      if (xCenter == undefined || context == undefined) { return; }

      const { i2, cp, p, y } = props;

      const percent = 1 / xCenter;
      const pos = Math.abs(i2 - xCenter);
      const point = percent * pos;

      let f = cp * (cp / point);
      if (f > 1) { f = 1; }
      const ef = tools.cellEasing(f, 0, 1, 1);

      if (!i2) { return; }

      tools.drawPath(context, () => {
        context.fillStyle = `rgba(14, 13, 9, ${0.1 + (1 - p) * 0.35})`;
        context.fillRect(0 + doc.width / 2 * (1 - ef), y, doc.width * ef, 1);
      });
    } 

    drawYLines(props : any) {
      const context = ctx.plane;
      const { yCenter } = plane;

      if (yCenter == undefined || context == undefined) { return; }

      const { i, cp, p, x } = props;

      const percent = 1 / yCenter;
      const pos = Math.abs(i - yCenter);
      const point = percent * pos;

      let f = cp * (cp / point);
      if (f > 1) { f = 1; }
      const ef = tools.cellEasing(f, 0, 1, 1);

      if (!i) { return; }

      tools.drawPath(context, () => {
        context.fillStyle = `rgba(14, 13, 9, ${0.1 + (1 - p) * 0.35})`;
        context.fillRect(x, 0 + doc.height / 2 * (1 - ef), 1, doc.height * ef);
      });
    } 

    startGeneratingGlitches() {
      const state = this.state;

      function generateItem() {
        const { cells, xCell, yCell } = plane;
        
        const item : IGlitchItem = {
          p : 0,
          color : `rgba(14, 13, 9, ${tools.random(0.01, 1)})`,
          blinks : Array(tools.random(0, 3, true)).fill(null).map(_ => { return { at : tools.random(0, 1), dur : tools.random(0, 0.3)}; }),
          pf : tools.random(0.0005, 0.0015),
          x : tools.random(0, cells[0], true) * xCell,
          y : tools.random(0, cells[1], true) * yCell,
          width : xCell,
          height : yCell
        };

        if (state.tabIsActive) { state.glitches.push(item); }
        setTimeout(generateItem, tools.random((5 + 100) / state.area, (25 + 1200) / state.area));
      }

      generateItem();
    }

    drawGlitches() {
      const context = ctx.main;
      const state = this.state;

      if (context == undefined) { return; }

      state.glitches.forEach((glitch, i) => {
        glitch.p += glitch.pf * state.delta;
        
        let show = true;

        glitch.blinks.every(blink => { if (glitch.p >= blink.at && glitch.p <= blink.at + blink.dur) { show = false; return false; }});

        if (!show) { return; }

        tools.drawPath(context, () => {
          if (canvas_cfg.shadowBlur) {
            context.shadowColor = 'black';
            context.shadowBlur = 30;
          }
          context.globalAlpha = state.fadeInProgress;
          context.fillStyle = glitch.color;
          context.fillRect(glitch.x, glitch.y, glitch.width, glitch.height);
        });

        if (glitch.p >= 1) { state.glitches.splice(i, 1); }
      });
    }
  }

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

	//

	import { onMount } from "svelte";

	import Copyright from "./_page/Copyright.svelte";

	let show_copy = $state(false);

	let background : BackgroundCanvas;

	onMount(() => { background = new BackgroundCanvas(); });
</script>

<svelte:window 
  on:resize={() => {
    getDimensions();
    background.resizeHandler();
  }} />

<svelte:head>
  <title>
    Shallow View
  </title>
</svelte:head>

<Copyright bind:showModal={show_copy} />

<main class="w-dvw h-dvh">
	<canvas class="absolute" bind:this={ui.plane}></canvas>
	<canvas class="absolute" bind:this={ui.main}></canvas>
	<section class="absolute flex justify-between w-full top-[6.25%] left-0 -translate-y-[50%]">
		<article class="ml-[4rem]">
			<button class="opacity-[0.8] text-lg underline underline-offset-3 cursor-pointer" onclick={() => { show_copy = true; }}>Copyright &copy;</button>
		</article>
		<article class="mr-[4rem]">
			<ul class="flex justify-end list-none space-x-[2rem]">
				{#each locales as locale}
					<li>
						<button class="cursor-pointer hover:scale-125 transition-transform" onclick={() => { setLocale(locale); }}>
							<img src={locales_ext[locale].img_src} class="w-[2rem]" alt={locales_ext[locale].full_title}/>
						</button>
					</li>
				{/each}
			</ul>
		</article>
	</section>
	<section class="absolute top-[25%] lg:top-[50%] left-[50%] lg:left-[30%] -translate-[50%] w-[75dvw] lg:w-[50dvw]">
		<article>
			<h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-8xl font-extrabold whitespace-nowrap uppercase">Shallow View</h1>
		</article>
		<article class="">
			<p>{m["entry_page.description"]()}</p>
		</article>
		<article>
			<button>{m["entry_page.btn_global_analysis"]()}</button>
		</article>
	</section>
	<section class="absolute bottom-0 left-[50%] -translate-x-[50%] translate-y-[25%] w-[75dvw] h-[75dvw]
		lg:top-[50%] lg:right-0 lg:translate-x-[50%] lg:-translate-y-[50%] lg:w-[75dvh] lg:max-w-none lg:h-[75dvh]">
		<img src={chess_board_svg} alt={getGameName()} /> 
	</section>
  <section class="absolute flex bottom-[56.25dvw] right-[50%] -translate-y-[50%] translate-x-[50%]
    lg:bottom-[6.25%] lg:right-0 lg:translate-y-[50%] lg:translate-x-0 lg:mr-[4rem]">
    <button class="opacity-[0.5]" onclick={() => {}}> <!--TODO : add when more games cursor-pointer hover:scale-125 transition-transform-->
        <img src={arrow_left_svg} class="w-[1.75rem]" alt={m["entry_page.btn_previous_game"]()} />
      </button>
			<h2 class="text-xl p-2 uppercase">{getGameName()}</h2>
      <button class="opacity-[0.5]" onclick={() => {}}> <!--TODO : add when more games cursor-pointer hover:scale-125 transition-transform-->
        <img src={arrow_right_svg} class="w-[1.75rem]" alt={m["entry_page.btn_next_game"]()} />
      </button>
  </section>
</main>

<style>
	:global(body) {
		margin: 0;
		overflow: hidden;
	}
</style>