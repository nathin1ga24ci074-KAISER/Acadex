<?php
// ─── header.php — Shared Navigation Header ───────────────────
// Usage: include 'includes/header.php';
// Set $page_title before including.
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
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
 
<nav class="navbar">
  <a href="index.php" class="nav-brand">
    <span class="brand-icon">⬡</span>
    <span>Acadex</span>
  </a>
  <ul class="nav-links">
    <li><a href="index.php"       class="<?= $current==='index'     ?'active':'' ?>">Dashboard</a></li>
    <li><a href="papers.php"      class="<?= $current==='papers'    ?'active':'' ?>">Papers</a></li>
    <li><a href="authors.php"     class="<?= $current==='authors'   ?'active':'' ?>">Authors</a></li>
    <li><a href="citations.php"   class="<?= $current==='citations' ?'active':'' ?>">Citations</a></li>
    <li><a href="analytics.php"   class="<?= $current==='analytics' ?'active':'' ?>">Analytics</a></li>
    <li><a href="add_paper.php"   class="btn-nav <?= $current==='add_paper'?'active':'' ?>">+ Add Paper</a></li>
  </ul>
</nav>
 
<main class="page-content">
 