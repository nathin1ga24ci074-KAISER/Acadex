<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acadex — Academic Publication Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --bg:#141210;
  --bg2:#1a1714;
  --bg3:#201e1a;
  --surface:#252219;
  --border:rgba(201,158,60,0.1);
  --border-lit:rgba(201,158,60,0.35);
  --gold:#c99e3c;
  --gold2:#e8bc5a;
  --amber:#f59e0b;
  --text:#f0ead8;
  --text2:#a89e88;
  --text3:#5a5244;
  --font-display:'Bebas Neue',sans-serif;
  --font-serif:'Instrument Serif',serif;
  --font-mono:'DM Mono',monospace;
  --font-body:'DM Sans',sans-serif;
}
 
html{scroll-behavior:smooth;}
 
body{
  background:var(--bg);
  color:var(--text);
  font-family:var(--font-body);
  overflow-x:hidden;
  cursor:none;
}
 
/* ── CURSOR ── */
#cur{
  position:fixed;width:8px;height:8px;
  background:var(--gold);border-radius:50%;
  pointer-events:none;z-index:9999;
  transform:translate(-50%,-50%);
  transition:width .25s,height .25s,opacity .25s;
  mix-blend-mode:screen;
}
#cur.grow{width:40px;height:40px;background:rgba(201,158,60,0.2);border:1px solid var(--gold);}
#cur-trail{
  position:fixed;width:32px;height:32px;
  border:1px solid rgba(201,158,60,0.3);border-radius:50%;
  pointer-events:none;z-index:9998;
  transform:translate(-50%,-50%);
  transition:left .12s ease,top .12s ease;
}
 
/* ── NAV ── */
.nav{
  position:fixed;top:0;left:0;right:0;z-index:100;
  display:flex;align-items:center;justify-content:space-between;
  padding:1.4rem 3rem;
  border-bottom:1px solid transparent;
  transition:background .4s,border-color .4s,backdrop-filter .4s;
}
.nav.scrolled{
  background:rgba(20,18,16,0.88);
  backdrop-filter:blur(16px);
  border-color:var(--border);
}
.nav-logo{
  font-family:var(--font-display);
  font-size:1.6rem;letter-spacing:.12em;
  color:var(--text);text-decoration:none;
}
.nav-logo span{color:var(--gold);}
.nav-links{display:flex;gap:2.5rem;list-style:none;}
.nav-links a{
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--text2);text-decoration:none;
  transition:color .2s;
}
.nav-links a:hover{color:var(--gold);}
.nav-cta{
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--bg);background:var(--gold);
  padding:8px 20px;text-decoration:none;
  transition:background .2s;
}
.nav-cta:hover{background:var(--gold2);}
 
/* ── HERO ── */
.hero{
  position:relative;min-height:100vh;
  display:flex;align-items:center;
  overflow:hidden;
  padding:0 3rem;
}
 
.hero-bg-text{
  position:absolute;
  font-family:var(--font-display);
  font-size:clamp(180px,22vw,360px);
  letter-spacing:-.02em;
  color:rgba(201,158,60,0.04);
  line-height:.85;
  bottom:-0.1em;right:-.05em;
  user-select:none;pointer-events:none;
  white-space:nowrap;
}
 
/* Grain overlay */
.hero::before{
  content:'';
  position:absolute;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.035'/%3E%3C/svg%3E");
  pointer-events:none;z-index:0;opacity:.7;
}
 
.hero-inner{
  position:relative;z-index:2;
  max-width:1200px;margin:0 auto;width:100%;
  padding-top:8rem;
}
 
.hero-tag{
  display:inline-flex;align-items:center;gap:8px;
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:2.5px;text-transform:uppercase;
  color:var(--gold);margin-bottom:2rem;
}
.hero-tag::before{
  content:'';width:24px;height:1px;background:var(--gold);
}
 
.hero-title{
  font-family:var(--font-display);
  font-size:clamp(80px,13vw,200px);
  line-height:.88;letter-spacing:.01em;
  color:var(--text);
  margin-bottom:1.5rem;
}
.hero-title em{
  font-family:var(--font-serif);
  font-style:italic;
  color:var(--gold);
  font-size:.85em;
}
 
.hero-desc{
  font-size:clamp(15px,1.4vw,18px);
  color:var(--text2);
  max-width:480px;
  line-height:1.75;
  font-weight:300;
  margin-bottom:3rem;
}
 
.hero-actions{
  display:flex;gap:1.25rem;align-items:center;
}
.btn-gold{
  background:var(--gold);color:var(--bg);
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:2px;text-transform:uppercase;
  padding:14px 32px;text-decoration:none;
  transition:background .2s,transform .2s;
  display:inline-block;
}
.btn-gold:hover{background:var(--gold2);transform:translateY(-2px);}
.btn-ghost{
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--text2);text-decoration:none;
  border-bottom:1px solid var(--text3);
  padding-bottom:2px;
  transition:color .2s,border-color .2s;
}
.btn-ghost:hover{color:var(--gold);border-color:var(--gold);}
 
/* Scroll indicator */
.scroll-ind{
  position:absolute;bottom:2.5rem;left:3rem;
  display:flex;align-items:center;gap:12px;
  font-family:var(--font-mono);font-size:.68rem;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--text3);z-index:2;
}
.scroll-ind-line{
  width:40px;height:1px;background:var(--text3);
  transform-origin:left;animation:lineGrow 2s ease infinite;
}
@keyframes lineGrow{0%{transform:scaleX(0);opacity:0}50%{transform:scaleX(1);opacity:1}100%{transform:scaleX(0);opacity:0}}
 
/* ── STATS STRIP ── */
.stats-strip{
  background:var(--bg2);
  border-top:1px solid var(--border);
  border-bottom:1px solid var(--border);
  padding:3rem;
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:0;
}
.strip-stat{
  padding:1.5rem 2rem;
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;gap:.5rem;
}
.strip-stat:last-child{border-right:none;}
.ss-num{
  font-family:var(--font-display);
  font-size:clamp(48px,5vw,72px);
  color:var(--text);line-height:1;
}
.ss-num span{color:var(--gold);font-size:.6em;}
.ss-label{
  font-family:var(--font-mono);font-size:.68rem;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--text3);
}
.ss-desc{font-size:.82rem;color:var(--text2);margin-top:.25rem;}
 
/* ── SECTION BASE ── */
section{padding:8rem 3rem;}
.section-inner{max-width:1200px;margin:0 auto;}
 
.section-tag{
  font-family:var(--font-mono);font-size:.68rem;
  letter-spacing:3px;text-transform:uppercase;
  color:var(--gold);margin-bottom:1.5rem;
  display:flex;align-items:center;gap:10px;
}
.section-tag::after{content:'';flex:0 0 32px;height:1px;background:var(--gold);}
 
/* ── FEATURES SECTION ── */
.features-sec{background:var(--bg);}
 
.features-header{
  display:grid;grid-template-columns:1fr 1fr;
  gap:4rem;align-items:end;
  margin-bottom:5rem;
}
.features-title{
  font-family:var(--font-display);
  font-size:clamp(52px,6vw,88px);
  line-height:.9;letter-spacing:.02em;
  color:var(--text);
}
.features-title em{
  font-family:var(--font-serif);
  font-style:italic;color:var(--gold);
}
.features-desc{
  font-size:.95rem;color:var(--text2);
  line-height:1.8;max-width:380px;
  align-self:end;
}
 
.features-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:1px;
  background:var(--border);
}
.feat-card{
  background:var(--bg);
  padding:2.5rem;
  display:flex;flex-direction:column;gap:1rem;
  transition:background .25s;
  position:relative;overflow:hidden;
}
.feat-card::before{
  content:'';
  position:absolute;bottom:0;left:0;right:0;height:2px;
  background:var(--gold);
  transform:scaleX(0);transform-origin:left;
  transition:transform .35s cubic-bezier(.25,1,.5,1);
}
.feat-card:hover{background:var(--bg2);}
.feat-card:hover::before{transform:scaleX(1);}
 
.feat-icon{
  font-size:1.6rem;
  width:48px;height:48px;
  background:rgba(201,158,60,0.08);
  border:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;
  border-radius:4px;
}
.feat-name{
  font-family:var(--font-display);
  font-size:1.4rem;letter-spacing:.04em;
  color:var(--text);
}
.feat-desc{
  font-size:.85rem;color:var(--text2);
  line-height:1.7;
}
.feat-tag{
  margin-top:auto;
  font-family:var(--font-mono);font-size:.65rem;
  letter-spacing:1.5px;text-transform:uppercase;
  color:var(--text3);
}
 
/* ── PAPERS SECTION ── */
.papers-sec{
  background:var(--bg2);
  border-top:1px solid var(--border);
  border-bottom:1px solid var(--border);
}
 
.papers-layout{
  display:grid;
  grid-template-columns:400px 1fr;
  gap:6rem;
  align-items:start;
}
 
.papers-sticky{
  position:sticky;top:8rem;
}
.papers-big{
  font-family:var(--font-display);
  font-size:clamp(64px,7vw,108px);
  line-height:.88;color:var(--text);
  margin-bottom:1.5rem;
}
.papers-big span{
  color:transparent;
  -webkit-text-stroke:1px rgba(201,158,60,0.4);
}
.papers-note{
  font-size:.875rem;color:var(--text2);
  line-height:1.75;margin-bottom:2rem;
}
.papers-link{
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:2px;text-transform:uppercase;
  color:var(--gold);text-decoration:none;
  display:inline-flex;align-items:center;gap:8px;
  border-bottom:1px solid rgba(201,158,60,0.3);
  padding-bottom:3px;
  transition:border-color .2s;
}
.papers-link:hover{border-color:var(--gold);}
 
.papers-list{display:flex;flex-direction:column;gap:0;}
.paper-row{
  padding:1.75rem 0;
  border-bottom:1px solid var(--border);
  display:grid;
  grid-template-columns:56px 1fr 80px;
  gap:1.25rem;align-items:center;
  cursor:default;
  transition:padding-left .25s;
}
.paper-row:first-child{border-top:1px solid var(--border);}
.paper-row:hover{padding-left:.75rem;}
.pr-num{
  font-family:var(--font-display);font-size:2.2rem;
  color:rgba(201,158,60,0.2);line-height:1;
}
.pr-info{}
.pr-title{
  font-size:.95rem;font-weight:500;
  color:var(--text);line-height:1.4;
  margin-bottom:.3rem;
}
.pr-meta{
  font-family:var(--font-mono);font-size:.68rem;
  letter-spacing:1px;text-transform:uppercase;
  color:var(--text3);
}
.pr-badge{
  font-family:var(--font-display);
  font-size:1.6rem;color:rgba(201,158,60,0.25);
  text-align:right;line-height:1;
}
.pr-badge-label{
  font-family:var(--font-mono);font-size:.62rem;
  letter-spacing:1px;color:var(--text3);
  text-transform:uppercase;display:block;
}
 
/* ── DB CONCEPTS SECTION ── */
.db-sec{background:var(--bg);}
 
.db-header{margin-bottom:4rem;}
.db-title{
  font-family:var(--font-display);
  font-size:clamp(52px,6vw,88px);
  line-height:.9;color:var(--text);
}
.db-title span{color:var(--gold);}
 
.db-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:1px;
  background:var(--border);
}
.db-card{
  background:var(--bg);
  padding:2rem 1.75rem;
  display:flex;flex-direction:column;
  gap:.75rem;
}
.dbc-num{
  font-family:var(--font-display);
  font-size:3rem;color:rgba(201,158,60,0.15);
  line-height:1;
}
.dbc-name{
  font-family:var(--font-display);
  font-size:1.2rem;letter-spacing:.04em;
  color:var(--text);
}
.dbc-desc{
  font-size:.82rem;color:var(--text2);
  line-height:1.65;
}
.dbc-badge{
  margin-top:auto;
  font-family:var(--font-mono);font-size:.62rem;
  letter-spacing:1.5px;text-transform:uppercase;
  color:var(--gold);padding-top:.75rem;
  border-top:1px solid var(--border);
}
 
/* ── ANALYTICS SECTION ── */
.analytics-sec{
  background:var(--surface);
  border-top:1px solid var(--border);
}
 
.analytics-layout{
  display:grid;grid-template-columns:1fr 1fr;
  gap:5rem;align-items:center;
}
 
.analytics-left{}
.analytics-big{
  font-family:var(--font-display);
  font-size:clamp(64px,8vw,120px);
  line-height:.88;color:var(--text);
  margin-bottom:1.5rem;
}
.analytics-big em{
  font-family:var(--font-serif);
  font-style:italic;color:var(--gold);
}
.analytics-desc{
  font-size:.9rem;color:var(--text2);
  line-height:1.8;margin-bottom:2rem;
  max-width:360px;
}
 
.chart-rows{display:flex;flex-direction:column;gap:1.25rem;}
.chart-row{display:flex;flex-direction:column;gap:.5rem;}
.cr-header{
  display:flex;justify-content:space-between;
  font-family:var(--font-mono);font-size:.68rem;
  letter-spacing:1.5px;text-transform:uppercase;
}
.cr-label{color:var(--text2);}
.cr-val{color:var(--gold);}
.cr-track{
  height:3px;background:rgba(201,158,60,0.08);
  border-radius:2px;overflow:hidden;
}
.cr-fill{
  height:100%;background:linear-gradient(90deg,var(--gold),var(--gold2));
  border-radius:2px;
  transition:width 1.2s cubic-bezier(.25,1,.5,1);
  width:0;
}
 
/* ── TECH SECTION ── */
.tech-sec{
  background:var(--bg2);
  border-top:1px solid var(--border);
  border-bottom:1px solid var(--border);
}
 
.tech-title{
  font-family:var(--font-display);
  font-size:clamp(52px,6vw,88px);
  line-height:.9;color:var(--text);
  margin-bottom:3rem;
}
 
.tech-grid{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:1.5rem;
}
.tech-item{
  padding:2rem;
  border:1px solid var(--border);
  display:flex;flex-direction:column;gap:.75rem;
  transition:border-color .25s,background .25s;
}
.tech-item:hover{
  border-color:var(--border-lit);
  background:var(--bg);
}
.ti-icon{font-size:1.8rem;}
.ti-name{
  font-family:var(--font-display);
  font-size:1.5rem;letter-spacing:.04em;
  color:var(--text);
}
.ti-role{
  font-size:.82rem;color:var(--text2);
  line-height:1.65;
}
.ti-tag{
  font-family:var(--font-mono);font-size:.62rem;
  letter-spacing:1.5px;text-transform:uppercase;
  color:var(--text3);margin-top:auto;
}
 
/* ── CTA SECTION ── */
.cta-sec{
  background:var(--bg);
  min-height:70vh;
  display:flex;align-items:center;
}
.cta-inner{
  max-width:1200px;margin:0 auto;
  padding:0 3rem;width:100%;
  display:flex;flex-direction:column;align-items:center;
  text-align:center;gap:2.5rem;
}
.cta-label{
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:3px;text-transform:uppercase;
  color:var(--gold);
}
.cta-big{
  font-family:var(--font-display);
  font-size:clamp(72px,11vw,160px);
  line-height:.85;color:var(--text);
  letter-spacing:.02em;
}
.cta-big strong{
  color:transparent;
  -webkit-text-stroke:1.5px var(--gold);
}
.cta-buttons{
  display:flex;gap:1.25rem;flex-wrap:wrap;
  justify-content:center;
}
.cta-btn{
  font-family:var(--font-mono);font-size:.72rem;
  letter-spacing:2px;text-transform:uppercase;
  padding:14px 32px;text-decoration:none;
  border:1px solid var(--border);
  color:var(--text2);
  transition:all .2s;
}
.cta-btn:hover{border-color:var(--gold);color:var(--gold);}
.cta-btn.primary{background:var(--gold);color:var(--bg);border-color:var(--gold);}
.cta-btn.primary:hover{background:var(--gold2);}
 
/* ── FOOTER ── */
footer{
  background:var(--bg2);
  border-top:1px solid var(--border);
  padding:2rem 3rem;
  display:flex;align-items:center;
  justify-content:space-between;
}
.footer-brand{
  font-family:var(--font-display);
  font-size:1.2rem;letter-spacing:.1em;
  color:var(--text2);
}
.footer-brand span{color:var(--gold);}
.footer-meta{
  font-family:var(--font-mono);font-size:.65rem;
  letter-spacing:1.5px;text-transform:uppercase;
  color:var(--text3);
}
 
/* ── MARQUEE ── */
.marquee-wrap{
  background:var(--bg2);
  border-top:1px solid var(--border);
  border-bottom:1px solid var(--border);
  height:40px;overflow:hidden;
  display:flex;align-items:center;
}
.marquee-track{
  display:flex;animation:mq 24s linear infinite;
  white-space:nowrap;
}
.mq-item{
  font-family:var(--font-mono);font-size:.68rem;
  letter-spacing:3px;text-transform:uppercase;
  color:var(--text3);padding:0 2.5rem;
}
.mq-item span{color:var(--gold);}
@keyframes mq{from{transform:translateX(0)}to{transform:translateX(-50%)}}
 
/* ── SCROLL REVEAL ── */
.reveal{
  opacity:0;transform:translateY(32px);
  transition:opacity .7s cubic-bezier(.25,1,.5,1),transform .7s cubic-bezier(.25,1,.5,1);
}
.reveal.in{opacity:1;transform:translateY(0);}
.reveal-delay-1{transition-delay:.1s}
.reveal-delay-2{transition-delay:.2s}
.reveal-delay-3{transition-delay:.3s}
.reveal-delay-4{transition-delay:.4s}
.reveal-delay-5{transition-delay:.5s}
</style>
</head>
<body>
 
<div id="cur"></div>
<div id="cur-trail"></div>
 
<!-- NAV -->
<nav class="nav" id="nav">
  <a href="index.php" class="nav-logo">ACAD<span>EX</span></a>
  <ul class="nav-links">
    <li><a href="papers.php">Papers</a></li>
    <li><a href="authors.php">Authors</a></li>
    <li><a href="citations.php">Citations</a></li>
    <li><a href="analytics.php">Analytics</a></li>
  </ul>
  <a href="dashboard.php" class="nav-cta">Enter Dashboard</a>
</nav>
 
<!-- HERO -->
<section class="hero">
  <div class="hero-bg-text">KNOWLEDGE</div>
  <div class="hero-inner">
    <div class="hero-tag">Academic Publication System · DBMS Project</div>
    <h1 class="hero-title">THE<br>RESEARCH<br><em>Archive</em></h1>
    <p class="hero-desc">A complete academic publication management platform — indexing papers, tracking authors, mapping citation networks, and surfacing research insights.</p>
    <div class="hero-actions">
      <a href="dashboard.php" class="btn-gold">Enter Dashboard</a>
      <a href="papers.php" class="btn-ghost">Browse Papers →</a>
    </div>
  </div>
  <div class="scroll-ind">
    <div class="scroll-ind-line"></div>
    
  </div>
</section>
 
<!-- STATS STRIP -->
<div class="stats-strip reveal">
  <div class="strip-stat">
    <div class="ss-num">05<span>+</span></div>
    <div class="ss-label">Research Papers</div>
    <div class="ss-desc">Indexed with metadata, DOIs, keywords and abstracts</div>
  </div>
  <div class="strip-stat">
    <div class="ss-num">05<span>+</span></div>
    <div class="ss-label">Authors</div>
    <div class="ss-desc">Researchers with h-index and institutional links</div>
  </div>
  <div class="strip-stat">
    <div class="ss-num">05<span>+</span></div>
    <div class="ss-label">Citations Tracked</div>
    <div class="ss-desc">Full citation graph with self-reference prevention</div>
  </div>
  <div class="strip-stat">
    <div class="ss-num">05<span>+</span></div>
    <div class="ss-label">Institutions</div>
    <div class="ss-desc">Universities and research centres worldwide</div>
  </div>
</div>
 
<!-- MARQUEE -->
<div class="marquee-wrap">
  <div class="marquee-track">
    <span class="mq-item"><span>⬡</span> ACADEX</span>
    <span class="mq-item"><span>◈</span> RESEARCH PAPERS</span>
    <span class="mq-item"><span>◈</span> CITATION NETWORK</span>
    <span class="mq-item"><span>◈</span> AUTHOR INDEX</span>
    <span class="mq-item"><span>◈</span> VENUE ANALYTICS</span>
    <span class="mq-item"><span>◈</span> FULLTEXT SEARCH</span>
    <span class="mq-item"><span>◈</span> MYSQL + PHP</span>
    <span class="mq-item"><span>◈</span> DBMS PROJECT 2025</span>
    <span class="mq-item"><span>⬡</span> ACADEX</span>
    <span class="mq-item"><span>◈</span> RESEARCH PAPERS</span>
    <span class="mq-item"><span>◈</span> CITATION NETWORK</span>
    <span class="mq-item"><span>◈</span> AUTHOR INDEX</span>
    <span class="mq-item"><span>◈</span> VENUE ANALYTICS</span>
    <span class="mq-item"><span>◈</span> FULLTEXT SEARCH</span>
    <span class="mq-item"><span>◈</span> MYSQL + PHP</span>
    <span class="mq-item"><span>◈</span> DBMS PROJECT 2025</span>
  </div>
</div>
 
<!-- FEATURES -->
<section class="features-sec">
  <div class="section-inner">
    <div class="features-header reveal">
      <div>
        <div class="section-tag">Core Features</div>
        <div class="features-title">BUILT FOR<br><em>Research</em><br>MANAGEMENT</div>
      </div>
      <p class="features-desc">Every feature is backed by a real SQL query — joins, aggregates, fulltext search, triggers, and stored procedures — all running live from a normalized MySQL database.</p>
    </div>
    <div class="features-grid">
      <div class="feat-card reveal reveal-delay-1">
        <div class="feat-icon">🔍</div>
        <div class="feat-name">FULLTEXT SEARCH</div>
        <div class="feat-desc">MySQL FULLTEXT index on title, abstract, and keywords. Relevance-ranked results — not a basic LIKE query.</div>
        <div class="feat-tag">SQL · MATCH AGAINST</div>
      </div>
      <div class="feat-card reveal reveal-delay-2">
        <div class="feat-icon">🔗</div>
        <div class="feat-name">CITATION NETWORK</div>
        <div class="feat-desc">Track which papers reference which. Self-citation blocked by a BEFORE INSERT trigger at the database level.</div>
        <div class="feat-tag">TRIGGER · CASCADE DELETE</div>
      </div>
      <div class="feat-card reveal reveal-delay-3">
        <div class="feat-icon">⚡</div>
        <div class="feat-name">H-INDEX ENGINE</div>
        <div class="feat-desc">One-click h-index computation via a MySQL stored procedure. Real academic impact metric, calculated inside the DB.</div>
        <div class="feat-tag">STORED PROCEDURE</div>
      </div>
      <div class="feat-card reveal reveal-delay-1">
        <div class="feat-icon">📊</div>
        <div class="feat-name">LIVE ANALYTICS</div>
        <div class="feat-desc">Venue stats, citation networks, author rankings — powered by complex JOIN queries and aggregate functions.</div>
        <div class="feat-tag">GROUP BY · COUNT · SUM</div>
      </div>
      <div class="feat-card reveal reveal-delay-2">
        <div class="feat-icon">🏛️</div>
        <div class="feat-name">DATABASE VIEWS</div>
        <div class="feat-desc">paper_details and author_stats views simplify complex queries into reusable virtual tables used across all pages.</div>
        <div class="feat-tag">CREATE VIEW</div>
      </div>
      <div class="feat-card reveal reveal-delay-3">
        <div class="feat-icon">🔒</div>
        <div class="feat-name">TRANSACTIONS</div>
        <div class="feat-desc">Adding a paper with authors runs as a single atomic transaction. Any failure triggers a full ROLLBACK.</div>
        <div class="feat-tag">BEGIN · COMMIT · ROLLBACK</div>
      </div>
    </div>
  </div>
</section>
 
<!-- PAPERS -->
<section class="papers-sec">
  <div class="section-inner">
    <div class="papers-layout">
      <div class="papers-sticky reveal">
        <div class="section-tag">Paper Archive</div>
        <div class="papers-big">RE<br>SEARCH<br><span>PAPERS</span></div>
        <p class="papers-note">Five landmark research papers pre-loaded — from the Transformer architecture to Chandrayaan-style distributed systems. Search, filter, add your own.</p>
        <a href="papers.php" class="papers-link">Browse all papers →</a>
      </div>
      <div class="papers-list reveal reveal-delay-2">
        <div class="paper-row">
          <div class="pr-num">01</div>
          <div class="pr-info">
            <div class="pr-title">Attention Is All You Need</div>
            <div class="pr-meta">NeurIPS · 2017 · Published</div>
          </div>
          <div class="pr-badge">03<span class="pr-badge-label">cites</span></div>
        </div>
        <div class="paper-row">
          <div class="pr-num">02</div>
          <div class="pr-info">
            <div class="pr-title">BERT: Pre-training of Deep Bidirectional Transformers</div>
            <div class="pr-meta">NeurIPS · 2019 · Published</div>
          </div>
          <div class="pr-badge">02<span class="pr-badge-label">cites</span></div>
        </div>
        <div class="paper-row">
          <div class="pr-num">03</div>
          <div class="pr-info">
            <div class="pr-title">Deep Residual Learning for Image Recognition</div>
            <div class="pr-meta">NeurIPS · 2016 · Published</div>
          </div>
          <div class="pr-badge">01<span class="pr-badge-label">cites</span></div>
        </div>
        <div class="paper-row">
          <div class="pr-num">04</div>
          <div class="pr-info">
            <div class="pr-title">ImageNet Large Scale Visual Recognition Challenge</div>
            <div class="pr-meta">IEEE Trans. AI · 2015 · Published</div>
          </div>
          <div class="pr-badge">01<span class="pr-badge-label">cites</span></div>
        </div>
        <div class="paper-row">
          <div class="pr-num">05</div>
          <div class="pr-info">
            <div class="pr-title">Scalable Distributed Database Architectures</div>
            <div class="pr-meta">ACM SIGMOD · 2022 · Published</div>
          </div>
          <div class="pr-badge">00<span class="pr-badge-label">cites</span></div>
        </div>
      </div>
    </div>
  </div>
</section>
 
<!-- DB CONCEPTS -->
<section class="db-sec">
  <div class="section-inner">
    <div class="db-header reveal">
      <div class="section-tag">DBMS Concepts</div>
      <div class="db-title">8 CONCEPTS<br><span>ONE SYSTEM</span></div>
    </div>
    <div class="db-grid">
      <div class="db-card reveal reveal-delay-1">
        <div class="dbc-num">01</div>
        <div class="dbc-name">NORMALIZATION</div>
        <div class="dbc-desc">Schema normalized to BCNF across 6 tables — no redundancy, no partial or transitive dependencies.</div>
        <div class="dbc-badge">1NF → 2NF → 3NF → BCNF</div>
      </div>
      <div class="db-card reveal reveal-delay-2">
        <div class="dbc-num">02</div>
        <div class="dbc-name">FOREIGN KEYS</div>
        <div class="dbc-desc">Every relationship enforced by FK constraints with CASCADE DELETE and SET NULL behaviors.</div>
        <div class="dbc-badge">Referential Integrity</div>
      </div>
      <div class="db-card reveal reveal-delay-3">
        <div class="dbc-num">03</div>
        <div class="dbc-name">TRIGGER</div>
        <div class="dbc-desc">BEFORE INSERT trigger on citations table prevents any paper from referencing itself — database-level enforcement.</div>
        <div class="dbc-badge">before_citation_insert</div>
      </div>
      <div class="db-card reveal reveal-delay-4">
        <div class="dbc-num">04</div>
        <div class="dbc-name">STORED PROC</div>
        <div class="dbc-desc">update_h_index() procedure computes author h-index entirely inside MySQL — no application logic needed.</div>
        <div class="dbc-badge">CALL update_h_index(id)</div>
      </div>
      <div class="db-card reveal reveal-delay-1">
        <div class="dbc-num">05</div>
        <div class="dbc-name">VIEWS</div>
        <div class="dbc-desc">paper_details and author_stats views encapsulate complex JOINs for reuse across all PHP pages.</div>
        <div class="dbc-badge">CREATE VIEW</div>
      </div>
      <div class="db-card reveal reveal-delay-2">
        <div class="dbc-num">06</div>
        <div class="dbc-name">TRANSACTIONS</div>
        <div class="dbc-desc">Multi-table inserts wrapped in BEGIN/COMMIT with ROLLBACK on failure — fully atomic operations.</div>
        <div class="dbc-badge">ACID Compliance</div>
      </div>
      <div class="db-card reveal reveal-delay-3">
        <div class="dbc-num">07</div>
        <div class="dbc-name">FULLTEXT INDEX</div>
        <div class="dbc-desc">MySQL FULLTEXT index enables relevance-ranked boolean-mode search across title, abstract and keywords.</div>
        <div class="dbc-badge">MATCH() AGAINST()</div>
      </div>
      <div class="db-card reveal reveal-delay-4">
        <div class="dbc-num">08</div>
        <div class="dbc-name">M-TO-M JOIN</div>
        <div class="dbc-desc">paper_authors junction table resolves the many-to-many relationship between papers and authors with role metadata.</div>
        <div class="dbc-badge">Junction Table Pattern</div>
      </div>
    </div>
  </div>
</section>
 
<!-- ANALYTICS -->
<section class="analytics-sec">
  <div class="section-inner">
    <div class="analytics-layout">
      <div class="analytics-left reveal">
        <div class="section-tag">Analytics Module</div>
        <div class="analytics-big">DATA<br><em>Insights</em></div>
        <p class="analytics-desc">Live charts and tables powered by aggregate SQL queries — venue stats, citation networks, author rankings, and impact factor analysis.</p>
        <a href="analytics.php" class="btn-gold" style="margin-top:1rem;display:inline-block;">View Analytics →</a>
      </div>
      <div class="chart-rows reveal reveal-delay-2" id="chart-rows">
        <div class="chart-row">
          <div class="cr-header"><span class="cr-label">NeurIPS (Conference)</span><span class="cr-val">3 papers</span></div>
          <div class="cr-track"><div class="cr-fill" data-w="100%"></div></div>
        </div>
        <div class="chart-row">
          <div class="cr-header"><span class="cr-label">IEEE Trans. AI (Journal)</span><span class="cr-val">1 paper</span></div>
          <div class="cr-track"><div class="cr-fill" data-w="33%"></div></div>
        </div>
        <div class="chart-row">
          <div class="cr-header"><span class="cr-label">ACM SIGMOD (Conference)</span><span class="cr-val">1 paper</span></div>
          <div class="cr-track"><div class="cr-fill" data-w="33%"></div></div>
        </div>
        <div class="chart-row">
          <div class="cr-header"><span class="cr-label">Attention Is All You Need</span><span class="cr-val">3 cites</span></div>
          <div class="cr-track"><div class="cr-fill" data-w="100%"></div></div>
        </div>
        <div class="chart-row">
          <div class="cr-header"><span class="cr-label">BERT</span><span class="cr-val">2 cites</span></div>
          <div class="cr-track"><div class="cr-fill" data-w="66%"></div></div>
        </div>
        <div class="chart-row">
          <div class="cr-header"><span class="cr-label">Deep Residual Learning</span><span class="cr-val">1 cite</span></div>
          <div class="cr-track"><div class="cr-fill" data-w="33%"></div></div>
        </div>
      </div>
    </div>
  </div>
</section>
 
<!-- TECH STACK -->
<section class="tech-sec">
  <div class="section-inner">
    <div class="section-tag reveal">Technology Stack</div>
    <div class="tech-title reveal">BUILT WITH</div>
    <div class="tech-grid">
      <div class="tech-item reveal reveal-delay-1">
        <div class="ti-icon">🗄️</div>
        <div class="ti-name">MySQL 8</div>
        <div class="ti-role">Core database — 6 normalized tables, views, trigger, stored procedure, FULLTEXT index, transactions.</div>
        <div class="ti-tag">Database Layer</div>
      </div>
      <div class="tech-item reveal reveal-delay-2">
        <div class="ti-icon">🐘</div>
        <div class="ti-name">PHP 8</div>
        <div class="ti-role">Server-side backend — DB connectivity, CRUD operations, prepared statements, session handling.</div>
        <div class="ti-tag">Backend Layer</div>
      </div>
      <div class="tech-item reveal reveal-delay-3">
        <div class="ti-icon">🎨</div>
        <div class="ti-name">HTML / CSS / JS</div>
        <div class="ti-role">Frontend — dark charcoal theme, scroll animations, animated charts, custom cursor, responsive layout.</div>
        <div class="ti-tag">Frontend Layer</div>
      </div>
      <div class="tech-item reveal reveal-delay-1">
        <div class="ti-icon">⚙️</div>
        <div class="ti-name">XAMPP</div>
        <div class="ti-role">Local Apache + MySQL server stack for development and testing on Windows.</div>
        <div class="ti-tag">Dev Environment</div>
      </div>
      <div class="tech-item reveal reveal-delay-2">
        <div class="ti-icon">🌐</div>
        <div class="ti-name">ngrok</div>
        <div class="ti-role">Secure public URL tunnel — makes localhost accessible anywhere without paid hosting.</div>
        <div class="ti-tag">Deployment</div>
      </div>
      <div class="tech-item reveal reveal-delay-3">
        <div class="ti-icon">🐙</div>
        <div class="ti-name">Git / GitHub</div>
        <div class="ti-role">Version control and source code hosting — professional development workflow.</div>
        <div class="ti-tag">Version Control</div>
      </div>
    </div>
  </div>
</section>
 
<!-- CTA -->
<section class="cta-sec">
  <div class="cta-inner">
    <div class="cta-label reveal">Ready to explore?</div>
    <div class="cta-big reveal reveal-delay-1">START<br><strong>YOUR</strong><br>RESEARCH</div>
    <div class="cta-buttons reveal reveal-delay-2">
      <a href="dashboard.php" class="cta-btn primary">Enter Dashboard</a>
      <a href="papers.php" class="cta-btn">Browse Papers</a>
      <a href="add_paper.php" class="cta-btn">Add Paper</a>
      <a href="analytics.php" class="cta-btn">Analytics</a>
    </div>
  </div>
</section>
 
<!-- FOOTER -->
<footer>
  <div class="footer-brand">ACAD<span>EX</span></div>
  <div class="footer-meta">Academic Publication Management · DBMS Project · MySQL + PHP</div>
</footer>
 
<script>
// ── Cursor
const cur = document.getElementById('cur');
const trail = document.getElementById('cur-trail');
let mx=0,my=0;
document.addEventListener('mousemove', e=>{
  mx=e.clientX; my=e.clientY;
  cur.style.left=mx+'px'; cur.style.top=my+'px';
  setTimeout(()=>{ trail.style.left=mx+'px'; trail.style.top=my+'px'; },80);
});
document.querySelectorAll('a,button,.feat-card,.tech-item,.paper-row,.db-card,.strip-stat').forEach(el=>{
  el.addEventListener('mouseenter',()=>cur.classList.add('grow'));
  el.addEventListener('mouseleave',()=>cur.classList.remove('grow'));
});
 
// ── Nav scroll
const nav = document.getElementById('nav');
window.addEventListener('scroll',()=>{
  nav.classList.toggle('scrolled', window.scrollY > 60);
});
 
// ── Scroll reveal
const obs = new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); }
  });
},{threshold:0.12});
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));
 
// ── Chart bars animate on reveal
const chartObs = new IntersectionObserver(entries=>{
  entries.forEach(e=>{
    if(e.isIntersecting){
      e.target.querySelectorAll('.cr-fill').forEach(f=>{ f.style.width=f.dataset.w; });
      chartObs.unobserve(e.target);
    }
  });
},{threshold:0.3});
const cr = document.getElementById('chart-rows');
if(cr) chartObs.observe(cr);
</script>
</body>
</html>
 