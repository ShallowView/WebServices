<script lang="ts">
	import '../app.css';

	let { children } = $props();

	// ---

	import { onMount } from "svelte";

  interface IBgCfg { 
    direction : 'diagonal' | 'up' | 'right' | 'down' | 'left', 
    speed : number, 
    squareSize : number, 
    gridOffset : { x : number, y : number },
    borderColor : string, 
    hoverFillColor : string 
  };

  class BackgroundCanvas { // FIXME
    canvas : HTMLCanvasElement;
    context : CanvasRenderingContext2D | null;
    cfg : IBgCfg;

    num_squares : { x : number, y : number };
    hovered_square : { x : number, y : number } | null;

    constructor(canvas : HTMLCanvasElement, cfg : IBgCfg) {
      this.canvas = canvas;
      this.context = this.canvas.getContext("2d");
      this.cfg = cfg;

      this.num_squares = { x : 0, y : 0 };
      this.hovered_square = null;

      this.resizeHandler();
      
      requestAnimationFrame(() => this.updateAnimation());
    }

    resizeHandler() {
      this.canvas.width = this.canvas.offsetWidth;
      this.canvas.height = this.canvas.offsetHeight;
      this.num_squares.x = Math.ceil(this.canvas.width / this.cfg.squareSize) + 1;
      this.num_squares.y = Math.ceil(this.canvas.height / this.cfg.squareSize) + 1;
    }

    handleMouseMove(e : MouseEvent) {
      if (!this.canvas) { return; }
      const rect = this.canvas.getBoundingClientRect();
      const mouse_x = e.clientX - rect.left;
      const mouse_y = e.clientY - rect.top;

      const { gridOffset, squareSize } = this.cfg;

      const start_x = Math.floor(gridOffset.x / squareSize) * squareSize;
      const start_y = Math.floor(gridOffset.y / squareSize) * squareSize;

      const hovered_square_x = Math.floor((mouse_x + gridOffset.x - start_x) / squareSize);
      const hovered_square_y = Math.floor((mouse_y + gridOffset.y - start_y) / squareSize);

      if (!this.hovered_square || this.hovered_square.x !== hovered_square_x || this.hovered_square.y !== hovered_square_y) {
        this.hovered_square = { x : hovered_square_x, y : hovered_square_y };
      }
    }

    handleMouseLeave() {
      this.hovered_square = null;
    }

    drawGrid() {
      if (!this.context) { return; }
      this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

      const { width, height } = this.canvas;
      const { gridOffset, squareSize } = this.cfg;

      const start_x = Math.floor(gridOffset.x / squareSize) * squareSize;
      const start_y = Math.floor(gridOffset.y / squareSize) * squareSize;

      for (let x = start_x; x < width + squareSize; x += squareSize) {
        for (let y = start_y; y < height + squareSize; y += squareSize) {
          const square_x = x - (gridOffset.x % squareSize);
          const square_y = y - (gridOffset.y % squareSize);
          
          if (this.hovered_square && 
          Math.floor((x - start_x) / squareSize) === this.hovered_square.x &&
          Math.floor((y - start_y) / squareSize) === this.hovered_square.y) {
            this.context.fillStyle = this.cfg.hoverFillColor;
            this.context.fillRect(square_x, square_y, squareSize, squareSize);
          }

          this.context.strokeStyle = this.cfg.borderColor;
          this.context.fillRect(square_x, square_y, squareSize, squareSize);
        }
      }
      
      const gradient = this.context.createRadialGradient(width / 2, height, 0, width / 2, height, Math.sqrt(width ** 2 + height ** 2) / 2);
      gradient.addColorStop(0, "rgba(0, 0, 0, 0)");
      gradient.addColorStop(1, "#060606");
      
      this.context.fillStyle = gradient;
      this.context.fillRect(0, 0, width, height);
    }

    updateAnimation() {
      const { speed, direction, squareSize, gridOffset } = this.cfg;

      const effective_speed = Math.max(speed, 0.1);

      switch(direction) {
        case "diagonal":
          gridOffset.x = (gridOffset.x - effective_speed + squareSize) % squareSize;
          gridOffset.y = (gridOffset.y - effective_speed + squareSize) % squareSize;
          break;
        case "up":
          gridOffset.y = (gridOffset.y + effective_speed + squareSize) % squareSize;
          break;
        case "right":
          gridOffset.x = (gridOffset.x - effective_speed + squareSize) % squareSize;
          break;
        case "down":
          gridOffset.y = (gridOffset.y - effective_speed + squareSize) % squareSize;
          break;
        case "left":
          gridOffset.x = (gridOffset.x + effective_speed + squareSize) % squareSize;
          break;
        default:
          break;
      }

      this.drawGrid();
      requestAnimationFrame(() => this.updateAnimation());
    }
  }

  const bg_cfg : IBgCfg = {
    direction : "left",
    squareSize : 40,
    speed : 1,
    gridOffset : { x : 0, y : 0 },
    borderColor : "#fff",
    hoverFillColor : "#222"
  };
  let bg_canvas_ref : HTMLCanvasElement;
  // svelte-ignore non_reactive_update
  let bg_object : BackgroundCanvas;

  onMount(() => { bg_object = new BackgroundCanvas(bg_canvas_ref, bg_cfg); })
</script>

<svelte:window on:resize={() => {
  bg_object.resizeHandler();
}} />

<canvas bind:this={bg_canvas_ref} class="w-dvw h-dvh" onmousemove={bg_object.handleMouseMove} onmouseleave={bg_object.handleMouseLeave}></canvas>

{@render children()}

<style>
	:global(body) {
		margin: 0;
		overflow-x: hidden;
	}
</style>