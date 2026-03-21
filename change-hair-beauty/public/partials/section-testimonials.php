<?php

declare(strict_types=1);

/** @var array $salon */

?>
<section class="chb-band chb-band--testimonials chb-band--ink" aria-label="Client testimonials">
    <div class="chb-wrap">
        <div class="chb-testi-head">
            <p class="chb-kicker chb-kicker-center chb-kicker-gold">Client Voices</p>
            <h2 class="chb-display chb-display-center chb-display-light chb-testi-title">What Our Clients Say</h2>
        </div>

        <div class="chb-testi-carousel" data-chb-testi>
            <div class="chb-testi-viewport">
                <div class="chb-testi-track">
                    <?php foreach ($salon['testimonials'] as $t): ?>
                        <blockquote class="chb-testi-card">
                            <span class="chb-testi-icon-wrap" aria-hidden="true">
                                <svg class="chb-testi-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                                </svg>
                            </span>
                            <p class="chb-testi-quote">“<?= h($t['text']) ?>”</p>
                            <footer class="chb-testi-name"><span class="chb-testi-name-dash" aria-hidden="true"></span><?= h($t['name']) ?></footer>
                        </blockquote>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="chb-testi-dots" data-chb-testi-dots role="tablist" aria-label="Testimonial slides"></div>
        </div>
    </div>
</section>
