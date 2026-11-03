<?php
require __DIR__ . '/includes/layout.php';
$slug = preg_match('/^[a-z0-9-]+$/', $_GET['slug'] ?? '') ? $_GET['slug'] : '';
$x = $slug ? entry($slug) : null;
if (!$x) {
    http_response_code(404);
    page_start(['title' => 'Página no encontrada', 'path' => '/404', 'noindex' => true, 'description' => 'La página que buscas no existe.']);
    echo '<h1>Página no encontrada</h1><p><a href="/blog">Volver al blog</a></p>';
    page_end(); exit;
}
$url = SITE_URL . '/blog/' . $x['slug'];
$rel = array_slice(array_filter(entries(), fn($y) => $y['topic'] === $x['topic'] && $y['slug'] !== $x['slug']), 0, 3);
$post = ['@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $x['title'], 'description' => $x['summary'], 'inLanguage' => 'es',
  'author' => ['@type' => 'Person', 'name' => AUTHOR], 'mainEntityOfPage' => $url, 'keywords' => implode(', ', $x['tags'])];
if ($x['date']) $post['datePublished'] = $x['date'];
$crumbs = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
  ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => SITE_URL . '/'],
  ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => SITE_URL . '/blog'],
  ['@type' => 'ListItem', 'position' => 3, 'name' => $x['title']]]];
page_start(['title' => $x['title'], 'description' => $x['summary'], 'path' => '/blog/' . $x['slug'], 'section' => '/blog', 'og_type' => 'article', 'jsonld' => [$post, $crumbs]]);
?>
<nav aria-label="Migas de pan" class="crumbs"><ol><li><a href="/">Inicio</a></li><li><a href="/blog">Blog</a></li><li aria-current="page"><?= e($x['title']) ?></li></ol></nav>
<article class="prose">
  <header>
    <p class="meta"><a href="/blog?tema=<?= e(topic_slug($x['topic'])) ?>"><?= e($x['topic']) ?></a><?php if ($x['date']): ?> · <time datetime="<?= e($x['date']) ?>"><?= e(fecha($x['date'])) ?></time><?php endif; ?> · <?= (int)$x['minutes'] ?> min de lectura</p>
    <h1><?= e($x['title']) ?></h1>
    <p class="lead"><?= e($x['summary']) ?></p>
  </header>
  <?php readfile(__DIR__ . '/content/' . $x['slug'] . '.html'); ?>
  <footer><ul class="tags"><?php foreach ($x['hashtags'] as $h): ?><li><?= e($h) ?></li><?php endforeach; ?></ul></footer>
</article>
<?php if ($rel): ?>
<aside aria-labelledby="rel"><h2 id="rel">Más sobre este tema</h2><div class="grid"><?php foreach ($rel as $y) card($y); ?></div></aside>
<?php endif; page_end();
