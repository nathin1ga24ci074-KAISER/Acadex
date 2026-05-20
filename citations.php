<?php
// ─── citations.php — Citation Management ─────────────────────
require 'includes/db.php';
$page_title = 'Citations';
 
$msg = '';
$err = '';
 
// ── Add Citation ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $citing = (int)($_POST['citing_paper_id'] ?? 0);
    $cited  = (int)($_POST['cited_paper_id']  ?? 0);
    $ctx    = trim($_POST['context'] ?? '');
 
    if (!$citing || !$cited)
        $err = 'Both papers must be selected.';
    elseif ($citing === $cited)
        $err = 'A paper cannot cite itself.';
    else {
        $db   = get_db();
        $stmt = $db->prepare("INSERT INTO citations (citing_paper_id, cited_paper_id, context) VALUES (?,?,?)");
        $stmt->bind_param('iis', $citing, $cited, $ctx);
        if ($stmt->execute()) {
            $msg = 'Citation added successfully.';
        } else {
            $err = str_contains($db->error, 'Duplicate') ? 'This citation already exists.' : $db->error;
        }
    }
}
 
// ── All Citations ─────────────────────────────────────────────
$citations = db_query("
    SELECT
        c.citation_id,
        p1.title  AS citing_title,
        p1.publication_year AS citing_year,
        p2.title  AS cited_title,
        p2.publication_year AS cited_year,
        c.context
    FROM citations c
    JOIN papers p1 ON c.citing_paper_id = p1.paper_id
    JOIN papers p2 ON c.cited_paper_id  = p2.paper_id
    ORDER BY c.citation_id DESC
");
 
// ── Papers list for dropdown ──────────────────────────────────
$papers = db_query("SELECT paper_id, title, publication_year FROM papers ORDER BY publication_year DESC, title");
 
// ── Citation stats ────────────────────────────────────────────
$top_cited = db_query("
    SELECT p.title, COUNT(*) AS cnt
    FROM citations c
    JOIN papers p ON c.cited_paper_id = p.paper_id
    GROUP BY c.cited_paper_id
    ORDER BY cnt DESC
    LIMIT 5
");
 
include 'includes/header.php';
?>
 
<div class="page-header">
  <h1>Citations</h1>
  <p><?= count($citations) ?> citation<?= count($citations) !== 1 ? 's' : '' ?> tracked</p>
</div>
 
<?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= htmlspecialchars($err) ?></div><?php endif; ?>
 
<div class="two-col" style="align-items:start; margin-bottom:2rem;">
 
  <!-- Add Citation Form -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">🔗</span> Add Citation</div>
    <form method="POST" action="citations.php">
      <div class="form-grid">
        <div class="form-group full">
          <label>Citing Paper (the one that cites) *</label>
          <select name="citing_paper_id" required>
            <option value="">— Select paper —</option>
            <?php foreach ($papers as $p): ?>
            <option value="<?= $p['paper_id'] ?>"
              <?= ($_POST['citing_paper_id'] ?? '') == $p['paper_id'] ? 'selected' : '' ?>>
              [<?= $p['publication_year'] ?>] <?= htmlspecialchars(mb_strimwidth($p['title'], 0, 70, '…')) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group full">
          <label>Cited Paper (the one being cited) *</label>
          <select name="cited_paper_id" required>
            <option value="">— Select paper —</option>
            <?php foreach ($papers as $p): ?>
            <option value="<?= $p['paper_id'] ?>"
              <?= ($_POST['cited_paper_id'] ?? '') == $p['paper_id'] ? 'selected' : '' ?>>
              [<?= $p['publication_year'] ?>] <?= htmlspecialchars(mb_strimwidth($p['title'], 0, 70, '…')) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group full">
          <label>Citation Context <span style="color:var(--text3)">(optional — sentence where cited)</span></label>
          <textarea name="context" rows="2"
                    placeholder="Building on the transformer architecture introduced in…"><?= htmlspecialchars($_POST['context'] ?? '') ?></textarea>
        </div>
      </div>
      <div style="margin-top:1.25rem;">
        <button type="submit" class="btn btn-primary">Add Citation</button>
      </div>
    </form>
  </div>
 
  <!-- Most Cited -->
  <div class="card">
    <div class="card-title"><span class="ct-icon">📊</span> Most Referenced Papers</div>
    <?php if ($top_cited): ?>
    <div class="bar-chart">
      <?php $max = max(array_column($top_cited, 'cnt')); ?>
      <?php foreach ($top_cited as $r): ?>
      <div class="bar-row">
        <div class="bar-label" title="<?= htmlspecialchars($r['title']) ?>">
          <?= htmlspecialchars(mb_strimwidth($r['title'], 0, 30, '…')) ?>
        </div>
        <div class="bar-track">
          <div class="bar-fill" data-width="<?= round($r['cnt']/$max*100) ?>%"></div>
        </div>
        <div class="bar-val"><?= $r['cnt'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--text3);font-size:0.85rem;">No citation data yet.</p>
    <?php endif; ?>
  </div>
 
</div>
 
<!-- CITATION TABLE -->
<div class="card" style="padding:0;">
  <div class="card-title" style="padding:1.25rem 1.5rem 0;">
    <span class="ct-icon">📋</span> All Citations
  </div>
  <?php if ($citations): ?>
  <div class="table-wrap" style="margin-top:1rem;">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Citing Paper</th>
          <th></th>
          <th>Cited Paper</th>
          <th>Context</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($citations as $i => $c): ?>
        <tr>
          <td class="mono" style="color:var(--text3);"><?= $i + 1 ?></td>
          <td>
            <div style="font-size:0.85rem;"><?= htmlspecialchars(mb_strimwidth($c['citing_title'], 0, 50, '…')) ?></div>
            <span class="badge badge-blue" style="margin-top:3px;"><?= $c['citing_year'] ?></span>
          </td>
          <td style="font-size:1.2rem;color:var(--accent);text-align:center;">→</td>
          <td>
            <div style="font-size:0.85rem;"><?= htmlspecialchars(mb_strimwidth($c['cited_title'], 0, 50, '…')) ?></div>
            <span class="badge badge-purple" style="margin-top:3px;"><?= $c['cited_year'] ?></span>
          </td>
          <td style="font-size:0.78rem;color:var(--text3);font-style:italic;max-width:200px;">
            <?= $c['context'] ? '"' . htmlspecialchars(mb_strimwidth($c['context'], 0, 80, '…')) . '"' : '—' ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <div class="empty-state">
    <div class="es-icon">🔗</div>
    <h3>No citations yet</h3>
    <p>Add your first citation using the form above.</p>
  </div>
  <?php endif; ?>
</div>
 
<?php include 'includes/footer.php'; ?>
 