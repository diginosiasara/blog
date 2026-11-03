# diginosia — sitio PHP
Separación de responsabilidades:
- **PHP** (`*.php`, `includes/`, `config.php`): rutas, plantillas, SEO, formulario, sitemap.
- **Datos** (`data/blog.json`, `content/*.html`): 62 entradas ya deduplicadas.
- **CSS** (`css/styles.css`) solo presentación · **JS** (`js/main.js`) solo mejoras (el sitio funciona sin JS).
- `tools/build_blog.py`: regenera datos desde el DOCX → `python3 tools/build_blog.py BLOG.docx`

Antes de publicar: cambia `SITE_URL` en `config.php` y en `robots.txt`; opcional `GA_ID`.
Requiere PHP 8+ con `mbstring`, Apache con `mod_rewrite` y `mail()` configurado.
