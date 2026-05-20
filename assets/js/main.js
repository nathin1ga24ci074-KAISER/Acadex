// ─── main.js — Acadex Frontend JS ────────────────────────────
 
document.addEventListener('DOMContentLoaded', () => {
 
  // ── Animate bar charts (analytics page) ──────────────────
  const fills = document.querySelectorAll('.bar-fill');
  if (fills.length) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          const el = e.target;
          el.style.width = el.dataset.width || '0%';
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.2 });
    fills.forEach(f => observer.observe(f));
  }
 
  // ── Auto-dismiss alerts after 4s ─────────────────────────
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.4s';
      setTimeout(() => alert.remove(), 400);
    }, 4000);
  });
 
  // ── Stat counters (count up animation) ───────────────────
  document.querySelectorAll('.stat-value[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count, 10);
    let current  = 0;
    const step   = Math.ceil(target / 40);
    const timer  = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = current.toLocaleString();
      if (current >= target) clearInterval(timer);
    }, 30);
  });
 
  // ── Search: highlight matching text ──────────────────────
  const searchInput = document.querySelector('#search-input');
  if (searchInput && searchInput.value.trim()) {
    const term = searchInput.value.trim().toLowerCase();
    document.querySelectorAll('.paper-card h3').forEach(el => {
      const text = el.textContent;
      const idx  = text.toLowerCase().indexOf(term);
      if (idx !== -1) {
        el.innerHTML =
          text.slice(0, idx) +
          `<mark style="background:rgba(91,143,255,0.3);color:inherit;border-radius:3px;">${text.slice(idx, idx + term.length)}</mark>` +
          text.slice(idx + term.length);
      }
    });
  }
 
  // ── Confirm before deletes ────────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!confirm(btn.dataset.confirm || 'Are you sure?')) e.preventDefault();
    });
  });
 
});
 