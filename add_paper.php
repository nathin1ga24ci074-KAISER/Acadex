<?php
// ─── add_paper.php — Add New Paper ───────────────────────────
require 'includes/db.php';
$page_title = 'Add Paper';
 
$venues  = db_query("SELECT venue_id, name, type FROM venues ORDER BY type, name");
$authors = db_query("SELECT author_id, first_name, last_name FROM authors ORDER BY last_name");
 
$msg = '';
$err = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title']   ?? '');
    $abstract= trim($_POST['abstract']?? '');
    $keywords= trim($_POST['keywords']?? '');
    $doi     = trim($_POST['doi']     ?? '') ?: null;
    $year    = (int)($_POST['year']   ?? 0);
    $venue   = (int)($_POST['venue_id']?? 0) ?: null;
    $status  = $_POST['status'] ?? 'Published';
    $sel_authors = array_map('intval', $_POST['author_ids'] ?? []);
 
    // Validate
    if (!$title)           $err = 'Title is required.';
    elseif ($year < 1900 || $year > (int)date('Y') + 1)
                           $err = 'Please enter a valid publication year.';
    elseif (!in_array($status, ['Published','Under Review','Preprint','Retracted']))
                           $err = 'Invalid status.';
    else {
        $db = get_db();
        $db->begin_transaction();
        try {
            // Insert paper
            $stmt = $db->prepare("
                INSERT INTO papers (title, abstract, keywords, doi, publication_year, venue_id, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('ssssiis', $title, $abstract, $keywords, $doi, $year, $venue, $status);
            $stmt->execute();
            $paper_id = $db->insert_id;
 
            // Attach authors
            if ($sel_authors) {
                $stmt2 = $db->prepare("
                    INSERT INTO paper_authors (paper_id, author_id, author_order, is_corresponding)
                    VALUES (?, ?, ?, ?)
                ");
                foreach ($sel_authors as $order => $aid) {
                    $is_corr = ($order === 0) ? 1 : 0;
                    $o = $order + 1;
                    $stmt2->bind_param('iiii', $paper_id, $aid, $o, $is_corr);
                    $stmt2->execute();
                }
            }
 
            $db->commit();
            $msg = "Paper \"" . htmlspecialchars($title) . "\" added successfully! <a href='papers.php' style='color:inherit;text-decoration:underline;'>View all papers →</a>";
        } catch (Exception $e) {
            $db->rollback();
            $err = 'Database error: ' . $e->getMessage();
        }
    }
}
 
include 'includes/header.php';
?>
 
<div class="page-header">
  <h1>Add New Paper</h1>
  <p>Register a research paper in the database</p>
</div>
 
<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
 
<div class="card" style="max-width:860px;">
  <form method="POST" action="add_paper.php">
 
    <div class="form-grid">
 
      <!-- Title -->
      <div class="form-group full">
        <label for="title">Paper Title *</label>
        <input type="text" id="title" name="title" required
               placeholder="e.g. Attention Is All You Need"
               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
      </div>
 
      <!-- Abstract -->
      <div class="form-group full">
        <label for="abstract">Abstract</label>
        <textarea id="abstract" name="abstract" rows="4"
                  placeholder="Brief summary of the paper…"><?= htmlspecialchars($_POST['abstract'] ?? '') ?></textarea>
      </div>
 
      <!-- Keywords -->
      <div class="form-group full">
        <label for="keywords">Keywords</label>
        <input type="text" id="keywords" name="keywords"
               placeholder="transformer, attention, NLP, deep learning"
               value="<?= htmlspecialchars($_POST['keywords'] ?? '') ?>">
      </div>
 
      <!-- DOI -->
      <div class="form-group">
        <label for="doi">DOI</label>
        <input type="text" id="doi" name="doi"
               placeholder="10.1234/example.2024"
               value="<?= htmlspecialchars($_POST['doi'] ?? '') ?>">
      </div>
 
      <!-- Year -->
      <div class="form-group">
        <label for="year">Publication Year *</label>
        <input type="number" id="year" name="year" required
               min="1900" max="<?= date('Y') + 1 ?>"
               placeholder="<?= date('Y') ?>"
               value="<?= htmlspecialchars($_POST['year'] ?? '') ?>">
      </div>
 
      <!-- Venue -->
      <div class="form-group">
        <label for="venue_id">Venue</label>
        <select id="venue_id" name="venue_id">
          <option value="">— Select venue —</option>
          <?php foreach ($venues as $v): ?>
          <option value="<?= $v['venue_id'] ?>"
            <?= ($_POST['venue_id'] ?? '') == $v['venue_id'] ? 'selected' : '' ?>>
            [<?= $v['type'] ?>] <?= htmlspecialchars($v['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
 
      <!-- Status -->
      <div class="form-group">
        <label for="status">Status *</label>
        <select id="status" name="status">
          <?php foreach (['Published','Under Review','Preprint','Retracted'] as $s): ?>
          <option value="<?= $s ?>" <?= ($_POST['status'] ?? 'Published') === $s ? 'selected' : '' ?>>
            <?= $s ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
 
      <!-- Authors (multi-select) -->
      <div class="form-group full">
        <label for="author_ids">Authors <span style="color:var(--text3);font-size:0.75rem;">(hold Ctrl/Cmd to select multiple; first selected = lead/corresponding author)</span></label>
        <select id="author_ids" name="author_ids[]" multiple style="min-height:130px;">
          <?php foreach ($authors as $a): 
            $selected = in_array($a['author_id'], array_map('intval', $_POST['author_ids'] ?? [])) ? 'selected' : '';
          ?>
          <option value="<?= $a['author_id'] ?>" <?= $selected ?>>
            <?= htmlspecialchars($a['last_name'] . ', ' . $a['first_name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
 
    </div><!-- /form-grid -->
 
    <div style="margin-top:1.5rem;display:flex;gap:0.75rem;">
      <button type="submit" class="btn btn-primary">Save Paper</button>
      <a href="papers.php" class="btn btn-outline">Cancel</a>
    </div>
 
  </form>
</div>
 
<?php include 'includes/footer.php'; ?>
 