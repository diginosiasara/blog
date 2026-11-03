<?php
require __DIR__ . '/includes/layout.php';
$con = array_filter(entries(), fn($x) => $x['type'] === 'articulo' && $x['date']);
usort($con, fn($a, $b) => strcmp($b['date'], $a['date']));
page_start(['title' => 'Sara López Figueredo · Sanidad, docencia e IA aplicada', 'home' => true, 'path' => '/', 'section' => '/',
  'description' => 'Portfolio de Sara López Figueredo: gestión sanitaria y documentación clínica, formación profesional, desarrollo web e inteligencia artificial aplicada a la salud.',
  'jsonld' => [['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => SITE_NAME, 'url' => SITE_URL . '/', 'inLanguage' => 'es']]]);
?>
<section class="hero">
  <p class="eyebrow">Sanidad · Docencia · IA aplicada</p>
  <h1>Hago simple lo complejo: de la gestión sanitaria a la inteligencia artificial</h1>
  <p class="lead">Más de diez años en el ámbito sanitario, docencia de desarrollo web y proyectos de IA local. Aquí comparto lo que aprendo.</p>
  <p><a class="btn" href="/blog">Leer el blog</a> <a class="btn btn-ghost" href="/contacto">Contactar</a></p>
</section>
<section aria-labelledby="ult">
  <h2 id="ult">Últimos artículos</h2>
  <div class="grid"><?php foreach (array_slice($con, 0, 3) as $x) card($x); ?></div>
  <p><a href="/blog">Ver las <?= count(entries()) ?> entradas del blog</a></p>
</section>
<?php page_end();
