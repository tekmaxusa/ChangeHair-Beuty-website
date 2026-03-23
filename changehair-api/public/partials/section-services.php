<?php

declare(strict_types=1);

/** @var array $salon */

?>
<section id="services" class="chb-band chb-band--white">
    <div class="chb-wrap chb-services-head">
        <p class="chb-kicker chb-kicker-center">Menu</p>
        <h2 class="chb-display chb-display-center">Our Services</h2>
        <p class="chb-lead-center">Cut, Color, Perm &amp; Style — prices below. Book online after you log in, or call us.</p>
    </div>
    <div class="chb-wrap chb-menu-grid">
        <?php foreach ($salon['service_menu'] as $cat): ?>
            <div class="chb-menu-col">
                <h3 class="chb-menu-cat"><?= h($cat['category']) ?></h3>
                <div class="chb-menu-lines">
                    <?php foreach ($cat['items'] as $item): ?>
                        <div class="chb-menu-line">
                            <span class="chb-menu-name"><?= h($item['name']) ?></span>
                            <span class="chb-menu-dots" aria-hidden="true"></span>
                            <span class="chb-menu-price"><?= h($item['price']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
