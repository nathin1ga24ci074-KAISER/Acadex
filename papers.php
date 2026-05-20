<?php
// ─── papers.php — Browse & Search Papers ─────────────────────
require 'includes/db.php';
$page_title = 'Papers';
 
$search  = trim($_GET['q']    ?? '');
$year    = trim($_GET['year'] ?? '');
$status  = trim($_GET['status'] ?? '');
$venue_t = trim($_GET['venue_type'] ?? '');
 
// Build dynamic query
$where  = ['1=1'];
$params = [];
$types  = '';
 
if ($search !== '') {
    $where[] = 'MATCH(p.title, p.abstract, p.keywords) AGAINST(? IN BOOLEAN MODE)';
    $params[] = $search . '*';
    $types   .= 's';
}
if ($year !== '') {
    $where[] = 'p.publication_year = ?';
    $params[] = (int)$year;
    $types   .= 'i';
}
if ($status !== '') {
    $where[] = 'p.status = ?';
    $params[] = $status;
    $types   .= 's';
}
if ($venue_t !== '') {
    $where[] = 'v.type = ?';
    $params[] = $venue_t;
    $types   .= 's';
}
 
$sql = "
    SELECT
        p.paper_id, p.title, p.abstract, p.keywords,
        p.doi, p.publication_year, p.status,
        v.name AS venue_name, v.type AS venue_type,
        COUNT(DISTINCT c.citation_id) AS citation_count,
        GROUP_CONCAT(
            CONCAT(a.first_name,' ',a.last_name)
            ORDER BY pa.author_order SEPARATOR ', '
        ) AS authors
    FROM papers p
    LEFT JOIN venues       v  ON p.venue_id = v.venue_id
    LEFT JOIN citations    c  ON c.cited_paper_id = p.paper_id
    LEFT JOIN paper_authors pa ON p.paper_id = pa.paper_id
    LEFT JOIN authors      a  ON pa.author_id = a.author_id
    WHERE " . implode(' AND ', $where) . "
    GROUP BY p.paper_id
    ORDER BY p.publication_year DESC
";
 
$papers = db_query($sql, $params, $types);
 
// Distinct years for filter
$years   = db_query("SELECT DISTINCT publication_year FROM papers ORDER BY publication_year DESC");
$statuses = ['Published','Under Review','Preprint','Retracted'];
$vtypes   = ['Journal','Conference','Workshop','Symposium'];
 
include 'includes/header.php';
?>
 
<div class="page-header">
  <h1>Research Papers</h1>
  <p><?= count($papers) ?> paper<?= count($papers) !== 1 ? 's' : '' ?> found<?= $search ? " for <em>\"".htmlspecialchars($search)."\"</em>" : '' ?></p>
</div>
 
<!-- SEARCH & FILTER -->
<div class="card" style="margin-bottom:1.5rem;">
  <form method="GET" action="papers.php">
    <div class="search-bar">
      <input type="text" id="search-input" name="q"
             value="<?= htmlspecialchars($search) ?>"
             placeholder="Search by title, abstract, or keywords…">
      <button type="submit" class="btn btn-primary">Search</button>
      <?php if ($search || $year || $status || $venue_t): ?>
      <a href="papers.php" class="btn btn-outline">Clear</a>
      <?php endif; ?>
    </div>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
      <!-- Year -->
      <select name="year" onchange="this.form.submit()">
        <option value="">All Years</option>
        <?php foreach ($years as $y): ?>
        <option value="<?= $y['publication_year'] ?>" <?= $year == $y['publication_year'] ? 'selected' : '' ?>>
          <?= $y['publication_year'] ?>
        </option>
        <?php endforeach; ?>
      </select>
      <!-- Status -->
      <select name="status" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <?php foreach ($statuses as $s): ?>
        <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <!-- Venue type -->
      <select name="venue_type" onchange="this.form.submit()">
        <option value="">All Venue Types</option>
        <?php foreach ($vtypes as $vt): ?>
        <option value="<?= $vt ?>" <?= $venue_t === $vt ? 'selected' : '' ?>><?= $vt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
</div>
 
<!-- PAPER LIST -->
<?php if ($papers): ?>
<div class="paper-list" style="display:flex;flex-direction:column;gap:1rem;">
  <?php foreach ($papers as $p):
    $cls = match($p['status']) {
      'Published'    => 'badge-green',
      'Under Review' => 'badge-yellow',
      'Preprint'     => 'badge-purple',
      default        => 'badge-red'
    };
  ?>
  <div class="paper-card">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;">
      <h3><?= htmlspecialchars($p['title']) ?></h3>
      <span class="cite-count" style="white-space:nowrap;">⟳ <?= $p['citation_count'] ?> cites</span>
    </div>
 
    <?php if ($p['authors']): ?>
    <div style="font-size:0.8rem;color:var(--text3);margin-top:0.3rem;">
      ✍ <?= htmlspecialchars($p['authors']) ?>
    </div>
    <?php endif; ?>
 
    <?php if ($p['abstract']): ?>
    <div class="paper-abstract"><?= htmlspecialchars($p['abstract']) ?></div>
    <?php endif; ?>
 
    <div class="paper-meta">
      <span class="badge badge-blue"><?= $p['publication_year'] ?></span>
      <span class="badge <?= $cls ?>"><?= $p['status'] ?></span>
      <?php if ($p['venue_name']): ?>
      <span class="badge badge-purple"><?= htmlspecialchars($p['venue_name']) ?></span>
      <?php endif; ?>
      <?php if ($p['venue_type']): ?>
      <span class="badge badge-yellow"><?= $p['venue_type'] ?></span>
      <?php endif; ?>
      <?php if ($p['doi']): ?>
      <span class="mono">DOI: <?= htmlspecialchars($p['doi']) ?></span>
      <?php endif; ?>
      <?php if ($p['keywords']): ?>
      <span style="font-size:0.75rem;color:var(--text3);">🔑 <?= htmlspecialchars($p['keywords']) ?></span>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
 
<?php else: ?>
<div class="empty-state">
  <div class="es-icon">🔍</div>
  <h3>No papers found</h3>
  <p>Try a different search or <a href="add_paper.php" style="color:var(--accent);">add a new paper</a>.</p>
</div>
<?php endif; ?>
 
<?php include 'includes/footer.php'; ?>
 