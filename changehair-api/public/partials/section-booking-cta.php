<?php

declare(strict_types=1);

/** @var string $bookHref */

?>
<section id="booking" class="chb-band chb-band--white">
    <div class="chb-wrap chb-book-cta">
        <h2 class="chb-display chb-display-center">Book appointment</h2>
        <p class="chb-lead-center">Request a time in a quick window — sign in, sign up, or use Google when you’re ready to submit. Track status anytime in your <a href="/dashboard/">client dashboard</a>.</p>
        <div class="chb-book-actions">
            <a class="chb-btn-gold chb-booking-open" href="<?= h($bookHref) ?>">Book appointment</a>
            <a class="chb-nav-book chb-nav-book--standalone" href="/contact.php">Contact us</a>
        </div>
    </div>
</section>
