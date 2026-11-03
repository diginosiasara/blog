/* Comportamiento únicamente. El sitio funciona sin JS: esto solo mejora. */
(() => {
  'use strict';
  document.documentElement.classList.add('js');

  // Menú móvil accesible (aria-expanded, Escape para cerrar)
  const btn = document.querySelector('.nav-toggle'), menu = document.getElementById('menu');
  if (btn && menu) {
    const set = (open) => { menu.classList.toggle('open', open); btn.setAttribute('aria-expanded', String(open)); };
    btn.addEventListener('click', () => set(btn.getAttribute('aria-expanded') !== 'true'));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { set(false); btn.focus(); } });
  }

  // Buscador del blog (mejora progresiva) con contador anunciado a lectores de pantalla
  const box = document.querySelector('.search[data-js]'), input = document.getElementById('buscar');
  if (box && input) {
    box.hidden = false;
    const cards = [...document.querySelectorAll('#lista .card')], out = document.getElementById('contador');
    input.addEventListener('input', () => {
      const q = input.value.trim().toLowerCase();
      let n = 0;
      cards.forEach((c) => { const ok = !q || c.dataset.search.includes(q); c.hidden = !ok; n += ok; });
      out.textContent = n === 1 ? '1 entrada' : `${n} entradas`;
    });
  }

  // Analítica (SEM): solo se carga si hay ID y el usuario acepta
  const ga = document.querySelector('meta[name="ga-id"]'), bar = document.getElementById('consent');
  const load = () => {
    const s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + ga.content;
    document.head.appendChild(s);
    window.dataLayer = window.dataLayer || []; window.gtag = function () { dataLayer.push(arguments); };
    gtag('js', new Date()); gtag('config', ga.content, { anonymize_ip: true });
  };
  if (ga && bar) {
    let c = null; try { c = localStorage.getItem('consent'); } catch (e) {}
    if (c === 'yes') load(); else if (c === null) bar.hidden = false;
    bar.addEventListener('click', (e) => {
      const v = e.target.dataset.consent; if (!v) return;
      try { localStorage.setItem('consent', v); } catch (x) {}
      bar.hidden = true; if (v === 'yes') load();
    });
  }
})();
