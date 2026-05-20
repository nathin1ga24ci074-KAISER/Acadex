<?php
// ─── analytics.php — Database Analytics ──────────────────────
require 'includes/db.php';
$page_title = 'Analytics';
 
// ── 1. Papers by publication status ──────────────────────────
$by_status = db_query("
    SELECT status, COUNT(*) AS cnt
    FROM papers GROUP BY status ORDER BY cnt DESC
");
 
// ── 2. Papers per venue (top 5) ───────────────────────────────
$by_venue = db_query("
    SELECT v.name, v.type, COUNT(p.paper_id) AS cnt
    FROM venues v
    LEFT JOIN papers p ON p.venue_id = v.venue_id
    GROUP BY v.venue_id ORDER BY cnt DESC LIMIT 5
");
 
// ── 3. Top authors by citation count ─────────────────────────
$top_authors = db_query("
    SELECT CONCAT(a.first_name,' ',a.last_name) AS name,
           COALESCE(SUM(pd.citation_count),0)   AS total_cites,
           COUNT(DISTINCT pa.paper_id)           AS papers
    FROM authors a
    LEFT JOIN paper_authors pa ON a.author_id = pa.author_id
    LEFT JOIN paper_details pd ON pa.paper_id  = pd.paper_id
    GROUP BY a.author_id
    ORDER BY total_cites DESC
    LIMIT 6
");
 
// ── 4. Institutions by paper count ───────────────────────────
$by_inst = db_query("
    SELECT i.name, i.country, COUNT(DISTINCT pa.paper_id) AS cnt
    FROM institutions i
    LEFT JOIN authors a ON a.institution_id = i.institution_id
    LEFT JOIN paper_authors pa ON pa.author_id = a.author_id
    GROUP BY i.institution_id
    ORDER BY cnt DESC
");
 
// ── 5. Citation network depth (papers that cite + are cited) ──
$network = db_query("
    SELECT p.title,
           COUNT(DISTINCT c1.citing_paper_id) AS incoming,
           COUNT(DISTINCT c2.cited_paper_id)  AS outgoing
    FROM papers p
    LEFT JOIN citations c1 ON c1.cited_paper_id  = p.paper_id
    LEFT JOIN citations c2 ON c2.citing_paper_id = p.paper_id
    GROUP BY p.paper_id
    HAVING incoming > 0 OR outgoing > 0
    ORDER BY incoming DESC
");
 
// ── 6. Average citations per paper ────────────────────────────
$avg_cites = db_row("SELECT AVG(citation_count) AS avg_c FROM paper_details")['avg_c'] ?? 0;
 
// ── 7. Venue impact summary ───────────────────────────────────
$venue_impact = db_query("
    SELECT name, type, impact_factor,
           (SELECT COUNT(*) FROM papers WHERE venue_id=venues.venue_id) AS paper_cnt
    FROM venues WHERE impact_factor IS NOT NULL ORDER BY impact_factor DESC
");
 
// Helpers for bar width
function max_col(array $rows, string $col): float {
    $vals = array_column($rows, $col);
    return $vals ? max($vals) : 1;
}
 
include 'includes/header.php';
?>
 
<div class="page-header">
  <h1>Analytics</h1>
  <p>Database statistics and publication insights</p>
</div>
 
<!-- ROW 1 -->
<div class="two-col" style="margin-bottom:1.5rem;">
 
  <!-- Publication Status -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">📊</span> Papers by Status</div>
    <?php
      $status_colors = [
        'Published'    => '#34d399',
        'Under Review' => '#fbbf24',
        'Preprint'     => '#a78bfa',
        'Retracted'    => '#f87171',
      ];
      $max_s = max_col($by_status, 'cnt');
    ?>
    <div class="bar-chart">
      <?php foreach ($by_status as $r): ?>
      <div class="bar-row">
        <div class="bar-label"><?= $r['status'] ?></div>
        <div class="bar-track">
          <div class="bar-fill"
               data-width="<?= round($r['cnt']/$max_s*100) ?>%"
               style="background:<?= $status_colors[$r['status']] ?? 'var(--accent)' ?>;"></div>
        </div>
        <div class="bar-val"><?= $r['cnt'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--border);font-size:0.8rem;color:var(--text3);">
      Avg citations per paper: <strong style="color:var(--accent)"><?= round($avg_cites, 1) ?></strong>
    </div>
  </div>
 
  <!-- Top Venues -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">🏛️</span> Top Venues by Papers</div>
    <?php $max_v = max_col($by_venue, 'cnt'); ?>
    <div class="bar-chart">
      <?php foreach ($by_venue as $r): ?>
      <div class="bar-row">
        <div class="bar-label" title="<?= htmlspecialchars($r['name']) ?>">
          <?= htmlspecialchars(mb_strimwidth($r['name'], 0, 22, '…')) ?>
        </div>
        <div class="bar-track">
          <div class="bar-fill" data-width="<?= round($r['cnt']/$max_v*100) ?>%"
               style="background:linear-gradient(90deg,var(--accent2),var(--accent));"></div>
        </div>
        <div class="bar-val"><?= $r['cnt'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
 
</div>
 
<!-- ROW 2 -->
<div class="two-col" style="margin-bottom:1.5rem;">
 
  <!-- Top Authors -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">🧑‍🔬</span> Top Authors by Citations</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Author</th><th>Papers</th><th>Total Citations</th></tr></thead>
        <tbody>
          <?php foreach ($top_authors as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['name']) ?></td>
            <td><span class="badge badge-blue"><?= $a['papers'] ?></span></td>
            <td><span class="badge badge-green"><?= $a['total_cites'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
 
  <!-- Institutions -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">🏫</span> Institutions by Output</div>
    <?php $max_i = max_col($by_inst, 'cnt'); ?>
    <div class="bar-chart">
      <?php foreach ($by_inst as $r): ?>
      <div class="bar-row">
        <div class="bar-label" title="<?= htmlspecialchars($r['name']) ?>">
          <?= htmlspecialchars(mb_strimwidth($r['name'], 0, 22, '…')) ?>
        </div>
        <div class="bar-track">
          <div class="bar-fill" data-width="<?= $max_i > 0 ? round($r['cnt']/$max_i*100) : 0 ?>%"
               style="background:linear-gradient(90deg,var(--accent3),var(--accent));"></div>
        </div>
        <div class="bar-val"><?= $r['cnt'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
 
</div>
 
<!-- ROW 3: Citation Network -->
<div class="card" style="margin-bottom:1.5rem;">
  <div class="card-title"><span class="ct-icon">🕸️</span> Citation Network — Incoming vs Outgoing</div>
  <?php if ($network): ?>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Paper</th>
          <th>Cited By (incoming ↓)</th>
          <th>Cites Others (outgoing ↑)</th>
          <th>Net Influence</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($network as $r): ?>
        <tr>
          <td><?= htmlspecialchars(mb_strimwidth($r['title'], 0, 55, '…')) ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="width:<?= min($r['incoming']*30,120) ?>px;height:6px;background:var(--accent);border-radius:3px;"></div>
              <span class="mono"><?= $r['incoming'] ?></span>
            </div>
          </td>
          <td>
            <div style="display:flex;align-items:center;gap:8px;">
              <div style="width:<?= min($r['outgoing']*30,120) ?>px;height:6px;background:var(--accent2);border-radius:3px;"></div>
              <span class="mono"><?= $r['outgoing'] ?></span>
            </div>
          </td>
          <td>
            <?php $net = $r['incoming'] - $r['outgoing']; ?>
            <span class="badge <?= $net > 0 ? 'badge-green' : ($net < 0 ? 'badge-red' : 'badge-yellow') ?>">
              <?= $net > 0 ? '+' : '' ?><?= $net ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p style="color:var(--text3);padding:1rem;">No citation relationships yet.</p>
  <?php endif; ?>
</div>
 
<!-- ROW 4: Venue Impact Factors -->
<?php if ($venue_impact): ?>
<div class="card">
  <div class="card-title"><span class="ct-icon">⚡</span> Journal Impact Factors</div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Venue</th><th>Type</th><th>Impact Factor</th><th>Papers in DB</th></tr></thead>
      <tbody>
        <?php foreach ($venue_impact as $v): ?>
        <tr>
          <td><?= htmlspecialchars($v['name']) ?></td>
          <td><span class="badge badge-purple"><?= $v['type'] ?></span></td>
          <td>
            <span style="font-family:var(--font-mono);color:var(--accent);font-weight:500;">
              <?= number_format($v['impact_factor'], 3) ?>
            </span>
          </td>
          <td><span class="badge badge-blue"><?= $v['paper_cnt'] ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
 
<?php include 'includes/footer.php'; ?>
 