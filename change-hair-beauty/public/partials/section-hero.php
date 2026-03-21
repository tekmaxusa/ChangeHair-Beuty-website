<?php

declare(strict_types=1);

/** @var array $salon */
/** @var string $bookHref */

$slides = $salon['hero_slides'];
$slidesJson = json_encode($slides, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

?>
<section class="chb-hero" aria-label="Welcome">
    <div class="chb-hero-bg">
        <img src="<?= h($salon['hero_bg']) ?>" alt="" class="chb-hero-bg-img" width="1920" height="1080" decoding="async" fetchpriority="high" referrerpolicy="no-referrer">
    </div>
    <div class="chb-hero-scrim" aria-hidden="true"></div>
    <div class="chb-hero-content">
        <h1 class="chb-hero-heading" id="chb-hero-slide"><?= h($slides[0]) ?></h1>
        <a class="chb-btn-gold chb-hero-cta" href="<?= h($bookHref) ?>">Book appointment</a>
    </div>
    <script type="application/json" id="chb-hero-slides-data"><?= $slidesJson ?></script>
    <script>
    (function(){
      var el = document.getElementById('chb-hero-slide');
      var raw = document.getElementById('chb-hero-slides-data');
      if (!el || !raw) return;
      var slides = JSON.parse(raw.textContent || '[]');
      if (!slides.length) return;
      var i = 0;
      setInterval(function(){
        i = (i + 1) % slides.length;
        el.textContent = slides[i];
      }, 5000);
    })();
    </script>
</section>
