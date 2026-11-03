<?php
require __DIR__ . '/includes/layout.php';
$tema = preg_match('/^[a-z0-9-]+$/', $_GET['tema'] ?? '') ? $_GET['tema'] : '';
$tipo = in_array($_GET['tipo'] ?? '', ['articulo', 'publicacion'], true) ? $_GET['tipo'] : '';
$lista = array_filter(entries(), fn($x) => (!$tema || topic_slug($x['topic']) === $tema) && (!$tipo || $x['type'] === $tipo));
usort($lista, fn($a, $b) => strcmp($b['date'] ?? '0', $a['date'] ?? '0'));
$filtrado = $tema || $tipo;
$q = fn(array $p) => '/blog' . ($p ? '?' . http_build_query($p) : '');
page_start(['title' => 'Blog: sanidad, codificación clínica, desarrollo web e IA', 'path' => '/blog', 'section' => '/blog', 'noindex' => $filtrado,
  'description' => count(entries()) . ' artículos y publicaciones sobre documentación sanitaria, CIE-10-ES, teleasistencia, arquitectura de software, IA local y docencia.']);
?>
<h1>Blog</h1>
<p class="lead"><?= count(entries()) ?> entradas organizadas por tema. Sin repeticiones.</p>
<nav aria-label="Filtrar por tema" class="filters">
  <ul>
    <li><a href="<?= e($q($tipo ? ['tipo' => $tipo] : [])) ?>"<?= !$tema ? ' aria-current="true"' : '' ?>>Todos</a></li>
<?php foreach (blog()['topics'] as $t): ?>
    <li><a href="<?= e($q(array_filter(['tema' => $t['slug'], 'tipo' => $tipo]))) ?>"<?= $tema === $t['slug'] ? ' aria-current="true"' : '' ?>><?= e($t['name']) ?></a></li>
<?php endforeach; ?>
  </ul>
</nav>
<nav aria-label="Filtrar por tipo" class="filters">
  <ul>
<?php foreach (['' => 'Todo', 'articulo' => 'Artículos', 'publicacion' => 'Publicaciones'] as $v => $l): ?>
    <li><a href="<?= e($q(array_filter(['tema' => $tema, 'tipo' => $v]))) ?>"<?= $tipo === $v ? ' aria-current="true"' : '' ?>><?= $l ?></a></li>
<?php endforeach; ?>
  </ul>
</nav>
<div class="search" hidden data-js>
  <label for="buscar">Buscar en esta lista</label>
  <input id="buscar" type="search" autocomplete="off">
</div>
<p id="contador" role="status" aria-live="polite"><?= count($lista) ?> entradas</p>
<div class="grid" id="lista"><?php foreach ($lista as $x) card($x); ?></div>
<?php page_end();
