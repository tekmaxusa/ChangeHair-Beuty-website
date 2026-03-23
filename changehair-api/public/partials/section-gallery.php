<?php

declare(strict_types=1);

/** @var array $salon */

?>
<section id="gallery" class="chb-band chb-band--beige">
    <div class="chb-wrap">
        <div class="chb-gallery-head">
            <h2 class="chb-display chb-display-center">Visual Portfolio</h2>
            <p class="chb-gallery-sub">A glimpse into our signature K-beauty transformations.</p>
        </div>
        <div class="chb-gallery-grid">
            <?php foreach ($salon['gallery'] as $idx => $item): ?>
                <a class="chb-gallery-cell" href="<?= h($item['url']) ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?= h($item['img']) ?>" alt="Gallery <?= (int) $idx + 1 ?>" width="600" height="600" loading="lazy" decoding="async">
                    <span class="chb-gallery-ig" aria-hidden="true">
                        <svg class="chb-gallery-ig-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                            <circle cx="12" cy="12" r="4"/>
                            <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
                        </svg>
                        <span class="chb-gallery-ig-label">Instagram</span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="chb-social-row">
            <?php
            $chbSocialInstagramLabel = 'Follow on Instagram';
            $chbSocialFacebookLabel = 'Follow on Facebook';
            require __DIR__ . '/social-ig-fb-anchors.php';
            ?>
        </div>
    </div>
</section>
