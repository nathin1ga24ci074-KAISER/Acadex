<?php
// ─── index.php — Dashboard ────────────────────────────────────
require 'includes/db.php';
$page_title = 'Dashboard';
 
// ── Stats ────────────────────────────────────────────────────
$totals = db_row("
    SELECT
        (SELECT COUNT(*) FROM papers)       AS papers,
        (SELECT COUNT(*) FROM authors)      AS authors,
        (SELECT COUNT(*) FROM institutions) AS institutions,
        (SELECT COUNT(*) FROM citations)    AS citations
");
 
// ── Recent papers ────────────────────────────────────────────
$recent = db_query("
    SELECT pd.paper_id, pd.title, pd.publication_year, pd.status,
           pd.venue_name, pd.citation_count,
           GROUP_CONCAT(CONCAT(a.first_name,' ',a.last_name)
               ORDER BY pa.author_order SEPARATOR ', ') AS authors
    FROM paper_details pd
    LEFT JOIN paper_authors pa ON pd.paper_id = pa.paper_id
    LEFT JOIN authors a        ON pa.author_id = a.author_id
    GROUP BY pd.paper_id
    ORDER BY pd.publication_year DESC
    LIMIT 5
");
 
// ── Top cited papers ─────────────────────────────────────────
$top_cited = db_query("
    SELECT paper_id, title, citation_count, publication_year
    FROM paper_details
    ORDER BY citation_count DESC
    LIMIT 5
");
 
// ── Papers by year ────────────────────────────────────────────
$by_year = db_query("
    SELECT publication_year, COUNT(*) AS cnt
    FROM papers
    GROUP BY publication_year
    ORDER BY publication_year DESC
    LIMIT 6
");
 
$max_year = max(array_column($by_year, 'cnt') ?: [1]);
 
include 'includes/header.php';
?>
 
<div class="page-header">
  <h1>Research Dashboard</h1>
  <p>Overview of the academic publication database</p>
</div>
 
<!-- STAT CARDS -->
<div class="stats-grid">
  <div class="stat-card" style="--accent-clr: var(--accent)">
    <div class="stat-label">Total Papers</div>
    <div class="stat-value" data-count="<?= $totals['papers'] ?>"><?= $totals['papers'] ?></div>
    <div class="stat-sub">in the database</div>
  </div>
  <div class="stat-card" style="--accent-clr: var(--accent2)">
    <div class="stat-label">Authors</div>
    <div class="stat-value" data-count="<?= $totals['authors'] ?>"><?= $totals['authors'] ?></div>
    <div class="stat-sub">registered researchers</div>
  </div>
  <div class="stat-card" style="--accent-clr: var(--accent3)">
    <div class="stat-label">Institutions</div>
    <div class="stat-value" data-count="<?= $totals['institutions'] ?>"><?= $totals['institutions'] ?></div>
    <div class="stat-sub">worldwide</div>
  </div>
  <div class="stat-card" style="--accent-clr: var(--warn)">
    <div class="stat-label">Citations</div>
    <div class="stat-value" data-count="<?= $totals['citations'] ?>"><?= $totals['citations'] ?></div>
    <div class="stat-sub">cross-references tracked</div>
  </div>
</div>
 
<!-- MAIN CONTENT -->
<div class="two-col">
 
  <!-- Recent Papers -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">📄</span> Recent Papers</div>
    <?php if ($recent): ?>
    <div style="display:flex;flex-direction:column;gap:1rem;">
      <?php foreach ($recent as $p): ?>
      <div class="paper-card" style="padding:1rem 1.2rem;">
        <h3><?= htmlspecialchars($p['title']) ?></h3>
        <div class="paper-meta">
          <span class="badge badge-blue"><?= $p['publication_year'] ?></span>
          <?php
            $cls = match($p['status']) {
              'Published'    => 'badge-green',
              'Under Review' => 'badge-yellow',
              'Preprint'     => 'badge-purple',
              default        => 'badge-red'
            };
          ?>
          <span class="badge <?= $cls ?>"><?= $p['status'] ?></span>
          <?php if ($p['venue_name']): ?>
          <span class="mono"><?= htmlspecialchars($p['venue_name']) ?></span>
          <?php endif; ?>
          <span class="cite-count">⟳ <?= $p['citation_count'] ?> cites</span>
        </div>
        <?php if ($p['authors']): ?>
        <div style="font-size:0.78rem;color:var(--text3);margin-top:0.4rem;">
          <?= htmlspecialchars($p['authors']) ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:1rem;text-align:right;">
      <a href="papers.php" class="btn btn-outline btn-sm">View all papers →</a>
    </div>
    <?php else: ?>
    <div class="empty-state"><div class="es-icon">📭</div><p>No papers yet.</p></div>
    <?php endif; ?>
  </div>
 
  <!-- Right column -->
  <div style="display:flex;flex-direction:column;gap:1.25rem;">
 
    <!-- Top Cited -->
    <div class="card">
      <div class="card-title"><span class="ct-icon">🏆</span> Most Cited Papers</div>
      <?php if ($top_cited): ?>
      <div class="bar-chart">
        <?php foreach ($top_cited as $i => $p): 
          $pct = $max_year > 0 ? round($p['citation_count'] / max(array_column($top_cited,'citation_count')) * 100) : 0;
        ?>
        <div class="bar-row">
          <div class="bar-label" title="<?= htmlspecialchars($p['title']) ?>">
            <?= htmlspecialchars(mb_strimwidth($p['title'], 0, 28, '…')) ?>
          </div>
          <div class="bar-track">
            <div class="bar-fill" data-width="<?= $pct ?>%"></div>
          </div>
          <div class="bar-val"><?= $p['citation_count'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p style="color:var(--text3);font-size:0.85rem;">No citation data yet.</p>
      <?php endif; ?>
    </div>
 
    <!-- Papers by Year -->
    <div class="card">
      <div class="card-title"><span class="ct-icon">📅</span> Papers by Year</div>
      <?php if ($by_year): ?>
      <div class="bar-chart">
        <?php foreach ($by_year as $row): 
          $pct = round($row['cnt'] / $max_year * 100);
        ?>
        <div class="bar-row">
          <div class="bar-label"><?= $row['publication_year'] ?></div>
          <div class="bar-track">
            <div class="bar-fill" data-width="<?= $pct ?>%" style="background:linear-gradient(90deg,var(--accent3),var(--accent));"></div>
          </div>
          <div class="bar-val"><?= $row['cnt'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p style="color:var(--text3);font-size:0.85rem;">No data.</p>
      <?php endif; ?>
    </div>
 
  </div>
</div>
 
<?php include 'includes/footer.php'; ?>
 