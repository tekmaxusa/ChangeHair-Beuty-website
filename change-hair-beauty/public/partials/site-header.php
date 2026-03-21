<?php

declare(strict_types=1);

/** @var bool $loggedIn */

$nextDash = rawurlencode('/dashboard/');
if (!isset($bookHref)) {
    $bookHref = '/book-appointment.php';
}
$dashHref = $loggedIn ? '/dashboard/' : '/login.php?next=' . $nextDash;

?>
<nav class="chb-topbar" id="chb-topbar" aria-label="Primary">
    <input type="checkbox" id="chb-nav-toggle" class="chb-nav-toggle" hidden>

    <div class="chb-topbar-inner">
        <a href="/" class="chb-logo" aria-label="CHANGE HAIR &amp; BEAUTY home">
            <span class="chb-logo-line1">CHANGE HAIR</span>
            <span class="chb-logo-line2">&amp; BEAUTY</span>
        </a>

        <label for="chb-nav-toggle" class="chb-nav-burger" aria-label="Open menu"><span></span><span></span><span></span></label>

        <div class="chb-nav-desktop">
            <a class="chb-nav-pill" href="<?= h($dashHref) ?>">CLIENT DASHBOARD</a>
            <a class="chb-nav-pill" href="/#services">MENU</a>
            <?php if (!$loggedIn): ?>
                <a class="chb-nav-quiet" href="/login.php?next=<?= h($nextDash) ?>">LOG IN</a>
                <a class="chb-nav-quiet" href="/signup.php">SIGN UP</a>
            <?php else: ?>
                <a class="chb-nav-quiet" href="/logout.php">LOG OUT</a>
            <?php endif; ?>
            <a class="chb-nav-book chb-booking-open" href="<?= h($bookHref) ?>">BOOK APPOINTMENT</a>
        </div>
    </div>

    <div class="chb-nav-drawer" aria-hidden="true">
        <a class="chb-nav-drawer-link" href="<?= h($dashHref) ?>">Client dashboard</a>
        <a class="chb-nav-drawer-link" href="/#services">Menu</a>
        <?php if (!$loggedIn): ?>
            <a class="chb-nav-drawer-link" href="/login.php?next=<?= h($nextDash) ?>">Log in</a>
            <a class="chb-nav-drawer-link" href="/signup.php">Sign up</a>
        <?php else: ?>
            <a class="chb-nav-drawer-link" href="/logout.php">Log out</a>
        <?php endif; ?>
        <a class="chb-btn-gold chb-nav-drawer-cta chb-booking-open" href="<?= h($bookHref) ?>">BOOK APPOINTMENT</a>
    </div>
</nav>

<?php require __DIR__ . '/booking-modal.php'; ?>
