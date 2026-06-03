<?php
$page_title = $page_title ?? 'Acadex';
$current    = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> · Acadex</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
 
<nav class="navbar">
  <a href="index.php" class="nav-brand">
    <span class="brand-icon">⬡</span>
    <span>ACADEX</span>
  </a>
  <ul class="nav-links">
    <li><a href="dashboard.php"  class="<?= $current==='dashboard'  ?'active':'' ?>">Dashboard</a></li>
    <li><a href="papers.php"     class="<?= $current==='papers'     ?'active':'' ?>">Papers</a></li>
    <li><a href="authors.php"    class="<?= $current==='authors'    ?'active':'' ?>">Authors</a></li>
    <li><a href="citations.php"  class="<?= $current==='citations'  ?'active':'' ?>">Citations</a></li>
    <li><a href="analytics.php"  class="<?= $current==='analytics'  ?'active':'' ?>">Analytics</a></li>
    <li><a href="add_paper.php"  class="btn-nav <?= $current==='add_paper'?'active':'' ?>">+ Add Paper</a></li>
  </ul>
</nav>
 
<main class="page-content">
 