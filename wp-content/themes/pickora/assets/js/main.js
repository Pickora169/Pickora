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
  if (!lb) return;
  var lastFocus = null;
  var focusables = 'a[href],button:not([disabled]),input,select,textarea,[tabindex]:not([tabindex="-1"])';
  function open() {
    lastFocus = document.activeElement;
    lb.classList.add('show');
    void lb.offsetWidth;
    lb.classList.add('in');
    document.body.style.overflow = 'hidden';
    lb.querySelector('.lb-x').focus();
  }
  function close() {
    lb.classList.remove('in');
    document.body.style.overflow = '';
    setTimeout(function () { lb.classList.remove('show'); }, 250);
    if (lastFocus) lastFocus.focus();
  }
  document.querySelectorAll('[data-pk-open]').forEach(function (el) {
    el.addEventListener('click', function (e) { e.preventDefault(); open(); });
  });
  lb.addEventListener('click', function (e) {
    if (e.target === lb || e.target.closest('.lb-x')) close();
  });
  document.addEventListener('keydown', function (e) {
    if (!lb.classList.contains('show')) return;
    if (e.key === 'Escape') { close(); return; }
    if (e.key === 'Tab') {
      var f = lb.querySelectorAll(focusables);
      if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
})();
