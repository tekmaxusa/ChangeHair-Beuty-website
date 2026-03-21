<?php

declare(strict_types=1);

/** @var array $salon */

$y = (int) date('Y');

$contactFlash = null;
if (isset($_SESSION['chb_contact_flash'])) {
    $contactFlash = $_SESSION['chb_contact_flash'];
    unset($_SESSION['chb_contact_flash']);
}

$csrf = (string) ($_SESSION['chb_csrf_contact'] ?? '');
$contactEmail = trim((string) ($salon['contact_email'] ?? ''));
$mailto = $contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)
    ? 'mailto:' . $contactEmail
    : '';

?>
<section id="contact" class="chb-band chb-band--beige">
    <div class="chb-wrap chb-contact-shell">
        <div class="chb-contact-top">
            <div class="chb-contact-form-col">
                <h2 class="chb-display">Contact Us</h2>
                <p class="chb-contact-lead">We'd love to hear from you</p>

                <?php if (is_array($contactFlash) && isset($contactFlash['ok'], $contactFlash['message'])): ?>
                    <p class="chb-contact-flash <?= !empty($contactFlash['ok']) ? 'chb-contact-flash--ok' : 'chb-contact-flash--err' ?>" role="status">
                        <?= h((string) $contactFlash['message']) ?>
                    </p>
                <?php endif; ?>

                <form class="chb-contact-form" method="post" action="/contact-send.php" novalidate>
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                    <div class="chb-contact-hp" aria-hidden="true">
                        <label>Leave blank <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                    </div>
                    <label class="chb-contact-label">
                        <span>Name</span>
                        <input type="text" name="name" required maxlength="120" autocomplete="name" placeholder="Your name">
                    </label>
                    <label class="chb-contact-label">
                        <span>Email</span>
                        <input type="email" name="email" required maxlength="254" autocomplete="email" placeholder="you@example.com">
                    </label>
                    <label class="chb-contact-label">
                        <span>Phone</span>
                        <input type="tel" name="phone" maxlength="40" autocomplete="tel" placeholder="Optional">
                    </label>
                    <label class="chb-contact-label">
                        <span>Message</span>
                        <textarea name="message" required maxlength="6000" rows="5" placeholder="How can we help?"></textarea>
                    </label>
                    <button type="submit" class="chb-btn-gold chb-contact-submit">Send Message</button>
                </form>
            </div>

            <div class="chb-contact-visit-col">
                <h2 class="chb-display chb-contact-visit-heading">Visit Our Studio</h2>
                <div class="chb-contact-block">
                    <p class="chb-kicker chb-kicker-gold">Location</p>
                    <p class="chb-prose"><?= h($salon['location_name']) ?><br><?= h($salon['address']) ?></p>
                </div>
                <div class="chb-contact-block">
                    <p class="chb-kicker chb-kicker-gold">Hours</p>
                    <ul class="chb-hours">
                        <?php foreach ($salon['hours'] as $line): ?>
                            <li><?= h($line) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="chb-contact-block">
                    <p class="chb-kicker chb-kicker-gold">Contact</p>
                    <p class="chb-prose">Phone: <a href="tel:<?= h($salon['phone_tel']) ?>"><?= h($salon['phone']) ?></a></p>
                    <?php if ($mailto !== ''): ?>
                        <p class="chb-prose"><a href="<?= h($mailto) ?>"><?= h($contactEmail) ?></a></p>
                    <?php endif; ?>
                    <p class="chb-prose"><a class="chb-tawk-open" href="<?= h($salon['tawk_chat']) ?>" target="_blank" rel="noopener noreferrer">Live chat</a></p>
                </div>
            </div>
        </div>

        <div class="chb-map">
            <iframe
                title="Change Hair &amp; Beauty location"
                src="<?= h($salon['map_embed']) ?>"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
        </div>
    </div>
</section>

<footer class="chb-site-footer">
    <div class="chb-footer-inner">
        <a href="/" class="chb-logo chb-logo--footer">
            <span class="chb-logo-line1">CHANGE HAIR</span>
            <span class="chb-logo-line2">&amp; BEAUTY</span>
        </a>
        <div class="chb-footer-social">
            <a class="chb-footer-social-link chb-tawk-open" href="<?= h($salon['tawk_chat']) ?>" target="_blank" rel="noopener noreferrer" title="Live chat">Chat</a>
            <?php
            $chbSocialInstagramLabel = 'Instagram';
            $chbSocialFacebookLabel = 'Facebook';
            require __DIR__ . '/social-ig-fb-anchors.php';
            ?>
        </div>
        <p class="chb-footer-copy">Copyright © <?= $y ?>, Change Hair &amp; Beauty</p>
    </div>
</footer>
