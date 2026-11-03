<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

/** Cabecera común: SEO (title, description, canonical, Open Graph, Twitter, JSON-LD) + accesibilidad (skip link, landmarks). */
function page_start(array $m): void {
    $title = $m['title'] . ($m['home'] ?? false ? '' : ' | ' . SITE_NAME);
    $url = SITE_URL . $m['path'];
    $desc = mb_substr($m['description'], 0, 158);
    $nav = ['/' => 'Inicio', '/blog' => 'Blog', '/contacto' => 'Contacto'];
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; script-src 'self' https://www.googletagmanager.com; connect-src 'self' https://*.google-analytics.com; style-src 'self'; frame-ancestors 'self'");
    $ld = $m['jsonld'] ?? [];
    $ld[] = ['@context' => 'https://schema.org', '@type' => 'Person', 'name' => AUTHOR, 'url' => SITE_URL . '/', 'sameAs' => [LINKEDIN, GITHUB], 'jobTitle' => 'Administrativa sanitaria, docente y desarrolladora'];
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($desc) ?>">
<meta name="author" content="<?= e(AUTHOR) ?>">
<meta name="robots" content="<?= ($m['noindex'] ?? false) ? 'noindex, follow' : 'index, follow, max-image-preview:large' ?>">
<meta name="theme-color" content="#fffcf8">
<link rel="canonical" href="<?= e($url) ?>">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='16' fill='%231a1a1a'/%3E%3Ctext x='16' y='21' font-size='14' text-anchor='middle' fill='%23fffcf8' font-family='serif'%3EDg%3C/text%3E%3C/svg%3E">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:locale" content="es_ES">
<meta property="og:type" content="<?= e($m['og_type'] ?? 'website') ?>">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($desc) ?>">
<meta property="og:url" content="<?= e($url) ?>">
<meta name="twitter:card" content="summary">
<link rel="stylesheet" href="/css/styles.css">
<?php foreach ($ld as $j): ?><script type="application/ld+json"><?= json_encode($j, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endforeach; ?>
<?php if (GA_ID): ?><meta name="ga-id" content="<?= e(GA_ID) ?>"><?php endif; ?>
<script src="/js/main.js" defer></script>
</head>
<body>
<a class="skip" href="#contenido">Saltar al contenido</a>
<header class="site-header">
  <a class="brand" href="/"><strong>Diginosia.com</strong><span><?= e(AUTHOR) ?></span></a>
  <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="menu">Menú</button>
  <nav id="menu" aria-label="Principal">
    <ul>
<?php foreach ($nav as $href => $label): $on = $href === ($m['section'] ?? ''); ?>
      <li><a href="<?= $href ?>"<?= $on ? ' aria-current="page"' : '' ?>><?= e($label) ?></a></li>
<?php endforeach; ?>
    </ul>
  </nav>
</header>
<main id="contenido" tabindex="-1">
<?php }

function page_end(): void { ?>
</main>
<footer class="site-footer">
  <p>© <?= date('Y') ?> <?= e(AUTHOR) ?> · <a href="<?= e(LINKEDIN) ?>" rel="me noopener" target="_blank">LinkedIn<span class="sr"> (se abre en pestaña nueva)</span></a> · <a href="<?= e(GITHUB) ?>" rel="me noopener" target="_blank">GitHub<span class="sr"> (se abre en pestaña nueva)</span></a> · <a href="/sitemap.xml">Mapa del sitio</a></p>
</footer>
<?php if (GA_ID): ?>
<div id="consent" class="consent" role="dialog" aria-label="Cookies de analítica" hidden>
  <p>Uso cookies de analítica para mejorar el sitio. ¿Las aceptas?</p>
  <button type="button" data-consent="yes">Aceptar</button> <button type="button" data-consent="no">Rechazar</button>
</div>
<?php endif; ?>
</body>
</html>
<?php }

function card(array $x): void {
    $url = '/blog/' . $x['slug']; ?>
  <article class="card" data-search="<?= e(mb_strtolower($x['title'] . ' ' . $x['summary'] . ' ' . implode(' ', $x['tags']))) ?>">
    <p class="meta"><span class="badge"><?= $x['type'] === 'articulo' ? 'Artículo' : 'Publicación' ?></span>
      <?php if ($x['date']): ?><time datetime="<?= e($x['date']) ?>"><?= e(fecha($x['date'])) ?></time><?php endif; ?> · <?= (int)$x['minutes'] ?> min</p>
    <h2><a href="<?= e($url) ?>"><?= e($x['title']) ?></a></h2>
    <p><?= e($x['summary']) ?></p>
    <ul class="tags"><?php foreach ($x['tags'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul>
  </article>
<?php }
