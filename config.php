<?php
declare(strict_types=1);
// ── Ajustes del sitio (único sitio donde cambiar datos) ──
const SITE_URL = 'https://www.diginosia.com';   // ⚠ cambiar por el dominio real
const SITE_NAME = 'Diginosia.com';
const AUTHOR = 'Sara López Figueredo Sainz';
const EMAIL = 'nosia_sl@hotmail.com';
const LINKEDIN = 'https://www.linkedin.com/in/diginosia/';
const GITHUB = 'https://github.com/diginosiasara';
const GA_ID = '';                               // SEM/analítica: 'G-XXXXXXXXXX' (solo carga con consentimiento)

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function blog(): array { static $d; return $d ??= json_decode(file_get_contents(__DIR__ . '/data/blog.json'), true, 512, JSON_THROW_ON_ERROR); }
function entries(): array { return blog()['entries']; }
function entry(string $slug): ?array { foreach (entries() as $x) if ($x['slug'] === $slug) return $x; return null; }
function topic_slug(string $name): string { foreach (blog()['topics'] as $t) if ($t['name'] === $name) return $t['slug']; return ''; }
function fecha(?string $iso): string {
    if (!$iso) return '';
    $m = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    [$y, $mo, $d] = array_map('intval', explode('-', $iso));
    return "$d de {$m[$mo - 1]} de $y";
}
