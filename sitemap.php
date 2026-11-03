<?php
require __DIR__ . '/config.php';
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach (['/', '/blog', '/contacto'] as $p) echo '<url><loc>' . e(SITE_URL . $p) . "</loc></url>\n";
foreach (entries() as $x) echo '<url><loc>' . e(SITE_URL . '/blog/' . $x['slug']) . '</loc>' . ($x['date'] ? '<lastmod>' . $x['date'] . '</lastmod>' : '') . "</url>\n";
echo '</urlset>';
