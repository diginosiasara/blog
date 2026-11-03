<?php
session_start();
require __DIR__ . '/includes/layout.php';
$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
$motivos = ['Colaboración', 'Formación', 'Proyecto de IA o desarrollo', 'Otro'];
$v = ['nombre' => '', 'email' => '', 'motivo' => '', 'mensaje' => ''];
$err = []; $ok = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim((string)($_POST[$k] ?? ''));
    if (!hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? '')) || ($_POST['web'] ?? '') !== '') { http_response_code(400); exit('Solicitud no válida'); }
    if (mb_strlen($v['nombre']) < 2) $err['nombre'] = 'Introduce tu nombre.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $err['email'] = 'Introduce un correo válido.';
    if (!in_array($v['motivo'], $motivos, true)) $err['motivo'] = 'Selecciona un motivo.';
    if (mb_strlen($v['mensaje']) < 10) $err['mensaje'] = 'Escribe un mensaje de al menos 10 caracteres.';
    if (time() - ($_SESSION['last'] ?? 0) < 30) $err['mensaje'] = 'Espera unos segundos antes de reenviar.';
    if (!$err) {
        $asunto = '=?UTF-8?B?' . base64_encode('Contacto web: ' . $v['motivo']) . '?=';
        $cab = 'From: ' . EMAIL . "\r\nReply-To: " . str_replace(["\r", "\n"], '', $v['email']) . "\r\nContent-Type: text/plain; charset=UTF-8";
        $ok = mail(EMAIL, $asunto, "Nombre: {$v['nombre']}\nEmail: {$v['email']}\n\n{$v['mensaje']}", $cab);
        $_SESSION['last'] = time();
        if ($ok) $v = array_fill_keys(array_keys($v), ''); else $err['envio'] = 'No se pudo enviar. Escríbeme a ' . EMAIL . '.';
    }
}
page_start(['title' => 'Contacto', 'path' => '/contacto', 'section' => '/contacto',
  'description' => 'Contacta con Sara López Figueredo para colaboraciones, formación o proyectos de IA y desarrollo web.']);
$f = fn($k) => isset($err[$k]) ? ' aria-invalid="true" aria-describedby="e-' . $k . '"' : '';
?>
<h1>Contacto</h1>
<p class="lead">Respondo personalmente, normalmente en 24 h.</p>
<?php if ($ok): ?><p class="notice ok" role="status">Mensaje enviado. ¡Gracias!</p><?php endif; ?>
<?php if ($err): ?>
<div class="notice bad" role="alert"><p>Revisa el formulario:</p><ul><?php foreach ($err as $k => $m): ?><li><a href="#<?= $k ?>"><?= e($m) ?></a></li><?php endforeach; ?></ul></div>
<?php endif; ?>
<form method="post" action="/contacto" novalidate class="form">
  <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
  <div class="hp" aria-hidden="true"><label>No rellenar <input name="web" tabindex="-1" autocomplete="off"></label></div>
  <label for="nombre">Nombre</label>
  <input id="nombre" name="nombre" autocomplete="name" required value="<?= e($v['nombre']) ?>"<?= $f('nombre') ?>>
  <label for="email">Correo electrónico</label>
  <input id="email" name="email" type="email" autocomplete="email" required value="<?= e($v['email']) ?>"<?= $f('email') ?>>
  <label for="motivo">Motivo</label>
  <select id="motivo" name="motivo" required<?= $f('motivo') ?>><option value="">Selecciona…</option><?php foreach ($motivos as $m): ?><option<?= $v['motivo'] === $m ? ' selected' : '' ?>><?= e($m) ?></option><?php endforeach; ?></select>
  <label for="mensaje">Mensaje</label>
  <textarea id="mensaje" name="mensaje" rows="6" required<?= $f('mensaje') ?>><?= e($v['mensaje']) ?></textarea>
  <button class="btn" type="submit">Enviar mensaje</button>
</form>
<?php page_end();
