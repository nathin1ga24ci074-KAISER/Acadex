<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acadex — Academic Publication Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Mono:wght@300;400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
 
:root{
  --bg:#0a0a0a;
  --cream:#f0ead6;
  --gold:#c9a84c;
  --blue:#2d5af0;
  --red:#d63031;
  --text:#f0ead6;
  --dim:rgba(240,234,214,0.35);
  --font-display:'Bebas Neue',sans-serif;
  --font-mono:'DM Mono',monospace;
  --font-body:'DM Sans',sans-serif;
}
 
html,body{
  width:100%;height:100%;
  overflow:hidden;
  background:var(--bg);
  color:var(--text);
  font-family:var(--font-body);
  cursor:none;
}
 
/* ── CUSTOM CURSOR ── */
#cursor{
  position:fixed;width:12px;height:12px;
  border-radius:50%;background:var(--cream);
  pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);
  transition:transform .12s,width .2s,height .2s,background .2s;
  mix-blend-mode:difference;
}
#cursor.big{width:48px;height:48px;}
 
/* ── HORIZONTAL TRACK ── */
#track{
  display:flex;
  width:max-content;
  height:100vh;
  will-change:transform;
  transition:transform .9s cubic-bezier(.16,1,.3,1);
}
 
/* ── PANELS ── */
.panel{
  flex-shrink:0;
  width:100vw;
  height:100vh;
  position:relative;
  overflow:hidden;
  display:flex;
  align-items:center;
  justify-content:center;
}
 
/* ── NOISE OVERLAY ── */
.panel::after{
  content:'';
  position:absolute;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.04'/%3E%3C/svg%3E");
  pointer-events:none;opacity:.6;z-index:2;
}
 
/* ── PANEL 0: HERO ── */
.p0{background:var(--bg);}
 
.hero-bg-text{
  position:absolute;
  font-family:var(--font-display);
  font-size:clamp(140px,18vw,280px);
  letter-spacing:-0.02em;
  color:rgba(240,234,214,0.04);
  line-height:0.9;
  top:50%;transform:translateY(-55%);
  white-space:nowrap;
  user-select:none;
  z-index:0;
}
 
.hero-content{
  position:relative;z-index:3;
  display:flex;flex-direction:column;
  align-items:center;text-align:center;
  gap:2rem;
}
 
.hero-eyebrow{
  font-family:var(--font-mono);
  font-size:11px;letter-spacing:3px;
  color:var(--gold);
  text-transform:uppercase;
  border:1px solid rgba(201,168,76,0.3);
  padding:6px 16px;
  border-radius:2px;
}
 
.hero-title{
  font-family:var(--font-display);
  font-size:clamp(72px,12vw,180px);
  line-height:0.88;
  letter-spacing:-0.01em;
  mix-blend-mode:normal;
}
 
.hero-title span{color:var(--gold);}
 
.hero-sub{
  font-size:clamp(14px,1.2vw,17px);
  color:var(--dim);
  max-width:420px;
  line-height:1.7;
  font-weight:300;
}
 
.hero-actions{
  display:flex;gap:1rem;
  align-items:center;
  margin-top:0.5rem;
}
 
.btn-primary{
  background:var(--cream);
  color:var(--bg);
  font-family:var(--font-mono);
  font-size:11px;letter-spacing:2px;
  text-transform:uppercase;
  padding:14px 28px;
  border:none;cursor:pointer;
  text-decoration:none;
  display:inline-block;
  font-weight:500;
  transition:background .2s;
}
.btn-primary:hover{background:var(--gold);}
 
.btn-ghost{
  font-family:var(--font-mono);
  font-size:11px;letter-spacing:2px;
  text-transform:uppercase;
  color:var(--dim);
  text-decoration:none;
  padding:14px 0;
  border-bottom:1px solid rgba(240,234,214,0.2);
  transition:color .2s,border-color .2s;
}
.btn-ghost:hover{color:var(--cream);border-color:var(--cream);}
 
/* scroll hint */
.scroll-hint{
  position:absolute;bottom:2.5rem;left:50%;transform:translateX(-50%);
  display:flex;flex-direction:column;align-items:center;gap:8px;
  z-index:3;animation:pulse 2s ease infinite;
}
.scroll-hint span{
  font-family:var(--font-mono);font-size:9px;letter-spacing:3px;
  color:var(--dim);text-transform:uppercase;
}
.scroll-line{width:1px;height:40px;background:var(--dim);transform-origin:top;animation:grow 2s ease infinite;}
@keyframes pulse{0%,100%{opacity:.4}50%{opacity:1}}
@keyframes grow{0%{transform:scaleY(0)}50%{transform:scaleY(1)}100%{transform:scaleY(0)}}
 
/* panel number */
.panel-num{
  position:absolute;bottom:2.5rem;right:2.5rem;
  font-family:var(--font-mono);font-size:10px;
  letter-spacing:2px;color:var(--dim);
  z-index:3;
}
 
/* ── PANEL 1: STATS ── */
.p1{background:#111;}
 
.stats-scene{
  position:relative;z-index:3;
  width:90vw;max-width:1100px;
  display:grid;grid-template-columns:1fr 1fr;
  gap:1px;
  border:1px solid rgba(240,234,214,0.08);
}
 
.stat-block{
  padding:4rem 3.5rem;
  border:1px solid rgba(240,234,214,0.06);
  display:flex;flex-direction:column;
  gap:0.75rem;
  position:relative;
  overflow:hidden;
  transition:background .3s;
  cursor:default;
}
.stat-block:hover{background:rgba(240,234,214,0.03);}
 
.stat-block::before{
  content:attr(data-n);
  position:absolute;
  right:2rem;top:50%;transform:translateY(-50%);
  font-family:var(--font-display);
  font-size:10rem;
  color:rgba(240,234,214,0.03);
  line-height:1;
  pointer-events:none;
}
 
.stat-num{
  font-family:var(--font-display);
  font-size:clamp(64px,6vw,96px);
  line-height:1;
  color:var(--cream);
}
.stat-num sup{font-size:0.4em;vertical-align:super;color:var(--gold);}
 
.stat-label{
  font-family:var(--font-mono);
  font-size:10px;letter-spacing:3px;
  color:var(--dim);text-transform:uppercase;
}
 
.stat-desc{
  font-size:13px;color:rgba(240,234,214,0.4);
  line-height:1.6;margin-top:0.5rem;
  max-width:220px;
}
 
.p1-header{
  position:absolute;top:3rem;left:50%;transform:translateX(-50%);
  font-family:var(--font-display);
  font-size:clamp(48px,5vw,72px);
  letter-spacing:0.05em;
  color:rgba(240,234,214,0.06);
  white-space:nowrap;
  z-index:1;
  text-transform:uppercase;
}
 
/* ── PANEL 2: PAPERS (dark editorial) ── */
.p2{background:var(--cream);}
 
.papers-scene{
  position:relative;z-index:3;
  width:90vw;max-width:1100px;
  display:grid;
  grid-template-columns:340px 1fr;
  gap:4rem;
  align-items:center;
}
 
.papers-left h2{
  font-family:var(--font-display);
  font-size:clamp(60px,7vw,100px);
  color:#0a0a0a;
  line-height:0.9;
  letter-spacing:0.02em;
}
.papers-left h2 em{
  font-style:normal;
  color:var(--blue);
  -webkit-text-stroke:0px;
}
.papers-left p{
  margin-top:1.5rem;
  font-size:13px;
  line-height:1.7;
  color:rgba(10,10,10,0.5);
  max-width:260px;
}
.papers-left a{
  display:inline-flex;align-items:center;gap:8px;
  margin-top:1.5rem;
  font-family:var(--font-mono);font-size:10px;
  letter-spacing:2px;text-transform:uppercase;
  color:#0a0a0a;text-decoration:none;
  border-bottom:1px solid #0a0a0a;
  padding-bottom:2px;
  transition:color .2s,border-color .2s;
}
.papers-left a:hover{color:var(--blue);border-color:var(--blue);}
 
.paper-list-editorial{
  display:flex;flex-direction:column;
  gap:0;
}
 
.paper-item{
  padding:1.4rem 0;
  border-bottom:1px solid rgba(10,10,10,0.1);
  display:grid;
  grid-template-columns:48px 1fr auto;
  gap:1.25rem;
  align-items:start;
  cursor:pointer;
  transition:background .15s;
}
.paper-item:first-child{border-top:1px solid rgba(10,10,10,0.1);}
.paper-item:hover .pi-title{color:var(--blue);}
 
.pi-num{
  font-family:var(--font-display);
  font-size:2rem;color:rgba(10,10,10,0.15);
  line-height:1.1;
}
.pi-title{
  font-size:14px;font-weight:500;
  color:#0a0a0a;line-height:1.4;
  transition:color .2s;
}
.pi-meta{
  font-family:var(--font-mono);
  font-size:9px;letter-spacing:1px;
  color:rgba(10,10,10,0.35);
  text-transform:uppercase;
  margin-top:4px;
}
.pi-cite{
  font-family:var(--font-display);
  font-size:1.8rem;
  color:rgba(10,10,10,0.15);
  line-height:1.2;
  white-space:nowrap;
  text-align:right;
}
.pi-cite-label{
  font-family:var(--font-mono);font-size:8px;
  letter-spacing:1px;color:rgba(10,10,10,0.3);
  text-transform:uppercase;display:block;
}
 
/* ── PANEL 3: AUTHORS ── */
.p3{background:#0d0d0d;}
 
.authors-scene{
  position:relative;z-index:3;
  width:90vw;max-width:1200px;
}
 
.authors-title{
  font-family:var(--font-display);
  font-size:clamp(80px,10vw,140px);
  letter-spacing:0.04em;
  color:var(--cream);
  line-height:0.88;
  margin-bottom:3.5rem;
}
.authors-title span{
  color:transparent;
  -webkit-text-stroke:1px rgba(240,234,214,0.3);
}
 
.author-grid{
  display:grid;
  grid-template-columns:repeat(5,1fr);
  gap:1px;
  background:rgba(240,234,214,0.05);
}
 
.author-card{
  background:#0d0d0d;
  padding:2rem 1.5rem;
  display:flex;flex-direction:column;gap:0.5rem;
  cursor:pointer;
  transition:background .2s;
  border:1px solid transparent;
}
.author-card:hover{
  background:#1a1a1a;
  border-color:rgba(240,234,214,0.08);
}
 
.ac-initials{
  width:44px;height:44px;
  border-radius:50%;
  background:rgba(240,234,214,0.06);
  border:1px solid rgba(240,234,214,0.1);
  display:flex;align-items:center;justify-content:center;
  font-family:var(--font-mono);font-size:12px;
  color:var(--dim);margin-bottom:0.75rem;
}
 
.ac-name{
  font-size:14px;font-weight:500;
  color:var(--cream);line-height:1.3;
}
.ac-inst{
  font-family:var(--font-mono);font-size:9px;
  letter-spacing:1px;color:var(--dim);
  text-transform:uppercase;
}
.ac-area{
  font-size:11px;color:rgba(240,234,214,0.3);
  margin-top:4px;line-height:1.4;
}
.ac-stats{
  display:flex;gap:1rem;margin-top:auto;padding-top:1rem;
}
.ac-stat span{
  font-family:var(--font-display);font-size:1.4rem;
  color:var(--gold);display:block;line-height:1;
}
.ac-stat label{
  font-family:var(--font-mono);font-size:8px;
  letter-spacing:1px;color:var(--dim);
  text-transform:uppercase;
}
 
/* ── PANEL 4: CITATIONS ── */
.p4{background:#f5f0e8;}
 
.citations-scene{
  position:relative;z-index:3;
  width:90vw;max-width:1100px;
  display:grid;
  grid-template-columns:1fr 480px;
  gap:5rem;
  align-items:center;
}
 
.cit-left{display:flex;flex-direction:column;gap:2rem;}
 
.cit-big{
  font-family:var(--font-display);
  font-size:clamp(100px,12vw,180px);
  line-height:0.85;
  color:#0a0a0a;
  letter-spacing:-0.01em;
}
.cit-big span{
  display:block;
  color:transparent;
  -webkit-text-stroke:1.5px #0a0a0a;
}
 
.cit-desc{
  font-size:13px;color:rgba(10,10,10,0.45);
  line-height:1.8;max-width:300px;
}
 
.cit-network{
  position:relative;
  height:340px;
}
 
.cit-node{
  position:absolute;
  border:1px solid rgba(10,10,10,0.15);
  border-radius:2px;
  padding:8px 14px;
  font-size:10px;
  font-family:var(--font-mono);
  letter-spacing:1px;
  color:rgba(10,10,10,0.6);
  background:rgba(255,255,255,0.6);
  backdrop-filter:blur(4px);
  white-space:nowrap;
  cursor:default;
  transition:all .3s;
}
.cit-node:hover{background:#0a0a0a;color:#f5f0e8;border-color:#0a0a0a;}
.cit-node.main{
  background:#0a0a0a;color:#f5f0e8;
  border-color:#0a0a0a;
  font-size:11px;
  padding:10px 18px;
}
 
.cit-line{
  position:absolute;
  height:1px;
  background:rgba(10,10,10,0.12);
  transform-origin:left center;
  pointer-events:none;
}
 
/* ── PANEL 5: ANALYTICS ── */
.p5{background:var(--bg);}
 
.analytics-scene{
  position:relative;z-index:3;
  width:90vw;max-width:1100px;
}
 
.analytics-header{
  display:flex;justify-content:space-between;
  align-items:flex-end;
  margin-bottom:3rem;
}
 
.analytics-title{
  font-family:var(--font-display);
  font-size:clamp(60px,7vw,96px);
  line-height:0.88;
  color:var(--cream);
}
.analytics-title span{color:var(--gold);}
 
.analytics-sub{
  font-family:var(--font-mono);font-size:10px;
  letter-spacing:2px;color:var(--dim);
  text-transform:uppercase;max-width:200px;
  text-align:right;line-height:1.7;
}
 
.bars{
  display:flex;flex-direction:column;gap:0;
}
 
.bar-row-xl{
  display:grid;
  grid-template-columns:180px 1fr 60px;
  gap:1.5rem;
  align-items:center;
  padding:1rem 0;
  border-bottom:1px solid rgba(240,234,214,0.05);
  cursor:default;
}
.bar-row-xl:hover .bar-xl-fill{opacity:1;}
 
.bar-xl-label{
  font-family:var(--font-mono);font-size:10px;
  letter-spacing:1px;color:var(--dim);
  text-transform:uppercase;text-align:right;
}
 
.bar-xl-track{
  height:2px;
  background:rgba(240,234,214,0.06);
  position:relative;
}
.bar-xl-fill{
  height:100%;
  background:var(--cream);
  opacity:0.6;
  transition:width 1.2s cubic-bezier(.25,1,.5,1),opacity .2s;
  width:0;
}
.bar-xl-val{
  font-family:var(--font-display);font-size:1.8rem;
  color:var(--cream);text-align:right;
  line-height:1;
}
 
/* ── PANEL 6: CTA ── */
.p6{background:#111;}
 
.cta-scene{
  position:relative;z-index:3;
  text-align:center;
  display:flex;flex-direction:column;
  align-items:center;gap:2rem;
}
 
.cta-overline{
  font-family:var(--font-mono);font-size:10px;
  letter-spacing:4px;color:var(--gold);
  text-transform:uppercase;
}
 
.cta-title{
  font-family:var(--font-display);
  font-size:clamp(64px,9vw,140px);
  line-height:0.88;
  color:var(--cream);
  letter-spacing:0.02em;
}
 
.cta-title strong{
  color:transparent;
  -webkit-text-stroke:1px var(--cream);
}
 
.cta-links{
  display:flex;gap:1.5rem;
  flex-wrap:wrap;justify-content:center;
}
 
.cta-link{
  font-family:var(--font-mono);font-size:10px;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--dim);text-decoration:none;
  padding:14px 28px;
  border:1px solid rgba(240,234,214,0.15);
  transition:all .2s;
}
.cta-link:hover{
  background:var(--cream);color:var(--bg);
  border-color:var(--cream);
}
.cta-link.accent{
  background:var(--gold);color:#0a0a0a;
  border-color:var(--gold);
}
.cta-link.accent:hover{background:var(--cream);border-color:var(--cream);}
 
.cta-footer{
  position:absolute;bottom:2rem;left:0;right:0;
  display:flex;justify-content:space-between;
  padding:0 3rem;z-index:3;
}
.cta-footer span{
  font-family:var(--font-mono);font-size:9px;
  letter-spacing:2px;color:var(--dim);
  text-transform:uppercase;
}
 
/* ── NAV ── */
nav{
  position:fixed;top:0;left:0;right:0;
  z-index:100;
  display:flex;justify-content:space-between;align-items:center;
  padding:1.5rem 3rem;
}
 
.nav-logo{
  font-family:var(--font-display);font-size:1.8rem;
  letter-spacing:0.1em;color:var(--cream);
  text-decoration:none;
  mix-blend-mode:difference;
}
 
.nav-dots{
  display:flex;gap:8px;align-items:center;
}
.nav-dot{
  width:6px;height:6px;border-radius:50%;
  background:var(--dim);
  cursor:pointer;
  transition:background .3s,transform .3s;
}
.nav-dot.active{background:var(--cream);transform:scale(1.5);}
 
.nav-right{
  display:flex;gap:2rem;align-items:center;
}
.nav-right a{
  font-family:var(--font-mono);font-size:9px;
  letter-spacing:2px;color:var(--dim);
  text-decoration:none;text-transform:uppercase;
  transition:color .2s;
}
.nav-right a:hover{color:var(--cream);}
 
/* ── MARQUEE ── */
.marquee-wrap{
  position:fixed;bottom:0;left:0;right:0;
  height:36px;overflow:hidden;
  border-top:1px solid rgba(240,234,214,0.06);
  z-index:50;
  background:rgba(10,10,10,0.8);
  backdrop-filter:blur(8px);
  display:flex;align-items:center;
}
.marquee-inner{
  display:flex;gap:0;
  animation:marquee 24s linear infinite;
  white-space:nowrap;
}
.marquee-item{
  font-family:var(--font-mono);font-size:9px;
  letter-spacing:3px;color:var(--dim);
  text-transform:uppercase;
  padding:0 3rem;
}
.marquee-item span{color:var(--gold);margin-right:0.5rem;}
@keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
 
/* ── PANEL PROGRESS LINE ── */
.progress-line{
  position:fixed;bottom:36px;left:0;height:1px;
  background:var(--gold);z-index:80;
  transition:width .9s cubic-bezier(.16,1,.3,1);
}
</style>
</head>
<body>
 
<div id="cursor"></div>
 
<!-- NAV -->
<nav>
  <a href="#" class="nav-logo">ACADEX</a>
  <div class="nav-dots" id="nav-dots">
    <div class="nav-dot active" data-panel="0"></div>
    <div class="nav-dot" data-panel="1"></div>
    <div class="nav-dot" data-panel="2"></div>
    <div class="nav-dot" data-panel="3"></div>
    <div class="nav-dot" data-panel="4"></div>
    <div class="nav-dot" data-panel="5"></div>
    <div class="nav-dot" data-panel="6"></div>
  </div>
  <div class="nav-right">
    <a href="papers.php">Papers</a>
    <a href="authors.php">Authors</a>
    <a href="analytics.php">Analytics</a>
    <a href="add_paper.php" style="color:var(--gold);">+ Add</a>
  </div>
</nav>
 
<!-- HORIZONTAL TRACK -->
<div id="track">
 
  <!-- ── PANEL 0: HERO ── -->
  <div class="panel p0">
    <div class="hero-bg-text">KNOWLEDGE</div>
    <div class="hero-content">
      <div class="hero-eyebrow">Academic Publication System · DBMS Project</div>
      <h1 class="hero-title">ACAD<span>EX</span></h1>
      <p class="hero-sub">A research publication management platform — tracking papers, authors, citations, and knowledge across institutions worldwide.</p>
      <div class="hero-actions">
        <a href="dashboard.php" class="btn-primary">Enter Dashboard</a>
        <a href="papers.php" class="btn-ghost">Browse Papers →</a>
      </div>
    </div>
    <div class="scroll-hint">
      <span>Scroll to explore</span>
      <div class="scroll-line"></div>
    </div>
    <div class="panel-num">01 / 07</div>
  </div>
 
  <!-- ── PANEL 1: STATS ── -->
  <div class="panel p1">
    <div class="p1-header">DATABASE</div>
    <div class="stats-scene">
      <div class="stat-block" data-n="1">
        <div class="stat-num" id="s-papers">05<sup>+</sup></div>
        <div class="stat-label">Research Papers</div>
        <div class="stat-desc">Indexed with full metadata, abstracts, DOIs, and venue information.</div>
      </div>
      <div class="stat-block" data-n="2">
        <div class="stat-num" id="s-authors">05<sup>+</sup></div>
        <div class="stat-label">Registered Authors</div>
        <div class="stat-desc">Researchers linked to institutions with h-index tracking.</div>
      </div>
      <div class="stat-block" data-n="3">
        <div class="stat-num" id="s-citations">05<sup>+</sup></div>
        <div class="stat-label">Citations Tracked</div>
        <div class="stat-desc">Full citation network with context and cross-references.</div>
      </div>
      <div class="stat-block" data-n="4">
        <div class="stat-num" id="s-inst">05<sup>+</sup></div>
        <div class="stat-label">Institutions</div>
        <div class="stat-desc">Universities and research centres from five countries.</div>
      </div>
    </div>
    <div class="panel-num">02 / 07</div>
  </div>
 
  <!-- ── PANEL 2: PAPERS ── -->
  <div class="panel p2">
    <div class="papers-scene">
      <div class="papers-left">
        <h2>RE<br>SEARCH<br><em>PAPERS</em></h2>
        <p>Full-text searchable archive of academic papers with FULLTEXT MySQL indexing, filter by year, status, and venue type.</p>
        <a href="papers.php">Browse all papers →</a>
      </div>
      <div class="paper-list-editorial">
        <div class="paper-item">
          <div class="pi-num">01</div>
          <div>
            <div class="pi-title">Attention Is All You Need</div>
            <div class="pi-meta">NeurIPS · 2017 · Published</div>
          </div>
          <div class="pi-cite">03<span class="pi-cite-label">cites</span></div>
        </div>
        <div class="paper-item">
          <div class="pi-num">02</div>
          <div>
            <div class="pi-title">BERT: Pre-training of Deep Bidirectional Transformers</div>
            <div class="pi-meta">NeurIPS · 2019 · Published</div>
          </div>
          <div class="pi-cite">02<span class="pi-cite-label">cites</span></div>
        </div>
        <div class="paper-item">
          <div class="pi-num">03</div>
          <div>
            <div class="pi-title">Deep Residual Learning for Image Recognition</div>
            <div class="pi-meta">NeurIPS · 2016 · Published</div>
          </div>
          <div class="pi-cite">01<span class="pi-cite-label">cites</span></div>
        </div>
        <div class="paper-item">
          <div class="pi-num">04</div>
          <div>
            <div class="pi-title">ImageNet Large Scale Visual Recognition Challenge</div>
            <div class="pi-meta">IEEE Trans. AI · 2015 · Published</div>
          </div>
          <div class="pi-cite">01<span class="pi-cite-label">cites</span></div>
        </div>
        <div class="paper-item">
          <div class="pi-num">05</div>
          <div>
            <div class="pi-title">Scalable Distributed Database Architectures</div>
            <div class="pi-meta">ACM SIGMOD · 2022 · Published</div>
          </div>
          <div class="pi-cite">00<span class="pi-cite-label">cites</span></div>
        </div>
      </div>
    </div>
    <div class="panel-num" style="color:rgba(10,10,10,0.3);">03 / 07</div>
  </div>
 
  <!-- ── PANEL 3: AUTHORS ── -->
  <div class="panel p3">
    <div class="authors-scene">
      <div class="authors-title">RE<br>SEARCH<br><span>ERS</span></div>
      <div class="author-grid">
        <div class="author-card">
          <div class="ac-initials">YL</div>
          <div class="ac-name">Yann LeCun</div>
          <div class="ac-inst">MIT</div>
          <div class="ac-area">Deep Learning, Computer Vision</div>
          <div class="ac-stats">
            <div class="ac-stat"><span>175</span><label>h-index</label></div>
            <div class="ac-stat"><span>02</span><label>papers</label></div>
          </div>
        </div>
        <div class="author-card">
          <div class="ac-initials">FL</div>
          <div class="ac-name">Fei-Fei Li</div>
          <div class="ac-inst">Stanford</div>
          <div class="ac-area">Computer Vision, AI</div>
          <div class="ac-stats">
            <div class="ac-stat"><span>90</span><label>h-index</label></div>
            <div class="ac-stat"><span>02</span><label>papers</label></div>
          </div>
        </div>
        <div class="author-card">
          <div class="ac-initials">PS</div>
          <div class="ac-name">Priya Sharma</div>
          <div class="ac-inst">IIT Bombay</div>
          <div class="ac-area">Natural Language Processing</div>
          <div class="ac-stats">
            <div class="ac-stat"><span>12</span><label>h-index</label></div>
            <div class="ac-stat"><span>02</span><label>papers</label></div>
          </div>
        </div>
        <div class="author-card">
          <div class="ac-initials">GH</div>
          <div class="ac-name">Geoffrey Hinton</div>
          <div class="ac-inst">Cambridge</div>
          <div class="ac-area">Neural Networks</div>
          <div class="ac-stats">
            <div class="ac-stat"><span>150</span><label>h-index</label></div>
            <div class="ac-stat"><span>02</span><label>papers</label></div>
          </div>
        </div>
        <div class="author-card">
          <div class="ac-initials">AK</div>
          <div class="ac-name">Aditya Kumar</div>
          <div class="ac-inst">ETH Zurich</div>
          <div class="ac-area">Distributed Systems</div>
          <div class="ac-stats">
            <div class="ac-stat"><span>18</span><label>h-index</label></div>
            <div class="ac-stat"><span>01</span><label>papers</label></div>
          </div>
        </div>
      </div>
    </div>
    <div class="panel-num">04 / 07</div>
  </div>
 
  <!-- ── PANEL 4: CITATIONS ── -->
  <div class="panel p4">
    <div class="citations-scene">
      <div class="cit-left">
        <div class="cit-big">CIT<span>ATION</span></div>
        <div class="cit-desc">A full citation graph linking papers across disciplines, years, and institutions. Self-citation prevention enforced at the database trigger level.</div>
        <a href="citations.php" style="display:inline-flex;align-items:center;gap:8px;font-family:var(--font-mono);font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#0a0a0a;text-decoration:none;border-bottom:1px solid #0a0a0a;padding-bottom:2px;">View Citation Network →</a>
      </div>
      <div class="cit-network" id="cit-net">
        <!-- Positioned by JS -->
        <div class="cit-node main" style="top:130px;left:150px;">Attention Is All You Need</div>
        <div class="cit-node" style="top:30px;left:10px;">BERT</div>
        <div class="cit-node" style="top:50px;right:20px;">ResNet</div>
        <div class="cit-node" style="bottom:80px;left:0px;">Distrib. DB</div>
        <div class="cit-node" style="bottom:30px;right:40px;">ImageNet</div>
        <svg style="position:absolute;inset:0;width:100%;height:100%;pointer-events:none;">
          <line x1="250" y1="148" x2="100" y2="55" stroke="rgba(10,10,10,0.1)" stroke-width="1"/>
          <line x1="310" y1="148" x2="400" y2="65" stroke="rgba(10,10,10,0.1)" stroke-width="1"/>
          <line x1="230" y1="170" x2="80" y2="270" stroke="rgba(10,10,10,0.1)" stroke-width="1"/>
          <line x1="290" y1="170" x2="400" y2="305" stroke="rgba(10,10,10,0.1)" stroke-width="1"/>
        </svg>
      </div>
    </div>
    <div class="panel-num" style="color:rgba(10,10,10,0.2);">05 / 07</div>
  </div>
 
  <!-- ── PANEL 5: ANALYTICS ── -->
  <div class="panel p5">
    <div class="analytics-scene">
      <div class="analytics-header">
        <div class="analytics-title">DATA<br><span>INSIGHTS</span></div>
        <div class="analytics-sub">Live analytics powered by MySQL views, joins, and aggregate queries.</div>
      </div>
      <div class="bars" id="bars">
        <div class="bar-row-xl">
          <div class="bar-xl-label">NeurIPS</div>
          <div class="bar-xl-track"><div class="bar-xl-fill" data-w="100%"></div></div>
          <div class="bar-xl-val">03</div>
        </div>
        <div class="bar-row-xl">
          <div class="bar-xl-label">IEEE Trans. AI</div>
          <div class="bar-xl-track"><div class="bar-xl-fill" data-w="33%"></div></div>
          <div class="bar-xl-val">01</div>
        </div>
        <div class="bar-row-xl">
          <div class="bar-xl-label">ACM SIGMOD</div>
          <div class="bar-xl-track"><div class="bar-xl-fill" data-w="33%"></div></div>
          <div class="bar-xl-val">01</div>
        </div>
        <div class="bar-row-xl">
          <div class="bar-xl-label">Nature</div>
          <div class="bar-xl-track"><div class="bar-xl-fill" data-w="0%"></div></div>
          <div class="bar-xl-val">00</div>
        </div>
        <div class="bar-row-xl">
          <div class="bar-xl-label">JMLR</div>
          <div class="bar-xl-track"><div class="bar-xl-fill" data-w="0%"></div></div>
          <div class="bar-xl-val">00</div>
        </div>
      </div>
    </div>
    <div class="panel-num">06 / 07</div>
  </div>
 
  <!-- ── PANEL 6: CTA ── -->
  <div class="panel p6">
    <div class="cta-scene">
      <div class="cta-overline">Ready to explore?</div>
      <div class="cta-title">START<br><strong>YOUR</strong><br>RESEARCH</div>
      <div class="cta-links">
        <a href="dashboard.php" class="cta-link accent">Dashboard</a>
        <a href="papers.php" class="cta-link">Browse Papers</a>
        <a href="add_paper.php" class="cta-link">Add Paper</a>
        <a href="analytics.php" class="cta-link">Analytics</a>
      </div>
    </div>
    <div class="cta-footer">
      <span>Acadex · DBMS College Project</span>
      <span>MySQL · PHP · HTML/CSS</span>
    </div>
    <div class="panel-num">07 / 07</div>
  </div>
 
</div><!-- /track -->
 
<!-- MARQUEE -->
<div class="marquee-wrap">
  <div class="marquee-inner">
    <span class="marquee-item"><span>⬡</span>ACADEX</span>
    <span class="marquee-item"><span>◈</span>RESEARCH PAPERS</span>
    <span class="marquee-item"><span>◈</span>CITATION NETWORK</span>
    <span class="marquee-item"><span>◈</span>AUTHOR INDEX</span>
    <span class="marquee-item"><span>◈</span>VENUE ANALYTICS</span>
    <span class="marquee-item"><span>◈</span>MYSQL + PHP</span>
    <span class="marquee-item"><span>◈</span>DBMS PROJECT 2025</span>
    <span class="marquee-item"><span>⬡</span>ACADEX</span>
    <span class="marquee-item"><span>◈</span>RESEARCH PAPERS</span>
    <span class="marquee-item"><span>◈</span>CITATION NETWORK</span>
    <span class="marquee-item"><span>◈</span>AUTHOR INDEX</span>
    <span class="marquee-item"><span>◈</span>VENUE ANALYTICS</span>
    <span class="marquee-item"><span>◈</span>MYSQL + PHP</span>
    <span class="marquee-item"><span>◈</span>DBMS PROJECT 2025</span>
  </div>
</div>
 
<!-- PROGRESS LINE -->
<div class="progress-line" id="progress"></div>
 
<script>
const PANELS = 7;
let current = 0;
let animating = false;
 
const track = document.getElementById('track');
const dots   = document.querySelectorAll('.nav-dot');
const progress = document.getElementById('progress');
const cursor = document.getElementById('cursor');
 
function goTo(n){
  if(n < 0 || n >= PANELS || n === current) return;
  animating = true;
  current = n;
  track.style.transform = `translateX(-${n * 100}vw)`;
  dots.forEach((d,i) => d.classList.toggle('active', i === n));
  progress.style.width = ((n / (PANELS-1)) * 100) + '%';
 
  // Animate bars when reaching analytics panel
  if(n === 5){
    setTimeout(()=>{
      document.querySelectorAll('.bar-xl-fill').forEach(b=>{
        b.style.width = b.dataset.w;
      });
    },400);
  }
 
  setTimeout(()=> animating = false, 1000);
}
 
// Wheel
let wheelTimeout;
window.addEventListener('wheel', e=>{
  e.preventDefault();
  clearTimeout(wheelTimeout);
  wheelTimeout = setTimeout(()=>{
    if(animating) return;
    if(e.deltaY > 0 || e.deltaX > 0) goTo(current + 1);
    else goTo(current - 1);
  }, 40);
}, {passive:false});
 
// Keys
window.addEventListener('keydown', e=>{
  if(e.key==='ArrowRight'||e.key==='ArrowDown') goTo(current+1);
  if(e.key==='ArrowLeft'||e.key==='ArrowUp')   goTo(current-1);
});
 
// Nav dots
dots.forEach(d=>{
  d.addEventListener('click',()=> goTo(+d.dataset.panel));
});
 
// Touch
let tx=0;
window.addEventListener('touchstart',e=>{ tx=e.touches[0].clientX; });
window.addEventListener('touchend',e=>{
  const dx = e.changedTouches[0].clientX - tx;
  if(Math.abs(dx)>50) goTo(current + (dx<0?1:-1));
});
 
// Cursor
document.addEventListener('mousemove',e=>{
  cursor.style.left = e.clientX+'px';
  cursor.style.top  = e.clientY+'px';
});
document.querySelectorAll('a,button,.nav-dot,.stat-block,.author-card,.paper-item,.bar-row-xl').forEach(el=>{
  el.addEventListener('mouseenter',()=> cursor.classList.add('big'));
  el.addEventListener('mouseleave',()=> cursor.classList.remove('big'));
});
 
// Init progress
progress.style.width = '0%';
 
// Prevent default scroll on body
document.body.addEventListener('scroll', e=>e.preventDefault(), {passive:false});
</script>
</body>
</html>
 