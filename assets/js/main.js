(function () {
  var header = document.querySelector('.site-header');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 8); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }
  function toggle(btn, target) {
    if (!btn || !target) return;
    btn.addEventListener('click', function () {
      var open = target.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      var input = target.querySelector('input');
      if (open && input) input.focus();
    });
  }
  toggle(document.getElementById('burger'), document.getElementById('nav'));
  toggle(document.getElementById('search-toggle'), document.getElementById('search-panel'));

  /* Featured review lightbox */
  var lb = document.getElementById('pk-lightbox');
  if (lb) {
    var lastFocus = null;
    var focusables = 'a[href],button:not([disabled]),input,select,textarea,[tabindex]:not([tabindex="-1"])';
    var openLb = function () {
      lastFocus = document.activeElement;
      lb.classList.add('show');
      void lb.offsetWidth;
      lb.classList.add('in');
      document.body.style.overflow = 'hidden';
      lb.querySelector('.lb-x').focus();
    };
    var closeLb = function () {
      lb.classList.remove('in');
      document.body.style.overflow = '';
      setTimeout(function () { lb.classList.remove('show'); }, 250);
      if (lastFocus) lastFocus.focus();
    };
    document.querySelectorAll('[data-pk-open]').forEach(function (el) {
      el.addEventListener('click', function (e) { e.preventDefault(); openLb(); });
    });
    lb.addEventListener('click', function (e) {
      if (e.target === lb || e.target.closest('.lb-x')) closeLb();
    });
    document.addEventListener('keydown', function (e) {
      if (!lb.classList.contains('show')) return;
      if (e.key === 'Escape') { closeLb(); return; }
      if (e.key === 'Tab') {
        var f = lb.querySelectorAll(focusables);
        if (!f.length) return;
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });
  }

  /* Search page (reads /search.json built by Jekyll) */
  var box = document.getElementById('pk-results');
  if (box) {
    var base = (document.querySelector('meta[name="pk-base"]') || {}).content || '';
    var q = new URLSearchParams(location.search).get('q') || '';
    var input = document.querySelector('#pk-results').closest('section').querySelector('input[type="search"]');
    if (input) input.value = q;
    var status = document.getElementById('pk-status');
    if (!q.trim()) { status.textContent = 'Type something to search.'; return; }
    fetch(base + '/search.json').then(function (r) { return r.json(); }).then(function (items) {
      var terms = q.toLowerCase().split(/\s+/).filter(Boolean);
      var hits = items.filter(function (i) {
        var hay = ((i.title || '') + ' ' + (i.category || '') + ' ' + (i.excerpt || '')).toLowerCase();
        return terms.every(function (t) { return hay.indexOf(t) > -1; });
      });
      status.textContent = hits.length + (hits.length === 1 ? ' result' : ' results') + ' for \u201C' + q + '\u201D';
      hits.forEach(function (i) {
        var card = document.createElement('article'); card.className = 'card';
        var bd = document.createElement('div'); bd.className = 'bd';
        var m = document.createElement('div'); m.className = 'meta'; m.textContent = i.category || '';
        var h = document.createElement('h3'); var a = document.createElement('a');
        a.href = i.url; a.textContent = i.title; h.appendChild(a);
        var p = document.createElement('p'); p.textContent = i.excerpt || '';
        bd.appendChild(m); bd.appendChild(h); bd.appendChild(p); card.appendChild(bd); box.appendChild(card);
      });
    }).catch(function () { status.textContent = 'Search is unavailable right now.'; });
  }
})();
