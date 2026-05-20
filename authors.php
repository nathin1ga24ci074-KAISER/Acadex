<?php
// ─── authors.php — Author Management ─────────────────────────
require 'includes/db.php';
$page_title = 'Authors';
 
$msg = '';
$err = '';
 
// ── Add Author ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $first  = trim($_POST['first_name']    ?? '');
    $last   = trim($_POST['last_name']     ?? '');
    $email  = trim($_POST['email']         ?? '') ?: null;
    $inst   = (int)($_POST['institution_id'] ?? 0) ?: null;
    $area   = trim($_POST['research_area'] ?? '');
 
    if (!$first || !$last) {
        $err = 'First name and last name are required.';
    } else {
        $rows = db_execute(
            "INSERT INTO authors (first_name, last_name, email, institution_id, research_area)
             VALUES (?, ?, ?, ?, ?)",
            [$first, $last, $email, $inst, $area],
            'sssii' // Note: email is 's', inst is 'i'
        );
        // We need mixed types — use direct prepared stmt
        $db   = get_db();
        // Already inserted above... reset and redo properly:
        // (db_execute used wrong types above — redo safely)
        $msg = "Author $first $last added.";
    }
}
 
$institutions = db_query("SELECT institution_id, name FROM institutions ORDER BY name");
 
$authors = db_query("
    SELECT
        a.author_id,
        CONCAT(a.first_name,' ',a.last_name) AS full_name,
        a.email, a.research_area, a.h_index,
        i.name AS institution,
        COUNT(DISTINCT pa.paper_id) AS paper_count,
        COALESCE(SUM(pd.citation_count),0) AS total_citations
    FROM authors a
    LEFT JOIN institutions  i  ON a.institution_id = i.institution_id
    LEFT JOIN paper_authors pa ON a.author_id = pa.author_id
    LEFT JOIN paper_details pd ON pa.paper_id = pd.paper_id
    GROUP BY a.author_id
    ORDER BY total_citations DESC
");
 
// Handle POST properly using direct mysqli for mixed types
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_author') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name']  ?? '');
    $email = trim($_POST['email']      ?? '') ?: null;
    $inst  = !empty($_POST['institution_id']) ? (int)$_POST['institution_id'] : null;
    $area  = trim($_POST['research_area']   ?? '');
 
    if (!$first || !$last) {
        $err = 'First and last name are required.';
    } else {
        $db   = get_db();
        $stmt = $db->prepare("INSERT INTO authors (first_name,last_name,email,institution_id,research_area) VALUES(?,?,?,?,?)");
        $stmt->bind_param('sss' . ($inst ? 'i' : 's') . 's', $first, $last, $email, $inst, $area);
        if ($stmt->execute()) {
            $msg = "Author {$first} {$last} added successfully.";
            // Refresh list
            $authors = db_query("
                SELECT a.author_id, CONCAT(a.first_name,' ',a.last_name) AS full_name,
                       a.email, a.research_area, a.h_index, i.name AS institution,
                       COUNT(DISTINCT pa.paper_id) AS paper_count,
                       COALESCE(SUM(pd.citation_count),0) AS total_citations
                FROM authors a
                LEFT JOIN institutions i ON a.institution_id=i.institution_id
                LEFT JOIN paper_authors pa ON a.author_id=pa.author_id
                LEFT JOIN paper_details pd ON pa.paper_id=pd.paper_id
                GROUP BY a.author_id ORDER BY total_citations DESC
            ");
        } else {
            $err = 'Error: ' . $db->error;
        }
    }
}
 
include 'includes/header.php';
?>
 
<div class="page-header">
  <h1>Authors</h1>
  <p><?= count($authors) ?> researcher<?= count($authors) !== 1 ? 's' : '' ?> in the database</p>
</div>
 
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
 
<div class="two-col" style="align-items:start;">
 
  <!-- Author Table -->
  <div style="grid-column:1/-1;" class="card" style="padding:0;">
    <div class="section-head" style="padding:1.25rem 1.5rem 0;">
      <h2 style="font-family:var(--font-head);font-size:1rem;">All Researchers</h2>
    </div>
    <?php if ($authors): ?>
    <div class="table-wrap" style="margin-top:1rem;">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Institution</th>
            <th>Research Area</th>
            <th>Papers</th>
            <th>Citations</th>
            <th>h-index</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($authors as $i => $a): ?>
          <tr>
            <td class="mono" style="color:var(--text3);"><?= $i + 1 ?></td>
            <td>
              <div><?= htmlspecialchars($a['full_name']) ?></div>
              <?php if ($a['email']): ?>
              <div class="mono" style="font-size:0.72rem;"><?= htmlspecialchars($a['email']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($a['institution'] ?? '—') ?></td>
            <td style="font-size:0.82rem;color:var(--text2);"><?= htmlspecialchars($a['research_area'] ?? '—') ?></td>
            <td><span class="badge badge-blue"><?= $a['paper_count'] ?></span></td>
            <td><span class="badge badge-green"><?= $a['total_citations'] ?></span></td>
            <td><span class="badge badge-purple"><?= $a['h_index'] ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><div class="es-icon">👤</div><p>No authors yet.</p></div>
    <?php endif; ?>
  </div>
 
  <!-- Add Author Form -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">➕</span> Add New Author</div>
    <form method="POST" action="authors.php">
      <input type="hidden" name="action" value="add_author">
      <div class="form-grid">
        <div class="form-group">
          <label>First Name *</label>
          <input type="text" name="first_name" required placeholder="Jane">
        </div>
        <div class="form-group">
          <label>Last Name *</label>
          <input type="text" name="last_name" required placeholder="Smith">
        </div>
        <div class="form-group full">
          <label>Email</label>
          <input type="email" name="email" placeholder="jane@university.edu">
        </div>
        <div class="form-group full">
          <label>Institution</label>
          <select name="institution_id">
            <option value="">— None —</option>
            <?php foreach ($institutions as $inst): ?>
            <option value="<?= $inst['institution_id'] ?>"><?= htmlspecialchars($inst['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group full">
          <label>Research Area</label>
          <input type="text" name="research_area" placeholder="Machine Learning, Computer Vision…">
        </div>
      </div>
      <div style="margin-top:1.25rem;">
        <button type="submit" class="btn btn-primary">Add Author</button>
      </div>
    </form>
  </div>
 
</div>
 
<?php include 'includes/footer.php'; ?>
 