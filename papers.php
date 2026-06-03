<?php
// ─── papers.php — Browse, Search, Edit, Delete Papers ────────
require 'includes/db.php';
$page_title = 'Papers';
$msg = ''; $err = '';
$edit_paper = null;
 
$venues  = db_query("SELECT venue_id, name, type FROM venues ORDER BY type, name");
$authors = db_query("SELECT author_id, first_name, last_name FROM authors ORDER BY last_name");
 
// ── DELETE ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)$_POST['paper_id'];
    $db = get_db();
    $stmt = $db->prepare("DELETE FROM papers WHERE paper_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $msg = "Paper deleted successfully.";
}
 
// ── UPDATE ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id       = (int)$_POST['paper_id'];
    $title    = trim($_POST['title']    ?? '');
    $abstract = trim($_POST['abstract'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $doi      = trim($_POST['doi']      ?? '') ?: null;
    $year     = (int)($_POST['year']    ?? 0);
    $venue    = !empty($_POST['venue_id']) ? (int)$_POST['venue_id'] : null;
    $status   = $_POST['status'] ?? 'Published';
 
    if (!$title || $year < 1900) {
        $err = 'Title and valid year are required.';
    } else {
        $db   = get_db();
        $stmt = $db->prepare("UPDATE papers SET title=?, abstract=?, keywords=?, doi=?, publication_year=?, venue_id=?, status=? WHERE paper_id=?");
        $stmt->bind_param('ssssiisi', $title, $abstract, $keywords, $doi, $year, $venue, $status, $id);
        if ($stmt->execute()) {
            $msg = "Paper updated successfully.";
        } else {
            $err = 'Error: ' . $db->error;
        }
    }
}
 
// ── LOAD EDIT DATA ────────────────────────────────────────────
if (isset($_GET['edit'])) {
    $edit_paper = db_row("SELECT * FROM papers WHERE paper_id = ?", [(int)$_GET['edit']], 'i');
}
 
// ── SEARCH & FILTER ───────────────────────────────────────────
$search  = trim($_GET['q']          ?? '');
$year    = trim($_GET['year']       ?? '');
$status  = trim($_GET['status']     ?? '');
$venue_t = trim($_GET['venue_type'] ?? '');
 
$where = ['1=1']; $params = []; $types = '';
 
if ($search !== '') {
    $where[] = 'MATCH(p.title, p.abstract, p.keywords) AGAINST(? IN BOOLEAN MODE)';
    $params[] = $search . '*'; $types .= 's';
}
if ($year !== '') {
    $where[] = 'p.publication_year = ?';
    $params[] = (int)$year; $types .= 'i';
}
if ($status !== '') {
    $where[] = 'p.status = ?';
    $params[] = $status; $types .= 's';
}
if ($venue_t !== '') {
    $where[] = 'v.type = ?';
    $params[] = $venue_t; $types .= 's';
}
 
$sql = "
    SELECT p.paper_id, p.title, p.abstract, p.keywords,
           p.doi, p.publication_year, p.status,
           v.name AS venue_name, v.type AS venue_type,
           COUNT(DISTINCT c.citation_id) AS citation_count,
           GROUP_CONCAT(CONCAT(a.first_name,' ',a.last_name)
               ORDER BY pa.author_order SEPARATOR ', ') AS authors
    FROM papers p
    LEFT JOIN venues       v  ON p.venue_id = v.venue_id
    LEFT JOIN citations    c  ON c.cited_paper_id = p.paper_id
    LEFT JOIN paper_authors pa ON p.paper_id = pa.paper_id
    LEFT JOIN authors      a  ON pa.author_id = a.author_id
    WHERE " . implode(' AND ', $where) . "
    GROUP BY p.paper_id
    ORDER BY p.publication_year DESC
";
 
$papers  = db_query($sql, $params, $types);
$years   = db_query("SELECT DISTINCT publication_year FROM papers ORDER BY publication_year DESC");
$statuses = ['Published','Under Review','Preprint','Retracted'];
$vtypes   = ['Journal','Conference','Workshop','Symposium'];
 
include 'includes/header.php';
?>
 
<div class="page-header">
  <h1>Research Papers</h1>
  <p><?= count($papers) ?> paper<?= count($papers) !== 1 ? 's' : '' ?> found<?= $search ? " for <em>\"".htmlspecialchars($search)."\"</em>" : '' ?></p>
</div>
 
<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
 
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
      <select name="year" onchange="this.form.submit()">
        <option value="">All Years</option>
        <?php foreach ($years as $y): ?>
        <option value="<?= $y['publication_year'] ?>" <?= $year == $y['publication_year'] ? 'selected' : '' ?>>
          <?= $y['publication_year'] ?>
        </option>
        <?php endforeach; ?>
      </select>
      <select name="status" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <?php foreach ($statuses as $s): ?>
        <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
      <select name="venue_type" onchange="this.form.submit()">
        <option value="">All Venue Types</option>
        <?php foreach ($vtypes as $vt): ?>
        <option value="<?= $vt ?>" <?= $venue_t === $vt ? 'selected' : '' ?>><?= $vt ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
</div>
 
<!-- EDIT FORM -->
<?php if ($edit_paper): ?>
<div class="card" style="border-color:var(--accent);margin-bottom:1.5rem;">
  <div class="card-title"><span class="ct-icon">✏️</span> Edit Paper</div>
  <form method="POST" action="papers.php">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="paper_id" value="<?= $edit_paper['paper_id'] ?>">
    <div class="form-grid">
      <div class="form-group full">
        <label>Title *</label>
        <input type="text" name="title" required value="<?= htmlspecialchars($edit_paper['title']) ?>">
      </div>
      <div class="form-group full">
        <label>Abstract</label>
        <textarea name="abstract" rows="3"><?= htmlspecialchars($edit_paper['abstract'] ?? '') ?></textarea>
      </div>
      <div class="form-group full">
        <label>Keywords</label>
        <input type="text" name="keywords" value="<?= htmlspecialchars($edit_paper['keywords'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>DOI</label>
        <input type="text" name="doi" value="<?= htmlspecialchars($edit_paper['doi'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Year *</label>
        <input type="number" name="year" required min="1900" max="<?= date('Y')+1 ?>" value="<?= $edit_paper['publication_year'] ?>">
      </div>
      <div class="form-group">
        <label>Venue</label>
        <select name="venue_id">
          <option value="">— None —</option>
          <?php foreach ($venues as $v): ?>
          <option value="<?= $v['venue_id'] ?>" <?= $v['venue_id'] == $edit_paper['venue_id'] ? 'selected' : '' ?>>
            [<?= $v['type'] ?>] <?= htmlspecialchars($v['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Status *</label>
        <select name="status">
          <?php foreach (['Published','Under Review','Preprint','Retracted'] as $s): ?>
          <option value="<?= $s ?>" <?= $edit_paper['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div style="margin-top:1.25rem;display:flex;gap:0.75rem;">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="papers.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>
 
<!-- PAPERS LIST -->
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
    <div style="font-size:0.8rem;color:var(--text3);margin-top:0.3rem;">✍ <?= htmlspecialchars($p['authors']) ?></div>
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
      <?php if ($p['doi']): ?>
      <span class="mono">DOI: <?= htmlspecialchars($p['doi']) ?></span>
      <?php endif; ?>
      <!-- EDIT & DELETE BUTTONS -->
      <span style="margin-left:auto;display:flex;gap:0.4rem;">
        <a href="papers.php?edit=<?= $p['paper_id'] ?>" class="btn btn-outline btn-sm">✏️ Edit</a>
        <form method="POST" style="display:inline;">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="paper_id" value="<?= $p['paper_id'] ?>">
          <button type="submit" class="btn btn-sm"
            style="background:rgba(248,113,113,0.15);color:var(--danger);border:1px solid rgba(248,113,113,0.3);"
            data-confirm="Delete this paper? All related citations will also be deleted.">
            🗑️ Delete
          </button>
        </form>
      </span>
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
 