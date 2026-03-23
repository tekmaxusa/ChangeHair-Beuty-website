<?php

declare(strict_types=1);

/** @var array $salon */

?>
<section id="story" class="chb-band chb-band--white">
    <div class="chb-wrap chb-story">
        <div class="chb-story-text">
            <p class="chb-kicker chb-story-kicker">Our Story</p>
            <h2 class="chb-display chb-story-title">Change Hair<br><span class="chb-story-title-italic">&amp; Beauty in Lewisville</span></h2>
            <div class="chb-prose chb-story-prose">
                <p>Located at The Vista in Lewisville TX, our salon is a sanctuary dedicated to the art of beauty. We believe that hair is the ultimate expression of self, and our mission is to provide personalized styling that enhances your natural beauty.</p>
                <p>Our team of expert stylists are trained in the latest K-beauty techniques, from the effortless waves of a digital perm to the precision of a down perm. We use only premium products and state-of-the-art equipment to ensure the health and vitality of your hair.</p>
            </div>
            <div class="chb-stats">
                <div>
                    <p class="chb-stat-num">20+</p>
                    <p class="chb-stat-label">Years Experience</p>
                </div>
                <div>
                    <p class="chb-stat-num">5k+</p>
                    <p class="chb-stat-label">Happy Clients</p>
                </div>
            </div>
        </div>
        <div class="chb-story-photo">
            <img src="<?= h($salon['story_img']) ?>" alt="Salon interior" width="800" height="1000" loading="lazy" decoding="async" referrerpolicy="no-referrer">
        </div>
    </div>
</section>
