(function () {
  var bar = document.getElementById('chb-topbar');
  if (bar) {
    function onScroll() {
      if (window.scrollY > 50) {
        bar.classList.add('chb-topbar--solid');
      } else {
        bar.classList.remove('chb-topbar--solid');
      }
    }

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  var toggle = document.getElementById('chb-nav-toggle');
  if (toggle) {
    document.querySelectorAll('.chb-nav-drawer a').forEach(function (a) {
      a.addEventListener('click', function () {
        toggle.checked = false;
      });
    });
  }

  document.addEventListener(
    'click',
    function (e) {
      var t = e.target;
      if (!t || !t.closest) return;
      var a = t.closest('a.chb-tawk-open');
      if (!a) return;
      if (window.Tawk_API && typeof Tawk_API.maximize === 'function') {
        e.preventDefault();
        Tawk_API.maximize();
      }
    },
    false
  );

  function initTestimonialsCarousel() {
    var root = document.querySelector('[data-chb-testi]');
    if (!root) return;

    var viewport = root.querySelector('.chb-testi-viewport');
    var track = root.querySelector('.chb-testi-track');
    var dotsHost = root.querySelector('[data-chb-testi-dots]');
    if (!viewport || !track || !dotsHost) return;

    var cards = Array.prototype.slice.call(track.children);
    var n = cards.length;
    if (n === 0) return;

    var gapPx = 20;
    var index = 0;

    function perView() {
      if (window.matchMedia('(min-width: 1100px)').matches) return 3;
      if (window.matchMedia('(min-width: 700px)').matches) return 2;
      return 1;
    }

    function maxIndex() {
      return Math.max(0, n - perView());
    }

    function clampIndex() {
      var m = maxIndex();
      if (index > m) index = m;
    }

    function buildDots() {
      var m = maxIndex();
      dotsHost.innerHTML = '';
      for (var i = 0; i <= m; i++) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'chb-testi-dot' + (i === index ? ' is-active' : '');
        btn.setAttribute('role', 'tab');
        btn.setAttribute('aria-selected', i === index ? 'true' : 'false');
        btn.setAttribute('aria-label', 'Show testimonial group ' + (i + 1) + ' of ' + (m + 1));
        (function (idx) {
          btn.addEventListener('click', function () {
            index = idx;
            layout();
          });
        })(i);
        dotsHost.appendChild(btn);
      }
    }

    function layout() {
      clampIndex();
      var pv = perView();
      var w = viewport.getBoundingClientRect().width;
      var totalGap = gapPx * Math.max(0, pv - 1);
      var cardW = (w - totalGap) / pv;

      track.style.gap = gapPx + 'px';
      cards.forEach(function (el) {
        el.style.flexBasis = cardW + 'px';
        el.style.width = cardW + 'px';
        el.style.minWidth = cardW + 'px';
        el.style.maxWidth = cardW + 'px';
      });

      var step = cardW + gapPx;
      track.style.transform = 'translate3d(' + (-index * step) + 'px,0,0)';

      var dots = dotsHost.querySelectorAll('.chb-testi-dot');
      if (dots.length !== maxIndex() + 1) {
        buildDots();
        dots = dotsHost.querySelectorAll('.chb-testi-dot');
      }
      dots.forEach(function (d, j) {
        var on = j === index;
        d.classList.toggle('is-active', on);
        d.setAttribute('aria-selected', on ? 'true' : 'false');
      });
    }

    if (typeof ResizeObserver !== 'undefined') {
      new ResizeObserver(layout).observe(viewport);
    }
    window.addEventListener('resize', layout, { passive: true });

    var startX = 0;
    var dx = 0;
    viewport.addEventListener(
      'touchstart',
      function (e) {
        if (!e.touches || !e.touches[0]) return;
        startX = e.touches[0].clientX;
        dx = 0;
      },
      { passive: true }
    );
    viewport.addEventListener(
      'touchmove',
      function (e) {
        if (!e.touches || !e.touches[0]) return;
        dx = e.touches[0].clientX - startX;
      },
      { passive: true }
    );
    viewport.addEventListener(
      'touchend',
      function () {
        if (Math.abs(dx) < 48) return;
        if (dx < 0 && index < maxIndex()) index += 1;
        else if (dx > 0 && index > 0) index -= 1;
        dx = 0;
        layout();
      },
      { passive: true }
    );

    buildDots();
    layout();
  }

  initTestimonialsCarousel();
})();
