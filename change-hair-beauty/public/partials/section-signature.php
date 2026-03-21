<?php

declare(strict_types=1);

/** @var array $salon */
/** @var string $bookHref */

?>
<section class="chb-band chb-band--beige">
    <div class="chb-wrap">
        <div class="chb-sig-intro">
            <p class="chb-kicker">Signature Services</p>
            <h2 class="chb-display chb-display-center">Cut · Color · Perm · Style</h2>
        </div>

        <?php foreach ($salon['signature'] as $block): ?>
            <div class="chb-sig-row<?= !empty($block['img_left']) ? ' chb-sig-row--flip' : '' ?>">
                <div class="chb-sig-copy">
                    <p class="chb-kicker"><?= h($block['label']) ?></p>
                    <h3 class="chb-sig-title"><?= h($block['title']) ?></h3>
                    <p class="chb-prose chb-prose-tight"><?= h($block['text']) ?></p>
                    <ul class="chb-sig-list">
                        <?php foreach ($block['items'] as $line): ?>
                            <li><?= h($line) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a class="chb-btn-gold" href="<?= h($bookHref) ?>">Book appointment</a>
                </div>
                <div class="chb-sig-media">
                    <img src="<?= h($block['img']) ?>" alt="" width="800" height="450" loading="lazy" decoding="async"<?= str_starts_with($block['img'], 'http') ? ' referrerpolicy="no-referrer"' : '' ?>>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
