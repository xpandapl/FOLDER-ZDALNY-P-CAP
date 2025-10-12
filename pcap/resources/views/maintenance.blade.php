<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Trwają prace serwisowe</title>
  <style>
    :root{
      --bg1:#0f172a; /* slate-900 */
      --bg2:#1e293b; /* slate-800 */
      --accent:#22d3ee; /* cyan-400 */
      --accent2:#a78bfa; /* violet-400 */
      --card-bg:rgba(255,255,255,.07);
      --card-brd:rgba(255,255,255,.15);
      --text:#e2e8f0; /* slate-200 */
      --muted:#94a3b8; /* slate-400 */
    }
    *{box-sizing:border-box}
    body{margin:0;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Noto Sans","Helvetica Neue",Arial,"Apple Color Emoji","Segoe UI Emoji";color:var(--text);min-height:100vh;display:grid;place-items:center;background:
      radial-gradient(1200px 600px at 10% 10%, rgba(34,211,238,.15), transparent 60%),
      radial-gradient(900px 500px at 90% 20%, rgba(167,139,250,.12), transparent 55%),
      linear-gradient(160deg, var(--bg1), var(--bg2));
      overflow:hidden}
    .orbs{position:absolute;inset:0;pointer-events:none;z-index:0}
    .orb{position:absolute;filter:blur(40px);opacity:.25;border-radius:50%}
    .orb.cyan{background:radial-gradient(circle at center, rgba(34,211,238,.65), transparent 60%);width:400px;height:400px;left:-60px;bottom:-80px}
    .orb.violet{background:radial-gradient(circle at center, rgba(167,139,250,.6), transparent 60%);width:420px;height:420px;right:-60px;top:-80px}

    .card{position:relative;z-index:1;backdrop-filter: blur(12px);-webkit-backdrop-filter: blur(12px);
      background:var(--card-bg); border:1px solid var(--card-brd); border-radius:20px; padding:28px; width:min(840px,92vw); box-shadow:0 20px 60px rgba(0,0,0,.25)}
    .card-inner{display:grid;grid-template-columns: 1.1fr 1fr;gap:24px;align-items:center}
    .media{display:flex;align-items:center;justify-content:center;background:linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));border:1px solid var(--card-brd);border-radius:16px;overflow:hidden;padding:14px}
    .media img, .media video{max-width:100%;height:auto;display:block}
  /* Tetris animation */
  .tetris-center{width:min(520px,92%)}
  .tetris-grid{width:100%;aspect-ratio:10/16;display:grid;grid-template-columns:repeat(10,1fr);grid-template-rows:repeat(16,1fr);gap:2px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.18);border-radius:10px;padding:6px;box-shadow:inset 0 8px 24px rgba(0,0,0,.18)}
  .cell{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:3px;box-shadow:inset 0 1px 0 rgba(255,255,255,.05)}
  .c1{background:linear-gradient(180deg, rgba(103,232,249,.95), rgba(34,211,238,.7));border-color:rgba(103,232,249,.8)} /* cyan */
  .c2{background:linear-gradient(180deg, rgba(196,181,253,.95), rgba(167,139,250,.7));border-color:rgba(196,181,253,.8)} /* violet */
  .c3{background:linear-gradient(180deg, rgba(129,140,248,.95), rgba(99,102,241,.7));border-color:rgba(129,140,248,.8)} /* indigo */
  .flash{animation:row-flash .35s ease-in-out 3}
  @keyframes row-flash{50%{filter:brightness(1.9)}0%,100%{filter:brightness(1)}}
  /* removed laptop base */
    .content h1{font-size:clamp(22px,3.2vw,32px);margin:0 0 10px;letter-spacing:.2px}
    .content p{margin:8px 0;color:var(--muted);line-height:1.6}
    .badge{display:inline-flex;align-items:center;gap:8px;background:rgba(34,211,238,.12);border:1px solid rgba(34,211,238,.25);color:#67e8f9;padding:6px 10px;border-radius:999px;font-size:12px;margin-bottom:12px}
    .row{display:flex;gap:12px;flex-wrap:wrap;margin-top:12px}
    .pill{font-size:12px;color:#c4b5fd;background:rgba(167,139,250,.12);border:1px solid rgba(167,139,250,.25);padding:6px 10px;border-radius:999px}
    .hint{font-size:12px;color:var(--muted);margin-top:10px}

    @media (max-width: 860px){
      .card-inner{grid-template-columns:1fr}
    }
      @media (prefers-reduced-motion: reduce){
        .mechanic .wrench-orbit, .mechanic .nut{animation:none !important}
      }
  </style>
</head>
<body>
  <div class="orbs">
    <div class="orb cyan"></div>
    <div class="orb violet"></div>
  </div>
  <div class="card">
    <div class="card-inner">
      <div class="media" aria-hidden="true">
        <div class="tetris-center"><div id="tetris" class="tetris-grid"></div></div>
      </div>
      <div class="content">
        <span class="badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 8v8m4-4H8m12 0a8 8 0 11-16 0 8 8 0 0116 0z" stroke="#67e8f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Trwają prace serwisowe
        </span>
        <h1>Pracujemy nad usprawnieniami</h1>
        <p>Na chwilę wstrzymaliśmy dostęp do aplikacji, aby bezpiecznie wdrożyć poprawki i nowe funkcje. Dziękujemy za cierpliwość!</p>
        <div class="row">
          <span class="pill">Bezpieczeństwo danych</span>
          <span class="pill">Stabilność</span>
          <span class="pill">Nowe funkcje</span>
        </div>
        <p class="hint">Szacowany koniec prac: do 30 października. Wprowadzanie danych w tym czasie może nie zostać zapisane.</p>
      </div>
    </div>
  </div>
</body>
<script>
  (function(){
    const W=10, H=16, el=document.getElementById('tetris');
    if(!el) return;
    const cells=[]; for(let r=0;r<H;r++){ for(let c=0;c<W;c++){ const d=document.createElement('div'); d.className='cell'; el.appendChild(d); cells.push(d);} }
    const board=Array.from({length:H},()=>Array(W).fill(0));
    // Single shape: pixel-art lowercase 'a.' (6x6)
    const Ashape = { m:[
      [0,1,1,0,0,0],
      [0,0,0,1,0,0],
      [0,1,1,1,0,0],
      [1,0,0,1,0,0],
      [0,1,1,1,0,1]
    ], c:2 };
    const SHAPES=[Ashape];
    function canPlace(m,x,y){ for(let r=0;r<m.length;r++) for(let c=0;c<m[0].length;c++) if(m[r][c]){ const nx=x+c, ny=y+r; if(nx<0||nx>=W||ny>=H|| (ny>=0 && board[ny][nx])) return false;} return true; }
    function place(m,x,y,c){ for(let r=0;r<m.length;r++) for(let c2=0;c2<m[0].length;c2++) if(m[r][c2]){ const nx=x+c2, ny=y+r; if(ny>=0) board[ny][nx]=c; } }
    function clearLines(){ let cleared=0; for(let r=H-1;r>=0;r--){ if(board[r].every(v=>v)){ // flash
          for(let c=0;c<W;c++) cells[r*W+c].classList.add('flash');
          cleared++; board.splice(r,1); board.unshift(Array(W).fill(0)); r++; } }
      // remove flash after delay
      if(cleared){ setTimeout(()=>{ cells.forEach(n=>n.classList.remove('flash')); }, 400); }
    }
    function render(active){ for(let i=0;i<cells.length;i++) cells[i].className='cell';
      for(let y=0;y<H;y++) for(let x=0;x<W;x++) if(board[y][x]) cells[y*W+x].classList.add('c'+board[y][x]);
      if(active){ const {m,x,y,c}=active; for(let r=0;r<m.length;r++) for(let cc=0;cc<m[0].length;cc++) if(m[r][cc]){ const nx=x+cc, ny=y+r; if(ny>=0 && nx>=0 && nx<W && ny<H) cells[ny*W+nx].classList.add('c'+c); } }
    }
    function randomPiece(){ const s=SHAPES[0]; const m=s.m.map(row=>row.slice());
      const w=m[0].length; const c = 1 + Math.floor(Math.random()*3); // vary color
      return {m,x:Math.floor((W-w)/2), y:-m.length, c}; }
    let piece=randomPiece(), tick=0, speed=140, resets=0;
    // Respect reduced motion: render static centered 'a.' and stop
    if(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches){
      const s=SHAPES[0]; const w=s.m[0].length; const h=s.m.length; const startX=Math.floor((W-w)/2), startY=Math.floor((H-h)/2);
      place(s.m,startX,startY,2); render(); return;
    }
    function loop(){ tick++; const down = (tick%1===0);
      if(down){ if(canPlace(piece.m,piece.x,piece.y+1)){ piece.y++; } else { place(piece.m,piece.x,piece.y,piece.c); clearLines(); piece=randomPiece(); resets++; if(resets>20){ // periodic reset for infinite loop aesthetics
              for(let r=0;r<H;r++) for(let c=0;c<W;c++) board[r][c]=0; resets=0; }
        } }
      // simple horizontal wiggle to mimic moves
      if(Math.random()<0.2){ const dir=Math.random()<0.5?-1:1; if(canPlace(piece.m,piece.x+dir,piece.y)) piece.x+=dir; }
      render(piece); setTimeout(loop,speed); }
    loop();
  })();
</script>
</html>
